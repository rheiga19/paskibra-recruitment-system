<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'foto_profil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Role Helpers ─────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPanitia(): bool
    {
        return $this->role === 'panitia';
    }

    public function isPeserta(): bool
    {
        return $this->role === 'peserta';
    }

    public function isAdminOrPanitia(): bool
    {
        return in_array($this->role, ['admin', 'panitia']);
    }

    // ─── Relasi ───────────────────────────────────────────────────
    public function profil()
    {
        return $this->hasOne(ProfilPeserta::class);
    }

    public function dokumen()
    {
        return $this->hasMany(DokumenPeserta::class);
    }

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function berita()
    {
        return $this->hasMany(Berita::class, 'admin_id');
    }

    public function penilaian()
    {
        return $this->hasMany(SeleksiHasil::class, 'dinilai_oleh');
    }
}