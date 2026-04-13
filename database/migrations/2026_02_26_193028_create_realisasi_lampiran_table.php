<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('realisasi_lampiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('realisasi_id')->constrained('realisasi')->onDelete('cascade');
            $table->string('nama_file');
            $table->string('path_file');
            $table->string('tipe_file');
            $table->unsignedBigInteger('ukuran_file');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realisasi_lampiran');
    }
};