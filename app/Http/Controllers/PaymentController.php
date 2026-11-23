<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PayHereService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private PayHereService $payHereService,
        private PaymentService $paymentService
    ) {}

    /**
     * Show redirect view to forward user to PayHere payment gateway.
     */
    public function pay(Order $order): View|RedirectResponse
    {
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403, 'You do not have access to this order.');
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.confirmation', $order)
                ->with('info', 'This order has already been paid.');
        }

        if ($order->status === Order::STATUS_CANCELLED) {
            return redirect()->route('customer.orders.show', $order)
                ->with('error', 'This order has been cancelled.');
        }

        $checkoutUrl = $this->payHereService->getCheckoutUrl();
        $payload = $this->payHereService->generatePaymentData($order);

        return view('checkout.payhere_redirect', compact('order', 'checkoutUrl', 'payload'));
    }

    /**
     * Handle PayHere Server-to-Server IPN (Instant Payment Notification).
     */
    public function notify(Request $request): Response
    {
        Log::info('PayHere IPN Received', ['payload' => $request->all()]);

        if (! $this->payHereService->verifyNotification($request->all())) {
            Log::warning('PayHere IPN signature verification failed', [
                'order_id' => $request->input('order_id'),
                'ip' => $request->ip(),
            ]);
            return response('Invalid signature', 400);
        }

        $orderId = (int) $request->input('order_id');
        $order = Order::find($orderId);

        if (! $order) {
            Log::error('PayHere IPN: Order not found', ['order_id' => $orderId]);
            return response('Order not found', 404);
        }

        $statusCode = (int) $request->input('status_code');
        $paymentId = (string) $request->input('payment_id');
        $payhereAmount = (float) $request->input('payhere_amount');

        // Check amount tolerance (within 1 cent)
        $expectedAmount = (float) $order->total_amount;
        if (abs($expectedAmount - $payhereAmount) > 0.05) {
            Log::error('PayHere IPN: Amount mismatch', [
                'order_id' => $order->id,
                'expected' => $expectedAmount,
                'received' => $payhereAmount,
            ]);
            return response('Amount mismatch', 400);
        }

        if ($statusCode === PayHereService::STATUS_SUCCESS) {
            if ($order->payment_status !== 'paid') {
                $meta = [
                    'payment_id' => $paymentId,
                    'method' => $request->input('method'),
                    'card_holder_name' => $request->input('card_holder_name'),
                    'card_no' => $request->input('card_no'),
                    'status_message' => $request->input('status_message'),
                    'ipn_time' => now()->toIso8601String(),
                ];

                $this->paymentService->markAsPaid($order, $paymentId, 'payhere', $meta);

                Log::info('PayHere IPN: Order marked as paid successfully', [
                    'order_id' => $order->id,
                    'payment_id' => $paymentId,
                ]);
            }
        } elseif (in_array($statusCode, [PayHereService::STATUS_FAILED, PayHereService::STATUS_CANCELED], true)) {
            $reason = (string) ($request->input('status_message') ?? 'Payment not completed');
            $this->paymentService->markAsFailed($order, $reason, $request->all());

            Log::warning('PayHere IPN: Payment failed/cancelled for order', [
                'order_id' => $order->id,
                'status_code' => $statusCode,
                'reason' => $reason,
            ]);
        }

        return response('OK', 200);
    }

    /**
     * Handle user redirect back from PayHere after payment attempt.
     */
    public function return(Order $order): RedirectResponse
    {
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403, 'You do not have access to this order.');
        }

        $order->refresh();

        // On localhost in Sandbox mode, PayHere cloud servers cannot ping http://localhost IPN directly.
        // Auto-complete payment on return so you can test the full end-to-end UX locally.
        if ($order->payment_status !== 'paid' && app()->isLocal() && config('shop.payhere.sandbox')) {
            $sandboxTxId = 'SANDBOX_' . strtoupper(substr(md5((string) microtime()), 0, 10));
            $this->paymentService->markAsPaid(
                $order,
                $sandboxTxId,
                'payhere',
                [
                    'method' => 'TEST_CARD',
                    'note' => 'Sandbox local payment simulation verified on return',
                ]
            );
            $order->refresh();
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.confirmation', $order)
                ->with('success', 'Payment successful! Your order has been placed.');
        }

        return redirect()->route('checkout.confirmation', $order)
            ->with('info', 'Your payment is being processed. We will update the status shortly.');
    }

    /**
     * Handle user cancelling payment on PayHere gateway.
     */
    public function cancel(Order $order): RedirectResponse
    {
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403, 'You do not have access to this order.');
        }

        return redirect()->route('checkout.index')
            ->with('warning', 'Payment was cancelled. You may review your order and try again.');
    }
}
