<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fix: user_id di tabel payments dibuat nullable
 * agar checkout bisa dilakukan oleh GUEST (tanpa login).
 *
 * Juga tambahkan kolom buyer_* yang dibutuhkan MidtransService
 * kalau belum ada dari migration sebelumnya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            // ── 1. Buat user_id nullable ────────────────────────────
            // Drop foreign key dulu sebelum ubah kolom
            // Nama FK biasanya 'payments_user_id_foreign' — sesuaikan jika berbeda
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Hapus FK lama yang NOT NULL, ganti dengan yang nullable
            // (Jika error "Key not found", bisa skip baris dropForeign ini)
            try {
                $table->dropForeign(['user_id']);
            } catch (\Throwable) {
                // FK sudah tidak ada atau nama berbeda — lanjutkan
            }

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null'); // Jika user dihapus, payment tetap ada

            // ── 2. Tambah kolom buyer info (jika belum ada) ─────────
            if (! Schema::hasColumn('payments', 'buyer_name')) {
                $table->string('buyer_name')->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('payments', 'buyer_email')) {
                $table->string('buyer_email')->nullable()->after('buyer_name');
            }
            if (! Schema::hasColumn('payments', 'buyer_phone')) {
                $table->string('buyer_phone', 20)->nullable()->after('buyer_email');
            }
            if (! Schema::hasColumn('payments', 'school_name')) {
                $table->string('school_name')->nullable()->after('buyer_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->dropColumn(array_filter(
                ['buyer_name', 'buyer_email', 'buyer_phone', 'school_name'],
                fn($col) => Schema::hasColumn('payments', $col)
            ));
        });
    }
};