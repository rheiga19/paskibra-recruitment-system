<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\Rekrutmen;
use App\Models\SeleksiHasil;
use App\Models\SeleksiTahap;
use Illuminate\Http\Request;

class HasilController extends Controller
{
    public function index(Request $request)
    {
        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->first();
        $rekrutmenId    = $request->get('rekrutmen_id', $rekrutmenAktif?->id);
        $rekrutmenList  = Rekrutmen::orderByDesc('tahun')->get();
        $rekrutmen      = Rekrutmen::find($rekrutmenId);

        $tahapList  = SeleksiTahap::where('rekrutmen_id', $rekrutmenId)->orderBy('urutan')->get();
        $tahapAktif = $request->filled('tahap_id')
            ? SeleksiTahap::find($request->tahap_id)
            : null;

        $query = SeleksiHasil::with('pendaftaran')
            ->whereHas('pendaftaran', fn($q) => $q->where('rekrutmen_id', $rekrutmenId))
            ->whereNotNull('nilai_total')
            ->orderByDesc('nilai_total');

        if ($tahapAktif) {
            $query->where('seleksi_tahap_id', $tahapAktif->id);
        }

        $hasil = $query->get();

        return view('panitia.hasil.index', [
            'hasil'           => $hasil,
            'rekrutmen'       => $rekrutmen,
            'rekrutmenList'   => $rekrutmenList,
            'rekrutmenId'     => $rekrutmenId,
            'tahapList'       => $tahapList,
            'tahapAktif'      => $tahapAktif,
            'totalDinilai'    => $hasil->count(),
            'totalLolos'      => $hasil->where('status', 'lolos')->count(),
            'totalTidakLolos' => $hasil->where('status', 'tidak_lolos')->count(),
            'nilaiTertinggi'  => $hasil->max('nilai_total'),
            'nilaiRataRata'   => $hasil->avg('nilai_total'),
        ]);
    }
}