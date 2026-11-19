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
            $table->foreignId('event_id')->constrained('event')->cascadeOnDelete();
            $table->foreignId('tahap_lomba_id')->nullable()->constrained('tahap_lomba')->nullOnDelete();
            $table->foreignId('pembuat_id')->constrained('users')->restrictOnDelete();
            $table->enum('jenis', ['tahap', 'juara', 'umum']);
            $table->string('judul', 150);
            $table->text('isi');
            $table->string('file_penilaian', 255)->nullable();
            $table->dateTime('dipublikasikan_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};
