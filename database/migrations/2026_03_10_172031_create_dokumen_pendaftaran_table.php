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
        Schema::create('dokumen_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran', 'id')->cascadeOnDelete();
            $table->enum('jenis', [
                'foto_4x6',
                'ktp_pelajar',
                'akta_kelahiran',
                'rapor',
                'surat_sehat',
                'surat_izin_ortu'
            ]);
            $table->string('path');
            $table->string('nama_file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_pendaftaran');
    }
};
