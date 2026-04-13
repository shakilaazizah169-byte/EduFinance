<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Multi-tenant: tambah user_id ke semua tabel data master.
 * Setiap sekolah (user admin) hanya bisa lihat data mereka sendiri.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Tabel kategori ─────────────────────────────────
        Schema::table('kategori', function (Blueprint $table) {
            if (! Schema::hasColumn('kategori', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('kategori_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id');
            }
        });

        // ── 2. Tabel kode_transaksi ───────────────────────────
        Schema::table('kode_transaksi', function (Blueprint $table) {
            if (! Schema::hasColumn('kode_transaksi', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('kode_transaksi_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id');
            }
        });

        // ── 3. Tabel mutasi_kas ───────────────────────────────
        Schema::table('mutasi_kas', function (Blueprint $table) {
            if (! Schema::hasColumn('mutasi_kas', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('mutasi_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id');
            }
        });

        // ── 4. (Opsional) Tabel perencanaan ──────────────────
        if (Schema::hasTable('perencanaan')) {
            Schema::table('perencanaan', function (Blueprint $table) {
                if (! Schema::hasColumn('perencanaan', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->index('user_id');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('kategori', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
        Schema::table('kode_transaksi', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
        Schema::table('mutasi_kas', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
        if (Schema::hasTable('perencanaan') && Schema::hasColumn('perencanaan', 'user_id')) {
            Schema::table('perencanaan', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};