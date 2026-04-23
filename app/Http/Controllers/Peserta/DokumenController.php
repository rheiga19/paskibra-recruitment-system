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
        'foto_4x6', 'sertifikat', 'surat_sehat', 'surat_izin_ortu',
    ];

    private const ALLOWED_MIMES = ['jpg', 'jpeg', 'png', 'pdf'];

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

    public function uploadSemua(Request $request)
    {
        $user = auth()->user();

        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->latest()->first();
        if ($rekrutmenAktif) {
            $sudahDaftar = Pendaftaran::where('user_id', $user->id)
                ->where('rekrutmen_id', $rekrutmenAktif->id)->exists();
            if ($sudahDaftar) {
                return back()->with('error', 'Dokumen tidak bisa diubah setelah mendaftar.');
            }
        }

        $rules = [];
        foreach (self::JENIS_LIST as $jenis) {
            $rules["dokumen.$jenis"] = ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'];
        }
        $request->validate($rules, [
            'dokumen.*.mimes' => 'File :attribute hanya boleh berformat JPG, PNG, atau PDF.',
            'dokumen.*.max'   => 'Ukuran file :attribute maksimal 2MB.',
        ]);

        $blocked = ['doc','docx','mp4','mp3','avi','mkv','exe','zip','rar','xls','xlsx','ppt','pptx'];
        foreach (self::JENIS_LIST as $jenis) {
            if ($request->hasFile("dokumen.$jenis")) {
                $ext = strtolower($request->file("dokumen.$jenis")->getClientOriginalExtension());
                if (in_array($ext, $blocked)) {
                    return back()->with('error', "File '$jenis' tidak diizinkan. Hanya JPG, PNG, dan PDF.");
                }
            }
        }

        $count  = 0;
        $folder = 'dokumen/' . $user->id . '_' . Str::slug($user->name);

        foreach (self::JENIS_LIST as $jenis) {
            if (!$request->hasFile("dokumen.$jenis")) continue;
            $file = $request->file("dokumen.$jenis");
            $ext  = strtolower($file->getClientOriginalExtension());

            $lama = DokumenPeserta::where('user_id', $user->id)->where('jenis', $jenis)->first();
            if ($lama) { $this->deleteFile($lama->path); $lama->delete(); }

            $path = $file->storeAs($folder, $jenis . '.' . $ext, 'local');
            DokumenPeserta::create([
                'user_id'   => $user->id,
                'jenis'     => $jenis,
                'path'      => $path,
                'nama_file' => $file->getClientOriginalName(),
            ]);
            $count++;
        }

        if ($count === 0) return back()->with('error', 'Tidak ada file yang dipilih.');
        return back()->with('success', $count . ' dokumen berhasil disimpan.');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'jenis' => ['required', 'in:' . implode(',', self::JENIS_LIST)],
            'file'  => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $user  = auth()->user();
        $jenis = $request->jenis;

        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->latest()->first();
        if ($rekrutmenAktif) {
            $sudahDaftar = Pendaftaran::where('user_id', $user->id)
                ->where('rekrutmen_id', $rekrutmenAktif->id)->exists();
            if ($sudahDaftar) return back()->with('error', 'Dokumen tidak bisa diubah setelah mendaftar.');
        }

        $lama = DokumenPeserta::where('user_id', $user->id)->where('jenis', $jenis)->first();
        if ($lama) { $this->deleteFile($lama->path); $lama->delete(); }

        $ext    = strtolower($request->file('file')->getClientOriginalExtension());
        $folder = 'dokumen/' . $user->id . '_' . Str::slug($user->name);
        $path   = $request->file('file')->storeAs($folder, $jenis . '.' . $ext, 'local');

        DokumenPeserta::create([
            'user_id'   => $user->id,
            'jenis'     => $jenis,
            'path'      => $path,
            'nama_file' => $request->file('file')->getClientOriginalName(),
        ]);

        return back()->with('success', 'Dokumen berhasil diupload.');
    }

    public function lihat(string $jenis)
    {
        abort_unless(in_array($jenis, self::JENIS_LIST), 422);
        $dok = DokumenPeserta::where('user_id', auth()->id())->where('jenis', $jenis)->firstOrFail();
        [$content, $mime] = $this->readFile($dok->path);
        return response($content, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . $dok->nama_file . '"');
    }

    public function lihatAdmin(DokumenPeserta $dokumenPeserta)
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->isPanitia(), 403);
        [$content, $mime] = $this->readFile($dokumenPeserta->path);
        return response($content, 200)
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
                ->where('rekrutmen_id', $rekrutmenAktif->id)->exists();
            if ($sudahDaftar) return back()->with('error', 'Dokumen tidak bisa dihapus setelah mendaftar.');
        }

        $dok = DokumenPeserta::where('user_id', $user->id)->where('jenis', $jenis)->first();
        if ($dok) {
            $this->deleteFile($dok->path);
            $dok->delete();
            return back()->with('success', 'Dokumen berhasil dihapus.');
        }

        return back()->with('error', 'Dokumen tidak ditemukan.');
    }

    // ── Helper: baca file — cek storage/app/private dulu ─────────────
    private function readFile(string $path): array
    {
        // Laravel 11: local disk → storage/app/private
        $privateFull = storage_path('app/private/' . $path);
        if (file_exists($privateFull)) {
            return [file_get_contents($privateFull), mime_content_type($privateFull)];
        }

        // Fallback: storage/app
        if (Storage::disk('local')->exists($path)) {
            return [Storage::disk('local')->get($path), Storage::disk('local')->mimeType($path)];
        }

        // Fallback: storage/app/public
        if (Storage::disk('public')->exists($path)) {
            return [Storage::disk('public')->get($path), Storage::disk('public')->mimeType($path)];
        }

        abort(404, 'File dokumen tidak ditemukan.');
    }

    // ── Helper: hapus file — cek storage/app/private dulu ────────────
    private function deleteFile(string $path): void
    {
        $privateFull = storage_path('app/private/' . $path);
        if (file_exists($privateFull)) { unlink($privateFull); return; }
        if (Storage::disk('local')->exists($path)) { Storage::disk('local')->delete($path); return; }
        if (Storage::disk('public')->exists($path)) { Storage::disk('public')->delete($path); }
    }
}