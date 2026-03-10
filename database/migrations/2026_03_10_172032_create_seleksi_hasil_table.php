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
        Schema::create('seleksi_hasil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seleksi_tahap_id')->constrained('seleksi_tahap', 'id')->cascadeOnDelete();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran', 'id')->cascadeOnDelete();
            $table->enum('status', ['lolos','tidak_lolos','pending'])->default('pending');
            $table->decimal('nilai_pancasila', 5, 2)->nullable();
            $table->decimal('nilai_tiu', 5, 2)->nullable();
            $table->decimal('nilai_pbb', 5, 2)->nullable();
            $table->decimal('nilai_fisik', 5, 2)->nullable();
            $table->decimal('nilai_wawancara', 5, 2)->nullable();
            $table->decimal('nilai_total', 5, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('dinilai_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['seleksi_tahap_id', 'pendaftaran_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seleksi_hasil');
    }
};
