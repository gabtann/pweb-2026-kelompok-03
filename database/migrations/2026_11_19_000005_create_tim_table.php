<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tim', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('event_lomba', 'event_id')->cascadeOnDelete();
            $table->string('nama_tim', 100);
            $table->string('kode_tim', 10)->unique();
            $table->string('file_dokumen_tim', 255)->nullable();
            $table->string('file_bukti_bayar', 255)->nullable();
            $table->enum('status_verifikasi', ['belum', 'menunggu', 'disetujui', 'ditolak'])->default('belum');
            $table->text('catatan_verifikasi')->nullable();
            $table->foreignId('verifikator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('diverifikasi_at')->nullable();
            $table->timestamps();

            // Dibutuhkan agar anggota_tim bisa composite FK (tim_id, event_id).
            $table->unique(['id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tim');
    }
};
