<?php
// database/seeders/DemoAdminSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\License;
use App\Models\Payment;
use App\Models\Kategori;
use App\Models\KodeTransaksi;
use App\Models\Perencanaan;
use App\Models\PerencanaanDetail;
use App\Models\Realisasi;
use App\Models\MutasiKas;
use App\Models\SchoolSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 🔥 HANYA JALANKAN DI LOCAL/DEVELOPMENT
        if (app()->environment('production')) {
            $this->command->error('❌ DemoAdminSeeder tidak boleh dijalankan di production!');
            return;
        }

        $this->command->info('🚀 Menjalankan DemoAdminSeeder...');

        // ============================================================
        // 1. BUAT USER ADMIN DEMO
        // ============================================================
        $admin = User::firstOrCreate(
            ['email' => 'demo@sekolah.sch.id'],
            [
                'name' => 'Admin Demo Sekolah',
                'password' => Hash::make('password'),
                'phone' => '081234567890',
                'role' => 'admin',
                'school_name' => 'Sekolah Demo Negeri 1',
                'email_verified_at' => now(),
            ]
        );
        $this->command->info("✅ User demo created: {$admin->email} / password");

        // ============================================================
        // 2. BUAT LISENSI AKTIF (2 TAHUN)
        // ============================================================
        $license = License::create([
            'license_key' => 'DEMO-' . strtoupper(uniqid()) . '-' . strtoupper(substr(md5(rand()), 0, 4)),
            'user_id' => $admin->id,
            'buyer_name' => $admin->name,
            'buyer_email' => $admin->email,
            'buyer_phone' => $admin->phone,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addYears(2), // Lisensi 2 tahun untuk testing
            'package_type' => 'yearly',
            'price' => 0, // Gratis untuk demo
        ]);
        $this->command->info("✅ License created: {$license->license_key} (expires: {$license->end_date})");

        // ============================================================
        // 3. BUAT PAYMENT DEMO (opsional, untuk riwayat)
        // ============================================================
        Payment::create([
            'order_id' => 'DEMO-' . date('Ymd') . '-' . strtoupper(uniqid()),
            'user_id' => $admin->id,
            'buyer_name' => $admin->name,
            'buyer_email' => $admin->email,
            'buyer_phone' => $admin->phone,
            'school_name' => $admin->school_name,
            'package_type' => 'yearly',
            'amount' => 0,
            'status' => 'settlement',
            'payment_type' => 'demo',
        ]);
        $this->command->info("✅ Demo payment created");

        // ============================================================
        // 4. BUAT SCHOOL SETTING
        // ============================================================
        SchoolSetting::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'nama_sekolah' => 'Sekolah Demo Negeri 1',
                'alamat' => 'Jl. Pendidikan No. 123, Kota Demo',
                'telepon' => '(021) 1234567',
                'email' => 'info@sekolahdemo.sch.id',
                'website' => 'https://sekolahdemo.sch.id',
                'npsn' => '12345678',
                'nama_kepala_sekolah' => 'Dr. Ahmad Wijaya, M.Pd',
                'nip_kepala_sekolah' => '196501011990011001',
                'nama_bendahara' => 'Siti Aisyah, S.E',
                'nip_bendahara' => '197503151995032001',
                'kota' => 'Kota Demo',
                'logo_sekolah' => null,
            ]
        );
        $this->command->info("✅ School setting created");

        // ============================================================
        // 5. BUAT KATEGORI (jika belum ada global)
        // ============================================================
        $kategoriData = [
            ['nama_kategori' => 'Penerimaan Pendapatan', 'user_id' => null],
            ['nama_kategori' => 'Penerimaan Non Pendapatan', 'user_id' => null],
            ['nama_kategori' => 'Pengeluaran Biaya', 'user_id' => null],
            ['nama_kategori' => 'Pengeluaran Non Biaya', 'user_id' => null],
        ];

        foreach ($kategoriData as $kat) {
            Kategori::firstOrCreate(
                ['nama_kategori' => $kat['nama_kategori'], 'user_id' => $kat['user_id']],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
        $this->command->info("✅ Kategori seeded");

        // ============================================================
        // 6. BUAT KODE TRANSAKSI (jika belum ada global)
        // ============================================================
        $kategoriPendapatan = Kategori::where('nama_kategori', 'Penerimaan Pendapatan')->first();
        $kategoriNonPendapatan = Kategori::where('nama_kategori', 'Penerimaan Non Pendapatan')->first();
        $kategoriBiaya = Kategori::where('nama_kategori', 'Pengeluaran Biaya')->first();
        $kategoriNonBiaya = Kategori::where('nama_kategori', 'Pengeluaran Non Biaya')->first();

        $kodeData = [
            ['kode' => '101', 'keterangan' => 'Pendapatan Usaha', 'kategori_id' => $kategoriPendapatan->kategori_id],
            ['kode' => '102', 'keterangan' => 'Pendapatan Lain-lain', 'kategori_id' => $kategoriPendapatan->kategori_id],
            ['kode' => '201', 'keterangan' => 'Penerimaan Modal Usaha', 'kategori_id' => $kategoriNonPendapatan->kategori_id],
            ['kode' => '301', 'keterangan' => 'Biaya Gaji Karyawan', 'kategori_id' => $kategoriBiaya->kategori_id],
            ['kode' => '302', 'keterangan' => 'Biaya Operasional', 'kategori_id' => $kategoriBiaya->kategori_id],
            ['kode' => '401', 'keterangan' => 'Pembelian Inventaris', 'kategori_id' => $kategoriNonBiaya->kategori_id],
        ];

        foreach ($kodeData as $kode) {
            KodeTransaksi::firstOrCreate(
                ['kode' => $kode['kode'], 'user_id' => null],
                [
                    'keterangan' => $kode['keterangan'],
                    'kategori_id' => $kode['kategori_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        $this->command->info("✅ Kode transaksi seeded");

        // ============================================================
        // 7. BUAT PERENCANAAN DEMO
        // ============================================================
        $tahun = now()->year;
        
        for ($bulan = 1; $bulan <= 3; $bulan++) {
            $perencanaan = Perencanaan::create([
                'user_id' => $admin->id,
                'judul' => 'Perencanaan Keuangan Bulan ' . Carbon::create()->month($bulan)->translatedFormat('F') . ' ' . $tahun,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ]);

            $details = [
                ['perencanaan' => 'Penerimaan Dana BOS', 'target' => 'Rp 50.000.000', 'deskripsi' => 'Dana BOS reguler', 'pelaksanaan' => 'Transfer awal bulan'],
                ['perencanaan' => 'Pembayaran Gaji Guru', 'target' => 'Rp 35.000.000', 'deskripsi' => 'Gaji bulanan', 'pelaksanaan' => 'Tanggal 25 setiap bulan'],
                ['perencanaan' => 'Biaya Listrik & Internet', 'target' => 'Rp 3.000.000', 'deskripsi' => 'Tagihan utilitas', 'pelaksanaan' => 'Bayar sebelum tanggal 20'],
            ];

            foreach ($details as $detail) {
                PerencanaanDetail::create([
                    'perencanaan_id' => $perencanaan->perencanaan_id,
                    'perencanaan' => $detail['perencanaan'],
                    'target' => $detail['target'],
                    'deskripsi' => $detail['deskripsi'],
                    'pelaksanaan' => $detail['pelaksanaan'],
                ]);
            }
        }
        $this->command->info("✅ Perencanaan demo created (3 months)");

        // ============================================================
        // 8. BUAT REALISASI DEMO
        // ============================================================
        $perencanaans = Perencanaan::where('user_id', $admin->id)->get();
        
        foreach ($perencanaans as $pr) {
            $details = PerencanaanDetail::where('perencanaan_id', $pr->perencanaan_id)->get();
            
            foreach ($details as $idx => $detail) {
                $status = ['sesuai', 'sebagian', 'tidak'][$idx % 3];
                $persentase = match($status) {
                    'sesuai' => 100,
                    'sebagian' => rand(60, 85),
                    'tidak' => rand(0, 30),
                };
                
                Realisasi::create([
                    'perencanaan_id' => $pr->perencanaan_id,
                    'detail_perencanaan_id' => $detail->detail_id,
                    'user_id' => $admin->id,
                    'judul' => $detail->perencanaan,
                    'tanggal_realisasi' => Carbon::create($tahun, $pr->bulan, rand(5, 25))->format('Y-m-d'),
                    'deskripsi' => "Realisasi {$detail->perencanaan}",
                    'status_target' => $status,
                    'persentase' => $persentase,
                    'keterangan_target' => match($status) {
                        'sesuai' => 'Target tercapai sesuai rencana',
                        'sebagian' => "Terealisasi {$persentase}% dari target",
                        'tidak' => 'Target tidak tercapai karena keterbatasan anggaran',
                    },
                ]);
            }
        }
        $this->command->info("✅ Realisasi demo created");

        // ============================================================
        // 9. BUAT MUTASI KAS DEMO
        // ============================================================
        $kode101 = KodeTransaksi::where('kode', '101')->first();
        $kode102 = KodeTransaksi::where('kode', '102')->first();
        $kode301 = KodeTransaksi::where('kode', '301')->first();
        $kode302 = KodeTransaksi::where('kode', '302')->first();
        $kode401 = KodeTransaksi::where('kode', '401')->first();

        $mutasiData = [
            ['tanggal' => now()->subDays(30), 'kode_id' => $kode101->kode_transaksi_id, 'uraian' => 'Penerimaan BOS Triwulan', 'debit' => 50000000, 'kredit' => 0],
            ['tanggal' => now()->subDays(28), 'kode_id' => $kode102->kode_transaksi_id, 'uraian' => 'Iuran Komite Sekolah', 'debit' => 8500000, 'kredit' => 0],
            ['tanggal' => now()->subDays(25), 'kode_id' => $kode301->kode_transaksi_id, 'uraian' => 'Pembayaran Gaji Guru', 'debit' => 0, 'kredit' => 35000000],
            ['tanggal' => now()->subDays(20), 'kode_id' => $kode302->kode_transaksi_id, 'uraian' => 'Pembayaran Listrik & Internet', 'debit' => 0, 'kredit' => 2800000],
            ['tanggal' => now()->subDays(15), 'kode_id' => $kode302->kode_transaksi_id, 'uraian' => 'Pembelian ATK', 'debit' => 0, 'kredit' => 1500000],
            ['tanggal' => now()->subDays(10), 'kode_id' => $kode401->kode_transaksi_id, 'uraian' => 'Pembelian Kursi & Meja', 'debit' => 0, 'kredit' => 5000000],
            ['tanggal' => now()->subDays(5), 'kode_id' => $kode102->kode_transaksi_id, 'uraian' => 'Donasi Alumni', 'debit' => 7500000, 'kredit' => 0],
            ['tanggal' => now(), 'kode_id' => $kode101->kode_transaksi_id, 'uraian' => 'Penerimaan BOS', 'debit' => 50000000, 'kredit' => 0],
        ];

        $saldo = 0;
        foreach ($mutasiData as $m) {
            $saldo += $m['debit'] - $m['kredit'];
            MutasiKas::create([
                'user_id' => $admin->id,
                'tanggal' => $m['tanggal'],
                'kode_transaksi_id' => $m['kode_id'],
                'uraian' => $m['uraian'],
                'debit' => $m['debit'],
                'kredit' => $m['kredit'],
                'saldo' => $saldo,
            ]);
        }
        $this->command->info("✅ Mutasi kas demo created (" . count($mutasiData) . " records)");

        // ============================================================
        // SELESAI
        // ============================================================
        $this->command->info("╔═══════════════════════════════════════════════════════════════╗");
        $this->command->info("║                    🎉 DEMO ADMIN CREATED! 🎉                  ║");
        $this->command->info("╠═══════════════════════════════════════════════════════════════╣");
        $this->command->info("║  Email    : demo@sekolah.sch.id                                ║");
        $this->command->info("║  Password : password                                          ║");
        $this->command->info("║  Lisensi  : ACTIVE (expires: " . $license->end_date->format('d/m/Y') . ")   ║");
        $this->command->info("╚═══════════════════════════════════════════════════════════════╝");
    }
}