<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom ke tabel users (jika belum ada)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'school_name')) {
                $table->string('school_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('school_name');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('admin')->after('phone');
            }
        });

        // Update tabel licenses: user_id nullable (karena lisensi dibuat SEBELUM user register)
        Schema::table('licenses', function (Blueprint $table) {
            // user_id boleh null karena lisensi dibuat dulu, baru user register
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Tambah kolom guest info (untuk lisensi yang dibeli tanpa akun)
            if (!Schema::hasColumn('licenses', 'buyer_email')) {
                $table->string('buyer_email')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('licenses', 'buyer_phone')) {
                $table->string('buyer_phone', 20)->nullable()->after('buyer_email');
            }
            if (!Schema::hasColumn('licenses', 'buyer_name')) {
                $table->string('buyer_name')->nullable()->after('buyer_phone');
            }
        });

        // Pastikan tabel payments punya kolom yang dibutuhkan
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'buyer_email')) {
                $table->string('buyer_email')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('payments', 'buyer_phone')) {
                $table->string('buyer_phone', 20)->nullable()->after('buyer_email');
            }
            if (!Schema::hasColumn('payments', 'buyer_name')) {
                $table->string('buyer_name')->nullable()->after('buyer_phone');
            }
            if (!Schema::hasColumn('payments', 'school_name')) {
                $table->string('school_name')->nullable()->after('buyer_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['school_name', 'phone', 'role']);
        });

        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn(['buyer_email', 'buyer_phone', 'buyer_name']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['buyer_email', 'buyer_phone', 'buyer_name', 'school_name']);
        });
    }
};