<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KodeTransaksiController;
use App\Http\Controllers\LaporanKasController;
use App\Http\Controllers\MutasiKasController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\PerencanaanController;
use App\Http\Controllers\PricingController;  // ← PAKAI INI, jangan LicenseController
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchoolSettingController;
use App\Http\Controllers\RealisasiController;
use App\Http\Controllers\LicenseLookupController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

// ================================================================
// PUBLIC ROUTES — Tanpa login
// ================================================================

Route::get('/force-logout', function() {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
});

Route::get('/home', fn() => redirect()->route('dashboard'));
Route::get('/', fn() => view('welcome'));

// ────────────── PRICING & CHECKOUT (PAKAI PricingController) ──────────────
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');
Route::get('/checkout/{package}', [PricingController::class, 'checkoutPage'])->name('checkout.page');
Route::post('/checkout', [PricingController::class, 'checkout'])->name('checkout');

// ────────────── PAYMENT CALLBACK ──────────────
Route::get('/payment/finish', [PaymentCallbackController::class, 'finish'])->name('payment.finish');
Route::get('/payment/unfinish', [PaymentCallbackController::class, 'unfinish'])->name('payment.unfinish');
Route::get('/payment/error', [PaymentCallbackController::class, 'error'])->name('payment.error');
Route::get('/payment/check-status/{orderId}', [PaymentCallbackController::class, 'checkStatus'])->name('payment.check-status');

Route::any('/midtrans/webhook', function (\Illuminate\Http\Request $req) {
    \Illuminate\Support\Facades\Log::info('Webhook', ['body' => $req->all()]);
    return app(\App\Http\Controllers\MidtransWebhookController::class)->handle($req);
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->name('midtrans.webhook');

Route::get('/api/license/lookup', [LicenseLookupController::class, 'lookup'])->name('license.lookup');

// ================================================================
// AUTH
// ================================================================

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);
Route::get('register/check-license', [RegisterController::class, 'checkLicense'])
    ->name('register.check-license')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('email/verify', 'App\Http\Controllers\Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}/{hash}', 'App\Http\Controllers\Auth\VerificationController@verify')->name('verification.verify');
Route::post('email/resend', 'App\Http\Controllers\Auth\VerificationController@resend')->name('verification.resend');

// ================================================================
// ROUTE YANG MEMBUTUHKAN LOGIN (TAPI LISENSI BOLEH EXPIRED)
// ================================================================
Route::middleware(['auth', 'check.license'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/details', [ProfileController::class, 'details'])->name('profile.details');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/activity', [ProfileController::class, 'activity'])->name('profile.activity');
    Route::get('/notifications', [ProfileController::class, 'notifications'])->name('profile.notifications');
    Route::post('/notifications/update', [ProfileController::class, 'updateNotifications'])->name('profile.notifications.update');
    Route::post('/delete-account', [ProfileController::class, 'deleteAccount'])->name('profile.delete-account');

    Route::get('/account-settings', [SchoolSettingController::class, 'index'])->name('school.settings');
    Route::post('/account-settings', [SchoolSettingController::class, 'update'])->name('school.settings.update');
    Route::post('/account-settings/delete-file', [SchoolSettingController::class, 'deleteFile'])->name('school.settings.delete-file');

    // READ-ONLY: Laporan & export (bisa lihat meskipun expired)
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/kode-transaksi', [KodeTransaksiController::class, 'index'])->name('kode-transaksi.index');
    Route::get('/kode-transaksi/{id}', [KodeTransaksiController::class, 'show'])->name('kode-transaksi.show')->where('id', '[0-9]+');
    Route::get('/perencanaan', [PerencanaanController::class, 'index'])->name('perencanaan.index');
    Route::get('/perencanaan/{perencanaan}', [PerencanaanController::class, 'show'])->name('perencanaan.show')->where('perencanaan', '[0-9]+');
    Route::get('/realisasi', [RealisasiController::class, 'index'])->name('realisasi.index');
    Route::get('/realisasi/{realisasi}', [RealisasiController::class, 'show'])->name('realisasi.show')->where('realisasi', '[0-9]+');
    Route::get('/mutasi-kas', [MutasiKasController::class, 'index'])->name('mutasi-kas.index');
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/invoice/{id}', [InvoiceController::class, 'show'])->name('invoice.show')->where('id', '[0-9]+');
    Route::get('/invoice/{id}/print', [InvoiceController::class, 'print'])->name('invoice.print')->where('id', '[0-9]+');
    Route::get('/laporan-mutasi', [LaporanKasController::class, 'index'])->name('laporan.mutasi');
    Route::get('/laporan-mutasi/export-excel', [LaporanKasController::class, 'exportExcel'])->name('laporan.export.excel');
    Route::get('/laporan-mutasi/export-pdf', [LaporanKasController::class, 'exportPdf'])->name('laporan.export.pdf');

     // 🔥 ROUTE UNTUK STORED PROCEDURE
    Route::get('/laporan/stored-procedure', [LaporanKasController::class, 'laporanWithStoredProcedure'])->name('laporan.stored-procedure');
    Route::get('/laporan/ringkasan-sp', [LaporanKasController::class, 'ringkasanWithStoredProcedure'])->name('laporan.ringkasan-sp');
    Route::get('/laporan/cek-lisensi-sp', [LaporanKasController::class, 'cekLisensiWithStoredProcedure'])->name('laporan.cek-lisensi-sp');
});

// ================================================================
// ROUTE YANG MEMBUTUHKAN LISENSI AKTIF (CREATE, EDIT, DELETE)
// ================================================================
Route::middleware(['auth', 'license.active', 'role:admin'])->group(function () {
    // Kategori - write operations
    Route::get('/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/{id}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
    Route::delete('/kategori/bulk-destroy', [KategoriController::class, 'bulkDestroy'])->name('kategori.bulk-destroy');

    // Kode Transaksi - write operations
    Route::get('/kode-transaksi/create', [KodeTransaksiController::class, 'create'])->name('kode-transaksi.create');
    Route::post('/kode-transaksi', [KodeTransaksiController::class, 'store'])->name('kode-transaksi.store');
    Route::get('/kode-transaksi/{id}/edit', [KodeTransaksiController::class, 'edit'])->name('kode-transaksi.edit');
    Route::put('/kode-transaksi/{id}', [KodeTransaksiController::class, 'update'])->name('kode-transaksi.update');
    Route::delete('/kode-transaksi/{id}', [KodeTransaksiController::class, 'destroy'])->name('kode-transaksi.destroy');

    // Perencanaan - full resource (kecuali index, show sudah di atas)
    Route::get('/perencanaan/create', [PerencanaanController::class, 'create'])->name('perencanaan.create');
    Route::post('/perencanaan', [PerencanaanController::class, 'store'])->name('perencanaan.store');
    Route::get('/perencanaan/{perencanaan}/edit', [PerencanaanController::class, 'edit'])->name('perencanaan.edit');
    Route::put('/perencanaan/{perencanaan}', [PerencanaanController::class, 'update'])->name('perencanaan.update');
    Route::delete('/perencanaan/{perencanaan}', [PerencanaanController::class, 'destroy'])->name('perencanaan.destroy');

    // Realisasi - full resource (kecuali index, show sudah di atas)
    Route::get('/realisasi/create', [RealisasiController::class, 'create'])->name('realisasi.create');
    Route::post('/realisasi', [RealisasiController::class, 'store'])->name('realisasi.store');
    Route::get('/realisasi/{realisasi}/edit', [RealisasiController::class, 'edit'])->name('realisasi.edit');
    Route::put('/realisasi/{realisasi}', [RealisasiController::class, 'update'])->name('realisasi.update');
    Route::delete('/realisasi/{realisasi}', [RealisasiController::class, 'destroy'])->name('realisasi.destroy');
    Route::get('realisasi/{lampiran}/download', [RealisasiController::class, 'downloadLampiran'])->name('realisasi.lampiran.download');
    Route::get('ajax/detail-perencanaan', [RealisasiController::class, 'getDetailPerencanaan'])->name('ajax.detail-perencanaan');

    // Mutasi Kas - write operations
    Route::get('/mutasi-kas/create', [MutasiKasController::class, 'create'])->name('mutasi-kas.create');
    Route::post('/mutasi-kas', [MutasiKasController::class, 'store'])->name('mutasi-kas.store');

    // Invoice - write operations
    Route::get('/invoice/create', [InvoiceController::class, 'create'])->name('invoice.create');
    Route::post('/invoice', [InvoiceController::class, 'store'])->name('invoice.store');
    Route::get('/invoice/{id}/edit', [InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::put('/invoice/{id}', [InvoiceController::class, 'update'])->name('invoice.update');
    Route::delete('/invoice/{id}', [InvoiceController::class, 'destroy'])->name('invoice.destroy');
});

// ================================================================
// SUPER ADMIN — Hanya super_admin, tidak butuh lisensi
// ================================================================

Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [\App\Http\Controllers\Admin\UserManagementController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/suspend', [\App\Http\Controllers\Admin\UserManagementController::class, 'suspend'])->name('users.suspend');
    Route::get('/users/{userId}/invoice/{licenseId}', [\App\Http\Controllers\Admin\UserManagementController::class, 'invoice'])->name('users.invoice');
    Route::post('/users/{id}/activate', [\App\Http\Controllers\Admin\UserManagementController::class, 'activate'])->name('users.activate');

    Route::get('/mutasi', [\App\Http\Controllers\Admin\SuperAdminMutasiController::class, 'index'])->name('mutasi.index');
    Route::get('/mutasi/export-pdf', [\App\Http\Controllers\Admin\SuperAdminMutasiController::class, 'exportPdf'])->name('mutasi.export.pdf');
    Route::get('/mutasi/export-excel', [\App\Http\Controllers\Admin\SuperAdminMutasiController::class, 'exportExcel'])->name('mutasi.export.excel');

    Route::get('/account-settings', [SchoolSettingController::class, 'index'])->name('school.settings');
    Route::post('/account-settings', [SchoolSettingController::class, 'update'])->name('school.settings.update');
});

// ================================================================
// ROUTE UNTUK LIHAT STATUS LISENSI (HANYA UNTUK USER LOGIN)
// ================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/license-status', function () {
        $user = Auth::user();
        $license = $user->license;
        
        return view('license.status', compact('user', 'license'));
    })->name('license.status');
});