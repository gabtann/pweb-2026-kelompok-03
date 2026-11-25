<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahap_lomba_id')->unique()->constrained('tahap_lomba')->cascadeOnDelete();
            $table->foreignId('pembuat_id')->constrained('users')->restrictOnDelete();
            $table->string('link_penilaian', 255)->nullable();
            $table->string('link_sertifikat', 255)->nullable();
            $table->dateTime('dipublikasikan_at')->nullable(); // null = belum diumumkan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};
