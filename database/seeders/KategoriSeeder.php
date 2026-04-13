<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('kategori')->whereNull('user_id')->count() > 0) {
            $this->command->warn('⏭  Kategori global sudah ada, skip');
            return;
        }

        $data = [
            'Penerimaan Pendapatan',
            'Penerimaan non Pendapatan',
            'Pengeluaran Biaya',
            'Pengeluaran Non Biaya',
        ];

        foreach ($data as $nama) {
            DB::table('kategori')->insert([
                'user_id'       => null,
                'nama_kategori' => $nama,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        $this->command->info('✅ Kategori global seeded (' . count($data) . ' records)');
    }
}