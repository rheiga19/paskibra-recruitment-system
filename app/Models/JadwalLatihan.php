<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalLatihan extends Model
{
    protected $table = 'jadwal_latihan';

    protected $fillable = [
        'rekrutmen_id', 'nama', 'tanggal',
        'jam_masuk', 'jam_pulang',
        'lokasi', 'keterangan', 'is_aktif',
    ];

    protected $casts = [
        'tanggal'  => 'date',
        'is_aktif' => 'boolean',
    ];

    public function rekrutmen()
    {
        return $this->belongsTo(Rekrutmen::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function jumlahHadir(): int
    {
        return $this->absensi()->where('status', 'hadir')->count();
    }

    public function jumlahAlpha(): int
    {
        return $this->absensi()->where('status', 'alpha')->count();
    }

    public function isHariIni(): bool
    {
        return $this->tanggal->isToday();
    }
}