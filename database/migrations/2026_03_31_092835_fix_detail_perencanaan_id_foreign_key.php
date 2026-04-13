<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Perbaiki kolom detail_perencanaan_id di tabel realisasi:
     * - Ganti tipe dari unsignedBigInteger → unsignedInteger (harus cocok dengan detail_id di perencanaan_details)
     * - Tambahkan foreign key constraint yang benar
     */
    public function up(): void
    {
        Schema::table('realisasi', function (Blueprint $table) {
            // Ubah tipe kolom agar cocok dengan detail_id (increments = unsignedInteger)
            $table->unsignedInteger('detail_perencanaan_id')->nullable()->change();

            // Tambahkan foreign key
            $table->foreign('detail_perencanaan_id')
                ->references('detail_id')
                ->on('perencanaan_details')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('realisasi', function (Blueprint $table) {
            $table->dropForeign(['detail_perencanaan_id']);
            $table->unsignedBigInteger('detail_perencanaan_id')->nullable()->change();
        });
    }
};