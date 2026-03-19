<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Rekrutmen;
use App\Models\Pendaftaran;
use App\Models\DokumenPeserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumenController extends Controller
{
    private const JENIS_LIST = [
        'foto_4x6', 'ktp_pelajar', 'akta_kelahiran',
        'rapor', 'surat_sehat', 'surat_izin_ortu',
    ];

    public function index()
    {
        $user             = auth()->user();
        $rekrutmenAktif   = Rekrutmen::where('is_aktif', true)->latest()->first();
        $pendaftaranAktif = $rekrutmenAktif
            ? Pendaftaran::where('user_id', $user->id)
                         ->where('rekrutmen_id', $rekrutmenAktif->id)
                         ->exists()
            : false;

        $dokumen = DokumenPeserta::where('user_id', $user->id)
                                 ->get()
                                 ->keyBy('jenis');

        return view('peserta.dokumen.index', compact('dokumen', 'pendaftaranAktif'));
    }

    // KEAMANAN: file disimpan di storage/app/private/dokumen (bukan public)
    // Akses file hanya lewat route peserta.dokumen.lihat yang cek auth
    public function upload(Request $request)
    {
        $request->validate([
            'jenis' => ['required', 'in:' . implode(',', self::JENIS_LIST)],
            'file'  => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $user  = auth()->user();
        $jenis = $request->jenis;

        // Dokumen dikunci setelah mendaftar
        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->latest()->first();
        if ($rekrutmenAktif) {
            $sudahDaftar = Pendaftaran::where('user_id', $user->id)
                ->where('rekrutmen_id', $rekrutmenAktif->id)
                ->exists();
            if ($sudahDaftar) {
                return back()->with('error', 'Dokumen tidak bisa diubah setelah mendaftar.');
            }
        }

        // Hapus file lama jika ada
        $lama = DokumenPeserta::where('user_id', $user->id)->where('jenis', $jenis)->first();
        if ($lama) {
            Storage::disk('local')->delete($lama->path);
            $lama->delete();
        }

        $namaAsli = $request->file('file')->getClientOriginalName();
        $ext      = strtolower($request->file('file')->getClientOriginalExtension());
        $folder   = 'dokumen/' . $user->id . '_' . Str::slug($user->name);
        $namaFile = $jenis . '.' . $ext;

        // Simpan ke storage/app/private (disk: local) — tidak bisa diakses via URL langsung
        $path = $request->file('file')->storeAs($folder, $namaFile, 'local');

        DokumenPeserta::create([
            'user_id'   => $user->id,
            'jenis'     => $jenis,
            'path'      => $path,
            'nama_file' => $namaAsli,
        ]);

        return back()->with('success', 'Dokumen ' . (DokumenPeserta::JENIS[$jenis] ?? $jenis) . ' berhasil diupload.');
    }

    // Route: GET peserta/dokumen/{jenis}/lihat → peserta.dokumen.lihat
    public function lihat(string $jenis)
    {
        abort_unless(in_array($jenis, self::JENIS_LIST), 422);

        $dok = DokumenPeserta::where('user_id', auth()->id())
                             ->where('jenis', $jenis)
                             ->firstOrFail();

        [$disk, $file, $mime] = $this->resolveFile($dok->path, $dok->nama_file);

        return response($file, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . $dok->nama_file . '"');
    }

    // Route: GET admin/dokumen/{dokumenPeserta}/lihat → admin.dokumen.lihat
    //        GET panitia/dokumen/{dokumenPeserta}/lihat → panitia.dokumen.lihat
    public function lihatAdmin(DokumenPeserta $dokumenPeserta)
    {
        abort_unless(
            auth()->user()->isAdmin() || auth()->user()->isPanitia(),
            403
        );

        [$disk, $file, $mime] = $this->resolveFile($dokumenPeserta->path, $dokumenPeserta->nama_file);

        return response($file, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . $dokumenPeserta->nama_file . '"');
    }

    public function hapus(Request $request, string $jenis)
    {
        abort_unless(in_array($jenis, self::JENIS_LIST), 422);

        $user = auth()->user();

        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->latest()->first();
        if ($rekrutmenAktif) {
            $sudahDaftar = Pendaftaran::where('user_id', $user->id)
                ->where('rekrutmen_id', $rekrutmenAktif->id)
                ->exists();
            if ($sudahDaftar) {
                return back()->with('error', 'Dokumen tidak bisa dihapus setelah mendaftar.');
            }
        }

        $dok = DokumenPeserta::where('user_id', $user->id)->where('jenis', $jenis)->first();
        if ($dok) {
            Storage::disk('local')->delete($dok->path);
            $dok->delete();
            return back()->with('success', 'Dokumen berhasil dihapus.');
        }

        return back()->with('error', 'Dokumen tidak ditemukan.');
    }

    private function resolveFile(string $path, string $namaFile): array
    {
        if (Storage::disk('local')->exists($path)) {
            return ['local', Storage::disk('local')->get($path), Storage::disk('local')->mimeType($path)];
        }

        if (Storage::disk('public')->exists($path)) {
            return ['public', Storage::disk('public')->get($path), Storage::disk('public')->mimeType($path)];
        }

        abort(404, 'File dokumen tidak ditemukan.');
    }
}