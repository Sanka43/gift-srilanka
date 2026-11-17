<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}
    public function index(Request $request): View
    {
        $query = Order::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('id', 'like', "%{$term}%")
                    ->orWhere('full_name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
            });
        }

        $orders = $query->paginate(15)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function exportCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders-' . date('Y-m-d-His') . '.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order ID', 'Customer name', 'Total', 'Status', 'Created date']);

            Order::with('user')->orderBy('id')->chunk(100, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    $customerName = $order->user_id ? ($order->user?->name ?? $order->full_name) : ($order->guest_name ?? $order->full_name);
                    fputcsv($handle, [
                        $order->id,
                        $customerName,
                        $order->total_amount,
                        $order->status,
                        $order->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    public function show(Order $order): View
    {
        $order->load('items.product', 'user');
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order): View
    {
        return view('admin.orders.edit', compact('order'));
    }

    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        $validated = $request->validated();

        if ($validated['status'] === Order::STATUS_CANCELLED && $order->status !== Order::STATUS_CANCELLED) {
            $this->orderService->cancelOrder($order, 'Order updated to cancelled by admin.');
            $order->update(collect($validated)->except('status')->all());
            return redirect()->route('admin.orders.show', $order)->with('success', 'Order updated to cancelled and stock restored.');
        }

        $order->update($validated);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $validated['status'],
            'note' => null,
        ]);
        if (strtolower($validated['status']) === 'shipped') {
            $order->update(['estimated_delivery_date' => Carbon::today()->addDays(3)]);
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order updated.');
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $validated = $request->validated();

        if ($validated['status'] === Order::STATUS_CANCELLED && $order->status !== Order::STATUS_CANCELLED) {
            $this->orderService->cancelOrder($order, 'Order status changed to cancelled by admin.');
            return redirect()->route('admin.orders.index')->with('success', 'Order #' . $order->id . ' status updated to cancelled and stock restored.');
        }

        $order->update(['status' => $validated['status']]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $validated['status'],
            'note' => null,
        ]);
        if (strtolower($validated['status']) === 'shipped') {
            $order->update(['estimated_delivery_date' => Carbon::today()->addDays(3)]);
        }

        return redirect()->route('admin.orders.index')->with('success', 'Order #' . $order->id . ' status updated to ' . $validated['status'] . '.');
    }

    public function approveCancel(Order $order): RedirectResponse
    {
        if (!$order->cancel_requested) {
            return redirect()->route('admin.orders.show', $order)->with('error', 'No cancellation request for this order.');
        }

        $this->orderService->cancelOrder($order, 'Customer cancellation request approved by admin.');

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order cancellation approved and stock restored.');
    }

    public function rejectCancel(Order $order): RedirectResponse
    {
        if (!$order->cancel_requested) {
            return redirect()->route('admin.orders.show', $order)->with('error', 'No cancellation request for this order.');
        }
        $order->update(['cancel_requested' => false]);
        return redirect()->route('admin.orders.show', $order)->with('success', 'Cancellation request rejected.');
    }
}
