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
            $table->string('file_submission', 255);
            $table->dateTime('submitted_at');
            $table->decimal('skor', 5, 2)->nullable();
            $table->text('catatan_penilaian')->nullable();
            $table->foreignId('penilai_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('dinilai_at')->nullable();
            $table->unsignedInteger('peringkat')->nullable();
            $table->enum('status_kelulusan', ['lolos', 'tidak_lolos'])->nullable(); // null = belum diumumkan
            $table->timestamps();

            $table->unique(['tim_id', 'tahap_lomba_id']); // kumpul 1x per tahap
            $table->unique(['tahap_lomba_id', 'peringkat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumpulan');
    }
};
