<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom buyer_* ke payments & licenses,
 * dan kolom user_id ke perencanaan untuk multi-tenant.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Tabel payments — tambah kolom buyer ────────────────
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'buyer_name')) {
                $table->string('buyer_name')->nullable()->after('status');
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
            // user_id boleh null untuk guest
            if (Schema::hasColumn('payments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }
        });

        // ── 2. Tabel licenses — tambah kolom buyer ───────────────
        Schema::table('licenses', function (Blueprint $table) {
            if (! Schema::hasColumn('licenses', 'buyer_name')) {
                $table->string('buyer_name')->nullable()->after('price');
            }
            if (! Schema::hasColumn('licenses', 'buyer_email')) {
                $table->string('buyer_email')->nullable()->after('buyer_name');
            }
            if (! Schema::hasColumn('licenses', 'buyer_phone')) {
                $table->string('buyer_phone', 20)->nullable()->after('buyer_email');
            }
            if (Schema::hasColumn('licenses', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }
        });

        // ── 3. Tabel perencanaan — tambah user_id (multi-tenant) ─
        Schema::table('perencanaans', function (Blueprint $table) {
            if (! Schema::hasColumn('perencanaans', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('perencanaan_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id');
            }
        });

        // ── 4. Tabel mutasi_kas — pastikan user_id ada ───────────
        Schema::table('mutasi_kas', function (Blueprint $table) {
            if (! Schema::hasColumn('mutasi_kas', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('mutasi_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['buyer_name', 'buyer_email', 'buyer_phone', 'school_name']);
        });
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn(['buyer_name', 'buyer_email', 'buyer_phone']);
        });
        Schema::table('perencanaan', function (Blueprint $table) {
            if (Schema::hasColumn('perencanaan', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};