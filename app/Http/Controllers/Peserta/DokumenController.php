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

    public function upload(Request $request)
    {
        $request->validate([
            'files'                => ['required', 'array'],
            'files.foto_4x6'       => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:1024'],
            'files.ktp_pelajar'    => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'files.akta_kelahiran' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'files.rapor'          => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'files.surat_sehat'    => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'files.surat_izin_ortu'=> ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'files.required'            => 'Tidak ada file yang dipilih.',
            'files.foto_4x6.mimes'      => 'Pas foto harus berformat JPG atau PNG (wajib berlatar merah).',
            'files.foto_4x6.max'        => 'Pas foto maksimal 1MB.',
            'files.ktp_pelajar.mimes'   => 'Kartu Pelajar/KTP harus berformat JPG, PNG, atau PDF.',
            'files.akta_kelahiran.mimes'=> 'Akta Kelahiran harus berformat JPG, PNG, atau PDF.',
            'files.rapor.mimes'         => 'Rapor harus berformat JPG, PNG, atau PDF.',
            'files.surat_sehat.mimes'   => 'Surat Sehat harus berformat JPG, PNG, atau PDF.',
            'files.surat_izin_ortu.mimes'=> 'Surat Izin Orang Tua harus berformat JPG, PNG, atau PDF.',
            'files.*.max'               => 'Ukuran file maksimal 2MB.',
        ]);

        $user = auth()->user();

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

        $berhasilCount = 0;

        foreach ($request->file('files', []) as $jenis => $file) {
            // Skip jika bukan jenis valid atau file null
            if (!in_array($jenis, self::JENIS_LIST) || !$file) continue;

            // Hapus file lama jika ada
            $lama = DokumenPeserta::where('user_id', $user->id)->where('jenis', $jenis)->first();
            if ($lama) {
                Storage::disk('local')->delete($lama->path);
                $lama->delete();
            }

            $namaAsli = $file->getClientOriginalName();
            $ext      = strtolower($file->getClientOriginalExtension());
            $folder   = 'dokumen/' . $user->id . '_' . Str::slug($user->name);
            $namaFile = $jenis . '.' . $ext;

            // Simpan ke storage/app/private (disk: local)
            $path = $file->storeAs($folder, $namaFile, 'local');

            DokumenPeserta::create([
                'user_id'   => $user->id,
                'jenis'     => $jenis,
                'path'      => $path,
                'nama_file' => $namaAsli,
            ]);

            $berhasilCount++;
        }

        if ($berhasilCount === 0) {
            return back()->with('error', 'Tidak ada dokumen yang berhasil disimpan.');
        }

        return back()->with('success', $berhasilCount . ' dokumen berhasil disimpan.');
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