<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fix: licenses.user_id dibuat nullable (lisensi bisa untuk guest yang belum register).
 * Tambahkan kolom buyer_* dan session_id yang dibutuhkan LicenseService & CheckLicense middleware.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            // ── 1. user_id nullable ────────────────────────────────
            $table->unsignedBigInteger('user_id')->nullable()->change();

            try {
                $table->dropForeign(['user_id']);
            } catch (\Throwable) {}

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('set null');

            // ── 2. Kolom buyer info (jika belum ada) ───────────────
            if (! Schema::hasColumn('licenses', 'buyer_name')) {
                $table->string('buyer_name')->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('licenses', 'buyer_email')) {
                $table->string('buyer_email')->nullable()->after('buyer_name');
            }
            if (! Schema::hasColumn('licenses', 'buyer_phone')) {
                $table->string('buyer_phone', 20)->nullable()->after('buyer_email');
            }

            // ── 3. session_id untuk single-device lock ────────────
            if (! Schema::hasColumn('licenses', 'session_id')) {
                $table->string('session_id')->nullable()->after('buyer_phone');
            }
            if (! Schema::hasColumn('licenses', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('session_id');
            }
            if (! Schema::hasColumn('licenses', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            foreach (['buyer_name', 'buyer_email', 'buyer_phone', 'session_id', 'last_login_at', 'last_login_ip'] as $col) {
                if (Schema::hasColumn('licenses', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};