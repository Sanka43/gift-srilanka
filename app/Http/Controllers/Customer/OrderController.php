<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Show logged-in customer all orders (paginate 10).
     */
    public function index()
    {
        $orders = auth()->user()->orders()->with('items')->latest()->paginate(10);
        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Show order details and items for the given order (only own orders).
     */
    public function show(Order $order)
    {
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403, 'You do not have access to this order.');
        }
        $order->load('items.product', 'statusHistories');
        return view('customer.orders.show', compact('order'));
    }

    public function requestCancel(Order $order)
    {
        if ((int) $order->user_id !== (int) auth()->id()) {
            abort(403, 'You do not have access to this order.');
        }
        if ($order->status !== Order::STATUS_PENDING) {
            return redirect()->route('customer.orders.show', $order)->with('error', 'Only pending orders can be requested for cancellation.');
        }
        if ($order->cancel_requested) {
            return redirect()->route('customer.orders.show', $order)->with('info', 'Cancellation has already been requested.');
        }
        $order->update(['cancel_requested' => true]);
        return redirect()->route('customer.orders.show', $order)->with('success', 'Cancellation requested. We will process it shortly.');
    }
}
