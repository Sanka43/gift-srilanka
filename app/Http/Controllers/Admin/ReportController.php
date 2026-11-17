<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->input('from', now()->subDays(30)->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));

        $revenue = (float) Order::where('status', Order::STATUS_DELIVERED)
            ->whereBetween('updated_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->sum('total_amount');

        $ordersByStatus = Order::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $topProducts = Product::query()
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', Order::STATUS_DELIVERED)
            ->whereBetween('orders.updated_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->selectRaw('products.id, products.name, sum(order_items.quantity) as sold')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sold')
            ->take(10)
            ->get();

        return view('admin.reports.index', compact('revenue', 'ordersByStatus', 'topProducts', 'from', 'to'));
    }
}
