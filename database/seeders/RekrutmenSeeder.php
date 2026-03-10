<?php

namespace Database\Seeders;

use App\Models\DokumenPendaftaran;
use App\Models\DokumenPeserta;
use App\Models\Pendaftaran;
use App\Models\Rekrutmen;
use App\Models\SeleksiHasil;
use App\Models\SeleksiTahap;
use App\Models\User;
use Illuminate\Database\Seeder;

class RekrutmenSeeder extends Seeder
{
    public function run(): void
    {
        // ── Rekrutmen aktif tahun ini ──────────────────────────────
        $rekrutmen = Rekrutmen::create([
            'tahun'         => 2026,
            'nama'          => 'Seleksi Paskibra Kecamatan Compreng 2026',
            'deskripsi'     => 'Rekrutmen anggota Paskibra Kecamatan Compreng untuk upacara HUT RI ke-81 tahun 2026.',
            'tanggal_buka'  => '2026-03-01',
            'tanggal_tutup' => '2026-04-30',
            'is_aktif'      => true,
            'kuota_putra'   => 8,
            'kuota_putri'   => 8,
            'syarat'        => "1. WNI berdomisili di Kecamatan Compreng\n2. Siswa SMP/MTs/SMA/MA/SMK aktif\n3. Tinggi badan minimal putra 163 cm, putri 155 cm\n4. Sehat jasmani dan rohani\n5. Belum pernah menjadi anggota Paskibra",
            'catatan'       => 'Seleksi dilaksanakan di Lapangan Kecamatan Compreng.',
        ]);

        // ── Rekrutmen tahun lalu (arsip) ───────────────────────────
        Rekrutmen::create([
            'tahun'         => 2025,
            'nama'          => 'Seleksi Paskibra Kecamatan Compreng 2025',
            'deskripsi'     => 'Rekrutmen anggota Paskibra Kecamatan Compreng tahun 2025.',
            'tanggal_buka'  => '2025-03-01',
            'tanggal_tutup' => '2025-04-30',
            'is_aktif'      => false,
            'kuota_putra'   => 8,
            'kuota_putri'   => 8,
            'syarat'        => "1. WNI berdomisili di Kecamatan Compreng\n2. Siswa SMP/MTs/SMA/MA/SMK aktif",
        ]);

        // ── Tahap seleksi untuk rekrutmen 2026 ────────────────────
        $tahap1 = SeleksiTahap::create([
            'rekrutmen_id'   => $rekrutmen->id,
            'nama'           => 'Seleksi Administrasi',
            'deskripsi'      => 'Verifikasi berkas dan kelengkapan dokumen peserta.',
            'urutan'         => 1,
            'passing_grade'  => 70,
            'is_aktif'       => false,
            'is_diumumkan'   => true,
            'is_tahap_final' => false,
            'komponen_nilai' => null,
            'tanggal_pengumuman' => '2026-05-05 08:00:00',
        ]);

        $tahap2 = SeleksiTahap::create([
            'rekrutmen_id'   => $rekrutmen->id,
            'nama'           => 'Seleksi Kesehatan & Fisik',
            'deskripsi'      => 'Pemeriksaan kesehatan dan tes kemampuan fisik dasar.',
            'urutan'         => 2,
            'passing_grade'  => 70,
            'is_aktif'       => true,
            'is_diumumkan'   => false,
            'is_tahap_final' => false,
            'komponen_nilai' => ['nilai_fisik', 'nilai_pbb'],
        ]);

        $tahap3 = SeleksiTahap::create([
            'rekrutmen_id'   => $rekrutmen->id,
            'nama'           => 'Seleksi Wawancara & Pengetahuan',
            'deskripsi'      => 'Tes wawancara, pengetahuan Pancasila, dan TIU.',
            'urutan'         => 3,
            'passing_grade'  => 75,
            'is_aktif'       => false,
            'is_diumumkan'   => false,
            'is_tahap_final' => true,
            'komponen_nilai' => ['nilai_pancasila', 'nilai_tiu', 'nilai_wawancara'],
        ]);

        // ── Daftarkan semua peserta ke rekrutmen 2026 ─────────────
        $peserta = User::where('role', 'peserta')->get();
        $admin   = User::where('role', 'admin')->first();

        foreach ($peserta as $user) {
            $profil = $user->profil;

            $pendaftaran = Pendaftaran::create([
                'user_id'        => $user->id,
                'rekrutmen_id'   => $rekrutmen->id,
                'no_pendaftaran' => 'TEMP',
                'nama_lengkap'   => $user->name,
                'nik'            => $profil->nik,
                'tempat_lahir'   => $profil->tempat_lahir,
                'tanggal_lahir'  => $profil->tanggal_lahir,
                'jenis_kelamin'  => $profil->jenis_kelamin,
                'no_hp'          => $profil->no_hp,
                'alamat_lengkap' => $profil->alamat_lengkap,
                'provinsi_nama'  => $profil->provinsi_nama,
                'kabupaten_nama' => $profil->kabupaten_nama,
                'kecamatan_nama' => $profil->kecamatan_nama,
                'desa_nama'      => $profil->desa_nama,
                'tinggi_badan'   => $profil->tinggi_badan,
                'berat_badan'    => $profil->berat_badan,
                'nama_sekolah'   => $profil->nama_sekolah,
                'jenjang'        => $profil->jenjang,
                'kelas'          => $profil->kelas,
                'nilai_rata'     => $profil->nilai_rata,
                'nama_ortu'      => $profil->nama_ortu,
                'hp_ortu'        => $profil->hp_ortu,
                'hubungan_ortu'  => $profil->hubungan_ortu,
                'prestasi'       => $profil->prestasi,
                'status'         => 'diverifikasi',
            ]);

            // Update no_pendaftaran
            $pendaftaran->update([
                'no_pendaftaran' => Pendaftaran::generateNoPendaftaran($pendaftaran->id, 2026),
            ]);

            // Snapshot dokumen
            foreach ($user->dokumen as $dok) {
                DokumenPendaftaran::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'jenis'          => $dok->jenis,
                    'path'           => $dok->path,
                    'nama_file'      => $dok->nama_file,
                ]);
            }

            // Input nilai tahap 1 (sudah diumumkan)
            SeleksiHasil::create([
                'seleksi_tahap_id' => $tahap1->id,
                'pendaftaran_id'   => $pendaftaran->id,
                'status'           => 'lolos',
                'nilai_total'      => rand(75, 95),
                'catatan'          => 'Dokumen lengkap dan valid.',
                'dinilai_oleh'     => $admin->id,
            ]);

            // Input nilai tahap 2 (sedang berjalan)
            $nilaiF = rand(65, 95);
            $nilaiP = rand(65, 95);
            $total  = round(($nilaiF + $nilaiP) / 2, 2);
            SeleksiHasil::create([
                'seleksi_tahap_id' => $tahap2->id,
                'pendaftaran_id'   => $pendaftaran->id,
                'status'           => $total >= 70 ? 'lolos' : 'tidak_lolos',
                'nilai_fisik'      => $nilaiF,
                'nilai_pbb'        => $nilaiP,
                'nilai_total'      => $total,
                'catatan'          => null,
                'dinilai_oleh'     => $admin->id,
            ]);
        }

        $this->command->info('✅ RekrutmenSeeder selesai — 2 rekrutmen, 3 tahap, 10 pendaftaran');
    }
}