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
        Schema::create('seleksi_tahap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekrutmen_id')->constrained('rekrutmen', 'id')->cascadeOnDelete();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(1);
            $table->json('komponen_nilai')->nullable();
            $table->decimal('passing_grade', 5, 2)->default(70);
            $table->boolean('is_aktif')->default(false);
            $table->boolean('is_diumumkan')->default(false);
            $table->boolean('is_tahap_final')->default(false);
            $table->timestamp('tanggal_pengumuman')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seleksi_tahap');
    }
};
