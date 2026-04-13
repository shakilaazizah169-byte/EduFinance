<?php

namespace App\Http\Controllers;
    
use App\Models\License;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PricingController extends Controller
{
    // Daftar paket
    protected array $packages = [
        'monthly' => [
            'type'     => 'monthly',
            'name'     => 'Paket Bulanan',
            'price'    => 100000,
            'duration' => '30 Hari',
            'features' => [
                'Akses penuh semua fitur',
                'Support via WhatsApp',
                'Update gratis',
                'Backup data',
            ],
        ],
        'yearly' => [
            'type'     => 'yearly',
            'name'     => 'Paket Tahunan',
            'price'    => 1000000,
            'duration' => '365 Hari',
            'features' => [
                'Akses penuh semua fitur',
                'Support prioritas',
                'Update gratis',
                'Backup data',
                'Hemat 2 bulan!',
            ],
        ],
        'lifetime' => [
            'type'     => 'lifetime',
            'name'     => 'Paket Lifetime',
            'price'    => 5000000,
            'duration' => 'Seumur Hidup',
            'features' => [
                'Akses penuh selamanya',
                'Support prioritas',
                'Update gratis selamanya',
                'Backup data',
                'Instalasi gratis',
            ],
        ],
    ];

    /**
     * Halaman pricing — PUBLIK, tanpa login
     */
    public function index()
    {
        $user = Auth::user();
        $currentLicense = null;
        $isLoggedIn = false;
        $userData = null;
        
        if ($user && $user->role !== 'super_admin') {
            $isLoggedIn = true;
            $userData = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'school_name' => $user->school_name,
            ];
            
            // Cek lisensi saat ini
            $license = $user->license;
            if ($license) {
                $currentLicense = [
                    'package_type' => $license->package_type,
                    'expired_at' => $license->end_date,
                    'is_active' => $license->isActive(),
                    'days_left' => $license->daysLeft(),
                ];
            }
        }
        
        return view('pricing.index', [
            'packages' => array_values($this->packages),
            'isLoggedIn' => $isLoggedIn,
            'userData' => $userData,
            'currentLicense' => $currentLicense,
        ]);
    }

    /**
     * Halaman checkout — REDIRECT ke pricing jika user sudah login
     * GET /checkout/{package}
     */
    public function checkoutPage(string $package)
    {
        abort_unless(array_key_exists($package, $this->packages), 404);
        
        // 🔥 Jika user sudah login, langsung proses checkout (skip form)
        if (Auth::check()) {
            $user = Auth::user();
            
            // Super admin tidak perlu beli lisensi
            if ($user->role === 'super_admin') {
                return redirect()->route('pricing')->with('error', 'Super admin tidak perlu membeli lisensi.');
            }
            
            // Langsung proses pembelian
            return $this->processInstantCheckout($package);
        }
        
        // Guest: tampilkan form checkout
        return view('pricing.checkout', [
            'package' => $this->packages[$package],
        ]);
    }
    
    /**
     * Proses checkout langsung untuk user yang sudah login
     */
    private function processInstantCheckout(string $packageType)
    {
        $user = Auth::user();
        $package = $this->packages[$packageType];
        
        try {
            $midtrans = new MidtransService();
            $result = $midtrans->createTransaction(
                buyerName: $user->name,
                buyerEmail: $user->email,
                buyerPhone: $user->phone ?? '08123456789',
                schoolName: $user->school_name ?? 'Sekolah Saya',
                package: $package,
                userId: $user->id,
            );
            
            if ($result['success']) {
                return redirect($result['redirect_url']);
            }
            
            return redirect()->route('pricing')
                ->with('error', $result['message'] ?? 'Gagal memproses pembayaran.');
                
        } catch (\Throwable $e) {
            Log::error('Instant checkout error: ' . $e->getMessage());
            return redirect()->route('pricing')
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    /**
     * Proses checkout — UNTUK GUEST (belum login)
     * POST /checkout
     */
    public function checkout(Request $request)
    {
        // 🔥 Jika user sudah login, tolak akses ke endpoint ini
        if (Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah login. Gunakan tombol beli langsung.',
                'redirect' => route('pricing')
            ], 400);
        }
        
        $request->validate([
            'package_type' => 'required|in:monthly,yearly,lifetime',
            'school_name'  => 'required|string|max:255',
            'phone'        => 'required|string|max:20',
            'email'        => 'required|email|max:255',
            'buyer_name'   => 'required|string|max:255',
        ]);

        try {
            // Cek email sudah punya lisensi?
            $existingLicense = License::where('buyer_email', $request->email)->exists();
            
            if ($existingLicense) {
                return response()->json([
                    'success' => false,
                    'field'   => 'email',
                    'message' => 'Email ini sudah terdaftar. Silakan login terlebih dahulu.',
                ], 422);
            }

            // Normalisasi nomor WA
            $normalPhone = preg_replace('/^\+?62/', '0', $request->phone);
            $existingLicenseByPhone = License::where('buyer_phone', $normalPhone)->exists();
            
            if ($existingLicenseByPhone) {
                return response()->json([
                    'success' => false,
                    'field'   => 'phone',
                    'message' => 'Nomor WhatsApp ini sudah terdaftar. Silakan login terlebih dahulu.',
                ], 422);
            }

            // Buat transaksi Midtrans
            $package = $this->packages[$request->package_type];
            $midtrans = new MidtransService();
            $result = $midtrans->createTransaction(
                buyerName: $request->buyer_name,
                buyerEmail: $request->email,
                buyerPhone: $request->phone,
                schoolName: $request->school_name,
                package: $package,
                userId: null, // Guest checkout
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $result['redirect_url'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 500);

        } catch (\Throwable $e) {
            Log::error('Checkout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
            ], 500);
        }
    }
}