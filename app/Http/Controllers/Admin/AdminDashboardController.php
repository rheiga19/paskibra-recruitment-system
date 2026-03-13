<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Rekrutmen;
use App\Models\SeleksiHasil;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->first();

        // ── Statistik Utama ──────────────────────────────────────
        $totalPendaftar  = Pendaftaran::count();
        $totalMenunggu   = Pendaftaran::where('status', 'menunggu')->count();
        $totalLulus      = Pendaftaran::where('is_lulus_final', true)->count();
        $totalPeserta    = User::where('role', 'peserta')->count();

        // ── Pendaftar per status ──────────────────────────────────
        $perStatus = Pendaftaran::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // ── Pendaftar per jenis kelamin ───────────────────────────
        $putra = Pendaftaran::where('jenis_kelamin', 'L')->count();
        $putri = Pendaftaran::where('jenis_kelamin', 'P')->count();

        // ── Pendaftar per jenjang ─────────────────────────────────
        $perJenjang = Pendaftaran::select('jenjang', DB::raw('count(*) as total'))
            ->groupBy('jenjang')
            ->orderByDesc('total')
            ->pluck('total', 'jenjang')
            ->toArray();

        // ── Grafik pendaftaran per hari (30 hari terakhir) ────────
        $grafikHarian = Pendaftaran::select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(29))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $labels = [];
        $dataGrafik = [];
        for ($i = 29; $i >= 0; $i--) {
            $tgl = now()->subDays($i)->format('Y-m-d');
            $labels[]    = now()->subDays($i)->format('d/m');
            $dataGrafik[] = $grafikHarian[$tgl]->total ?? 0;
        }

        // ── Pendaftar terbaru ─────────────────────────────────────
        $pendaftaranTerbaru = Pendaftaran::with('user')
            ->latest()
            ->take(8)
            ->get();

        // ── Rekrutmen list ────────────────────────────────────────
        $rekrutmenList = Rekrutmen::withCount('pendaftaran')
            ->orderByDesc('tahun')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'rekrutmenAktif',
            'totalPendaftar',
            'totalMenunggu',
            'totalLulus',
            'totalPeserta',
            'perStatus',
            'putra', 'putri',
            'perJenjang',
            'labels', 'dataGrafik',
            'pendaftaranTerbaru',
            'rekrutmenList',
        ));
    }
}