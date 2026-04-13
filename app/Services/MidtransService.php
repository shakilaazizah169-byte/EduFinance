<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

/**
 * ROOT CAUSE FIX — Kenapa buyer_name, buyer_email, buyer_phone, school_name NULL:
 *
 * Versi lama: createTransaction(User $user, array $package)
 *   → Payment::create() tidak menyertakan kolom buyer_* sama sekali
 *   → Method hanya terima object $user, bukan data form checkout
 *
 * PricingController memanggil:
 *   $midtrans->createTransaction(
 *       buyerName: $request->buyer_name,    ← named params
 *       buyerEmail: $request->email,
 *       buyerPhone: $request->phone,
 *       schoolName: $request->school_name,
 *       package: $package,
 *       userId: auth()->id(),
 *   );
 *
 * Tapi MidtransService lama TIDAK punya parameter tersebut!
 * Method-nya masih: createTransaction(User $user, array $package)
 * → Data form checkout diabaikan → semua NULL
 *
 * FIX: Ganti signature method + tambah buyer_* ke Payment::create()
 */
class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized  = config('midtrans.is_sanitized', true);
        Config::$is3ds        = config('midtrans.is_3ds', true);
        Config::$appendNotifUrl = route('midtrans.webhook');
    }

    /**
     * Buat transaksi baru — signature baru yang menerima data buyer langsung
     *
     * @param string      $buyerName  Nama pembeli dari form checkout
     * @param string      $buyerEmail Email pembeli
     * @param string      $buyerPhone HP pembeli
     * @param string      $schoolName Nama sekolah
     * @param array       $package    Data paket ['type','name','price',...]
     * @param int|null    $userId     ID user jika sudah login, null jika guest
     */
   public function createTransaction(
        string  $buyerName,
        string  $buyerEmail,
        string  $buyerPhone,
        string  $schoolName,
        array   $package,
        ?int    $userId = null,
    ): array {
        $orderId = 'KAS-' . date('Ymd') . '-' . strtoupper(Str::random(6))
                . ($userId ? '-' . $userId : '-G');

        // 🔥 PASTIKAN userId tidak null (tapi tetap bisa null untuk guest)
        \Log::info('MidtransService: Membuat payment', [
            'userId' => $userId,
            'buyerEmail' => $buyerEmail,
            'isUserLoggedIn' => !is_null($userId)
        ]);

        $payment = Payment::create([
            'order_id'     => $orderId,
            'user_id'      => $userId,  // Bisa null untuk guest
            'package_type' => $package['type'],
            'amount'       => $package['price'],
            'status'       => 'pending',
            'buyer_name'   => $buyerName,
            'buyer_email'  => $buyerEmail,
            'buyer_phone'  => $buyerPhone,
            'school_name'  => $schoolName,
        ]);

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $package['price'],
            ],
            'customer_details' => [
                'first_name' => $buyerName,
                'email'      => $buyerEmail,
                'phone'      => $buyerPhone,
            ],
            'item_details' => [[
                'id'       => $package['type'],
                'price'    => $package['price'],
                'quantity' => 1,
                'name'     => $package['name'],
            ]],
            'callbacks' => [
                'finish'   => route('payment.finish'),
                'unfinish' => route('payment.unfinish'),
                'error'    => route('payment.error'),
            ],
        ];

        try {
            $snap = Snap::createTransaction($params);

            Log::info('Midtrans transaction created', [
                'order_id'  => $orderId,
                'buyer'     => $buyerEmail,
                'school'    => $schoolName,
                'package'   => $package['type'],
            ]);

            return [
                'success'      => true,
                'snap_token'   => $snap->token,
                'redirect_url' => $snap->redirect_url,
                'payment'      => $payment,
                'order_id'     => $orderId,
            ];

        } catch (\Throwable $e) {
            Log::error('Midtrans transaction failed', ['error' => $e->getMessage()]);
            $payment->delete(); // Rollback payment record

            return [
                'success' => false,
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage(),
            ];
        }
    }

    public function checkStatus(string $orderId): array
    {
        try {
            return ['success' => true, 'status' => Transaction::status($orderId)];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function cancelTransaction(string $orderId): array
    {
        try {
            return ['success' => true, 'result' => Transaction::cancel($orderId)];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}