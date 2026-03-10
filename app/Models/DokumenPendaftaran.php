<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenPendaftaran extends Model
{
    protected $table = 'dokumen_pendaftaran';

    protected $fillable = [
        'pendaftaran_id',
        'jenis',
        'path',
        'nama_file',
    ];

    // ─── Relasi ───────────────────────────────────────────────────
    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────
    public function getLabelAttribute(): string
    {
        return DokumenPeserta::JENIS[$this->jenis] ?? $this->jenis;
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}