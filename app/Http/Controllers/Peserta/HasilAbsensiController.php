<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Rekrutmen;
use App\Models\Pendaftaran;

class HasilAbsensiController extends Controller
{
    public function hasilSeleksi()
    {
        $user        = auth()->user();
        $pendaftaran = Pendaftaran::where('user_id', $user->id)->latest()->first();

        $hasilList = [];
        if ($pendaftaran) {
            $hasilList = $pendaftaran->hasilSeleksi()
                ->with('tahap')
                ->whereHas('tahap', fn($q) => $q->where('is_diumumkan', true))
                ->orderBy('created_at')
                ->get();
        }

        return view('peserta.hasil.hasil-seleksi', compact('pendaftaran', 'hasilList'));
    }

    public function kartuAnggota()
    {
        $user      = auth()->user();
        $rekrutmen = Rekrutmen::where('is_aktif', true)->first();

        $pendaftaran = Pendaftaran::with(['dokumen'])
            ->where('user_id', $user->id)
            ->where('is_lulus_final', true)
            ->when($rekrutmen, fn($q) => $q->where('rekrutmen_id', $rekrutmen->id))
            ->first();

        if (!$pendaftaran) {
            return back()->with('error', 'Kartu hanya tersedia untuk peserta yang lulus seleksi.');
        }

        return view('peserta.kartu-anggota', compact('pendaftaran', 'rekrutmen'));
    }

    public function absensiIndex()
    {
        $user         = auth()->user();
        $rekrutmen    = Rekrutmen::where('is_aktif', true)->first();
        $pendaftaran  = null;
        $rekapAbsensi = collect();

        if ($rekrutmen) {
            $pendaftaran = Pendaftaran::where('user_id', $user->id)
                ->where('rekrutmen_id', $rekrutmen->id)
                ->where('is_lulus_final', true)
                ->first();

            if ($pendaftaran) {
                $rekapAbsensi = Absensi::with('jadwal')
                    ->where('pendaftaran_id', $pendaftaran->id)
                    ->orderByDesc('created_at')
                    ->get();
            }
        }

        $totalHadir = $rekapAbsensi->where('status', 'hadir')->count();
        $totalAll   = $rekapAbsensi->count();
        $persen     = $totalAll > 0 ? round($totalHadir / $totalAll * 100) : 0;

        return view('peserta.absensi', compact(
            'pendaftaran', 'rekapAbsensi', 'totalHadir', 'totalAll', 'persen'
        ));
    }
}