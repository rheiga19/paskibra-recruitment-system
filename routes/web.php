<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

// ── HOME ────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/pengumuman', [HomeController::class, 'pengumuman'])->name('pengumuman');
Route::get('/berita',           [HomeController::class, 'beritaIndex'])->name('berita.index');
Route::get('/berita/{beritum}', [HomeController::class, 'beritaShow'])->name('berita.show');
Route::get('/galeri',           [HomeController::class, 'galeriIndex'])->name('galeri.index');
Route::get('/galeri/{galeri}',  [HomeController::class, 'galeriShow'])->name('galeri.show');

// ── REDIRECT DASHBOARD ──────────────────────────────────────────────
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isAdmin())   return redirect()->route('admin.dashboard');
    if ($user->isPanitia()) return redirect()->route('panitia.dashboard');
    return redirect()->route('peserta.dashboard');
})->middleware('auth')->name('dashboard');

require __DIR__.'/auth.php';

// ══════════════════════════════════════════════════════════════════════
// ADMIN
// ══════════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // ── Dashboard ──
    Route::get('dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])
        ->name('dashboard');

    // ── Rekrutmen ──
    Route::resource('rekrutmen', \App\Http\Controllers\Admin\RekrutmenController::class);
    Route::patch('rekrutmen/{rekrutmen}/toggle', [\App\Http\Controllers\Admin\RekrutmenController::class, 'toggleAktif'])
        ->name('rekrutmen.toggle');

    // ── Pendaftaran ──
    // PENTING: route statis harus di atas route dinamis {pendaftaran}
    Route::get('pendaftaran/download-semua-zip',
        [\App\Http\Controllers\Admin\DokumenDownloadController::class, 'downloadSemua'])
        ->name('pendaftaran.download-semua-zip');
    Route::get('pendaftaran/{pendaftaran}/dokumen-zip',
        [\App\Http\Controllers\Admin\DokumenDownloadController::class, 'downloadSatu'])
        ->name('pendaftaran.dokumen-zip');
    Route::get   ('pendaftaran',                         [\App\Http\Controllers\Admin\PendaftaranAdminController::class, 'index'])
        ->name('pendaftaran.index');
    Route::get   ('pendaftaran/{pendaftaran}',            [\App\Http\Controllers\Admin\PendaftaranAdminController::class, 'show'])
        ->name('pendaftaran.show');
    Route::patch ('pendaftaran/{pendaftaran}/verifikasi', [\App\Http\Controllers\Admin\PendaftaranAdminController::class, 'verifikasi'])
        ->name('pendaftaran.verifikasi');
    Route::delete('pendaftaran/{pendaftaran}',            [\App\Http\Controllers\Admin\PendaftaranAdminController::class, 'destroy'])
        ->name('pendaftaran.destroy');

    // ── Dokumen Peserta — Lihat (private, lewat controller) ──
    // Admin & panitia bisa akses lewat route ini
    Route::get('dokumen/{dokumenPeserta}/lihat',
        [\App\Http\Controllers\Peserta\PesertaController::class, 'dokumenLihatAdmin'])
        ->name('dokumen.lihat');

    // ── Seleksi ──
    Route::get ('seleksi',                                    [\App\Http\Controllers\Admin\SeleksiController::class, 'index'])
        ->name('seleksi.index');
    Route::get ('seleksi/hasil-akhir',                        [\App\Http\Controllers\Admin\SeleksiController::class, 'hasilAkhir'])
        ->name('seleksi.hasil-akhir');
    Route::post('seleksi/proses-kelulusan',                   [\App\Http\Controllers\Admin\SeleksiController::class, 'prosesKelulusan'])
        ->name('seleksi.proses-kelulusan');
    Route::get ('seleksi/{pendaftaran}/{seleksiTahap}/input',  [\App\Http\Controllers\Admin\SeleksiController::class, 'inputNilai'])
        ->name('seleksi.input-nilai');
    Route::put ('seleksi/{pendaftaran}/{seleksiTahap}/simpan', [\App\Http\Controllers\Admin\SeleksiController::class, 'simpanNilai'])
        ->name('seleksi.simpan-nilai');
    Route::post  ('rekrutmen/{rekrutmen}/tahap',               [\App\Http\Controllers\Admin\SeleksiController::class, 'storeTahap'])
        ->name('seleksi.storeTahap');
    Route::delete('seleksi/tahap/{seleksiTahap}',              [\App\Http\Controllers\Admin\SeleksiController::class, 'destroyTahap'])
        ->name('seleksi.destroyTahap');
    Route::patch ('seleksi/tahap/{seleksiTahap}/pengumuman',   [\App\Http\Controllers\Admin\SeleksiController::class, 'togglePengumuman'])
        ->name('seleksi.togglePengumuman');

    // ── Berita ──
    Route::get   ('berita',                   [\App\Http\Controllers\Admin\BeritaController::class, 'index'])        ->name('berita.index');
    Route::get   ('berita/create',            [\App\Http\Controllers\Admin\BeritaController::class, 'create'])       ->name('berita.create');
    Route::post  ('berita',                   [\App\Http\Controllers\Admin\BeritaController::class, 'store'])        ->name('berita.store');
    Route::get   ('berita/{beritum}/edit',    [\App\Http\Controllers\Admin\BeritaController::class, 'edit'])         ->name('berita.edit');
    Route::put   ('berita/{beritum}',         [\App\Http\Controllers\Admin\BeritaController::class, 'update'])       ->name('berita.update');
    Route::delete('berita/{beritum}',         [\App\Http\Controllers\Admin\BeritaController::class, 'destroy'])      ->name('berita.destroy');
    Route::patch ('berita/{beritum}/publish', [\App\Http\Controllers\Admin\BeritaController::class, 'togglePublish'])->name('berita.publish');

    // ── Galeri ──
    Route::get   ('galeri',          [\App\Http\Controllers\Admin\GaleriController::class, 'index'])  ->name('galeri.index');
    Route::post  ('galeri',          [\App\Http\Controllers\Admin\GaleriController::class, 'store'])  ->name('galeri.store');
    Route::put   ('galeri/{galeri}', [\App\Http\Controllers\Admin\GaleriController::class, 'update']) ->name('galeri.update');
    Route::delete('galeri/{galeri}', [\App\Http\Controllers\Admin\GaleriController::class, 'destroy'])->name('galeri.destroy');

    // ── Manajemen User ──
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)
        ->except(['show']);
    Route::patch('users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])
        ->name('users.reset-password');

    // ── Pengaturan ──
    Route::get('pengaturan',  [\App\Http\Controllers\Admin\PengaturanController::class, 'edit'])  ->name('pengaturan.edit');
    Route::put('pengaturan',  [\App\Http\Controllers\Admin\PengaturanController::class, 'update'])->name('pengaturan.update');

    // ── Absensi ──
    Route::get   ('absensi',                  [\App\Http\Controllers\Admin\AbsensiController::class, 'index'])       ->name('absensi.index');
    Route::post  ('absensi',                  [\App\Http\Controllers\Admin\AbsensiController::class, 'store'])       ->name('absensi.store');
    Route::post  ('absensi/bulk',             [\App\Http\Controllers\Admin\AbsensiController::class, 'storeBulk'])   ->name('absensi.store-bulk');
    Route::get   ('absensi/rekap',            [\App\Http\Controllers\Admin\AbsensiController::class, 'rekap'])       ->name('absensi.rekap');
    Route::get   ('absensi/export-excel',     [\App\Http\Controllers\Admin\AbsensiController::class, 'exportExcel'])->name('absensi.export-excel');
    Route::get   ('absensi/export-pdf',       [\App\Http\Controllers\Admin\AbsensiController::class, 'exportPdf'])  ->name('absensi.export-pdf');
    Route::get   ('absensi/{jadwal}/scan',    [\App\Http\Controllers\Admin\AbsensiController::class, 'scan'])        ->name('absensi.scan');
    Route::post  ('absensi/{jadwal}/qr',      [\App\Http\Controllers\Admin\AbsensiController::class, 'prosesQr'])   ->name('absensi.qr');
    Route::post  ('absensi/{jadwal}/manual',  [\App\Http\Controllers\Admin\AbsensiController::class, 'inputManual'])->name('absensi.manual');
    Route::patch ('absensi/record/{absensi}', [\App\Http\Controllers\Admin\AbsensiController::class, 'updateStatus'])->name('absensi.update-status');
    Route::delete('absensi/{jadwal}',         [\App\Http\Controllers\Admin\AbsensiController::class, 'destroy'])    ->name('absensi.destroy');
});


// ══════════════════════════════════════════════════════════════════════
// PANITIA
// ══════════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:panitia'])
    ->prefix('panitia')
    ->name('panitia.')
    ->group(function () {

    // ── Dashboard ──
    Route::get('dashboard', [\App\Http\Controllers\Panitia\DashboardController::class, 'dashboard'])
        ->name('dashboard');

    // ── Dokumen Peserta — Lihat (panitia juga bisa akses) ──
    Route::get('dokumen/{dokumenPeserta}/lihat',
        [\App\Http\Controllers\Peserta\PesertaController::class, 'dokumenLihatAdmin'])
        ->name('dokumen.lihat');

    // ── Verifikasi Administrasi ──
    Route::get  ('verifikasi',               [\App\Http\Controllers\Panitia\VerifikasiController::class, 'index'])
        ->name('verifikasi.index');
    Route::get  ('verifikasi/{pendaftaran}',  [\App\Http\Controllers\Panitia\VerifikasiController::class, 'show'])
        ->name('verifikasi.show');
    Route::patch('verifikasi/{pendaftaran}',  [\App\Http\Controllers\Panitia\VerifikasiController::class, 'proses'])
        ->name('verifikasi.proses');

    // ── Input Nilai Seleksi ──
    Route::get('seleksi',                              [\App\Http\Controllers\Panitia\SeleksiController::class, 'index'])
        ->name('seleksi.index');
    Route::get('seleksi/{pendaftaran}/{tahap}/input',  [\App\Http\Controllers\Panitia\SeleksiController::class, 'input'])
        ->name('seleksi.input');
    Route::put('seleksi/{pendaftaran}/{tahap}/simpan', [\App\Http\Controllers\Panitia\SeleksiController::class, 'simpan'])
        ->name('seleksi.simpan');

    // ── Hasil Akhir ──
    Route::get('hasil', [\App\Http\Controllers\Panitia\HasilController::class, 'index'])
        ->name('hasil.index');

    // ── Absensi ──
    Route::get  ('absensi',                  [\App\Http\Controllers\Admin\AbsensiController::class, 'index'])        ->name('absensi.index');
    Route::get  ('absensi/{jadwal}/scan',    [\App\Http\Controllers\Admin\AbsensiController::class, 'scan'])         ->name('absensi.scan');
    Route::post ('absensi/{jadwal}/qr',      [\App\Http\Controllers\Admin\AbsensiController::class, 'prosesQr'])    ->name('absensi.qr');
    Route::post ('absensi/{jadwal}/manual',  [\App\Http\Controllers\Admin\AbsensiController::class, 'inputManual']) ->name('absensi.manual');
    Route::patch('absensi/record/{absensi}', [\App\Http\Controllers\Admin\AbsensiController::class, 'updateStatus'])->name('absensi.update-status');
});



// ══════════════════════════════════════════════════════════════════════
// PESERTA
// ══════════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:peserta'])
    ->prefix('peserta')
    ->name('peserta.')
    ->group(function () {
 
    // ── Dashboard ──
    Route::get('dashboard', [\App\Http\Controllers\Peserta\DashboardController::class, 'dashboard'])
        ->name('dashboard');
 
    // ── Profil / Biodata ──
    Route::get('profil/edit', [\App\Http\Controllers\Peserta\ProfilController::class, 'edit'])
        ->name('profil.edit');
    Route::put('profil',      [\App\Http\Controllers\Peserta\ProfilController::class, 'update'])
        ->name('profil.update');
 
    // ── Dokumen ──
    Route::get   ('dokumen',               [\App\Http\Controllers\Peserta\DokumenController::class, 'index'])
        ->name('dokumen.index');
    Route::post  ('dokumen/upload',        [\App\Http\Controllers\Peserta\DokumenController::class, 'upload'])
        ->name('dokumen.upload');
    Route::get   ('dokumen/{jenis}/lihat', [\App\Http\Controllers\Peserta\DokumenController::class, 'lihat'])
        ->name('dokumen.lihat');
    Route::delete('dokumen/{jenis}',       [\App\Http\Controllers\Peserta\DokumenController::class, 'hapus'])
        ->name('dokumen.hapus');
 
    // ── Pendaftaran ──
    // PENTING: route statis di atas route dinamis {pendaftaran}
    Route::post('pendaftaran/{rekrutmen}/apply',  [\App\Http\Controllers\Peserta\PendaftaranController::class, 'apply'])
        ->name('pendaftaran.apply');
    Route::get ('pendaftaran',                    [\App\Http\Controllers\Peserta\PendaftaranController::class, 'index'])
        ->name('pendaftaran.index');
    Route::get ('pendaftaran/{pendaftaran}',       [\App\Http\Controllers\Peserta\PendaftaranController::class, 'show'])
        ->name('pendaftaran.show');
    Route::get ('pendaftaran/{pendaftaran}/kartu', [\App\Http\Controllers\Peserta\PendaftaranController::class, 'kartu'])
        ->name('pendaftaran.kartu');
 
    // ── Hasil Seleksi ──
    Route::get('hasil-seleksi', [\App\Http\Controllers\Peserta\HasilAbsensiController::class, 'hasilSeleksi'])
        ->name('hasil.index');
 
    // ── Absensi ──
    Route::get('absensi', [\App\Http\Controllers\Peserta\HasilAbsensiController::class, 'absensiIndex'])
        ->name('absensi');
 
    // ── Kartu Anggota ──
    Route::get('kartu', [\App\Http\Controllers\Peserta\HasilAbsensiController::class, 'kartuAnggota'])
        ->name('kartu');
});