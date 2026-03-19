<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Rekrutmen;
use App\Models\Pendaftaran;
use App\Models\ProfilPeserta;
use App\Models\DokumenPeserta;
use App\Models\DokumenPendaftaran;

class PendaftaranController extends Controller
{
    public function index()
    {
        $pendaftaran = Pendaftaran::where('user_id', auth()->id())
                                  ->with('rekrutmen')
                                  ->latest()
                                  ->get();

        return view('peserta.pendaftaran.index', compact('pendaftaran'));
    }

    public function show(Pendaftaran $pendaftaran)
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

    public function kartu(Pendaftaran $pendaftaran)
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

    public function apply(Rekrutmen $rekrutmen)
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
        $tahun  = $rekrutmen->tahun ?? date('Y');
        $prefix = 'PSK-' . $tahun . '-';

        $last = Pendaftaran::where('rekrutmen_id', $rekrutmen->id)
            ->where('no_pendaftaran', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(no_pendaftaran, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->value('no_pendaftaran');

        $urutan = $last ? (int) substr($last, strlen($prefix)) + 1 : 1;

        do {
            $no     = $prefix . str_pad($urutan, 4, '0', STR_PAD_LEFT);
            $exists = Pendaftaran::where('no_pendaftaran', $no)->exists();
            if ($exists) $urutan++;
        } while ($exists);

        return $no;
    }
}