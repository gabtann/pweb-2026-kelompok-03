<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembuat_id')->constrained('users')->restrictOnDelete();
            $table->string('nama_event', 150);
            $table->string('tema', 150);
            $table->text('deskripsi');
            $table->string('gambar_pendukung', 255);
            $table->dateTime('registrasi_mulai');
            $table->dateTime('registrasi_selesai');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};
