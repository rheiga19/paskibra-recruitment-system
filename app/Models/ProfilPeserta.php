<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilPeserta extends Model
{
    protected $table = 'profil_peserta';

    protected $fillable = [
        'user_id',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'no_hp',
        'alamat_lengkap',
        'provinsi_kode',
        'provinsi_nama',
        'kabupaten_kode',
        'kabupaten_nama',
        'kecamatan_kode',
        'kecamatan_nama',
        'desa_kode',
        'desa_nama',
        'tinggi_badan',
        'berat_badan',
        'nama_sekolah',
        'jenjang',
        'kelas',
        'nilai_rata',
        'nama_ortu',
        'hp_ortu',
        'hubungan_ortu',
        'prestasi',
        'is_profil_lengkap',
    ];

    protected $casts = [
        'tanggal_lahir'     => 'date',
        'is_profil_lengkap' => 'boolean',
        'nilai_rata'        => 'float',
        'tinggi_badan'      => 'integer',
        'berat_badan'       => 'integer',
    ];

    // ─── Relasi ───────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Cek apakah semua field wajib sudah diisi.
     */
    public function cekKelengkapan(): bool
    {
        $wajib = [
            'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
            'no_hp', 'alamat_lengkap', 'tinggi_badan', 'berat_badan',
            'nama_sekolah', 'jenjang', 'kelas', 'nilai_rata',
            'nama_ortu', 'hp_ortu', 'hubungan_ortu',
        ];

        foreach ($wajib as $field) {
            if (empty($this->$field)) return false;
        }

        return true;
    }

    /**
     * Persentase kelengkapan profil (untuk progress bar).
     */
    public function persentaseKelengkapan(): int
    {
        $semua = [
            'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
            'no_hp', 'alamat_lengkap',
            'provinsi_nama', 'kabupaten_nama', 'kecamatan_nama', 'desa_nama',
            'tinggi_badan', 'berat_badan',
            'nama_sekolah', 'jenjang', 'kelas', 'nilai_rata',
            'nama_ortu', 'hp_ortu', 'hubungan_ortu',
        ];

        $terisi = collect($semua)->filter(fn($f) => !empty($this->$f))->count();

        return (int) round(($terisi / count($semua)) * 100);
    }

    /**
     * Accessor: nama jenjang lengkap.
     */
    public function getJenjangLabelAttribute(): string
    {
        return $this->jenjang ?? '-';
    }

    /**
     * Accessor: umur peserta.
     */
    public function getUmurAttribute(): ?int
    {
        return $this->tanggal_lahir
            ? $this->tanggal_lahir->age
            : null;
    }
}