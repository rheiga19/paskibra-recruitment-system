<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Galeri;
use App\Models\Pengaturan;
use App\Models\Pendaftaran;
use App\Models\Rekrutmen;

class HomeController extends Controller
{
    public function index()
    {
        $pengaturan     = Pengaturan::ambil();
        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->first();
        $berita         = Berita::published()->latest()->take(3)->get();
        $galeri         = Galeri::latest()->take(6)->get();

        return view('home.index', compact('pengaturan', 'rekrutmenAktif', 'berita', 'galeri'));
    }

    public function pengumuman()
    {
        $pengaturan     = Pengaturan::ambil();
        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->first();

        // Data lulus hanya dimuat kalau admin sudah aktifkan pengumuman
        $lulusList = collect();

        if ($pengaturan->pengumuman_aktif) {
            $lulusList = Pendaftaran::with('rekrutmen')
                ->where('is_lulus_final', true)
                ->orderBy('jenis_kelamin')   // L (Putra) dulu, lalu P (Putri)
                ->orderBy('nama_lengkap')
                ->get(['no_pendaftaran', 'nama_lengkap', 'jenis_kelamin', 'nama_sekolah', 'rekrutmen_id']);
        }

        return view('home.pengumuman.pengumuman', compact('pengaturan', 'rekrutmenAktif', 'lulusList'));
    }
}