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
        $rekrutmenAktif = Rekrutmen::with('seleksiTahap')->where('is_aktif', true)->first();
        $berita         = Berita::published()->latest()->take(3)->get();
        $galeri         = Galeri::latest()->take(6)->get();

        // Tahap seleksi untuk timeline — hanya dari rekrutmen aktif
        $tahapSeleksi = $rekrutmenAktif
            ? $rekrutmenAktif->seleksiTahap   // sudah orderBy('urutan')
            : collect();

        return view('home.index', compact(
            'pengaturan', 'rekrutmenAktif', 'berita', 'galeri', 'tahapSeleksi'
        ));
    }

    public function pengumuman()
    {
        $pengaturan     = Pengaturan::ambil();
        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->first();

        $lulusList = collect();
        if ($pengaturan->pengumuman_aktif) {
            $lulusList = Pendaftaran::with('rekrutmen')
                ->where('is_lulus_final', true)
                ->orderBy('jenis_kelamin')
                ->orderBy('nama_lengkap')
                ->get(['no_pendaftaran', 'nama_lengkap', 'jenis_kelamin', 'nama_sekolah', 'rekrutmen_id']);
        }

        return view('home.pengumuman.index', compact('pengaturan', 'rekrutmenAktif', 'lulusList'));
    }

    public function beritaIndex()
    {
        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->first();
        $berita         = Berita::published()->latest()->paginate(9);

        return view('home.berita.index', compact('rekrutmenAktif', 'berita'));
    }

    public function beritaShow(Berita $beritum)
    {
        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->first();
        $beritaLain     = Berita::published()->where('id', '!=', $beritum->id)->latest()->take(3)->get();

        return view('home.berita.show', compact('rekrutmenAktif', 'beritum', 'beritaLain'));
    }

    public function galeriIndex()
    {
        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->first();
        $galeri         = Galeri::latest()->paginate(12);

        return view('home.galeri.index', compact('rekrutmenAktif', 'galeri'));
    }

    public function galeriShow(Galeri $galeri)
    {
        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->first();
        $galeriLain     = Galeri::where('id', '!=', $galeri->id)->latest()->take(6)->get();

        return view('home.galeri.show', compact('rekrutmenAktif', 'galeri', 'galeriLain'));
    }
}