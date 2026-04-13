<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseLookupController extends Controller
{
    /**
     * Lookup data pembeli berdasarkan license_key.
     * Dipanggil via AJAX dari halaman register.
     *
     * GET /api/license/lookup?key=XXXX-XXXX-XXXX-XXXX
     *
     * Data nama/email/phone ada di tabel licenses.
     * Data school_name HANYA ada di tabel payments — di-join untuk ambil nilainya.
     */
    public function lookup(Request $request): JsonResponse
    {
        $key = strtoupper(trim($request->query('key', '')));

        if (strlen($key) < 10) {
            return response()->json(['valid' => false], 200);
        }

        /** @var \App\Models\License|null $license */
        $license = License::where('license_key', $key)->first();

        if (! $license) {
            return response()->json(['valid' => false], 200);
        }

        // Pastikan lisensi belum dipakai untuk register
        if ($license->user_id !== null) {
            return response()->json([
                'valid'   => false,
                'message' => 'Lisensi ini sudah digunakan.',
            ], 200);
        }

        // Ambil school_name dari payments karena tidak disimpan di licenses.
        // Match via email + package_type supaya tidak salah ambil data orang lain.
        $payment = Payment::where('buyer_email', $license->buyer_email)
            ->where('package_type', $license->package_type)
            ->whereIn('status', ['settlement', 'capture'])
            ->latest()
            ->first();

        $schoolName = $payment?->school_name ?? $license->school_name ?? null;

        return response()->json([
            'valid'        => true,
            'package_type' => $license->package_type,
            'package_type' => $this->packageLabel($license->package_type),
            'expires_at'   => $license->expires_at?->translatedFormat('d M Y') ?? 'Seumur hidup',
            'prefill'      => [
                'name'        => $license->buyer_name,
                'email'       => $license->buyer_email,
                'phone'       => $license->buyer_phone,
                'school_name' => $schoolName,
            ],
        ]);
    }

    private function packageLabel(string $type): string
    {
        return match ($type) {
            'monthly'  => 'Paket Bulanan',
            'yearly'   => 'Paket Tahunan',
            'lifetime' => 'Paket Lifetime',
            default    => ucfirst($type),
        };
    }
}