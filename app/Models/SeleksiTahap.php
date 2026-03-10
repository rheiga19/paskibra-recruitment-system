<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeleksiTahap extends Model
{
    protected $table = 'seleksi_tahap';

    protected $fillable = [
        'rekrutmen_id',
        'nama',
        'deskripsi',
        'urutan',
        'komponen_nilai',
        'passing_grade',
        'is_aktif',
        'is_diumumkan',
        'is_tahap_final',
        'tanggal_pengumuman',
    ];

    protected $casts = [
        'komponen_nilai'     => 'array',
        'passing_grade'      => 'float',
        'is_aktif'           => 'boolean',
        'is_diumumkan'       => 'boolean',
        'is_tahap_final'     => 'boolean',
        'tanggal_pengumuman' => 'datetime',
    ];

    // ─── Relasi ───────────────────────────────────────────────────
    public function rekrutmen()
    {
        return $this->belongsTo(Rekrutmen::class);
    }

    public function hasil()
    {
        return $this->hasMany(SeleksiHasil::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────
    public function jumlahLolos(): int
    {
        return $this->hasil()->where('status', 'lolos')->count();
    }

    public function jumlahTidakLolos(): int
    {
        return $this->hasil()->where('status', 'tidak_lolos')->count();
    }

    public function jumlahPending(): int
    {
        return $this->hasil()->where('status', 'pending')->count();
    }
}