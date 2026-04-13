<?php

namespace App\Mail;

use App\Models\License;
use App\Models\Payment;
use App\Models\SchoolSetting;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LicenseMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public readonly License $license,
        public readonly Payment $payment,
    ) {}

    public function build(): static
    {
        Carbon::setLocale('id');

        $packageLabel = match ($this->license->package_type) {
            'monthly'  => 'Paket Bulanan',
            'yearly'   => 'Paket Tahunan',
            'lifetime' => 'Paket Lifetime',
            default    => ucfirst($this->license->package_type),
        };

        $endDate = $this->license->package_type === 'lifetime'
            ? 'Seumur Hidup'
            : Carbon::parse($this->license->end_date)->translatedFormat('d F Y');

        // ── Generate PDF invoice ────────────────────────────────────────────
        $pdfData = $this->buildInvoicePdf($packageLabel);

        $mail = $this
            ->subject('🔑 Kode Lisensi EduFinance (' . $packageLabel . ') — ' . $this->payment->order_id)
            ->view('emails.license')
            ->with([
                'buyerName'    => $this->payment->buyer_name ?? $this->license->buyer_name ?? 'Pelanggan',
                'licenseKey'   => $this->license->license_key,
                'packageLabel' => $packageLabel,
                'startDate'    => Carbon::parse($this->license->start_date)->translatedFormat('d F Y'),
                'endDate'      => $endDate,
                'price'        => 'Rp ' . number_format($this->payment->amount, 0, ',', '.'),
                'orderId'      => $this->payment->order_id,
                'registerUrl'  => url('/register?license_key=' . $this->license->license_key),
            ]);

        // ── Lampirkan PDF jika berhasil di-generate ─────────────────────────
        if ($pdfData !== null) {
            $filename = 'Invoice-' . $this->payment->order_id . '.pdf';
            $mail->attachData($pdfData, $filename, [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Generate PDF dari blade invoice yang sudah ada
    // ─────────────────────────────────────────────────────────────────────────
    private function buildInvoicePdf(string $packageLabel): ?string
    {
        try {
            // Ambil SchoolSetting milik super_admin — sama seperti di controller invoice
            $superAdmin = User::where('role', 'super_admin')->orderBy('id')->first();
            $setting    = $superAdmin
                ? SchoolSetting::where('user_id', $superAdmin->id)->first()
                : null;

            // Blade invoice butuh $user berisi data pembeli
            // Dibuat sebagai stdClass karena pembeli belum tentu punya akun
            $user = (object) [
                'name'        => $this->payment->buyer_name  ?? $this->license->buyer_name  ?? '-',
                'email'       => $this->payment->buyer_email ?? $this->license->buyer_email ?? '-',
                'school_name' => $this->payment->school_name ?? $this->license->school_name ?? '-',
                'phone'       => $this->payment->buyer_phone ?? $this->license->buyer_phone ?? '-',
            ];

            $pdf = Pdf::loadView('admin.users.invoice', [
                'license'      => $this->license,
                'payment'      => $this->payment,
                'setting'      => $setting,
                'user'         => $user,
                'packageLabel' => $packageLabel,
            ])->setPaper('a4', 'portrait');

            return $pdf->output();

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error(
                '📄 Gagal generate PDF invoice untuk email: ' . $e->getMessage(),
                ['order_id' => $this->payment->order_id]
            );

            // Email tetap terkirim walau PDF gagal — tidak blocking
            return null;
        }
    }
}