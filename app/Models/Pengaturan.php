<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = [
        'pendaftaran_aktif',
        'pengumuman_aktif',
        'pengumuman_diaktifkan_at',
        'pesan_pengumuman',
        'nama_kecamatan',
        'alamat_sekretariat',
        'no_hp_panitia',
    ];

    protected $casts = [
        'pendaftaran_aktif'        => 'boolean',
        'pengumuman_aktif'         => 'boolean',
        'pengumuman_diaktifkan_at' => 'datetime',
    ];

    public static function ambil(): static
    {
        return static::firstOrCreate([], [
            'pendaftaran_aktif' => false,
            'pengumuman_aktif'  => false,
        ]);
    }
}