<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PayHereService
{
    public const SANDBOX_URL = 'https://sandbox.payhere.lk/pay/checkout';
    public const LIVE_URL = 'https://www.payhere.lk/pay/checkout';

    public const STATUS_SUCCESS = 2;
    public const STATUS_PENDING = 0;
    public const STATUS_CANCELED = -1;
    public const STATUS_FAILED = -2;
    public const STATUS_CHARGEDBACK = -3;

    public function __construct(
        private ?string $merchantId = null,
        private ?string $merchantSecret = null,
        private ?bool $isSandbox = null,
        private ?string $currency = null,
    ) {
        $this->merchantId = $merchantId ?? (string) config('shop.payhere.merchant_id', '');
        $this->merchantSecret = $merchantSecret ?? (string) config('shop.payhere.merchant_secret', '');
        $this->isSandbox = $isSandbox ?? (bool) config('shop.payhere.sandbox', false);
        $this->currency = $currency ?? (string) config('shop.payhere.currency', 'LKR');
    }

    public function isConfigured(): bool
    {
        return $this->merchantId !== '' && $this->merchantSecret !== '';
    }

    public function getCheckoutUrl(): string
    {
        return $this->isSandbox ? self::SANDBOX_URL : self::LIVE_URL;
    }

    public function isSandbox(): bool
    {
        return (bool) $this->isSandbox;
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Generate MD5 hash for PayHere checkout redirect.
     */
    public function generateCheckoutHash(string|int $orderId, float $amount, ?string $currency = null): string
    {
        $curr = $currency ?? $this->currency;
        $formattedAmount = number_format($amount, 2, '.', '');
        $secretHash = strtoupper(md5($this->merchantSecret));

        return strtoupper(
            md5($this->merchantId . $orderId . $formattedAmount . $curr . $secretHash)
        );
    }

    /**
     * Generate the complete payload for PayHere checkout form.
     */
    public function generatePaymentData(Order $order): array
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('PayHere merchant credentials are not configured.');
        }

        $amount = (float) $order->total_amount;
        $formattedAmount = number_format($amount, 2, '.', '');
        $hash = $this->generateCheckoutHash($order->id, $amount, $this->currency);

        $nameParts = explode(' ', trim($order->customer_name ?: $order->full_name), 2);
        $firstName = trim($nameParts[0] ?: 'Customer');
        $lastName = trim($nameParts[1] ?? 'Customer');

        $itemsSummary = "Order #{$order->id} from " . config('app.name', 'Gifts Sri Lanka');

        $cleanAddress = trim(preg_replace('/\s+/', ' ', (string) ($order->address ?: 'Colombo')));
        $cleanCity = trim(preg_replace('/\s+/', ' ', (string) ($order->district ?: 'Colombo')));
        $cleanPhone = trim(preg_replace('/[^0-9+]/', '', (string) ($order->phone ?: '0770000000')));
        $cleanEmail = trim((string) ($order->customer_email ?: (auth()->user()?->email ?? 'customer@example.com')));

        return [
            'merchant_id' => $this->merchantId,
            'return_url' => route('checkout.payment.return', $order),
            'cancel_url' => route('checkout.payment.cancel', $order),
            'notify_url' => route('payment.payhere.notify'),
            'order_id' => (string) $order->id,
            'items' => $itemsSummary,
            'currency' => $this->currency,
            'amount' => $formattedAmount,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $cleanEmail,
            'phone' => $cleanPhone,
            'address' => $cleanAddress,
            'city' => $cleanCity,
            'country' => 'Sri Lanka',
            'hash' => $hash,
        ];
    }

    /**
     * Verify PayHere server-to-server IPN MD5 signature.
     */
    public function verifyNotification(array $payload): bool
    {
        // Never verify against an empty secret: md5('') is guessable, so IPNs could be forged.
        if (! $this->isConfigured()) {
            Log::error('PayHere IPN rejected: merchant credentials are not configured.');
            return false;
        }

        $merchantId = (string) ($payload['merchant_id'] ?? '');
        $orderId = (string) ($payload['order_id'] ?? '');
        $payhereAmount = (string) ($payload['payhere_amount'] ?? '');
        $payhereCurrency = (string) ($payload['payhere_currency'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $receivedMd5Sig = (string) ($payload['md5sig'] ?? '');

        if ($merchantId === '' || $orderId === '' || $receivedMd5Sig === '') {
            Log::warning('PayHere IPN verification failed: missing required fields', [
                'order_id' => $orderId,
            ]);
            return false;
        }

        if ($merchantId !== $this->merchantId) {
            Log::warning('PayHere IPN verification failed: merchant_id mismatch', [
                'expected' => $this->merchantId,
                'received' => $merchantId,
            ]);
            return false;
        }

        $secretHash = strtoupper(md5($this->merchantSecret));
        $localMd5Sig = strtoupper(
            md5($merchantId . $orderId . $payhereAmount . $payhereCurrency . $statusCode . $secretHash)
        );

        $matches = hash_equals($localMd5Sig, strtoupper($receivedMd5Sig));

        if (! $matches) {
            Log::warning('PayHere IPN verification failed: signature mismatch', [
                'order_id' => $orderId,
            ]);
        }

        return $matches;
    }
}
