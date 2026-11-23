<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService
    ) {}

    public function index(Request $request): View
    {
        $query = Order::with('user')->latest();

        // Search
        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function ($q) use ($term) {
                $q->where('id', 'like', "%{$term}%")
                    ->orWhere('full_name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('transaction_id', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
            });
        }

        // Filter: Payment Method
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        // Filter: Payment Status
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        // Filter: Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        // Summary metrics
        $totalPaid = (float) Order::where('payment_status', 'paid')->sum('total_amount');
        $cardPaid = (float) Order::where('payment_method', 'online')->where('payment_status', 'paid')->sum('total_amount');
        $bankPaid = (float) Order::where('payment_method', 'cash_on_delivery')->where('payment_status', 'paid')->sum('total_amount');
        $pendingCount = Order::where('payment_status', 'pending')->count();
        $pendingAmount = (float) Order::where('payment_status', 'pending')->sum('total_amount');

        $payments = $query->paginate(20)->withQueryString();

        return view('admin.payments.index', compact(
            'payments',
            'totalPaid',
            'cardPaid',
            'bankPaid',
            'pendingCount',
            'pendingAmount'
        ));
    }

    /**
     * Update payment status manually (e.g. approve bank slip or manual verification).
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'payment_status' => ['required', 'in:paid,pending,failed'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $newStatus = $validated['payment_status'];
        $txId = $validated['transaction_id'] ?: $order->transaction_id;
        $note = $validated['note'] ?? "Payment status updated to {$newStatus} by admin.";

        if ($newStatus === 'paid') {
            $gateway = $order->payment_gateway ?: ($order->payment_method === 'online' ? 'payhere' : 'bank_transfer');
            $this->paymentService->markAsPaid(
                $order,
                $txId,
                $gateway,
                ['admin_id' => auth()->id(), 'manual_update' => true, 'note' => $note]
            );
        } elseif ($newStatus === 'failed') {
            $this->paymentService->markAsFailed($order, $note, ['admin_id' => auth()->id()]);
        } else {
            $order->update([
                'payment_status' => 'pending',
                'transaction_id' => $txId,
            ]);
            $order->statusHistories()->create([
                'status' => $order->status,
                'note' => $note,
            ]);
        }

        return redirect()->back()->with('success', "Payment status for Order #{$order->id} updated successfully.");
    }

    /**
     * Export payments list to CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments-' . date('Y-m-d-His') . '.csv"',
        ];

        return response()->stream(function () use ($request) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Order ID',
                'Customer Name',
                'Customer Phone',
                'Payment Method',
                'Gateway',
                'Transaction ID',
                'Amount (LKR)',
                'Payment Status',
                'Order Status',
                'Date',
            ]);

            $query = Order::with('user')->latest();

            if ($request->filled('method')) {
                $query->where('payment_method', $request->method);
            }
            if ($request->filled('status')) {
                $query->where('payment_status', $request->status);
            }

            $query->chunk(100, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    $customerName = $order->user_id ? ($order->user?->name ?? $order->full_name) : ($order->guest_name ?? $order->full_name);
                    fputcsv($handle, [
                        $order->id,
                        $customerName,
                        $order->phone,
                        $order->payment_method === 'online' ? 'Card / Online' : 'Bank Transfer',
                        $order->payment_gateway ?? ($order->payment_method === 'online' ? 'PayHere' : 'Bank'),
                        $order->transaction_id ?? 'N/A',
                        $order->total_amount,
                        ucfirst($order->payment_status),
                        ucfirst($order->status),
                        $order->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
