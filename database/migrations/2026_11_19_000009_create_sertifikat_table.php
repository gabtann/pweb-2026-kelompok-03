<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('event')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tim_id')->nullable()->constrained('tim')->nullOnDelete();
            $table->string('jenis_sertifikat', 50);
            $table->string('link_gdrive', 255);
            $table->foreignId('pengunggah_id')->constrained('users')->restrictOnDelete();
            $table->dateTime('diunggah_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikat');
    }
};
