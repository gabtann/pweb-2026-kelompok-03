<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_lomba', function (Blueprint $table) {
            $table->unsignedBigInteger('event_id')->primary();
            $table->foreign('event_id')->references('id')->on('event')->cascadeOnDelete();
            $table->string('link_guidebook', 255);
            $table->text('syarat_ketentuan');
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->decimal('biaya_pendaftaran', 10, 2)->nullable(); // null = gratis
            $table->unsignedTinyInteger('min_anggota')->default(2);
            $table->unsignedTinyInteger('max_anggota')->default(3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_lomba');
    }
};
