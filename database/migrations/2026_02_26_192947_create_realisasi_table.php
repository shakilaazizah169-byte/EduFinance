<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('realisasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('perencanaan_id')->constrained('perencanaans')->onDelete('cascade');
            $table->unsignedBigInteger('detail_perencanaan_id')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->date('tanggal_realisasi');
            $table->text('deskripsi');
            $table->enum('status_target', ['sesuai', 'tidak', 'sebagian']);
            $table->decimal('persentase', 5, 2)->default(0);
            $table->text('keterangan_target')->nullable();
            $table->text('catatan_tambahan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realisasi');
    }
};