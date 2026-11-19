<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_tim', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tim_id');
            $table->foreignId('event_id')->constrained('event_lomba', 'event_id')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('peran', ['ketua', 'anggota'])->default('anggota');
            $table->dateTime('joined_at');

            // Virtual (bukan stored): MySQL menolak stored generated column
            // jika kolom dasarnya (tim_id) punya FK dengan CASCADE.
            $table->unsignedBigInteger('ketua_tim')->nullable()
                ->virtualAs("CASE WHEN peran = 'ketua' THEN tim_id ELSE NULL END");

            $table->timestamps();

            // tim_id harus milik event_id yang sama (composite FK).
            $table->foreign(['tim_id', 'event_id'])
                ->references(['id', 'event_id'])->on('tim')->cascadeOnDelete();

            $table->unique(['event_id', 'user_id']); // 1 user hanya 1 tim per lomba
            $table->unique('ketua_tim');             // 1 ketua per tim
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_tim');
    }
};
