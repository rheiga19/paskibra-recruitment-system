<?php

namespace Database\Seeders;

use App\Models\Galeri;
use Illuminate\Database\Seeder;

class GaleriSeeder extends Seeder
{
    public function run(): void
    {
        $galeri = [
            ['judul' => 'Upacara HUT RI ke-80 Kecamatan Compreng',       'keterangan' => 'Dokumentasi upacara HUT RI ke-80 di Lapangan Kecamatan Compreng tahun 2025.'],
            ['judul' => 'Latihan Baris-Berbaris Paskibra 2025',           'keterangan' => 'Sesi latihan baris-berbaris intensif Paskibra Compreng 2025.'],
            ['judul' => 'Pelantikan Anggota Paskibra 2025',               'keterangan' => 'Prosesi pelantikan resmi anggota Paskibra terpilih tahun 2025.'],
            ['judul' => 'Seleksi Fisik Paskibra 2025',                   'keterangan' => 'Dokumentasi seleksi kemampuan fisik calon anggota Paskibra 2025.'],
            ['judul' => 'Gladi Bersih Upacara HUT RI ke-80',              'keterangan' => 'Gladi bersih upacara HUT RI ke-80 di Lapangan Kecamatan Compreng.'],
            ['judul' => 'Foto Bersama Anggota Paskibra 2025',             'keterangan' => 'Foto bersama seluruh anggota Paskibra Kecamatan Compreng tahun 2025.'],
        ];

        foreach ($galeri as $g) {
            Galeri::create([
                'judul'      => $g['judul'],
                'path'       => 'galeri/dummy/foto-' . rand(1, 6) . '.jpg',
                'keterangan' => $g['keterangan'],
            ]);
        }

        $this->command->info('✅ GaleriSeeder selesai — 6 foto galeri');
    }
}