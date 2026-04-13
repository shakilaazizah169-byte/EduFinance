<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\SuperAdminMutasi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncSuperAdminMutasi extends Command
{
    protected $signature = 'superadmin:sync-mutasi
                            {--dry-run : Preview data tanpa menyimpan ke database}
                            {--force  : Jalankan tanpa konfirmasi}';

    protected $description = 'Sinkronisasi data payment lama (settlement) ke tabel super_admin_mutasi';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->newLine();
        $this->line('╔══════════════════════════════════════════════════════╗');
        $this->line('║      Sync Payments → Super Admin Mutasi              ║');
        $this->line('╚══════════════════════════════════════════════════════╝');
        $this->newLine();

        if ($isDryRun) {
            $this->warn('  [DRY RUN] — tidak ada data yang akan disimpan.');
            $this->newLine();
        }

        // ── Ambil semua payment settlement yang BELUM ada di mutasi ──────
        $payments = Payment::whereIn('status', ['settlement', 'capture'])
            ->whereNotIn('id', function ($q) {
                $q->select('payment_id')->from('super_admin_mutasi');
            })
            ->orderBy('created_at')   // urut dari yang paling lama agar saldo akumulatif benar
            ->get();

        if ($payments->isEmpty()) {
            $this->info('  ✅ Semua payment settlement sudah tersinkronisasi. Tidak ada yang perlu diproses.');
            return self::SUCCESS;
        }

        // ── Preview tabel ─────────────────────────────────────────────────
        $this->info("  Ditemukan {$payments->count()} payment yang belum masuk ke mutasi:");
        $this->newLine();

        $headers = ['No', 'Order ID', 'Tanggal', 'Nama Instansi', 'Pembeli', 'Paket', 'Jumlah (Rp)'];
        $rows = $payments->map(fn($p, $i) => [
            $i + 1,
            $p->order_id,
            $p->created_at->format('d/m/Y'),
            $p->school_name ?? '-',
            $p->buyer_name  ?? '-',
            ucfirst($p->package_type),
            number_format($p->amount, 0, ',', '.'),
        ])->toArray();

        $this->table($headers, $rows);

        $totalAmount = $payments->sum('amount');
        $this->newLine();
        $this->line("  Total pemasukan yang akan disinkronisasi: <fg=green>Rp " . number_format($totalAmount, 0, ',', '.') . "</>");
        $this->newLine();

        // ── Dry run: berhenti di sini ─────────────────────────────────────
        if ($isDryRun) {
            $this->warn('  [DRY RUN] Selesai preview. Jalankan tanpa --dry-run untuk menyimpan data.');
            return self::SUCCESS;
        }

        // ── Konfirmasi ────────────────────────────────────────────────────
        if (! $this->option('force')) {
            if (! $this->confirm("  Lanjutkan sinkronisasi {$payments->count()} record ke tabel super_admin_mutasi?")) {
                $this->warn('  Dibatalkan oleh user.');
                return self::SUCCESS;
            }
        }

        // ── Eksekusi ──────────────────────────────────────────────────────
        $this->newLine();
        $this->line('  Memproses...');
        $this->newLine();

        $bar     = $this->output->createProgressBar($payments->count());
        $success = 0;
        $skipped = 0;
        $errors  = [];

        DB::transaction(function () use ($payments, $bar, &$success, &$skipped, &$errors) {
            foreach ($payments as $payment) {
                try {
                    $result = SuperAdminMutasi::generateDariPayment($payment);

                    if ($result === null) {
                        // null berarti sudah ada (generateDariPayment cek duplikat)
                        $skipped++;
                    } else {
                        $success++;
                    }
                } catch (\Throwable $e) {
                    $errors[] = "[{$payment->order_id}] " . $e->getMessage();
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        // ── Laporan hasil ─────────────────────────────────────────────────
        $this->line('  ─────────────────────────────────────────');
        $this->info("  ✅ Berhasil disinkronisasi : {$success} record");

        if ($skipped > 0) {
            $this->warn("  ⏭  Dilewati (duplikat)      : {$skipped} record");
        }

        if (! empty($errors)) {
            $this->error("  ❌ Gagal                    : " . count($errors) . " record");
            foreach ($errors as $err) {
                $this->error("     → {$err}");
            }
        }

        $this->line('  ─────────────────────────────────────────');

        // Hitung saldo akhir setelah sync
        $saldoAkhir = SuperAdminMutasi::orderByDesc('id')->value('saldo') ?? 0;
        $this->newLine();
        $this->line("  Saldo Super Admin terkini: <fg=green>Rp " . number_format($saldoAkhir, 0, ',', '.') . "</>");
        $this->newLine();

        return empty($errors) ? self::SUCCESS : self::FAILURE;
    }
}