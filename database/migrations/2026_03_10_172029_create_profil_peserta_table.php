<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profil_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Data Pribadi
            $table->string('nik', 16)->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('no_hp')->nullable();
            $table->text('alamat_lengkap')->nullable();
            // Wilayah (dari API)
            $table->string('provinsi_kode')->nullable();
            $table->string('provinsi_nama')->nullable();
            $table->string('kabupaten_kode')->nullable();
            $table->string('kabupaten_nama')->nullable();
            $table->string('kecamatan_kode')->nullable();
            $table->string('kecamatan_nama')->nullable();
            $table->string('desa_kode')->nullable();
            $table->string('desa_nama')->nullable();
            // Fisik
            $table->integer('tinggi_badan')->nullable();
            $table->integer('berat_badan')->nullable();
            // Data Sekolah
            $table->string('nama_sekolah')->nullable();
            $table->enum('jenjang', ['SMP','MTs','SMA','MA','SMK'])->nullable();
            $table->string('kelas')->nullable();
            $table->decimal('nilai_rata', 5, 2)->nullable();
            // Data Ortu
            $table->string('nama_ortu')->nullable();
            $table->string('hp_ortu')->nullable();
            $table->enum('hubungan_ortu', ['Ayah','Ibu','Wali'])->nullable();
            $table->text('prestasi')->nullable();
            // Status kelengkapan
            $table->boolean('is_profil_lengkap')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_peserta');
    }
};
