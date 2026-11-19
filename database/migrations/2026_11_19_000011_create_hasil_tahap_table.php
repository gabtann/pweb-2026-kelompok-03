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
            $table->boolean('lolos')->default(false);
            $table->unsignedInteger('peringkat')->nullable();
            $table->string('gelar_juara', 30)->nullable();
            $table->timestamps();

            $table->unique(['tahap_lomba_id', 'tim_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_tahap');
    }
};
