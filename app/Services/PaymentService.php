<?php

namespace App\Services;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentService
{
    /**
     * Get initial payment status when creating an order.
     */
    public function getInitialPaymentStatus(string $paymentMethod): string
    {
        return $paymentMethod === 'online' ? 'pending' : 'cod';
    }

    /**
     * Mark order as paid (e.g. after gateway confirmation).
     */
    public function markAsPaid(Order $order, ?string $transactionId = null, string $gateway = 'payhere', ?array $meta = null): void
    {
        DB::transaction(function () use ($order, $transactionId, $gateway, $meta) {
            $updateData = [
                'payment_status' => 'paid',
                'payment_gateway' => $gateway,
            ];

            if ($transactionId) {
                $updateData['transaction_id'] = $transactionId;
            }

            if ($meta) {
                $updateData['payment_meta'] = $meta;
            }

            // If order was pending, advance status to processing upon successful payment
            if ($order->status === Order::STATUS_PENDING) {
                $updateData['status'] = Order::STATUS_PROCESSING;
            }

            $order->update($updateData);

            $order->statusHistories()->create([
                'status' => $order->status,
                'note' => "Payment successful via {$gateway}. Transaction ID: " . ($transactionId ?? 'N/A'),
            ]);
        });

        // Send order confirmation email
        $this->sendOrderConfirmationEmail($order);
    }

    /**
     * Mark order payment as failed.
     */
    public function markAsFailed(Order $order, string $reason, ?array $meta = null): void
    {
        $order->update([
            'payment_status' => 'failed',
            'payment_meta' => $meta,
        ]);

        $order->statusHistories()->create([
            'status' => $order->status,
            'note' => "Payment failed: {$reason}",
        ]);
    }

    /**
     * Safely dispatch order confirmation email.
     */
    public function sendOrderConfirmationEmail(Order $order): void
    {
        $email = $order->customer_email;
        if (! $email) {
            return;
        }

        try {
            Mail::to($email)->queue(new OrderConfirmationMail($order));
        } catch (\Throwable $e) {
            Log::warning('Order confirmation email queue failed', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);

            try {
                Mail::to($email)->send(new OrderConfirmationMail($order));
            } catch (\Throwable $sendException) {
                Log::error('Order confirmation email send failed', [
                    'order_id' => $order->id,
                    'message' => $sendException->getMessage(),
                ]);
            }
        }
    }
}
