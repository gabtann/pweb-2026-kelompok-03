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
            $table->foreignId('event_id')->constrained('event')->cascadeOnDelete();
            $table->unsignedTinyInteger('nomor_tahap');
            $table->string('nama_tahap', 50);
            $table->enum('jenis_berkas', ['pdf', 'pptx']);
            $table->dateTime('batas_mulai');
            $table->dateTime('batas_akhir');
            $table->unsignedInteger('kuota_lolos')->default(10);
            $table->timestamps();

            $table->unique(['event_id', 'nomor_tahap']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahap_lomba');
    }
};
