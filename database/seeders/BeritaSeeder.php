<?php

namespace Database\Seeders;

use App\Models\Berita;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BeritaSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $beritaList = [
            [
                'judul'    => 'Pendaftaran Paskibra Kecamatan Compreng 2026 Resmi Dibuka',
                'konten'   => '<p>Panitia Paskibra Kecamatan Compreng dengan bangga mengumumkan pembukaan pendaftaran anggota Paskibra tahun 2026. Pendaftaran dibuka mulai tanggal 1 Maret hingga 30 April 2026.</p><p>Pendaftaran dilakukan secara online melalui website resmi ini. Peserta diwajibkan mengisi data diri secara lengkap dan mengunggah dokumen yang dipersyaratkan.</p><p>Informasi lebih lanjut dapat menghubungi panitia melalui nomor yang tertera di halaman kontak.</p>',
                'published' => true,
            ],
            [
                'judul'    => 'Syarat dan Ketentuan Seleksi Paskibra 2026',
                'konten'   => '<p>Berikut adalah syarat dan ketentuan yang harus dipenuhi oleh calon peserta seleksi Paskibra Kecamatan Compreng tahun 2026:</p><ul><li>WNI yang berdomisili di Kecamatan Compreng</li><li>Siswa aktif SMP/MTs/SMA/MA/SMK</li><li>Tinggi badan minimal putra 163 cm, putri 155 cm</li><li>Sehat jasmani dan rohani</li><li>Belum pernah menjadi anggota Paskibra</li></ul>',
                'published' => true,
            ],
            [
                'judul'    => 'Jadwal Seleksi Paskibra Kecamatan Compreng 2026',
                'konten'   => '<p>Berikut jadwal lengkap pelaksanaan seleksi Paskibra 2026:</p><ul><li>Pendaftaran Online: 1 Maret – 30 April 2026</li><li>Seleksi Administrasi: 5 Mei 2026</li><li>Seleksi Kesehatan & Fisik: 10 Mei 2026</li><li>Seleksi Wawancara: 15 Mei 2026</li><li>Pengumuman Hasil: 20 Mei 2026</li></ul>',
                'published' => true,
            ],
            [
                'judul'    => 'Tips Persiapan Mengikuti Seleksi Paskibra',
                'konten'   => '<p>Bagi kamu yang ingin mengikuti seleksi Paskibra, berikut beberapa tips persiapan yang bisa dilakukan:</p><p>Pertama, jaga kondisi fisik dengan rutin berolahraga terutama lari dan push-up. Kedua, pelajari materi Pancasila dan wawasan kebangsaan. Ketiga, latih sikap tegap dan disiplin dalam keseharian.</p>',
                'published' => false,
            ],
        ];

        foreach ($beritaList as $b) {
            $slug = Str::slug($b['judul']);
            Berita::create([
                'judul'        => $b['judul'],
                'slug'         => $slug,
                'konten'       => $b['konten'],
                'is_published' => $b['published'],
                'admin_id'     => $admin->id,
            ]);
        }

        $this->command->info('✅ BeritaSeeder selesai — 4 berita (3 published, 1 draft)');
    }
}