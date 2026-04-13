<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\LicenseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/test', function() {
    return response()->json(['message' => 'API is working!']);
});

Route::prefix('license')->group(function () {
    // Public endpoints
    Route::post('/check', [LicenseController::class, 'checkLicense']);
    Route::post('/buy', [LicenseController::class, 'buyLicense']);
    Route::post('/payment-callback', [LicenseController::class, 'paymentCallback']);
    
    // Protected endpoints (require API auth)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/history/{email}', [LicenseController::class, 'licenseHistory']);
        Route::get('/my-status', [LicenseController::class, 'myStatus']);
    });
});

Route::post('/midtrans/callback', [App\Http\Controllers\MidtransWebhookController::class, 'handle'])
    ->name('midtrans.callback');
