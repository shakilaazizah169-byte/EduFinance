<?php
// database/migrations/xxxx_fix_role_column_final.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Cek apakah kolom 'role' sudah ada
        if (Schema::hasColumn('users', 'role')) {
            // Ubah kolom role dengan query SQL mentah
            DB::statement('ALTER TABLE users MODIFY role VARCHAR(50) DEFAULT "user" NOT NULL');
            
            // Update role super_admin jika belum ada
            DB::table('users')->updateOrInsert(
                ['email' => 'superadmin@example.com'],
                [
                    'name' => 'Super Admin',
                    'password' => bcrypt('password123'),
                    'role' => 'super_admin',
                    'phone' => '08123456789',
                    'school_name' => 'Admin System',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down()
    {
        // Kembalikan ke ukuran semula (sesuaikan dengan kebutuhan)
        DB::statement('ALTER TABLE users MODIFY role VARCHAR(191) DEFAULT "user" NOT NULL');
    }
};