<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Galeri;
use App\Models\Pengaturan;
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
}