<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            KategoriSeeder::class,
            KodeTransaksiSeeder::class,
        ]);

        // 🔥 SEEDER UNTUK DEMO (HANYA DI LOCAL)
        if (app()->environment('local')) {
            $this->call([
                DemoAdminSeeder::class,    // Demo admin dengan lisensi + data lengkap
            ]);
        }
    }
}