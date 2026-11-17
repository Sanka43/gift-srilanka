<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private const LOW_STOCK_THRESHOLD = 5;

    public function index()
    {
        $ordersCount = Order::count();
        $pendingOrdersCount = Order::where('status', Order::STATUS_PENDING)->count();
        $totalRevenue = (float) Order::where('status', Order::STATUS_DELIVERED)->sum('total_amount');
        $lowStockProducts = Product::where('stock', '<=', self::LOW_STOCK_THRESHOLD)->where('status', true)->count();

        // Previous period for growth (e.g. last 30 days vs prior 30 days)
        $lastMonthOrders = Order::where('created_at', '>=', now()->subDays(30))->count();
        $priorMonthOrders = Order::whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->count();
        $ordersGrowth = $priorMonthOrders > 0 ? round((($lastMonthOrders - $priorMonthOrders) / $priorMonthOrders) * 100, 1) : 0;

        $lastMonthRevenue = (float) Order::where('status', Order::STATUS_DELIVERED)->where('updated_at', '>=', now()->subDays(30))->sum('total_amount');
        $priorMonthRevenue = (float) Order::where('status', Order::STATUS_DELIVERED)->whereBetween('updated_at', [now()->subDays(60), now()->subDays(30)])->sum('total_amount');
        $revenueGrowth = $priorMonthRevenue > 0 ? round((($lastMonthRevenue - $priorMonthRevenue) / $priorMonthRevenue) * 100, 1) : 0;

        $recentOrders = Order::with('user')->latest()->take(8)->get();
        $lowStockProductsList = Product::with('category')->where('stock', '<=', self::LOW_STOCK_THRESHOLD)->where('status', true)->orderBy('stock')->take(10)->get();

        // Monthly revenue for last 6 months (delivered orders) – SQL compatible with MySQL and SQLite
        $monthExpression = DB::getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', updated_at)"
            : "DATE_FORMAT(updated_at, '%Y-%m')";
        $revenueByMonth = Order::query()
            ->where('status', Order::STATUS_DELIVERED)
            ->selectRaw("{$monthExpression} as month, SUM(total_amount) as total")
            ->where('updated_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Fill in missing months with 0 and ensure all values are numeric
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->put(now()->subMonths($i)->format('Y-m'), 0);
        }
        $revenueByMonth = $months->merge($revenueByMonth)->map(function ($value) {
            return (float) ($value ?? 0);
        })->toArray();

        return view('admin.dashboard', compact(
            'ordersCount',
            'pendingOrdersCount',
            'totalRevenue',
            'lowStockProducts',
            'ordersGrowth',
            'revenueGrowth',
            'recentOrders',
            'lowStockProductsList',
            'revenueByMonth'
        ));
    }
}
