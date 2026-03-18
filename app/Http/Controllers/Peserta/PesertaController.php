<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Rekrutmen;
use App\Models\Pendaftaran;
use App\Models\ProfilPeserta;
use App\Models\DokumenPeserta;
use App\Models\DokumenPendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PesertaController extends Controller
{
    // ── Dashboard ──────────────────────────────────────────────────────
    public function dashboard()
    {
        $user            = auth()->user();
        $rekrutmenAktif  = Rekrutmen::where('is_aktif', true)->latest()->first();
        $pendaftaran     = $rekrutmenAktif
            ? Pendaftaran::where('user_id', $user->id)
                         ->where('rekrutmen_id', $rekrutmenAktif->id)
                         ->first()
            : null;

        $profil        = ProfilPeserta::where('user_id', $user->id)->first();
        $profilLengkap = $this->cekProfilLengkap($profil);
        $dok           = $this->cekDokumen($user->id);

        return view('peserta.dashboard', compact(
            'rekrutmenAktif', 'pendaftaran', 'profil', 'profilLengkap', 'dok'
        ));
    }

    // ── Profil / Biodata ───────────────────────────────────────────────
    public function profilEdit()
    {
        $profil = ProfilPeserta::firstOrNew(['user_id' => auth()->id()]);
        return view('peserta.profil.edit', compact('profil'));
    }

    public function profilUpdate(Request $request)
    {
        $request->validate([
            'nama_lengkap'   => ['required', 'string', 'max:100'],
            'nik'            => ['required', 'digits:16'],
            'jenis_kelamin'  => ['required', 'in:L,P'],
            'tempat_lahir'   => ['required', 'string', 'max:60'],
            'tanggal_lahir'  => ['required', 'date', 'before:today'],
            'no_hp'          => ['required', 'string', 'max:15'],
            'alamat_lengkap' => ['required', 'string', 'max:300'],
            'nama_sekolah'   => ['required', 'string', 'max:100'],
            'jenjang'        => ['required', 'in:SMP,MTs,SMA,MA,SMK'],
            'kelas'          => ['required', 'in:VII,VIII,IX,X,XI,XII'],
            'nilai_rata'     => ['required', 'numeric', 'min:0', 'max:100'],
            'tinggi_badan'   => ['required', 'integer', 'min:100', 'max:250'],
            'berat_badan'    => ['required', 'integer', 'min:20',  'max:200'],
            'golongan_darah' => ['nullable', 'in:A,B,AB,O'],
            'nama_ortu'      => ['required', 'string', 'max:100'],
            'hp_ortu'        => ['required', 'string', 'max:15'],
            'hubungan_ortu'  => ['required', 'in:Ayah,Ibu,Wali'],
            'prestasi'       => ['nullable', 'string', 'max:500'],
        ]);

        auth()->user()->update(['name' => $request->nama_lengkap]);

        ProfilPeserta::updateOrCreate(
            ['user_id' => auth()->id()],
            $request->only([
                'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
                'no_hp', 'alamat_lengkap', 'nama_sekolah', 'jenjang', 'kelas',
                'nilai_rata', 'tinggi_badan', 'berat_badan', 'golongan_darah',
                'nama_ortu', 'hp_ortu', 'hubungan_ortu', 'prestasi',
            ])
        );

        return redirect()->route('peserta.profil.edit')
                         ->with('success', 'Biodata berhasil disimpan.');
    }

    // ── Dokumen — Tampilkan ────────────────────────────────────────────
    public function dokumenIndex()
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

    // ── Dokumen — Upload ───────────────────────────────────────────────
    // KEAMANAN: file disimpan di storage/app/private/dokumen (bukan public)
    // Akses file hanya lewat route peserta.dokumen.lihat yang cek auth
    public function dokumenUpload(Request $request)
    {
        $jenisList = ['foto_4x6', 'ktp_pelajar', 'akta_kelahiran', 'rapor', 'surat_sehat', 'surat_izin_ortu'];

        $request->validate([
            'jenis' => ['required', 'in:' . implode(',', $jenisList)],
            'file'  => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $user  = auth()->user();
        $jenis = $request->jenis;

        // Cek sudah mendaftar — dokumen dikunci
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
            // Hapus dari private storage
            Storage::disk('local')->delete($lama->path);
            $lama->delete();
        }

        $namaAsli = $request->file('file')->getClientOriginalName();
        $ext      = strtolower($request->file('file')->getClientOriginalExtension());
        // Folder private: dokumen/{id}_{nama-slug}/
        $folder   = 'dokumen/' . $user->id . '_' . Str::slug($user->name);
        $namaFile = $jenis . '.' . $ext;

        // Simpan ke storage/app/private (disk: local) — TIDAK bisa diakses via URL
        $path = $request->file('file')->storeAs($folder, $namaFile, 'local');

        DokumenPeserta::create([
            'user_id'   => $user->id,
            'jenis'     => $jenis,
            'path'      => $path,
            'nama_file' => $namaAsli,
        ]);

        return back()->with('success', 'Dokumen ' . (DokumenPeserta::JENIS[$jenis] ?? $jenis) . ' berhasil diupload.');
    }

    // ── Dokumen — Lihat / Serve (PRIVATE + AUTH) ───────────────────────
    // Route: GET peserta/dokumen/{jenis}/lihat  → name: peserta.dokumen.lihat
    public function dokumenLihat(string $jenis)
    {
        $jenisList = ['foto_4x6', 'ktp_pelajar', 'akta_kelahiran', 'rapor', 'surat_sehat', 'surat_izin_ortu'];
        abort_unless(in_array($jenis, $jenisList), 422);

        $dok = DokumenPeserta::where('user_id', auth()->id())
                             ->where('jenis', $jenis)
                             ->firstOrFail();

        [$disk, $file, $mime] = $this->resolveFile($dok->path, $dok->nama_file);

        return response($file, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . $dok->nama_file . '"');
    }

    // ── Dokumen — Lihat milik peserta (untuk ADMIN/PANITIA) ────────────
    // Route: GET admin/dokumen/{dokumenPeserta}/lihat → name: admin.dokumen.lihat
    public function dokumenLihatAdmin(DokumenPeserta $dokumenPeserta)
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

    // ── Helper: cari file di local dulu, fallback ke public ────────────
    // Menangani dokumen lama (disk public) dan dokumen baru (disk local)
    private function resolveFile(string $path, string $namaFile): array
    {
        // Coba disk local dulu (upload baru)
        if (Storage::disk('local')->exists($path)) {
            $mime = Storage::disk('local')->mimeType($path);
            $file = Storage::disk('local')->get($path);
            return ['local', $file, $mime];
        }

        // Fallback ke disk public (upload lama sebelum migrasi)
        if (Storage::disk('public')->exists($path)) {
            $mime = Storage::disk('public')->mimeType($path);
            $file = Storage::disk('public')->get($path);
            return ['public', $file, $mime];
        }

        abort(404, 'File dokumen tidak ditemukan.');
    }

    // ── Dokumen — Hapus ────────────────────────────────────────────────
    public function dokumenHapus(Request $request, string $jenis)
    {
        $jenisList = ['foto_4x6', 'ktp_pelajar', 'akta_kelahiran', 'rapor', 'surat_sehat', 'surat_izin_ortu'];
        abort_unless(in_array($jenis, $jenisList), 422);

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

    // ── Pendaftaran — Index ────────────────────────────────────────────
    public function pendaftaranIndex()
    {
        $pendaftaran = Pendaftaran::where('user_id', auth()->id())
                                  ->with('rekrutmen')
                                  ->latest()
                                  ->get();

        return view('peserta.pendaftaran.index', compact('pendaftaran'));
    }

    // ── Pendaftaran — Show ─────────────────────────────────────────────
    public function pendaftaranShow(Pendaftaran $pendaftaran)
    {
        abort_unless($pendaftaran->user_id === auth()->id(), 403);
        $pendaftaran->load('dokumen', 'rekrutmen');

        $jenisList = [
            'foto_4x6'        => 'Foto 4x6',
            'ktp_pelajar'     => 'KTP/Kartu Pelajar',
            'akta_kelahiran'  => 'Akta Kelahiran',
            'rapor'           => 'Rapor',
            'surat_sehat'     => 'Surat Keterangan Sehat',
            'surat_izin_ortu' => 'Surat Izin Orang Tua',
        ];

        return view('peserta.pendaftaran.show', compact('pendaftaran', 'jenisList'));
    }

    // ── Pendaftaran — Cetak Kartu ──────────────────────────────────────
    public function pendaftaranKartu(Pendaftaran $pendaftaran)
    {
        abort_unless($pendaftaran->user_id === auth()->id(), 403);

        if (!in_array($pendaftaran->status, ['diverifikasi', 'lulus'])) {
            return redirect()->route('peserta.pendaftaran.show', $pendaftaran)
                             ->with('error', 'Kartu hanya bisa dicetak setelah lolos administrasi.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('peserta.pendaftaran.kartu_pdf', compact('pendaftaran'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('kartu-peserta-' . $pendaftaran->no_pendaftaran . '.pdf');
    }

    // ── Apply ──────────────────────────────────────────────────────────
    public function pendaftaranApply(Rekrutmen $rekrutmen)
    {
        $user = auth()->user();

        if (Pendaftaran::where('user_id', $user->id)->where('rekrutmen_id', $rekrutmen->id)->exists()) {
            return redirect()->route('peserta.dashboard')
                             ->with('error', 'Kamu sudah mendaftar di rekrutmen ini.');
        }

        if (!$rekrutmen->is_aktif || now()->gt($rekrutmen->tanggal_tutup)) {
            return redirect()->route('peserta.dashboard')
                             ->with('error', 'Rekrutmen sudah ditutup.');
        }

        $profil = ProfilPeserta::where('user_id', $user->id)->first();
        if (!$this->cekProfilLengkap($profil)) {
            return redirect()->route('peserta.profil.edit')
                             ->with('error', 'Lengkapi biodata terlebih dahulu.');
        }

        $dok = $this->cekDokumen($user->id);
        if (in_array(false, $dok)) {
            return redirect()->route('peserta.dokumen.index')
                             ->with('error', 'Lengkapi semua dokumen terlebih dahulu.');
        }

        $pendaftaran = Pendaftaran::create([
            'user_id'        => $user->id,
            'rekrutmen_id'   => $rekrutmen->id,
            'no_pendaftaran' => $this->generateNoPendaftaran($rekrutmen),
            'nama_lengkap'   => $user->name,
            'nik'            => $profil->nik,
            'tempat_lahir'   => $profil->tempat_lahir,
            'tanggal_lahir'  => $profil->tanggal_lahir,
            'jenis_kelamin'  => $profil->jenis_kelamin,
            'no_hp'          => $profil->no_hp,
            'alamat_lengkap' => $profil->alamat_lengkap,
            'provinsi_nama'  => $profil->provinsi_nama  ?? null,
            'kabupaten_nama' => $profil->kabupaten_nama ?? null,
            'kecamatan_nama' => $profil->kecamatan_nama ?? null,
            'desa_nama'      => $profil->desa_nama      ?? null,
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
            'status'         => 'menunggu',
        ]);

        // Snapshot dokumen ke dokumen_pendaftaran (path tetap sama, private)
        $dokumenPeserta = DokumenPeserta::where('user_id', $user->id)->get();
        foreach ($dokumenPeserta as $d) {
            DokumenPendaftaran::create([
                'pendaftaran_id' => $pendaftaran->id,
                'jenis'          => $d->jenis,
                'path'           => $d->path,
                'nama_file'      => $d->nama_file,
            ]);
        }

        return redirect()->route('peserta.pendaftaran.show', $pendaftaran->id)
                         ->with('success', 'Pendaftaran berhasil! No. Daftar: ' . $pendaftaran->no_pendaftaran);
    }

    // ── Hasil Seleksi ──────────────────────────────────────────────────
    public function hasilSeleksi()
    {
        $user        = auth()->user();
        $pendaftaran = Pendaftaran::where('user_id', $user->id)->latest()->first();

        $hasilList = [];
        if ($pendaftaran) {
            $hasilList = $pendaftaran->hasilSeleksi()
                ->with('tahap')
                ->whereHas('tahap', fn($q) => $q->where('is_diumumkan', true))
                ->orderBy('created_at')
                ->get();
        }

        return view('peserta.hasil.hasil-seleksi', compact('pendaftaran', 'hasilList'));
    }

    // ── Kartu Anggota ──────────────────────────────────────────────────
    public function kartuAnggota()
    {
        $user        = auth()->user();
        $rekrutmen   = Rekrutmen::where('is_aktif', true)->first();

        $pendaftaran = Pendaftaran::with(['dokumen'])
            ->where('user_id', $user->id)
            ->where('is_lulus_final', true)
            ->when($rekrutmen, fn($q) => $q->where('rekrutmen_id', $rekrutmen->id))
            ->first();

        if (!$pendaftaran) {
            return back()->with('error', 'Kartu hanya tersedia untuk peserta yang lulus seleksi.');
        }

        return view('peserta.kartu-anggota', compact('pendaftaran', 'rekrutmen'));
    }

    // ── Rekap Absensi Peserta ──────────────────────────────────────────
    public function absensiIndex()
    {
        $user         = auth()->user();
        $rekrutmen    = Rekrutmen::where('is_aktif', true)->first();
        $pendaftaran  = null;
        $rekapAbsensi = collect();

        if ($rekrutmen) {
            $pendaftaran = Pendaftaran::where('user_id', $user->id)
                ->where('rekrutmen_id', $rekrutmen->id)
                ->where('is_lulus_final', true)
                ->first();

            if ($pendaftaran) {
                $rekapAbsensi = \App\Models\Absensi::with('jadwal')
                    ->where('pendaftaran_id', $pendaftaran->id)
                    ->orderByDesc('created_at')
                    ->get();
            }
        }

        $totalHadir = $rekapAbsensi->where('status', 'hadir')->count();
        $totalAll   = $rekapAbsensi->count();
        $persen     = $totalAll > 0 ? round($totalHadir / $totalAll * 100) : 0;

        return view('peserta.absensi', compact('pendaftaran', 'rekapAbsensi', 'totalHadir', 'totalAll', 'persen'));
    }

    // ── Helpers ────────────────────────────────────────────────────────
    private function cekProfilLengkap(?ProfilPeserta $profil): bool
    {
        if (!$profil) return false;
        return !empty($profil->nik)
            && !empty($profil->jenis_kelamin)
            && !empty($profil->tempat_lahir)
            && !empty($profil->tanggal_lahir)
            && !empty($profil->alamat_lengkap)
            && !empty($profil->nama_sekolah)
            && !empty($profil->jenjang)
            && !empty($profil->kelas)
            && !empty($profil->nilai_rata)
            && !empty($profil->tinggi_badan)
            && !empty($profil->berat_badan)
            && !empty($profil->nama_ortu)
            && !empty($profil->hp_ortu)
            && !empty($profil->hubungan_ortu);
    }

    private function cekDokumen(int $userId): array
    {
        $uploaded = DokumenPeserta::where('user_id', $userId)->pluck('jenis')->toArray();
        return [
            'foto_4x6'        => in_array('foto_4x6',        $uploaded),
            'ktp_pelajar'     => in_array('ktp_pelajar',     $uploaded),
            'akta_kelahiran'  => in_array('akta_kelahiran',  $uploaded),
            'rapor'           => in_array('rapor',           $uploaded),
            'surat_sehat'     => in_array('surat_sehat',     $uploaded),
            'surat_izin_ortu' => in_array('surat_izin_ortu', $uploaded),
        ];
    }

    private function generateNoPendaftaran(Rekrutmen $rekrutmen): string
    {
        $tahun = $rekrutmen->tahun ?? date('Y');
        $prefix = 'PSK-' . $tahun . '-';

        // Cari nomor urut tertinggi yang sudah ada, lalu +1
        $last = Pendaftaran::where('rekrutmen_id', $rekrutmen->id)
            ->where('no_pendaftaran', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(no_pendaftaran, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->value('no_pendaftaran');

        $urutan = $last
            ? (int) substr($last, strlen($prefix)) + 1
            : 1;

        // Loop sampai dapat nomor yang benar-benar belum ada (failsafe)
        do {
            $no = $prefix . str_pad($urutan, 4, '0', STR_PAD_LEFT);
            $exists = Pendaftaran::where('no_pendaftaran', $no)->exists();
            if ($exists) $urutan++;
        } while ($exists);

        return $no;
    }
}