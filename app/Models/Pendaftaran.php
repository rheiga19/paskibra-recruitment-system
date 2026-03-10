<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    protected $table = 'pendaftaran';

    protected $fillable = [
        'user_id', 'rekrutmen_id', 'no_pendaftaran',
        'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
        'no_hp', 'alamat_lengkap',
        'provinsi_nama', 'kabupaten_nama', 'kecamatan_nama', 'desa_nama',
        'tinggi_badan', 'berat_badan',
        'nama_sekolah', 'jenjang', 'kelas', 'nilai_rata',
        'nama_ortu', 'hp_ortu', 'hubungan_ortu', 'prestasi',
        'status', 'catatan_admin', 'is_lulus_final', 'nilai_akhir', 'tanggal_lulus_final',
    ];

    protected $casts = [
        'tanggal_lahir'       => 'date',
        'is_lulus_final'      => 'boolean',
        'tanggal_lulus_final' => 'datetime',
        'nilai_rata'          => 'float',
        'nilai_akhir'         => 'float',
    ];

    public const STATUS = [
        'menunggu'    => 'Menunggu Verifikasi',
        'diverifikasi' => 'Sudah Diverifikasi',
        'lulus'       => 'Lulus',
        'tidak_lulus' => 'Tidak Lulus',
    ];

    public const STATUS_COLOR = [
        'menunggu'    => 'yellow',
        'diverifikasi' => 'blue',
        'lulus'       => 'green',
        'tidak_lulus' => 'red',
    ];

    // ─── Relasi ───────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rekrutmen()
    {
        return $this->belongsTo(Rekrutmen::class);
    }

    public function dokumen()
    {
        return $this->hasMany(DokumenPendaftaran::class);
    }

    public function hasilSeleksi()
    {
        return $this->hasMany(SeleksiHasil::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Generate nomor pendaftaran: PSK-{TAHUN}-{ID 5 digit}
     */
    public static function generateNoPendaftaran(int $id, int $tahun): string
    {
        return 'PSK-' . $tahun . '-' . str_pad($id, 5, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLOR[$this->status] ?? 'gray';
    }

    public function getUmurAttribute(): ?int
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : null;
    }

    // ─── Scopes ───────────────────────────────────────────────────
    public function scopeMenunggu($query)
    {
        return $query->where('status', 'menunggu');
    }

    public function scopeDiverifikasi($query)
    {
        return $query->where('status', 'diverifikasi');
    }

    public function scopeLulus($query)
    {
        return $query->where('is_lulus_final', true);
    }

    public function scopePutra($query)
    {
        return $query->where('jenis_kelamin', 'L');
    }

    public function scopePutri($query)
    {
        return $query->where('jenis_kelamin', 'P');
    }
}