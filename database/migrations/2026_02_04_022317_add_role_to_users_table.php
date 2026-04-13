<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom role jika belum ada
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin'])->default('admin')->after('password');
            }
            
            // Jika ingin ubah nama primary key dari 'id' ke 'user_id'
            // TAPI HATI-HATI: ini akan mempengaruhi foreign key di tabel lain
            // if (Schema::hasColumn('users', 'id') && !Schema::hasColumn('users', 'user_id')) {
            //     $table->renameColumn('id', 'user_id');
            // }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom role
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            
            // Kembalikan nama kolom jika diubah
            // if (Schema::hasColumn('users', 'user_id') && !Schema::hasColumn('users', 'id')) {
            //     $table->renameColumn('user_id', 'id');
            // }
        });
    }
};