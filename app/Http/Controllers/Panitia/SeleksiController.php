<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Rekrutmen;
use App\Models\SeleksiHasil;
use App\Models\SeleksiTahap;
use Illuminate\Http\Request;

class SeleksiController extends Controller
{
    public function index(Request $request)
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

    public function input(Pendaftaran $pendaftaran, SeleksiTahap $tahap)
    {
        $hasil = SeleksiHasil::firstOrNew([
            'pendaftaran_id'   => $pendaftaran->id,
            'seleksi_tahap_id' => $tahap->id,
        ]);

        return view('panitia.seleksi.input', compact('pendaftaran', 'tahap', 'hasil'));
    }

    public function simpan(Request $request, Pendaftaran $pendaftaran, SeleksiTahap $tahap)
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

        $rataRata = SeleksiHasil::where('pendaftaran_id', $pendaftaran->id)->avg('nilai_total');
        $pendaftaran->update(['nilai_akhir' => round($rataRata, 2)]);

        return redirect()->route('panitia.seleksi.index', [
            'rekrutmen_id' => $pendaftaran->rekrutmen_id,
            'tahap_id'     => $tahap->id,
        ])->with('success', "Nilai {$pendaftaran->nama_lengkap} berhasil disimpan.");
    }
}