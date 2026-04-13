<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateLicenseJob;
use App\Models\License;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Transaction;

class PaymentCallbackController extends Controller
{
    public function __construct() {}

    public function finish(Request $request)
    {
        return view('payment.finish', [
            'order_id' => $request->order_id,
        ]);
    }

    /**
     * AJAX polling — dipanggil tiap 5 detik dari finish.blade.php
     */
    public function checkStatus(string $orderId)
    {
        $payment = Payment::where('order_id', $orderId)->firstOrFail();

        // Jika DB sudah settlement — tidak perlu hit Midtrans lagi
        if (in_array($payment->status, ['settlement', 'capture'])) {
            Log::info("checkStatus: {$orderId} sudah settlement di DB");
            return response()->json([
                'success' => true,
                'status'  => $payment->status,
            ]);
        }

        try {
            Config::$serverKey    = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');

            $response = Transaction::status($orderId);
            $status = $response->transaction_status;

            Log::info("checkStatus Midtrans: {$orderId} → {$status}");

            $payment->update([
                'status'       => $status,
                'payment_type' => $response->payment_type ?? $payment->payment_type,
                'raw_response' => json_encode($response),
            ]);

            if (in_array($status, ['settlement', 'capture'])) {
                if (!$this->licenseAlreadyExists($payment)) {
                    Log::info("checkStatus: dispatch GenerateLicenseJob untuk {$orderId}");
                    $user = $payment->user_id ? User::find($payment->user_id) : null;
                    
                    // Dispatch ke queue — akan diproses async
                    GenerateLicenseJob::dispatch($payment, $user);
                } else {
                    Log::info("checkStatus: skip generate - recent license already exists for {$orderId}");
                }
            }

            return response()->json([
                'success' => true,
                'status'  => $status,
            ]);

        } catch (\Throwable $e) {
            Log::error("checkStatus error: {$orderId} → " . $e->getMessage());

            return response()->json([
                'success' => false,
                'status'  => $payment->status,
                'message' => 'Gagal cek status ke Midtrans.',
            ], 500);
        }
    }

    public function unfinish(Request $request)
    {
        return view('payment.unfinish', ['order_id' => $request->order_id]);
    }

    public function error(Request $request)
    {
        return view('payment.error', ['order_id' => $request->order_id]);
    }

    private function licenseAlreadyExists(Payment $payment): bool
    {
        // 🔥 PERBAIKAN: Cek apakah sudah ada license untuk payment ini dalam 5 menit terakhir
        // Tujuannya untuk mencegah duplikasi dalam waktu singkat, BUKAN mencegah perpanjangan
        
        $existing = License::where('buyer_email', $payment->buyer_email)
            ->where('package_type', $payment->package_type)
            ->where('created_at', '>=', now()->subMinutes(5)) // Hanya cek 5 menit terakhir
            ->exists();
        
        if ($existing) {
            Log::info('licenseAlreadyExists: TRUE - found recent license', [
                'email' => $payment->buyer_email,
                'package' => $payment->package_type
            ]);
        } else {
            Log::info('licenseAlreadyExists: FALSE - no recent license', [
                'email' => $payment->buyer_email,
                'package' => $payment->package_type
            ]);
        }
        
        return $existing;
    }
}