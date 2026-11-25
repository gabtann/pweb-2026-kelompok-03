<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumpulan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tim_id')->constrained('tim')->cascadeOnDelete();
            $table->foreignId('tahap_lomba_id')->constrained('tahap_lomba')->cascadeOnDelete();
            // Aturan "pengumpul harus ketua" dicek di aplikasi.
            $table->foreignId('pengumpul_id')->constrained('users')->restrictOnDelete();
            $table->string('file_submission', 255);
            $table->dateTime('submitted_at');
            $table->enum('status_validasi', ['menunggu', 'valid', 'tidak_valid'])->default('menunggu');
            $table->text('catatan_penilaian')->nullable();
            $table->foreignId('penilai_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('dinilai_at')->nullable();
            $table->timestamps();

            $table->unique(['tim_id', 'tahap_lomba_id']); // kumpul 1x per tahap
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumpulan');
    }
};
