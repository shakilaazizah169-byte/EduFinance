<?php

namespace App\Services;

use App\Jobs\SendLicenseEmailJob;
use App\Jobs\SendLicenseWhatsAppJob;
use App\Jobs\GenerateSuperAdminMutasiJob;
use App\Models\License;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LicenseService
{
    public function __construct() {}

    public function generateLicense(Payment $payment, ?User $user = null): License
    {
        Log::info('🔄 generateLicense dipanggil', [
            'order_id'    => $payment->order_id,
            'package'     => $payment->package_type,
            'buyer_email' => $payment->buyer_email,
            'user_id'     => $user?->id ?? 'NULL',
        ]);

        $startDate = Carbon::now();
        
        // 🔥 PERBAIKAN: Cek apakah user sudah punya lisensi AKTIF
        $existingLicense = null;
        if ($user) {
            $existingLicense = License::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('end_date', '>', Carbon::now())
                ->latest()
                ->first();
        }
        
        // Jika tidak ada user, cek berdasarkan email
        if (!$existingLicense && $payment->buyer_email) {
            $existingLicense = License::where('buyer_email', $payment->buyer_email)
                ->where('status', 'active')
                ->where('end_date', '>', Carbon::now())
                ->latest()
                ->first();
        }
        
        // 🔥 HITUNG END DATE dengan logic PERPANJANGAN
        if ($existingLicense) {
            // Jika sudah punya lisensi aktif, TAMBAHKAN durasi ke expired date LAMA
            $endDate = $existingLicense->end_date->copy()->addDays(
                $this->getDurationDays($payment->package_type)
            );
            Log::info('📅 Perpanjangan lisensi', [
                'old_expired' => $existingLicense->end_date->format('Y-m-d'),
                'new_expired' => $endDate->format('Y-m-d'),
                'added_days' => $this->getDurationDays($payment->package_type)
            ]);
        } else {
            // Jika belum punya lisensi atau expired, mulai dari SEKARANG
            $endDate = Carbon::now()->addDays(
                $this->getDurationDays($payment->package_type)
            );
            Log::info('📅 Lisensi baru', [
                'start_date' => $startDate->format('Y-m-d'),
                'new_expired' => $endDate->format('Y-m-d'),
            ]);
        }

        // Generate license key
        $schoolName = $payment->school_name ?? $user?->school_name ?? 'SEKOLAH';
        $schoolCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $schoolName), 0, 4)) ?: 'SCHL';
        $licenseKey = $schoolCode
            . '-' . strtoupper(Str::random(4))
            . '-' . strtoupper(Str::random(4))
            . '-' . strtoupper(Str::random(4));

        $license = License::create([
            'license_key'  => $licenseKey,
            'user_id'      => $user?->id ?? $existingLicense?->user_id,
            'buyer_name'   => $payment->buyer_name,
            'buyer_email'  => $payment->buyer_email,
            'buyer_phone'  => $payment->buyer_phone,
            'status'       => 'active',
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'package_type' => $payment->package_type,
            'price'        => $payment->amount,
        ]);

        // 🔥 Jika ada lisensi lama yang masih aktif, EXPIRED kan (optional, biar tidak double)
        if ($existingLicense && $existingLicense->id !== $license->id) {
            $existingLicense->update(['status' => 'expired']);
            Log::info('📌 Lisensi lama di-expired', ['old_id' => $existingLicense->id]);
        }

        Log::info('✅ Lisensi dibuat', [
            'key' => $licenseKey,
            'order' => $payment->order_id,
            'user_id' => $license->user_id,
            'end_date' => $endDate->format('Y-m-d'),
        ]);

        $this->sendNotifications($license, $payment);
        
        return $license;
    }

    /**
     * Get duration days based on package type
     */
    private function getDurationDays(string $packageType): int
    {
        return match ($packageType) {
            'monthly'  => 30,
            'yearly'   => 365,
            'lifetime' => 36500, // 100 tahun
            default    => 30,
        };
    }

    public function resendNotification(License $license, Payment $payment): void
    {
        Log::info('🔄 Resend notification - dispatch jobs', [
            'license_key' => $license->license_key,
            'order_id' => $payment->order_id,
        ]);

        $email = $payment->buyer_email ?? $license->buyer_email ?? $license->user?->email;
        $phone = $payment->buyer_phone ?? $license->buyer_phone ?? $license->user?->phone;

        if ($email) {
            SendLicenseEmailJob::dispatch($license, $payment)
                ->onQueue('emails');
            Log::info("📧 Resend email job di-dispatch ke: {$email}");
        }

        if ($phone) {
            SendLicenseWhatsAppJob::dispatch($license, $payment)
                ->onQueue('sms')
                ->delay(now()->addSeconds(5));
            Log::info("📱 Resend WhatsApp job di-dispatch ke: {$phone}");
        }
    }

    public function expireLicenses(): int
    {
        $count = License::where('status', 'active')
            ->where('end_date', '<', Carbon::now())
            ->update(['status' => 'expired']);
        Log::info("⏰ {$count} lisensi di-expire.");
        return $count;
    }

    private function sendNotifications(License $license, Payment $payment): void
    {
        Log::info('📬 Dispatch notification jobs', [
            'email' => $payment->buyer_email ?? $license->buyer_email ?? $license->user?->email ?? 'KOSONG',
            'phone' => $payment->buyer_phone ?? $license->buyer_phone ?? $license->user?->phone ?? 'KOSONG',
            'key'   => $license->license_key,
        ]);

        $email = $payment->buyer_email ?? $license->buyer_email ?? $license->user?->email;
        $phone = $payment->buyer_phone ?? $license->buyer_phone ?? $license->user?->phone;

        // Dispatch Email Job
        if ($email) {
            SendLicenseEmailJob::dispatch($license, $payment)
                ->onQueue('emails')
                ->delay(now()->addSeconds(5)); // Tunggu 5 detik setelah license dibuat
            Log::info("📧 SendLicenseEmailJob di-dispatch ke email: {$email}");
        }

        // Dispatch WhatsApp Job
        if ($phone) {
            SendLicenseWhatsAppJob::dispatch($license, $payment)
                ->onQueue('sms')
                ->delay(now()->addSeconds(10)); // Tunggu 10 detik
            Log::info("📱 SendLicenseWhatsAppJob di-dispatch ke phone: {$phone}");
        }

        // Dispatch SuperAdminMutasi Job
        GenerateSuperAdminMutasiJob::dispatch($payment)
            ->onQueue('default')
            ->delay(now()->addSeconds(3));
        Log::info("💰 GenerateSuperAdminMutasiJob di-dispatch untuk order_id: {$payment->order_id}");
    }
}