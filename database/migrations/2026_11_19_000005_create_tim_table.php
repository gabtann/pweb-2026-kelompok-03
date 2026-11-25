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
            $table->foreignId('event_id')->constrained('event')->cascadeOnDelete();
            $table->foreignId('ketua_id')->constrained('users')->restrictOnDelete();
            $table->string('nama_tim', 100);
            $table->string('kode_tim', 10)->unique();
            $table->string('asal_perguruan_tinggi', 150);
            $table->string('nim_ketua', 20);
            $table->string('file_ktm', 255);
            $table->string('file_pakta', 255);
            $table->string('file_bukti_bayar', 255)->nullable();
            $table->enum('status_verifikasi', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_verifikasi')->nullable();
            $table->foreignId('verifikator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('diverifikasi_at')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'ketua_id']);
            $table->unique(['event_id', 'nim_ketua']);
            // Dibutuhkan agar anggota_tim bisa composite FK (tim_id, event_id).
            $table->unique(['id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tim');
    }
};
