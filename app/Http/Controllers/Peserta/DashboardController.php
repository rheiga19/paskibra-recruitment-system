<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Rekrutmen;
use App\Models\Pendaftaran;
use App\Models\ProfilPeserta;
use App\Models\DokumenPeserta;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user           = auth()->user();
        $rekrutmenAktif = Rekrutmen::where('is_aktif', true)->latest()->first();
        $pendaftaran    = $rekrutmenAktif
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
}