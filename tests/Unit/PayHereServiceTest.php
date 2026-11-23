<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Services\PayHereService;
use PHPUnit\Framework\TestCase;

class PayHereServiceTest extends TestCase
{
    private string $merchantId = '1234567';
    private string $merchantSecret = 'secret_key_sample';

    public function test_generates_correct_checkout_hash(): void
    {
        $service = new PayHereService(
            merchantId: $this->merchantId,
            merchantSecret: $this->merchantSecret,
            isSandbox: true,
            currency: 'LKR'
        );

        $orderId = '1001';
        $amount = 2500.50;
        $currency = 'LKR';

        // Calculation: strtoupper(md5($merchantId . $orderId . number_format($amount, 2, '.', '') . $currency . strtoupper(md5($merchantSecret))))
        $expectedSecretHash = strtoupper(md5($this->merchantSecret));
        $expectedHash = strtoupper(md5("{$this->merchantId}{$orderId}2500.50{$currency}{$expectedSecretHash}"));

        $hash = $service->generateCheckoutHash($orderId, $amount, $currency);

        $this->assertEquals($expectedHash, $hash);
    }

    public function test_verifies_valid_ipn_signature(): void
    {
        $service = new PayHereService(
            merchantId: $this->merchantId,
            merchantSecret: $this->merchantSecret,
            isSandbox: true,
            currency: 'LKR'
        );

        $orderId = '1001';
        $payhereAmount = '2500.50';
        $payhereCurrency = 'LKR';
        $statusCode = '2';

        $secretHash = strtoupper(md5($this->merchantSecret));
        $validMd5Sig = strtoupper(md5("{$this->merchantId}{$orderId}{$payhereAmount}{$payhereCurrency}{$statusCode}{$secretHash}"));

        $payload = [
            'merchant_id' => $this->merchantId,
            'order_id' => $orderId,
            'payment_id' => '320025112233',
            'payhere_amount' => $payhereAmount,
            'payhere_currency' => $payhereCurrency,
            'status_code' => $statusCode,
            'md5sig' => $validMd5Sig,
        ];

        $this->assertTrue($service->verifyNotification($payload));
    }

    public function test_rejects_tampered_ipn_signature(): void
    {
        $service = new PayHereService(
            merchantId: $this->merchantId,
            merchantSecret: $this->merchantSecret,
            isSandbox: true,
            currency: 'LKR'
        );

        $payload = [
            'merchant_id' => $this->merchantId,
            'order_id' => '1001',
            'payment_id' => '320025112233',
            'payhere_amount' => '100.00', // tampered amount
            'payhere_currency' => 'LKR',
            'status_code' => '2',
            'md5sig' => 'INVALID_SIGNATURE_HASH',
        ];

        $this->assertFalse($service->verifyNotification($payload));
    }

    public function test_switches_endpoints_between_sandbox_and_live(): void
    {
        $sandboxService = new PayHereService(
            merchantId: $this->merchantId,
            merchantSecret: $this->merchantSecret,
            isSandbox: true
        );
        $this->assertEquals(PayHereService::SANDBOX_URL, $sandboxService->getCheckoutUrl());

        $liveService = new PayHereService(
            merchantId: $this->merchantId,
            merchantSecret: $this->merchantSecret,
            isSandbox: false
        );
        $this->assertEquals(PayHereService::LIVE_URL, $liveService->getCheckoutUrl());
    }
}
