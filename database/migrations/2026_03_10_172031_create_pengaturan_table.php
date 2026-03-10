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
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->boolean('pendaftaran_aktif')->default(false);
            $table->boolean('pengumuman_aktif')->default(false);
            $table->timestamp('pengumuman_diaktifkan_at')->nullable();
            $table->text('pesan_pengumuman')->nullable();
            $table->string('nama_kecamatan')->nullable();
            $table->string('alamat_sekretariat')->nullable();
            $table->string('no_hp_panitia')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};
