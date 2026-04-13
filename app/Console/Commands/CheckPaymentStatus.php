<?php

namespace App\Console\Commands;

use App\Models\License;
use App\Models\Payment;
use App\Services\LicenseService;
use Illuminate\Console\Command;
use Midtrans\Config;
use Midtrans\Transaction;

class CheckPaymentStatus extends Command
{
    protected $signature   = 'payments:check-status {--hours=24}';
    protected $description = 'Cek pending payments ke Midtrans & generate lisensi + kirim WA/Email jika settlement';

    /**
     * FIX: Gunakan type-hint di handle() agar Laravel inject LicenseService
     * dengan WhatsAppService yang sudah ter-konstruksi via DI container.
     *
     * DULU (SALAH):
     *   $licenseService = new LicenseService();   ← constructor injection tidak jalan
     *
     * SEKARANG (BENAR):
     *   public function handle(LicenseService $licenseService)  ← Laravel inject otomatis
     */
    public function handle(LicenseService $licenseService): int
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        $hours   = (int) $this->option('hours');
        $pending = Payment::where('status', 'pending')
            ->where('created_at', '>=', now()->subHours($hours))
            ->get();

        if ($pending->isEmpty()) {
            $this->info("✅ Tidak ada pending payment dalam {$hours} jam terakhir.");
            return self::SUCCESS;
        }

        $this->info("📋 Ditemukan {$pending->count()} pending payment...");
        $bar = $this->output->createProgressBar($pending->count());
        $bar->start();

        $created = 0;
        $skipped = 0;
        $errors  = 0;

        foreach ($pending as $payment) {
            try {
                /** @var object $response */
                $response = Transaction::status($payment->order_id);

                $status = $response->transaction_status;

                $payment->update([
                    'status'       => $status,
                    'payment_type' => $response->payment_type ?? $payment->payment_type,
                    'raw_response' => json_encode($response),
                ]);

                if (in_array($status, ['settlement', 'capture'])) {
                    if ($this->licenseAlreadyExists($payment)) {
                        $skipped++;
                    } else {
                        // LicenseService sudah di-inject — WA & Email otomatis jalan
                        $licenseService->generateLicense($payment);
                        $created++;
                        $this->newLine();
                        $this->info("  ✅ {$payment->order_id} → lisensi + WA + email terkirim");
                    }
                }
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error("  ❌ {$payment->order_id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Selesai — Dibuat: {$created} | Skip (sudah ada): {$skipped} | Error: {$errors}");

        return self::SUCCESS;
    }

    private function licenseAlreadyExists(Payment $payment): bool
    {
        $q = License::where('package_type', $payment->package_type)
            ->where('created_at', '>=', $payment->created_at->startOfDay());

        if ($payment->buyer_email) {
            $q->where('buyer_email', $payment->buyer_email);
        } elseif ($payment->user_id) {
            $q->where('user_id', $payment->user_id);
        }

        return $q->exists();
    }
}