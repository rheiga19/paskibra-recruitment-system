<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<h1 align="center">Sistem Rekrutmen & Absensi Paskibra</h1>
<h3 align="center">Kecamatan Compreng — Kabupaten Subang</h3>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Bootstrap-4.x-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
  <img src="https://img.shields.io/badge/Status-Development-F59E0B?style=for-the-badge" alt="Status">
</p>

---

## 📋 Tentang Proyek

Sistem Rekrutmen & Absensi Paskibra Kecamatan Compreng adalah aplikasi web berbasis Laravel yang mengelola seluruh proses rekrutmen anggota Pasukan Pengibar Bendera (Paskibra) secara digital — mulai dari pendaftaran peserta, upload dokumen persyaratan, verifikasi administrasi, penilaian seleksi bertahap, pengumuman hasil, hingga absensi latihan berbasis scan QR Code untuk peserta yang telah lulus seleksi final.

---

## ✨ Fitur Lengkap per Role

### 👤 Peserta
- Registrasi & login akun
- Lengkapi biodata diri, data sekolah, dan data orang tua / wali
- Upload 6 dokumen persyaratan:
  - Foto 4×6
  - KTP / Kartu Pelajar
  - Akta Kelahiran
  - Rapor
  - Surat Keterangan Sehat
  - Surat Izin Orang Tua
- Kirim pendaftaran ke rekrutmen yang sedang aktif
- Lihat status verifikasi administrasi secara realtime
- Download **Kartu Peserta (PDF)** setelah lolos administrasi — dilengkapi QR Code untuk absensi
- Lihat hasil seleksi per tahap yang sudah diumumkan panitia
- Lihat rekap kehadiran / absensi latihan milik sendiri

### 🗂️ Panitia
- Dashboard ringkasan statistik rekrutmen aktif
- **Verifikasi administrasi** — periksa dokumen, terima atau tolak + catatan
- **Input nilai seleksi** per tahap per peserta
- **Scan QR Code** kartu peserta untuk absensi latihan (via kamera browser, tanpa app tambahan)
- Absensi otomatis mencatat: tanggal, jam hadir, sesi kegiatan, lokasi, dan status (hadir/izin/alpa)
- Lihat & cetak rekap absensi per sesi kegiatan

### ⚙️ Admin
- Kelola data rekrutmen (buat, edit, buka/tutup periode pendaftaran)
- Kelola tahap-tahap seleksi per rekrutmen
- Toggle pengumuman hasil per tahap seleksi
- Proses kelulusan final peserta
- Kelola sesi kegiatan / latihan (nama sesi, tanggal, lokasi)
- Lihat rekap absensi seluruh peserta lulus
- Manajemen **berita** kegiatan (CRUD + publish/draft + thumbnail)
- Manajemen **galeri** foto kegiatan (upload multiple, edit, hapus)
- Manajemen akun pengguna (admin, panitia, peserta) + reset password
- Pengaturan sistem (nama kecamatan, dll)

---

## 🏗️ Tech Stack

| Komponen | Teknologi |
|---|---|
| Backend Framework | Laravel 12 |
| Authentication | Laravel Breeze (Blade + Alpine.js) |
| Frontend Template | Stisla Admin Template |
| CSS Framework | Bootstrap 4 |
| Database | MySQL 8 |
| PDF Generator | barryvdh/laravel-dompdf |
| QR Scanner | html5-qrcode (kamera browser) |
| QR Generator | api.qrserver.com (embed di kartu PDF) |
| Local Server | Laragon |
| PHP Version | 8.2 |

---

## 👥 Role & Hak Akses

| Role | Akses Halaman |
|---|---|
| `admin` | Dashboard, Rekrutmen, Pendaftaran, Seleksi, Sesi & Absensi, Berita, Galeri, Users, Pengaturan |
| `panitia` | Dashboard, Verifikasi, Input Nilai, Scan Absensi, Rekap Absensi |
| `peserta` | Dashboard, Profil, Dokumen, Pendaftaran, Hasil Seleksi, Absensi Saya |

---

## 🗄️ Struktur Database

```
users                — akun pengguna (admin / panitia / peserta)
profil_peserta       — biodata lengkap peserta
dokumen_peserta      — dokumen milik user (sebelum mendaftar)
rekrutmen            — data periode rekrutmen
pendaftaran          — snapshot data peserta saat apply
dokumen_pendaftaran  — snapshot dokumen saat apply
seleksi_tahap        — tahap-tahap seleksi dalam rekrutmen
seleksi_hasil        — nilai peserta per tahap seleksi
sesi_kegiatan        — sesi latihan yang bisa diabsen
absensi              — rekam hadir peserta per sesi (hasil scan QR)
berita               — artikel berita & pengumuman
galeri               — foto dokumentasi kegiatan
pengaturan           — konfigurasi sistem
```

---

## 🔄 Alur Sistem

```
[1] Peserta daftar akun & login
        ↓
[2] Lengkapi biodata + upload 6 dokumen
        ↓
[3] Kirim pendaftaran → data & dokumen di-snapshot
        ↓
[4] Panitia verifikasi administrasi → Terima / Tolak
        ↓
[5] Admin buat tahap seleksi (Fisik, Wawancara, PBB, dll)
        ↓
[6] Panitia input nilai per peserta per tahap
        ↓
[7] Admin umumkan hasil per tahap → peserta bisa lihat
        ↓
[8] Admin proses kelulusan final → is_lulus_final = true
        ↓
[9] Peserta download Kartu Peserta PDF (ada QR Code)
        ↓
[10] Admin buat sesi latihan
        ↓
[11] Panitia scan QR kartu peserta → absensi otomatis tercatat
        ↓
[12] Peserta & admin/panitia lihat rekap kehadiran
```

---

## 🚀 Instalasi

### Prasyarat
- PHP 8.2+
- Composer
- MySQL 8.0+
- Laragon / XAMPP / WAMP

### Langkah Instalasi

**1. Clone repositori**
```bash
git clone https://github.com/username/paskibra-compreng.git
cd paskibra-compreng
```

**2. Install dependensi PHP**
```bash
composer install
```

**3. Konfigurasi environment**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Atur koneksi database di `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paskibraka
DB_USERNAME=root
DB_PASSWORD=
```

**5. Atur nama kecamatan di `.env`**
```env
NAMA_KECAMATAN=Compreng
```

**6. Jalankan migrasi & seeder**
```bash
php artisan migrate:fresh --seed
```

**7. Buat symbolic link storage**
```bash
php artisan storage:link
```

**8. Jalankan server**
```bash
php artisan serve
```

Buka di browser: `http://localhost:8000`


---

## 📦 Package Tambahan

```bash
# PDF Generator untuk kartu peserta
composer require barryvdh/laravel-dompdf

# Publish config DomPDF (opsional)
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

QR Scanner di halaman absensi panitia menggunakan library **html5-qrcode** via CDN — tidak perlu instalasi tambahan.

---

## 📁 Struktur Folder Penting

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── AdminDashboardController.php
│   │   │   ├── RekrutmenController.php
│   │   │   ├── PendaftaranAdminController.php
│   │   │   ├── SeleksiController.php
│   │   │   ├── SesiController.php        ← Kelola sesi latihan
│   │   │   ├── AbsensiController.php     ← Rekap absensi
│   │   │   ├── BeritaController.php
│   │   │   ├── GaleriController.php
│   │   │   ├── UserController.php
│   │   │   └── PengaturanController.php
│   │   ├── Panitia/
│   │   │   └── PanitiaController.php     ← Dashboard, verifikasi, seleksi, scan absensi
│   │   └── Peserta/
│   │       └── PesertaController.php     ← Dashboard, profil, dokumen, pendaftaran, absensi
│   └── Middleware/
│       └── CheckRole.php
├── Models/
│   ├── User.php
│   ├── ProfilPeserta.php
│   ├── DokumenPeserta.php
│   ├── DokumenPendaftaran.php
│   ├── Rekrutmen.php
│   ├── Pendaftaran.php
│   ├── SeleksiTahap.php
│   ├── SeleksiHasil.php
│   ├── SesiKegiatan.php              ← Model sesi latihan
│   ├── Absensi.php                   ← Model absensi
│   ├── Berita.php
│   ├── Galeri.php
│   └── Pengaturan.php
resources/
└── views/
    ├── admin/
    │   ├── dashboard.blade.php
    │   ├── rekrutmen/
    │   ├── pendaftaran/
    │   ├── seleksi/
    │   ├── sesi/                     ← Kelola sesi latihan
    │   ├── absensi/                  ← Rekap absensi
    │   ├── berita/
    │   ├── galeri/
    │   └── users/
    ├── panitia/
    │   ├── dashboard.blade.php
    │   ├── verifikasi/
    │   ├── seleksi/
    │   └── absensi/                  ← Scanner QR + rekap
    ├── peserta/
    │   ├── dashboard.blade.php
    │   ├── profil/
    │   ├── dokumen/
    │   ├── pendaftaran/
    │   │   ├── show.blade.php
    │   │   └── kartu_pdf.blade.php   ← Template kartu PDF + QR
    │   ├── hasil-seleksi.blade.php
    │   └── absensi.blade.php         ← Rekap kehadiran peserta
    └── layouts/
        ├── app.blade.php
        ├── guest.blade.php
        └── partials/
            └── sidebar.blade.php
storage/
└── app/public/
    ├── dokumen/{id}_{nama-slug}/     ← Dokumen peserta terorganisir per user
    │   ├── foto_4x6.jpg
    │   ├── ktp_pelajar.pdf
    │   ├── akta_kelahiran.pdf
    │   ├── rapor.jpg
    │   ├── surat_sehat.pdf
    │   └── surat_izin_ortu.pdf
    ├── berita/                       ← Thumbnail berita
    └── galeri/                       ← Foto galeri kegiatan
```

---

## 📱 Fitur Absensi QR Code

### Cara Kerja
1. Peserta yang **lulus seleksi final** mendapatkan Kartu Peserta PDF yang berisi QR Code unik
2. QR Code berisi data: `{no_pendaftaran}|{nama_lengkap}`
3. Panitia membuka halaman **Scan Absensi** di browser HP
4. Panitia pilih **sesi kegiatan** terlebih dahulu
5. Kamera browser aktif — panitia arahkan ke QR Code kartu peserta
6. Sistem otomatis mencatat:
   - ✅ Nama peserta
   - ✅ Tanggal & jam hadir
   - ✅ Sesi kegiatan
   - ✅ Lokasi kegiatan
   - ✅ Status kehadiran (Hadir / Izin / Alpa)
7. Notifikasi langsung muncul di layar (berhasil / sudah absen / tidak ditemukan)

### Kondisi Absensi
| Kondisi | Hasil |
|---|---|
| QR valid + peserta lulus final | ✅ Absensi berhasil dicatat |
| QR valid + peserta belum lulus | ❌ Ditolak — bukan peserta aktif |
| QR sudah pernah scan di sesi ini | ⚠️ Peringatan — sudah absen |
| QR tidak dikenali | ❌ Data tidak ditemukan |

---

## 🔒 Keamanan

- Semua route dilindungi middleware `auth` + `role:admin/panitia/peserta`
- Upload file divalidasi: hanya `jpg`, `jpeg`, `png`, `pdf`, maksimal 2MB
- Dokumen dan data pendaftaran **tidak bisa diubah** setelah peserta mendaftar
- Data pendaftaran menggunakan **snapshot** — perubahan profil setelah mendaftar tidak mempengaruhi data yang sudah tersimpan
- Absensi hanya bisa dilakukan untuk peserta dengan `is_lulus_final = true`
- Kartu peserta hanya bisa didownload oleh pemilik akun yang bersangkutan

---

## ⚙️ Konfigurasi DomPDF

Untuk kartu peserta PDF berfungsi dengan baik, pastikan konfigurasi berikut di `config/dompdf.php`:

```php
'options' => [
    'isRemoteEnabled'      => true, 
    'isHtml5ParserEnabled' => true,
    'defaultFont'          => 'DejaVu Sans',
    'chroot'               => public_path(),
],
```

> **Catatan:** Logo dan foto peserta di-embed sebagai **base64** agar DomPDF bisa merender gambar lokal tanpa masalah path.

---

## 📸 Screenshot

> *(Tambahkan screenshot aplikasi di sini)*

| Halaman | Preview |
|---|---|
| Dashboard Peserta | *(screenshot)* |
| Upload Dokumen | *(screenshot)* |
| Kartu Peserta PDF | *(screenshot)* |
| Scanner QR Absensi | *(screenshot)* |
| Rekap Absensi | *(screenshot)* |
| Dashboard Admin | *(screenshot)* |

---

## 🤝 Kontribusi

Proyek ini dikembangkan untuk keperluan internal Paskibra Kecamatan Compreng.  
Jika menemukan bug atau ingin menambahkan fitur, silakan buat issue atau pull request.

---

## 📝 Lisensi

Proyek ini dibuat untuk keperluan internal **Paskibra Kecamatan Compreng, Kabupaten Subang**.  
Tidak untuk didistribusikan atau digunakan secara komersial tanpa izin.

---

<p align="center">
  Email : rheigaruhulqudus@gmail.com
  Linkedin : rheigaruhulqudus
</p>