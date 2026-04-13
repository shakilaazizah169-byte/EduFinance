<?php

namespace App\Jobs;

use App\Models\License;
use App\Models\Payment;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendLicenseWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 30;

    protected License $license;
    protected Payment $payment;

    public function __construct(License $license, Payment $payment)
    {
        $this->license = $license;
        $this->payment = $payment;
    }

    public function handle(WhatsAppService $wa): void
    {
        $phone = $this->payment->buyer_phone 
               ?? $this->license->buyer_phone 
               ?? $this->license->user?->phone;

        if (!$phone) {
            Log::warning('Nomor WA tidak ditemukan untuk license: ' . $this->license->license_key);
            return;
        }

        $result = $wa->sendLicenseInfo($this->license, $this->payment);
        
        if (!$result) {
            Log::error("📱 Gagal kirim WA ke {$phone}");
            throw new \Exception("WhatsApp send failed");
        }
        
        Log::info("📱 WA lisensi terkirim ke: {$phone}");
    }
}