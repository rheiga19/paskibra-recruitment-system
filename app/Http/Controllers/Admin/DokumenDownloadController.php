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

        $namaZip = 'dokumen_' . $pendaftaran->no_pendaftaran . '_' . str_replace(' ', '_', $pendaftaran->nama_lengkap) . '.zip';
        $tmpPath = storage_path('app/tmp/' . $namaZip);

        if (!is_dir(storage_path('app/tmp'))) {
            mkdir(storage_path('app/tmp'), 0755, true);
        }

        $zip = new ZipArchive();
        abort_unless($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true, 500, 'Gagal membuat ZIP.');

        foreach ($dokumen as $dok) {
            $filePath = $this->resolveFilePath($dok->path);
            if ($filePath) {
                $ext      = pathinfo($dok->nama_file, PATHINFO_EXTENSION);
                $zip->addFile($filePath, $dok->jenis . '.' . $ext);
            }
        }

        $zip->close();

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

        $namaZip = 'dokumen_semua_peserta_' . now()->format('Ymd_His') . '.zip';
        $tmpPath = storage_path('app/tmp/' . $namaZip);

        if (!is_dir(storage_path('app/tmp'))) {
            mkdir(storage_path('app/tmp'), 0755, true);
        }

        $zip = new ZipArchive();
        abort_unless($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true, 500, 'Gagal membuat ZIP.');

        foreach ($pendaftarans as $p) {
            $dokumen = DokumenPeserta::where('user_id', $p->user_id)->get();
            if ($dokumen->isEmpty()) continue;

            $folderDalamZip = $p->no_pendaftaran . '_' . str_replace(' ', '_', $p->nama_lengkap) . '/';

            foreach ($dokumen as $dok) {
                $filePath = $this->resolveFilePath($dok->path);
                if ($filePath) {
                    $ext = pathinfo($dok->nama_file, PATHINFO_EXTENSION);
                    $zip->addFile($filePath, $folderDalamZip . $dok->jenis . '.' . $ext);
                }
            }
        }

        $zip->close();

        return response()->download($tmpPath, $namaZip)->deleteFileAfterSend(true);
    }

    // ── Helper: cari file di local dulu, fallback ke public ────────────
    // Menangani dokumen lama (disk public) dan dokumen baru (disk local)
    private function resolveFilePath(string $path): ?string
    {
        // Coba disk local dulu (upload baru — storage/app/dokumen/...)
        $localPath = storage_path('app/' . $path);
        if (file_exists($localPath)) {
            return $localPath;
        }

        // Fallback ke disk public (upload lama — storage/app/public/dokumen/...)
        $publicPath = storage_path('app/public/' . $path);
        if (file_exists($publicPath)) {
            return $publicPath;
        }

        return null;
    }
}