<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_latihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekrutmen_id')->constrained('rekrutmen')->cascadeOnDelete();
            $table->string('nama');
            $table->date('tanggal');
            $table->time('jam_masuk');
            $table->time('jam_pulang');
            $table->string('lokasi')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_latihan_id')->constrained('jadwal_latihan')->cascadeOnDelete();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha'])->default('alpha');
            $table->timestamp('waktu_masuk')->nullable();
            $table->timestamp('waktu_pulang')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['jadwal_latihan_id', 'pendaftaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
        Schema::dropIfExists('jadwal_latihan');
    }
};