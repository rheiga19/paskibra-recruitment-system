<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Rekrutmen;
use App\Models\SeleksiHasil;
use App\Models\SeleksiTahap;
use Illuminate\Http\Request;

class PanitiaController extends Controller
{
    // ── Dashboard ──────────────────────────────────────────────────────
    public function dashboard()
    {
        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->first();

        $totalPendaftar    = Pendaftaran::count();
        $menungguVerifikasi = Pendaftaran::where('status', 'menunggu')->count();
        $sudahDinilai      = SeleksiHasil::whereNotNull('nilai_total')->distinct('pendaftaran_id')->count();
        $lulusFinal        = Pendaftaran::where('is_lulus_final', true)->count();

        $pendaftaranMenunggu = Pendaftaran::where('status', 'menunggu')
            ->latest()->limit(6)->get();

        return view('panitia.dashboard', compact(
            'rekrutmenAktif',
            'totalPendaftar',
            'menungguVerifikasi',
            'sudahDinilai',
            'lulusFinal',
            'pendaftaranMenunggu'
        ));
    }

    // ── Verifikasi Index ───────────────────────────────────────────────
    public function verifikasiIndex(Request $request)
    {
        $query = Pendaftaran::latest();

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%");
            });
        }
        if ($status = $request->status) $query->where('status', $status);
        if ($jk     = $request->jk)     $query->where('jenis_kelamin', $jk);

        $pendaftaran = $query->with('dokumen')->paginate(15)->withQueryString();

        return view('panitia.verifikasi.index', [
            'pendaftaran' => $pendaftaran,
            'total'       => Pendaftaran::count(),
            'menunggu'    => Pendaftaran::where('status', 'menunggu')->count(),
            'diterima'    => Pendaftaran::where('status', 'diverifikasi')->count(),
            'ditolak'     => Pendaftaran::where('status', 'ditolak')->count(),
        ]);
    }

    // ── Verifikasi Show ────────────────────────────────────────────────
    public function verifikasiShow(Pendaftaran $pendaftaran)
    {
        // Load relasi dokumen
        $pendaftaran->load('dokumen');

        // Ubah collection dokumen jadi array key => item
        $dokumen = $pendaftaran->dokumen->keyBy('jenis');

        $ids  = Pendaftaran::orderBy('id')->pluck('id');
        $idx  = $ids->search($pendaftaran->id);
        $prev = $idx > 0                 ? Pendaftaran::find($ids[$idx - 1]) : null;
        $next = $idx < $ids->count() - 1 ? Pendaftaran::find($ids[$idx + 1]) : null;

        return view('panitia.verifikasi.show', compact('pendaftaran', 'dokumen', 'prev', 'next'));
    }

    // ── Verifikasi Proses ──────────────────────────────────────────────
    public function verifikasiProses(Request $request, Pendaftaran $pendaftaran)
    {
        $request->validate([
            'keputusan' => ['required', 'in:diverifikasi,ditolak'],
            'catatan'   => ['nullable', 'string', 'max:500'],
        ]);

        $pendaftaran->update([
            'status'             => $request->keputusan,
            'catatan_verifikasi' => $request->catatan,
            'diverifikasi_oleh'  => auth()->id(),
            'diverifikasi_at'    => now(),
        ]);

        $pesan = $request->keputusan === 'diverifikasi'
            ? "Pendaftaran {$pendaftaran->nama_lengkap} berhasil diterima."
            : "Pendaftaran {$pendaftaran->nama_lengkap} ditolak.";

        return redirect()->route('panitia.verifikasi.index')->with('success', $pesan);
    }

    // ── Seleksi Index ──────────────────────────────────────────────────
    public function seleksiIndex(Request $request)
    {
        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->first();
        $rekrutmenId    = $request->get('rekrutmen_id', $rekrutmenAktif?->id);
        $rekrutmenList  = Rekrutmen::orderByDesc('tahun')->get();

        $tahapList  = SeleksiTahap::where('rekrutmen_id', $rekrutmenId)->orderBy('urutan')->get();
        $tahapAktif = $request->filled('tahap_id')
            ? SeleksiTahap::find($request->tahap_id)
            : $tahapList->first();

        $tahapId = $tahapAktif?->id;

        $query = Pendaftaran::where('rekrutmen_id', $rekrutmenId)
            ->where('status', '!=', 'menunggu')
            ->with(['hasilSeleksi' => fn($q) => $q->where('seleksi_tahap_id', $tahapId)])
            ->latest();

        if ($search = $request->search) {
            $query->where('nama_lengkap', 'like', "%{$search}%");
        }
        if ($jk = $request->jk) $query->where('jenis_kelamin', $jk);

        if ($tahapAktif) {
            if ($request->nilai === 'belum') {
                $query->whereDoesntHave('hasilSeleksi', fn($q) =>
                    $q->where('seleksi_tahap_id', $tahapAktif->id)->whereNotNull('nilai_total')
                );
            } elseif ($request->nilai === 'sudah') {
                $query->whereHas('hasilSeleksi', fn($q) =>
                    $q->where('seleksi_tahap_id', $tahapAktif->id)->whereNotNull('nilai_total')
                );
            }
        }

        $pendaftaran  = $query->paginate(15)->withQueryString();
        $totalPeserta = Pendaftaran::where('rekrutmen_id', $rekrutmenId)
            ->where('status', '!=', 'menunggu')->count();
        $sudah        = $tahapAktif
            ? SeleksiHasil::where('seleksi_tahap_id', $tahapAktif->id)->whereNotNull('nilai_total')->count()
            : 0;

        return view('panitia.seleksi.index', [
            'pendaftaran'   => $pendaftaran,
            'tahap'         => $tahapAktif,
            'tahapList'     => $tahapList,
            'rekrutmenList' => $rekrutmenList,
            'rekrutmenId'   => $rekrutmenId,
            'totalPeserta'  => $totalPeserta,
            'belumDinilai'  => $totalPeserta - $sudah,
            'sudahDinilai'  => $sudah,
            'lolosSeleksi'  => $tahapAktif
                ? SeleksiHasil::where('seleksi_tahap_id', $tahapAktif->id)->where('status', 'lolos')->count()
                : 0,
        ]);
    }

    // ── Seleksi Input ──────────────────────────────────────────────────
    public function seleksiInput(Pendaftaran $pendaftaran, SeleksiTahap $tahap)
    {
        $hasil = SeleksiHasil::firstOrNew([
            'pendaftaran_id'   => $pendaftaran->id,
            'seleksi_tahap_id' => $tahap->id,
        ]);

        return view('panitia.seleksi.input', compact('pendaftaran', 'tahap', 'hasil'));
    }

    // ── Seleksi Simpan ─────────────────────────────────────────────────
    public function seleksiSimpan(Request $request, Pendaftaran $pendaftaran, SeleksiTahap $tahap)
    {
        $request->validate([
            'nilai_pancasila' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai_tiu'       => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai_pbb'       => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai_fisik'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai_wawancara' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'catatan'         => ['nullable', 'string', 'max:500'],
        ]);

        $nilaiArr = collect([
            $request->nilai_pancasila,
            $request->nilai_tiu,
            $request->nilai_pbb,
            $request->nilai_fisik,
            $request->nilai_wawancara,
        ])->filter(fn($v) => !is_null($v));

        $nilaiTotal = $nilaiArr->count()
            ? round($nilaiArr->sum() / $nilaiArr->count(), 2)
            : null;

        $kkm    = $tahap->passing_grade ?? 70;
        $status = $nilaiTotal !== null
            ? ($nilaiTotal >= $kkm ? 'lolos' : 'tidak_lolos')
            : 'pending';

        SeleksiHasil::updateOrCreate(
            ['pendaftaran_id' => $pendaftaran->id, 'seleksi_tahap_id' => $tahap->id],
            [
                'nilai_pancasila' => $request->nilai_pancasila,
                'nilai_tiu'       => $request->nilai_tiu,
                'nilai_pbb'       => $request->nilai_pbb,
                'nilai_fisik'     => $request->nilai_fisik,
                'nilai_wawancara' => $request->nilai_wawancara,
                'nilai_total'     => $nilaiTotal,
                'status'          => $status,
                'catatan'         => $request->catatan,
                'dinilai_oleh'    => auth()->id(),
            ]
        );

        // Update nilai_akhir di tabel pendaftaran
        $rataRata = SeleksiHasil::where('pendaftaran_id', $pendaftaran->id)->avg('nilai_total');
        $pendaftaran->update(['nilai_akhir' => round($rataRata, 2)]);

        return redirect()->route('panitia.seleksi.index', [
            'rekrutmen_id' => $pendaftaran->rekrutmen_id,
            'tahap_id'     => $tahap->id,
        ])->with('success', "Nilai {$pendaftaran->nama_lengkap} berhasil disimpan.");
    }

    // ── Hasil Akhir ────────────────────────────────────────────────────
    public function hasilIndex(Request $request)
    {
        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->first();
        $rekrutmenId    = $request->get('rekrutmen_id', $rekrutmenAktif?->id);
        $rekrutmenList  = Rekrutmen::orderByDesc('tahun')->get();
        $rekrutmen      = Rekrutmen::find($rekrutmenId);

        $tahapList  = SeleksiTahap::where('rekrutmen_id', $rekrutmenId)->orderBy('urutan')->get();
        $tahapAktif = $request->filled('tahap_id')
            ? SeleksiTahap::find($request->tahap_id)
            : null; // null = tampilkan semua tahap

        // Query hasil
        $query = SeleksiHasil::with('pendaftaran')
            ->whereHas('pendaftaran', fn($q) => $q->where('rekrutmen_id', $rekrutmenId))
            ->whereNotNull('nilai_total')
            ->orderByDesc('nilai_total');

        if ($tahapAktif) {
            $query->where('seleksi_tahap_id', $tahapAktif->id);
        }

        $hasil = $query->get();

        return view('panitia.hasil.index', [
            'hasil'          => $hasil,
            'rekrutmen'      => $rekrutmen,
            'rekrutmenList'  => $rekrutmenList,
            'rekrutmenId'    => $rekrutmenId,
            'tahapList'      => $tahapList,
            'tahapAktif'     => $tahapAktif,
            'totalDinilai'   => $hasil->count(),
            'totalLolos'     => $hasil->where('status', 'lolos')->count(),
            'totalTidakLolos'=> $hasil->where('status', 'tidak_lolos')->count(),
            'nilaiTertinggi' => $hasil->max('nilai_total'),
            'nilaiRataRata'  => $hasil->avg('nilai_total'),
        ]);
    }
}