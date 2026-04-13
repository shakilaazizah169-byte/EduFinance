<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insertOrIgnore([
            'name'              => 'Super Admin',
            'email'             => 'superadmin@kassekolah.com',
            'password'          => Hash::make('password'),
            'role'              => 'super_admin',
            'phone'             => '081234567890',
            'school_name'       => null,
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $this->command->info('✅ Super Admin seeded');
    }
}