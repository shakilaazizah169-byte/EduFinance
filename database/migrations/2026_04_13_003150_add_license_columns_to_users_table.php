<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('lisensi_status', ['active', 'expired', 'never'])->default('never')->after('remember_token');
            $table->timestamp('lisensi_expired_at')->nullable()->after('lisensi_status');
            $table->timestamp('last_license_purchased_at')->nullable()->after('lisensi_expired_at');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['lisensi_status', 'lisensi_expired_at', 'last_license_purchased_at']);
        });
    }
};  