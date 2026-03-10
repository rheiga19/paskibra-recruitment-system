<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rekrutmen extends Model
{
    protected $table = 'rekrutmen';

    protected $fillable = [
        'tahun',
        'nama',
        'deskripsi',
        'tanggal_buka',
        'tanggal_tutup',
        'is_aktif',
        'kuota_putra',
        'kuota_putri',
        'syarat',
        'catatan',
    ];

    protected $casts = [
        'tanggal_buka'  => 'date',
        'tanggal_tutup' => 'date',
        'is_aktif'      => 'boolean',
    ];

    // ─── Relasi ───────────────────────────────────────────────────
    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function seleksiTahap()
    {
        return $this->hasMany(SeleksiTahap::class)->orderBy('urutan');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Apakah rekrutmen sedang dalam periode pendaftaran.
     */
    public function isSedangBuka(): bool
    {
        return $this->is_aktif
            && now()->between($this->tanggal_buka, $this->tanggal_tutup);
    }

    /**
     * Jumlah pendaftar putra.
     */
    public function jumlahPutra(): int
    {
        return $this->pendaftaran()->where('jenis_kelamin', 'L')->count();
    }

    /**
     * Jumlah pendaftar putri.
     */
    public function jumlahPutri(): int
    {
        return $this->pendaftaran()->where('jenis_kelamin', 'P')->count();
    }

    /**
     * Apakah kuota putra sudah penuh.
     */
    public function kuotaPutraPenuh(): bool
    {
        if (!$this->kuota_putra) return false;
        return $this->jumlahPutra() >= $this->kuota_putra;
    }

    /**
     * Apakah kuota putri sudah penuh.
     */
    public function kuotaPutriPenuh(): bool
    {
        if (!$this->kuota_putri) return false;
        return $this->jumlahPutri() >= $this->kuota_putri;
    }

    // ─── Scopes ───────────────────────────────────────────────────
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}