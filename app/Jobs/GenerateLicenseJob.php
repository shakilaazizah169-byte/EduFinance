<?php

namespace App\Jobs;

use App\Models\License;
use App\Models\Payment;
use App\Models\User;
use App\Services\LicenseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateLicenseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;
    public $backoff = [10, 30, 60]; // exponential backoff

    protected Payment $payment;
    protected ?User $user;

    public function __construct(Payment $payment, ?User $user = null)
    {
        $this->payment = $payment;
        $this->user = $user;
    }

    public function handle(LicenseService $licenseService): void
    {
        try {
            Log::info('🔄 GenerateLicenseJob dimulai', [
                'order_id' => $this->payment->order_id,
                'package' => $this->payment->package_type,
                'user_id' => $this->user?->id ?? 'NULL',
            ]);

            // Generate license
            $license = $licenseService->generateLicense($this->payment, $this->user);

            Log::info('✅ GenerateLicenseJob selesai', [
                'license_key' => $license->license_key,
                'order_id' => $this->payment->order_id,
                'user_id' => $license->user_id,
            ]);

        } catch (\Throwable $e) {
            Log::error('❌ GenerateLicenseJob gagal: ' . $e->getMessage(), [
                'order_id' => $this->payment->order_id,
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Lempar exception agar Laravel mengulangi job sesuai $tries
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('❌ GenerateLicenseJob FAILED setelah max retries', [
            'order_id' => $this->payment->order_id,
            'error' => $exception->getMessage(),
        ]);

        // Optional: kirim notifikasi ke admin atau catat di database
        // NotifyAdminFailedLicense::dispatch($this->payment, $exception);
    }
}
