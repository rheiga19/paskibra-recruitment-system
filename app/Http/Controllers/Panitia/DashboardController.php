<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Rekrutmen;
use App\Models\SeleksiHasil;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->first();

        $totalPendaftar     = Pendaftaran::count();
        $menungguVerifikasi = Pendaftaran::where('status', 'menunggu')->count();
        $sudahDinilai       = SeleksiHasil::whereNotNull('nilai_total')->distinct('pendaftaran_id')->count();
        $lulusFinal         = Pendaftaran::where('is_lulus_final', true)->count();

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
}