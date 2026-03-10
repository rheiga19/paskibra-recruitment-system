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
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('rekrutmen_id')->constrained('rekrutmen', 'id')->cascadeOnDelete();
            $table->string('no_pendaftaran')->unique();
            // Snapshot data pribadi saat apply
            $table->string('nama_lengkap');
            $table->string('nik', 16);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('no_hp');
            $table->text('alamat_lengkap');
            $table->string('provinsi_nama')->nullable();
            $table->string('kabupaten_nama')->nullable();
            $table->string('kecamatan_nama')->nullable();
            $table->string('desa_nama')->nullable();
            $table->integer('tinggi_badan');
            $table->integer('berat_badan');
            // Snapshot data sekolah saat apply
            $table->string('nama_sekolah');
            $table->enum('jenjang', ['SMP','MTs','SMA','MA','SMK']);
            $table->string('kelas');
            $table->decimal('nilai_rata', 5, 2);
            $table->string('nama_ortu');
            $table->string('hp_ortu');
            $table->enum('hubungan_ortu', ['Ayah','Ibu','Wali']);
            $table->text('prestasi')->nullable();
            // Status
            $table->enum('status', [
                'menunggu',
                'diverifikasi',
                'lulus',
                'tidak_lulus'
            ])->default('menunggu');
            $table->text('catatan_admin')->nullable();
            $table->boolean('is_lulus_final')->default(false);
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->timestamp('tanggal_lulus_final')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'rekrutmen_id']); // 1 user = 1x apply per rekrutmen
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};
