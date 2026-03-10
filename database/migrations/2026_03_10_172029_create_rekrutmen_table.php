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
        Schema::create('rekrutmen', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_buka');
            $table->date('tanggal_tutup');
            $table->boolean('is_aktif')->default(false);
            $table->integer('kuota_putra')->nullable();
            $table->integer('kuota_putri')->nullable();
            $table->text('syarat')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekrutmen');
    }
};
