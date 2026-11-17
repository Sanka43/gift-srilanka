<?php

namespace App\Services;

use App\Models\Order;

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
    public function markAsPaid(Order $order): void
    {
        $order->update(['payment_status' => 'paid']);
    }
}
