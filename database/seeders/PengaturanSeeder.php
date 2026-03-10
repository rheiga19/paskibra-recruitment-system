<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        Pengaturan::create([
            'pendaftaran_aktif'  => true,
            'pengumuman_aktif'   => false,
            'pesan_pengumuman'   => 'Selamat kepada peserta yang dinyatakan lulus seleksi Paskibra Kecamatan Compreng tahun ini.',
            'nama_kecamatan'     => 'Kecamatan Compreng',
            'alamat_sekretariat' => 'Jl. Raya Compreng No. 1, Compreng, Subang, Jawa Barat',
            'no_hp_panitia'      => '081234567890',
        ]);

        $this->command->info('✅ PengaturanSeeder selesai');
    }
}