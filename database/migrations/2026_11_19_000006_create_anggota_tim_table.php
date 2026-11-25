<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Anggota SELAIN ketua. Ketua tersimpan di tabel tim (ketua_id, nim_ketua).
     */
    public function up(): void
    {
        Schema::create('anggota_tim', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tim_id');
            $table->unsignedBigInteger('event_id');
            $table->string('nama_lengkap', 100);
            $table->string('nim', 20);
            $table->timestamps();

            // tim_id harus milik event_id yang sama (composite FK).
            $table->foreign(['tim_id', 'event_id'])
                ->references(['id', 'event_id'])->on('tim')->cascadeOnDelete();
            $table->unique(['event_id', 'nim']); // 1 NIM hanya 1 tim per lomba
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_tim');
    }
};
