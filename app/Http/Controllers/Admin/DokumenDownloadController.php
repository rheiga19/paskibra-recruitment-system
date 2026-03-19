<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\DokumenPeserta;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DokumenDownloadController extends Controller
{
    // ── Download ZIP dokumen 1 peserta ─────────────────────────────────
    public function downloadSatu(Pendaftaran $pendaftaran)
    {
        $dokumen = DokumenPeserta::where('user_id', $pendaftaran->user_id)->get();
        abort_if($dokumen->isEmpty(), 404, 'Dokumen tidak ditemukan.');

        // Bersihkan nama file dari karakter berbahaya
        $namaAman = preg_replace('/[^A-Za-z0-9_\-]/', '_', $pendaftaran->no_pendaftaran . '_' . $pendaftaran->nama_lengkap);
        $namaZip  = 'dokumen_' . $namaAman . '.zip';

        // Gunakan storage_path dengan DIRECTORY_SEPARATOR yang benar
        $tmpDir  = storage_path('app' . DIRECTORY_SEPARATOR . 'tmp');
        $tmpPath = $tmpDir . DIRECTORY_SEPARATOR . $namaZip;

        // Buat folder tmp jika belum ada
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        // Hapus ZIP lama jika ada (mencegah konflik)
        if (file_exists($tmpPath)) {
            unlink($tmpPath);
        }

        $zip = new ZipArchive();
        $result = $zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        abort_unless($result === true, 500, 'Gagal membuat ZIP. Error: ' . $result);

        $adaFile = false;
        foreach ($dokumen as $dok) {
            $filePath = $this->resolveFilePath($dok->path);
            if ($filePath && file_exists($filePath)) {
                $ext = strtolower(pathinfo($dok->nama_file, PATHINFO_EXTENSION));
                $zip->addFile($filePath, $dok->jenis . '.' . $ext);
                $adaFile = true;
            }
        }

        $zip->close();

        abort_unless($adaFile && file_exists($tmpPath), 404, 'Tidak ada file dokumen yang ditemukan.');

        return response()->download($tmpPath, $namaZip)->deleteFileAfterSend(true);
    }

    // ── Download ZIP semua peserta ─────────────────────────────────────
    public function downloadSemua()
    {
        $rekrutmenId  = request('rekrutmen_id');
        $pendaftarans = Pendaftaran::with('user')
            ->when($rekrutmenId, fn($q) => $q->where('rekrutmen_id', $rekrutmenId))
            ->get();

        abort_if($pendaftarans->isEmpty(), 404, 'Belum ada pendaftaran.');

        $namaZip = 'dokumen_semua_' . now()->format('Ymd_His') . '.zip';
        $tmpDir  = storage_path('app' . DIRECTORY_SEPARATOR . 'tmp');
        $tmpPath = $tmpDir . DIRECTORY_SEPARATOR . $namaZip;

        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        if (file_exists($tmpPath)) {
            unlink($tmpPath);
        }

        $zip = new ZipArchive();
        $result = $zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        abort_unless($result === true, 500, 'Gagal membuat ZIP. Error: ' . $result);

        foreach ($pendaftarans as $p) {
            $dokumen = DokumenPeserta::where('user_id', $p->user_id)->get();
            if ($dokumen->isEmpty()) continue;

            // Nama folder dalam ZIP — bersihkan karakter
            $namaFolder = preg_replace('/[^A-Za-z0-9_\-]/', '_', $p->no_pendaftaran . '_' . $p->nama_lengkap) . '/';

            foreach ($dokumen as $dok) {
                $filePath = $this->resolveFilePath($dok->path);
                if ($filePath && file_exists($filePath)) {
                    $ext = strtolower(pathinfo($dok->nama_file, PATHINFO_EXTENSION));
                    $zip->addFile($filePath, $namaFolder . $dok->jenis . '.' . $ext);
                }
            }
        }

        $zip->close();

        abort_unless(file_exists($tmpPath), 500, 'Gagal membuat file ZIP.');

        return response()->download($tmpPath, $namaZip)->deleteFileAfterSend(true);
    }

    // ── Helper: cari file di local dulu, fallback ke public ────────────
    private function resolveFilePath(string $path): ?string
    {
        $localPath = storage_path('app' . DIRECTORY_SEPARATOR . $path);
        if (file_exists($localPath)) {
            return $localPath;
        }

        $publicPath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $path);
        if (file_exists($publicPath)) {
            return $publicPath;
        }

        return null;
    }
}