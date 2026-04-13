<?php

namespace App\Services;

use App\Models\License;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WhatsApp via Fonnte (https://fonnte.com)
 *
 * Setup .env:
 *   WA_API_KEY=LqNMvPNRrK1sMDShrhf9
 *   WA_API_URL=https://api.fonnte.com/send
 */
class WhatsAppService
{
    // Fonnte endpoint
    private const FONNTE_URL = 'https://api.fonnte.com/send';

    protected string $token;

    public function __construct()
    {
        // Ambil dari .env — fallback ke nilai langsung jika .env belum diisi
        $this->token = env('WA_API_KEY', '');
    }

    // ─────────────────────────────────────────────────────────────
    // PUBLIC API
    // ─────────────────────────────────────────────────────────────

    /**
     * Kirim kode lisensi ke pembeli setelah payment settlement.
     * Dipanggil dari LicenseService::generateLicense()
     */
    public function sendLicenseInfo(License $license, Payment $payment): bool
    {
        $phone = $this->resolvePhone($license, $payment);
        if (! $phone) {
            Log::warning('WhatsApp: Nomor tujuan tidak ditemukan, skip.', [
                'order_id' => $payment->order_id,
            ]);
            return false;
        }

        $message = $this->buildLicenseMessage($license, $payment);
        return $this->send($phone, $message, $payment->order_id);
    }

    /**
     * Kirim pesan teks bebas ke nomor tertentu
     */
    public function sendRaw(string $phone, string $message): bool
    {
        return $this->send($phone, $message);
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────

    /**
     * Bangun teks pesan lisensi yang informatif
     */
    private function buildLicenseMessage(License $license, Payment $payment): string
    {
        $name       = $payment->buyer_name   ?? $license->user?->name   ?? 'Pelanggan';
        $school     = $payment->school_name  ?? $license->user?->school_name ?? '-';
        $packageLabel = match ($payment->package_type) {
            'monthly'  => 'Paket Bulanan (30 Hari)',
            'yearly'   => 'Paket Tahunan (365 Hari)',
            'lifetime' => 'Paket Lifetime (Selamanya)',
            default    => ucfirst($payment->package_type),
        };
        $amount    = 'Rp ' . number_format($payment->amount, 0, ',', '.');
        $startDate = $license->start_date->format('d/m/Y');
        $endDate   = $license->end_date->format('d/m/Y');
        $appUrl    = config('app.url');

        return <<<MSG
        ✅ *PEMBAYARAN BERHASIL*
        ━━━━━━━━━━━━━━━━━━━━━━

        Halo *{$name}*!
        Terima kasih sudah berlangganan *Aplikasi EduFinance*.

        🏫 Instansi  : {$school}
        📦 Paket    : {$packageLabel}
        💰 Nominal  : {$amount}
        🆔 Order ID : {$payment->order_id}

        ━━━━━━━━━━━━━━━━━━━━━━
        🔑 *KODE LISENSI ANDA:*

        `{$license->license_key}`

        ━━━━━━━━━━━━━━━━━━━━━━
        📅 Masa Berlaku:
        Mulai  : {$startDate}
        Selesai: {$endDate}

        ━━━━━━━━━━━━━━━━━━━━━━
        📝 *CARA AKTIVASI:*
        1️⃣  Buka: {$appUrl}/register
        2️⃣  Isi Nama Instansi, Email & Password
        3️⃣  Masukkan *Kode Lisensi* di atas
        4️⃣  Klik *Buat Akun* → selesai!

        ⚠️ Simpan kode ini dengan baik.
        Jangan bagikan ke orang lain!

        Butuh bantuan? Balas pesan ini 🙏
        MSG;
    }

    /**
     * Cari nomor WA tujuan dari Payment atau License
     */
    private function resolvePhone(License $license, Payment $payment): ?string
    {
        $raw = $payment->buyer_phone
            ?? $license->buyer_phone
            ?? $license->user?->phone
            ?? null;

        return $raw ? $this->normalizePhone($raw) : null;
    }

    /**
     * Normalisasi nomor: 08xxx → 628xxx, +62 → 62
     */
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone); // hapus karakter non-angka

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Pastikan minimal 10 digit (validasi longgar)
        return strlen($phone) >= 10 ? $phone : '';
    }

    /**
     * Kirim via Fonnte API
     *
     * Docs: https://documenter.getpostman.com/view/1548199/Szf782rb
     * Header: Authorization: <token>
     * Body  : target, message, countryCode (opsional)
     */
    private function send(string $phone, string $message, string $context = '-'): bool
    {
        if (empty($this->token)) {
            Log::warning('WhatsApp: WA_API_KEY kosong di .env — pesan tidak dikirim.', ['ctx' => $context]);
            return false;
        }

        if (empty($phone)) {
            Log::warning('WhatsApp: Nomor setelah normalisasi kosong.', ['ctx' => $context]);
            return false;
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => $this->token,  // Fonnte pakai token langsung, tanpa "Bearer"
                ])
                ->post(self::FONNTE_URL, [
                    'target'      => $phone,
                    'message'     => $message,
                    'countryCode' => '62',   // Indonesia — opsional tapi disarankan
                    'delay'       => '2',    // detik sebelum kirim (opsional)
                ]);

            $body = $response->json();

            // Fonnte response: {"status":true,"detail":"...","id":...}
            if ($response->successful() && ($body['status'] ?? false) === true) {
                Log::info('📱 WhatsApp terkirim', [
                    'to'      => $phone,
                    'ctx'     => $context,
                    'detail'  => $body['detail'] ?? '-',
                    'msg_id'  => $body['id'] ?? '-',
                ]);
                return true;
            }

            Log::error('📱 WhatsApp gagal terkirim', [
                'to'       => $phone,
                'ctx'      => $context,
                'http'     => $response->status(),
                'response' => $body,
            ]);
            return false;

        } catch (\Throwable $e) {
            Log::error('📱 WhatsApp exception', [
                'to'      => $phone,
                'ctx'     => $context,
                'error'   => $e->getMessage(),
            ]);
            return false;
        }
    }
}