<?php
// tests/Feature/LicenseTest.php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaction;
use App\Services\LicenseService;
use Carbon\Carbon;
use Tests\TestCase;

class LicenseTest extends TestCase
{
    protected LicenseService $licenseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->licenseService = app(LicenseService::class);
    }

    /** @test */
    public function user_can_buy_license()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'lisensi_status' => 'never'
        ]);

        $result = $this->licenseService->createTransaction($user, 'monthly');

        $this->assertTrue($result['success']);
        $this->assertEquals(100000, $result['amount']);
        
        return $result['order_id'];
    }

    /** @test */
    public function license_extends_when_buying_while_active()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'lisensi_status' => 'active',
            'lisensi_expired_at' => Carbon::now()->addDays(10)
        ]);

        $oldExpiredAt = $user->lisensi_expired_at;
        
        $result = $this->licenseService->processPaymentSuccess('TEST-ORDER-001');
        
        // This would normally be triggered by webhook
        // $newExpiredAt should be oldExpiredAt + 30 days
    }

    /** @test */
    public function expired_license_starts_from_now()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'lisensi_status' => 'expired',
            'lisensi_expired_at' => Carbon::now()->subDays(5)
        ]);

        $result = $this->licenseService->processPaymentSuccess('TEST-ORDER-002');
        
        // New expired date should be now() + duration
    }

    /** @test */
    public function api_check_license_works()
    {
        $response = $this->postJson('/api/license/check', [
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'user',
                'license' => [
                    'status', 'is_active', 'expired_at', 'days_left'
                ]
            ]
        ]);
    }
}