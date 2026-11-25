<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_tahap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahap_lomba_id')->constrained('tahap_lomba')->cascadeOnDelete();
            $table->foreignId('tim_id')->constrained('tim')->cascadeOnDelete();
            $table->decimal('skor', 5, 2)->nullable();
            $table->enum('status_kelulusan', ['lolos', 'tidak_lolos']);
            $table->unsignedInteger('peringkat')->nullable();
            $table->timestamps();

            $table->unique(['tahap_lomba_id', 'tim_id']);
            $table->unique(['tahap_lomba_id', 'peringkat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_tahap');
    }
};
