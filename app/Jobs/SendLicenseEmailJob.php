<?php

namespace App\Jobs;

use App\Models\License;
use App\Models\Payment;
use App\Mail\LicenseMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLicenseEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Maksimal percobaan kalau gagal
    public $tries = 3;
    
    // Timeout 60 detik per job
    public $timeout = 60;

    protected License $license;
    protected Payment $payment;

    public function __construct(License $license, Payment $payment)
    {
        $this->license = $license;
        $this->payment = $payment;
    }

    public function handle(): void
    {
        $email = $this->payment->buyer_email 
              ?? $this->license->buyer_email 
              ?? $this->license->user?->email;

        if (!$email) {
            Log::warning('Email tidak ditemukan untuk license: ' . $this->license->license_key);
            return;
        }

        try {
            Mail::to($email)->send(new LicenseMail($this->license, $this->payment));
            Log::info("📧 Email lisensi terkirim ke: {$email}");
        } catch (\Throwable $e) {
            Log::error("📧 Gagal kirim email ke {$email}: " . $e->getMessage());
            
            // Lempar exception agar job dicoba ulang
            throw $e;
        }
    }
}