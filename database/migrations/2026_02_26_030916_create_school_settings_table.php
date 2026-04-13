<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel school_settings — TERPISAH dari users.
 *
 * Kenapa tabel terpisah, bukan kolom di users?
 * → Settings sekolah bisa banyak (logo, ttd, alamat, dll)
 * → Tidak mengotori tabel users
 * → Bisa di-extend tanpa alter users
 * → Lebih mudah di-join untuk PDF/laporan
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');

            // Identitas sekolah
            $table->string('nama_sekolah')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('npsn', 20)->nullable();       // Nomor Pokok Instansi Nasional

            // Kepala Instansi
            $table->string('nama_kepala_sekolah')->nullable();
            $table->string('nip_kepala_sekolah', 30)->nullable();

            // Bendahara / Tata Usaha
            $table->string('nama_bendahara')->nullable();
            $table->string('nip_bendahara', 30)->nullable();

            // Logo & Tanda Tangan (path storage)
            $table->string('logo_sekolah')->nullable();   // storage/school/{user_id}/logo.png
            $table->string('logo_yayasan')->nullable();   // opsional
            $table->string('ttd_kepala')->nullable();     // tanda tangan kepala sekolah
            $table->string('ttd_bendahara')->nullable();  // tanda tangan bendahara

            // Kota untuk surat (ex: "Depok, ")
            $table->string('kota')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};