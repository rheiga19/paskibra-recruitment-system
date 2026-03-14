<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'jadwal_latihan_id', 'pendaftaran_id',
        'status', 'waktu_masuk', 'waktu_pulang',
        'dicatat_oleh', 'keterangan',
    ];

    protected $casts = [
        'waktu_masuk'  => 'datetime',
        'waktu_pulang' => 'datetime',
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalLatihan::class, 'jadwal_latihan_id');
    }

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function labelStatus(): string
    {
        return match($this->status) {
            'hadir' => 'Hadir',
            'izin'  => 'Izin',
            'sakit' => 'Sakit',
            default => 'Alpha',
        };
    }

    public function badgeStatus(): string
    {
        return match($this->status) {
            'hadir' => 'success',
            'izin'  => 'info',
            'sakit' => 'warning',
            default => 'danger',
        };
    }
}