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
            $table->enum('jenis_lomba', ['uiux', 'ict', 'software_development']);
            $table->string('nama_event', 150);
            $table->string('tema', 150);
            $table->text('deskripsi');
            $table->string('gambar_pendukung', 255);
            $table->string('link_guidebook', 255);
            $table->text('syarat_ketentuan');
            $table->dateTime('registrasi_mulai');
            $table->dateTime('registrasi_selesai');
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->decimal('biaya_pendaftaran', 10, 2)->nullable(); // null = gratis
            $table->unsignedTinyInteger('min_anggota')->default(2);
            $table->unsignedTinyInteger('max_anggota')->default(3); // termasuk ketua
            $table->timestamps();
            $table->softDeletes();

            $table->index('jenis_lomba');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};
