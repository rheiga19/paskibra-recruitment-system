<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    protected $table = 'galeri';

    protected $fillable = [
        'judul',
        'path',
        'keterangan',
    ];

    public function getFotoUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}