<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahap_lomba', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('event_lomba', 'event_id')->cascadeOnDelete();
            $table->unsignedTinyInteger('nomor_tahap');
            $table->string('nama_tahap', 50);
            $table->string('jenis_berkas', 50);
            $table->dateTime('batas_mulai');
            $table->dateTime('batas_akhir');
            $table->unsignedInteger('kuota_lolos')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'nomor_tahap']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahap_lomba');
    }
};
