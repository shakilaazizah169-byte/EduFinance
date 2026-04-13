<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\SuperAdminMutasi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateSuperAdminMutasiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 30;

    protected Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function handle(): void
    {
        try {
            $mutasi = SuperAdminMutasi::generateDariPayment($this->payment);
            
            if ($mutasi) {
                Log::info("💰 SuperAdminMutasi dibuat untuk payment: {$this->payment->order_id}");
            } else {
                Log::warning("⚠️ SuperAdminMutasi sudah ada untuk payment: {$this->payment->order_id}");
            }
        } catch (\Throwable $e) {
            Log::error("❌ Gagal buat SuperAdminMutasi: " . $e->getMessage());
            throw $e;
        }
    }
}