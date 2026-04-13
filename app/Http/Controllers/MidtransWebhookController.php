<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateLicenseJob;
use App\Models\License;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function __construct() {}

    public function handle(Request $request): \Illuminate\Http\JsonResponse
    {
        Log::info('🚀 Midtrans webhook diterima', [
            'method'  => $request->method(),
            'ip'      => $request->ip(),
            'payload' => $request->all(),
        ]);

        $logFile = storage_path('logs/midtrans-' . date('Y-m-d') . '.log');
        file_put_contents(
            $logFile,
            "\n[" . date('Y-m-d H:i:s') . "] " . json_encode($request->all()) . "\n",
            FILE_APPEND
        );

        $orderId = $request->input('order_id');
        $status  = $request->input('transaction_status');

        if (! $orderId || ! $status) {
            Log::warning('Webhook: payload tidak lengkap', $request->all());
            return response()->json(['status' => 'ignored', 'reason' => 'incomplete payload']);
        }

        $payment = Payment::where('order_id', $orderId)->first();

        if (! $payment) {
            Log::warning("Webhook: order_id {$orderId} tidak ditemukan di DB");
            return response()->json(['status' => 'ok', 'reason' => 'payment not found']);
        }

        $payment->update([
            'status'       => $status,
            'payment_type' => $request->input('payment_type', $payment->payment_type),
            'raw_response' => json_encode($request->all()),
        ]);

        Log::info("Webhook: {$orderId} → {$status}");

        if (in_array($status, ['settlement', 'capture'])) {
            $this->processSettlement($payment);
        }

        if (in_array($status, ['cancel', 'deny', 'expire'])) {
            Log::info("Webhook: {$orderId} dibatalkan/expired ({$status})");
        }

        return response()->json([
            'status'  => 'ok',
            'message' => 'Webhook diproses',
            'received_at' => now()->toDateTimeString(),
        ]);
    }

    private function processSettlement(Payment $payment): void
    {
        if ($this->licenseAlreadyExists($payment)) {
            Log::info("Webhook: lisensi sudah ada untuk {$payment->order_id} — skip");
            return;
        }

        try {
            Log::info("Webhook: dispatch GenerateLicenseJob untuk {$payment->order_id}");
            
            // 🔥 FIX: Ambil user dari payment->user_id
            $user = null;
            if ($payment->user_id) {
                $user = User::find($payment->user_id);
                Log::info("Webhook: user ditemukan", [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            } else {
                Log::info("Webhook: guest checkout (tidak ada user_id)");
            }
            
            // Dispatch ke queue — akan diproses async
            GenerateLicenseJob::dispatch($payment, $user);
            Log::info("Webhook: ✅ GenerateLicenseJob di-dispatch untuk order_id: {$payment->order_id}");
        } catch (\Throwable $e) {
            Log::error("Webhook: ❌ gagal dispatch GenerateLicenseJob untuk {$payment->order_id}: " . $e->getMessage());
        }
    }

    private function licenseAlreadyExists(Payment $payment): bool
    {
        $q = License::where('package_type', $payment->package_type)
            ->whereDate('created_at', today());

        if ($payment->buyer_email) {
            $q->where('buyer_email', $payment->buyer_email);
        } elseif ($payment->user_id) {
            $q->where('user_id', $payment->user_id);
        }

        return $q->exists();
    }
}