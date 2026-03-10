<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeleksiHasil extends Model
{
    protected $table = 'seleksi_hasil';

    protected $fillable = [
        'seleksi_tahap_id',
        'pendaftaran_id',
        'status',
        'nilai_pancasila',
        'nilai_tiu',
        'nilai_pbb',
        'nilai_fisik',
        'nilai_wawancara',
        'nilai_total',
        'catatan',
        'dinilai_oleh',
    ];

    protected $casts = [
        'nilai_pancasila' => 'float',
        'nilai_tiu'       => 'float',
        'nilai_pbb'       => 'float',
        'nilai_fisik'     => 'float',
        'nilai_wawancara' => 'float',
        'nilai_total'     => 'float',
    ];

    // ─── Relasi ───────────────────────────────────────────────────
    public function tahap()
    {
        return $this->belongsTo(SeleksiTahap::class, 'seleksi_tahap_id');
    }

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function penilai()
    {
        return $this->belongsTo(User::class, 'dinilai_oleh');
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Hitung rata-rata dari komponen yang diisi, simpan ke nilai_total.
     */
    public function hitungDanSimpanNilaiTotal(): float
    {
        $komponen = array_filter([
            $this->nilai_pancasila,
            $this->nilai_tiu,
            $this->nilai_pbb,
            $this->nilai_fisik,
            $this->nilai_wawancara,
        ], fn($v) => $v !== null);

        $total = empty($komponen)
            ? 0
            : round(array_sum($komponen) / count($komponen), 2);

        $this->update(['nilai_total' => $total]);

        return $total;
    }

    /**
     * Tentukan status lolos/tidak berdasarkan passing grade tahap.
     */
    public function tentukanStatus(): void
    {
        $passingGrade = $this->tahap->passing_grade ?? 70;

        $this->update([
            'status' => $this->nilai_total >= $passingGrade ? 'lolos' : 'tidak_lolos',
        ]);
    }
}