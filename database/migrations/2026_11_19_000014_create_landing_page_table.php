<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_page', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['about', 'faq', 'rules', 'timeline']);
            $table->string('judul', 150);
            $table->text('isi');
            $table->unsignedInteger('urutan')->nullable();
            $table->foreignId('pengubah_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_page');
    }
};
