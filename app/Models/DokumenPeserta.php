<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenPeserta extends Model
{
    protected $table = 'dokumen_peserta';

    protected $fillable = [
        'user_id',
        'jenis',
        'path',
        'nama_file',
    ];

    public const JENIS = [
        'foto_4x6'        => 'Foto 4x6',
        'ktp_pelajar'     => 'KTP / Kartu Pelajar',
        'akta_kelahiran'  => 'Akta Kelahiran',
        'rapor'           => 'Rapor',
        'surat_sehat'     => 'Surat Keterangan Sehat',
        'surat_izin_ortu' => 'Surat Izin Orang Tua',
    ];

    // ─── Relasi ───────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────
    public function getLabelAttribute(): string
    {
        return self::JENIS[$this->jenis] ?? $this->jenis;
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}