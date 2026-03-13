<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Rekrutmen;
use App\Models\SeleksiHasil;
use App\Models\SeleksiTahap;
use Illuminate\Http\Request;

class SeleksiController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->first();
        $rekrutmenId    = $request->get('rekrutmen_id', $rekrutmenAktif?->id);
        $rekrutmen      = Rekrutmen::orderByDesc('tahun')->get();
        $tahapList      = SeleksiTahap::where('rekrutmen_id', $rekrutmenId)
                            ->orderBy('urutan')->get();
        $tahapAktif     = $request->filled('tahap_id')
                            ? SeleksiTahap::find($request->tahap_id)
                            : $tahapList->first();

        $peserta = collect();
        if ($tahapAktif) {
            $peserta = Pendaftaran::with([
                    'hasilSeleksi' => fn($q) => $q->where('seleksi_tahap_id', $tahapAktif->id)
                ])
                ->where('rekrutmen_id', $rekrutmenId)
                ->where('status', '!=', 'menunggu')
                ->orderBy('nama_lengkap')
                ->paginate(20)
                ->withQueryString();
        }

        return view('admin.seleksi.index', compact(
            'rekrutmen', 'rekrutmenId', 'tahapList', 'tahapAktif', 'peserta'
        ));
    }

    // ── Input Nilai ───────────────────────────────────────────────────
    public function inputNilai(Request $request, Pendaftaran $pendaftaran, SeleksiTahap $seleksiTahap)
    {
        $hasil = SeleksiHasil::firstOrNew([
            'pendaftaran_id'   => $pendaftaran->id,
            'seleksi_tahap_id' => $seleksiTahap->id,
        ]);

        return view('admin.seleksi.input-nilai', compact('pendaftaran', 'hasil'))
            ->with('tahap', $seleksiTahap);
    }

    // ── Simpan Nilai ──────────────────────────────────────────────────
    public function simpanNilai(Request $request, Pendaftaran $pendaftaran, SeleksiTahap $seleksiTahap)
    {
        $data = $request->validate([
            'nilai_pancasila' => 'nullable|numeric|min:0|max:100',
            'nilai_tiu'       => 'nullable|numeric|min:0|max:100',
            'nilai_pbb'       => 'nullable|numeric|min:0|max:100',
            'nilai_fisik'     => 'nullable|numeric|min:0|max:100',
            'nilai_wawancara' => 'nullable|numeric|min:0|max:100',
            'catatan'         => 'nullable|string|max:1000',
        ]);

        $data['dinilai_oleh']     = auth()->id();
        $data['seleksi_tahap_id'] = $seleksiTahap->id;
        $data['pendaftaran_id']   = $pendaftaran->id;

        // Hitung rata-rata komponen yang diisi
        $komponen = array_filter([
            $data['nilai_pancasila'] ?? null,
            $data['nilai_tiu']       ?? null,
            $data['nilai_pbb']       ?? null,
            $data['nilai_fisik']     ?? null,
            $data['nilai_wawancara'] ?? null,
        ], fn($v) => $v !== null);

        $data['nilai_total'] = empty($komponen)
            ? 0
            : round(array_sum($komponen) / count($komponen), 2);

        $data['status'] = $data['nilai_total'] >= ($seleksiTahap->passing_grade ?? 70)
            ? 'lolos' : 'tidak_lolos';

        SeleksiHasil::updateOrCreate(
            ['pendaftaran_id' => $pendaftaran->id, 'seleksi_tahap_id' => $seleksiTahap->id],
            $data
        );

        // Update nilai_akhir di pendaftaran (rata-rata semua tahap)
        $rataRata = SeleksiHasil::where('pendaftaran_id', $pendaftaran->id)->avg('nilai_total');
        $pendaftaran->update(['nilai_akhir' => round($rataRata, 2)]);

        return redirect()->route('admin.seleksi.index', [
            'rekrutmen_id' => $pendaftaran->rekrutmen_id,
            'tahap_id'     => $seleksiTahap->id,
        ])->with('success', "Nilai {$pendaftaran->nama_lengkap} berhasil disimpan.");
    }

    // ── Hasil Akhir ───────────────────────────────────────────────────
    public function hasilAkhir(Request $request)
    {
        $rekrutmenId = $request->get('rekrutmen_id', Rekrutmen::where('is_aktif', true)->value('id'));
        $rekrutmen   = Rekrutmen::find($rekrutmenId);

        $peserta = Pendaftaran::with(['hasilSeleksi.tahap'])
            ->where('rekrutmen_id', $rekrutmenId)
            ->orderByDesc('nilai_akhir')
            ->paginate(25)
            ->withQueryString();

        $rekrutmenList = Rekrutmen::orderByDesc('tahun')->get();

        return view('admin.seleksi.hasil-akhir', compact(
            'peserta', 'rekrutmen', 'rekrutmenList', 'rekrutmenId'
        ));
    }

    // ── Proses Kelulusan ──────────────────────────────────────────────
    public function prosesKelulusan(Request $request)
    {
        $request->validate([
            'rekrutmen_id' => 'required|exists:rekrutmen,id',
            'ids'          => 'nullable|array',
            'ids.*'        => 'exists:pendaftaran,id',
        ]);

        // Reset semua jadi tidak lulus
        Pendaftaran::where('rekrutmen_id', $request->rekrutmen_id)
            ->update(['is_lulus_final' => false, 'status' => 'tidak_lulus']);

        // Yang dicentang → lulus
        if (!empty($request->ids)) {
            Pendaftaran::whereIn('id', $request->ids)->update([
                'is_lulus_final'      => true,
                'status'              => 'lulus',
                'tanggal_lulus_final' => now(),
            ]);
        }

        $jumlah = count($request->ids ?? []);
        return back()->with('success', "{$jumlah} peserta berhasil ditetapkan lulus.");
    }

    // ── Tambah Tahap ──────────────────────────────────────────────────
    public function storeTahap(Request $request, Rekrutmen $rekrutmen)
    {
        $data = $request->validate([
            'nama'          => 'required|string|max:255',
            'urutan'        => 'required|integer|min:1',
            'passing_grade' => 'nullable|numeric|min:0|max:100',
            'deskripsi'     => 'nullable|string',
        ]);
        $rekrutmen->seleksiTahap()->create($data);
        return back()->with('success', 'Tahap seleksi berhasil ditambahkan.');
    }

    // ── Hapus Tahap ───────────────────────────────────────────────────
    public function destroyTahap(SeleksiTahap $seleksiTahap)
    {
        $seleksiTahap->hasilSeleksi()->delete();
        $seleksiTahap->delete();
        return back()->with('success', 'Tahap seleksi berhasil dihapus.');
    }

    // ── Toggle Pengumuman ─────────────────────────────────────────────
    public function togglePengumuman(SeleksiTahap $seleksiTahap)
    {
        $seleksiTahap->update([
            'is_diumumkan'       => !$seleksiTahap->is_diumumkan,
            'tanggal_pengumuman' => !$seleksiTahap->is_diumumkan ? now() : null,
        ]);

        $pesan = $seleksiTahap->is_diumumkan
            ? 'Hasil tahap berhasil diumumkan.'
            : 'Pengumuman berhasil disembunyikan.';

        return back()->with('success', $pesan);
    }
}