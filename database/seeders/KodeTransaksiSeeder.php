<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KodeTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('kode_transaksi')->whereNull('user_id')->count() > 0) {
            $this->command->warn('⏭  Kode transaksi global sudah ada, skip');
            return;
        }

        $katPendapatan    = DB::table('kategori')->whereNull('user_id')->where('nama_kategori', 'Penerimaan Pendapatan')->value('kategori_id');
        $katNonPendapatan = DB::table('kategori')->whereNull('user_id')->where('nama_kategori', 'Penerimaan non Pendapatan')->value('kategori_id');
        $katBiaya         = DB::table('kategori')->whereNull('user_id')->where('nama_kategori', 'Pengeluaran Biaya')->value('kategori_id');
        $katNonBiaya      = DB::table('kategori')->whereNull('user_id')->where('nama_kategori', 'Pengeluaran Non Biaya')->value('kategori_id');

        $kodes = [
            ['kode' => '101', 'keterangan' => 'Pendapatan Usaha',                                        'kategori_id' => $katPendapatan],
            ['kode' => '102', 'keterangan' => 'Pendapatan Lain-lain',                                    'kategori_id' => $katPendapatan],
            ['kode' => '103', 'keterangan' => 'Bagi Hasil Bank',                                         'kategori_id' => $katPendapatan],
            ['kode' => '201', 'keterangan' => 'Penerimaan Modal Usaha',                                  'kategori_id' => $katNonPendapatan],
            ['kode' => '202', 'keterangan' => 'Penerimaan Pinjaman dari Pihak Ke-3',                     'kategori_id' => $katNonPendapatan],
            ['kode' => '203', 'keterangan' => 'Penerimaan Cicilan Pinjaman Karyawan / Anggota Koperasi', 'kategori_id' => $katNonPendapatan],
            ['kode' => '301', 'keterangan' => 'Biaya Promosi / Marketing',                               'kategori_id' => $katBiaya],
            ['kode' => '302', 'keterangan' => 'Biaya Gaji, Insentif, Komisi Karyawan',                   'kategori_id' => $katBiaya],
            ['kode' => '303', 'keterangan' => 'Biaya Jasa Tenaga Ahli / Konsultan',                      'kategori_id' => $katBiaya],
            ['kode' => '304', 'keterangan' => 'Biaya Packing, Angkut dan Kurir',                         'kategori_id' => $katBiaya],
            ['kode' => '305', 'keterangan' => 'Biaya Transportasi dan Kendaraan',                        'kategori_id' => $katBiaya],
            ['kode' => '306', 'keterangan' => 'Biaya Konsumsi dan Jamuan',                               'kategori_id' => $katBiaya],
            ['kode' => '307', 'keterangan' => 'Biaya Listrik / Telpon / Internet',                       'kategori_id' => $katBiaya],
            ['kode' => '308', 'keterangan' => 'Biaya Sewa Alat / Penunjang',                             'kategori_id' => $katBiaya],
            ['kode' => '309', 'keterangan' => 'Biaya Perjalanan Dinas',                                  'kategori_id' => $katBiaya],
            ['kode' => '310', 'keterangan' => 'Biaya Administrasi, Umum, dll',                           'kategori_id' => $katBiaya],
            ['kode' => '401', 'keterangan' => 'Pembelian Alat Kerja Inventaris',                         'kategori_id' => $katNonBiaya],
            ['kode' => '402', 'keterangan' => 'Pengeluaran Pinjaman Karyawan / Anggota Koperasi',        'kategori_id' => $katNonBiaya],
        ];

        foreach ($kodes as $k) {
            DB::table('kode_transaksi')->insert([
                'user_id'     => null,
                'kode'        => $k['kode'],
                'keterangan'  => $k['keterangan'],
                'kategori_id' => $k['kategori_id'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $this->command->info('✅ Kode transaksi global seeded (' . count($kodes) . ' records)');
    }
}