<?php

namespace Database\Seeders;

use App\Models\DokumenPeserta;
use App\Models\ProfilPeserta;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────────────────
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@paskibra.id',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // ── Panitia ───────────────────────────────────────────────
        User::create([
            'name'     => 'Panitia Seleksi',
            'email'    => 'panitia@paskibra.id',
            'password' => Hash::make('panitia123'),
            'role'     => 'panitia',
        ]);

        // ── Peserta dummy (10 orang) ───────────────────────────────
        $peserta = [
            ['name' => 'Ahmad Fauzi',        'email' => 'ahmad@gmail.com',   'jk' => 'L', 'sekolah' => 'SMAN 1 Compreng',  'jenjang' => 'SMA', 'kelas' => 'XI IPA 1', 'tinggi' => 172, 'berat' => 62],
            ['name' => 'Budi Santoso',        'email' => 'budi@gmail.com',    'jk' => 'L', 'sekolah' => 'SMKN 1 Subang',    'jenjang' => 'SMK', 'kelas' => 'XI TKJ',   'tinggi' => 168, 'berat' => 58],
            ['name' => 'Candra Wijaya',       'email' => 'candra@gmail.com',  'jk' => 'L', 'sekolah' => 'MAN 1 Subang',     'jenjang' => 'MA',  'kelas' => 'XI IPS',   'tinggi' => 175, 'berat' => 65],
            ['name' => 'Deni Kurniawan',      'email' => 'deni@gmail.com',    'jk' => 'L', 'sekolah' => 'SMAN 2 Subang',    'jenjang' => 'SMA', 'kelas' => 'X IPA 2',  'tinggi' => 170, 'berat' => 60],
            ['name' => 'Eko Prasetyo',        'email' => 'eko@gmail.com',     'jk' => 'L', 'sekolah' => 'SMPN 1 Compreng',  'jenjang' => 'SMP', 'kelas' => 'IX A',     'tinggi' => 163, 'berat' => 52],
            ['name' => 'Fitri Rahayu',        'email' => 'fitri@gmail.com',   'jk' => 'P', 'sekolah' => 'SMAN 1 Compreng',  'jenjang' => 'SMA', 'kelas' => 'XI IPA 2', 'tinggi' => 158, 'berat' => 48],
            ['name' => 'Gita Permatasari',    'email' => 'gita@gmail.com',    'jk' => 'P', 'sekolah' => 'MAN 1 Subang',     'jenjang' => 'MA',  'kelas' => 'X IPA',    'tinggi' => 160, 'berat' => 50],
            ['name' => 'Hani Fitriani',       'email' => 'hani@gmail.com',    'jk' => 'P', 'sekolah' => 'SMKN 2 Subang',   'jenjang' => 'SMK', 'kelas' => 'XI AK',    'tinggi' => 155, 'berat' => 46],
            ['name' => 'Indah Lestari',       'email' => 'indah@gmail.com',   'jk' => 'P', 'sekolah' => 'SMPN 2 Compreng', 'jenjang' => 'SMP', 'kelas' => 'IX B',     'tinggi' => 157, 'berat' => 47],
            ['name' => 'Jihan Nur Azizah',    'email' => 'jihan@gmail.com',   'jk' => 'P', 'sekolah' => 'SMAN 2 Subang',   'jenjang' => 'SMA', 'kelas' => 'X IPS 1',  'tinggi' => 162, 'berat' => 51],
        ];

        foreach ($peserta as $p) {
            $user = User::create([
                'name'     => $p['name'],
                'email'    => $p['email'],
                'password' => Hash::make('peserta123'),
                'role'     => 'peserta',
            ]);

            // Buat profil lengkap
            ProfilPeserta::create([
                'user_id'          => $user->id,
                'nik'              => '3214' . rand(100000000000, 999999999999),
                'tempat_lahir'     => 'Subang',
                'tanggal_lahir'    => now()->subYears(rand(14, 17))->subDays(rand(0, 365))->toDateString(),
                'jenis_kelamin'    => $p['jk'],
                'no_hp'            => '0812' . rand(10000000, 99999999),
                'alamat_lengkap'   => 'Jl. Desa Compreng No. ' . rand(1, 100) . ', Compreng, Subang',
                'provinsi_kode'    => '32',
                'provinsi_nama'    => 'Jawa Barat',
                'kabupaten_kode'   => '3213',
                'kabupaten_nama'   => 'Kabupaten Subang',
                'kecamatan_kode'   => '3213110',
                'kecamatan_nama'   => 'Compreng',
                'desa_kode'        => '3213110001',
                'desa_nama'        => 'Compreng',
                'tinggi_badan'     => $p['tinggi'],
                'berat_badan'      => $p['berat'],
                'nama_sekolah'     => $p['sekolah'],
                'jenjang'          => $p['jenjang'],
                'kelas'            => $p['kelas'],
                'nilai_rata'       => rand(750, 950) / 10,
                'nama_ortu'        => 'Orang Tua ' . $p['name'],
                'hp_ortu'          => '0813' . rand(10000000, 99999999),
                'hubungan_ortu'    => 'Ayah',
                'prestasi'         => 'Juara ' . rand(1, 3) . ' Lomba ' . collect(['Baris-Berbaris', 'Pramuka', 'Olahraga', 'Akademik'])->random(),
                'is_profil_lengkap' => true,
            ]);

            // Buat dokumen dummy (path placeholder)
            $jenisDokumen = ['foto_4x6', 'ktp_pelajar', 'akta_kelahiran', 'rapor', 'surat_sehat', 'surat_izin_ortu'];
            foreach ($jenisDokumen as $jenis) {
                DokumenPeserta::create([
                    'user_id'   => $user->id,
                    'jenis'     => $jenis,
                    'path'      => 'dokumen/dummy/' . $jenis . '.pdf',
                    'nama_file' => $jenis . '_' . strtolower(str_replace(' ', '_', $p['name'])) . '.pdf',
                ]);
            }
        }

        $this->command->info('✅ UserSeeder selesai — 1 admin, 1 panitia, 10 peserta');
    }
}