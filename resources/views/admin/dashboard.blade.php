@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    {{-- Page title --}}
    <div>
        <h1 class="font-display text-2xl font-bold text-stone-800">Dashboard</h1>
        <p class="mt-1 text-sm text-stone-500">Overview of your store performance.</p>
    </div>

    {{-- 4 Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="admin-card bg-white rounded-[18px] border border-stone-100 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-stone-500">Total Orders</p>
                    <p class="mt-1 text-2xl font-bold text-stone-800">{{ number_format($ordersCount) }}</p>
                    @if(isset($ordersGrowth))
                        <p class="mt-1.5 flex items-center gap-1 text-xs font-medium {{ $ordersGrowth >= 0 ? 'text-primary-600' : 'text-danger-500' }}">
                            @if($ordersGrowth >= 0)<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>@else<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>@endif
                            {{ $ordersGrowth >= 0 ? '+' : '' }}{{ $ordersGrowth }}% vs last period
                        </p>
                    @endif
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 text-primary-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </span>
            </div>
        </div>

        <div class="admin-card bg-white rounded-[18px] border border-stone-100 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-stone-500">Total Revenue</p>
                    <p class="mt-1 text-2xl font-bold text-stone-800">LKR {{ number_format($totalRevenue, 0) }}</p>
                    @if(isset($revenueGrowth))
                        <p class="mt-1.5 flex items-center gap-1 text-xs font-medium {{ $revenueGrowth >= 0 ? 'text-primary-600' : 'text-danger-500' }}">
                            @if($revenueGrowth >= 0)<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>@else<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>@endif
                            {{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}% vs last period
                        </p>
                    @endif
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-warning-100 text-warning-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
        </div>

        <div class="admin-card bg-white rounded-[18px] border border-stone-100 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-stone-500">Pending Orders</p>
                    <p class="mt-1 text-2xl font-bold text-stone-800">{{ number_format($pendingOrdersCount) }}</p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
        </div>

        <div class="admin-card bg-white rounded-[18px] border border-stone-100 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-stone-500">Low Stock Products</p>
                    <p class="mt-1 text-2xl font-bold text-stone-800">{{ $lowStockProducts }}</p>
                </div>
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-danger-100 text-danger-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
            </div>
        </div>
    </div>

    {{-- Revenue chart + Recent orders row --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Revenue chart --}}
        <div class="admin-card xl:col-span-2 rounded-[18px] border border-stone-100 bg-white p-6">
            <h2 class="font-display text-lg font-semibold text-stone-800">Monthly Revenue</h2>
            <p class="mt-0.5 text-sm text-stone-500">Last 6 months (delivered orders)</p>
            @php
                $monthsData = $revenueByMonth ?? [];
                $revenueValues = array_values($monthsData);
                $maxRevenue = $revenueValues ? max(1, (float) max($revenueValues)) : 1;
                $count = count($monthsData);
            @endphp
            <div class="mt-6" style="min-height: 220px;">
                <svg viewBox="0 0 500 200" class="w-full h-52" preserveAspectRatio="xMidYMid meet">
                    @if($count >= 2)
                        @php
                            $points = [];
                            $idx = 0;
                            foreach ($monthsData as $total) {
                                $val = (float) ($total ?? 0);
                                $x = 50 + ($count > 1 ? ($idx / ($count - 1)) * 400 : 200);
                                $y = 180 - max(2, ($val / $maxRevenue) * 160);
                                $points[] = $x . ',' . $y;
                                $idx++;
                            }
                        @endphp
                        <polyline points="{{ implode(' ', $points) }}" fill="none" stroke="rgb(34, 197, 94)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    @endif
                    @foreach($monthsData as $total)
                        @php $val = (float) ($total ?? 0); $x = 50 + ($count > 1 ? ($loop->index / ($count - 1)) * 400 : 200); $y = 180 - max(2, ($val / $maxRevenue) * 160); @endphp
                        <circle cx="{{ $x }}" cy="{{ $y }}" r="5" fill="rgb(34, 197, 94)" class="drop-shadow-sm"/>
                    @endforeach
                </svg>
                <div class="flex justify-between gap-2 mt-2 px-1">
                    @foreach($monthsData as $month => $total)
                        <span class="text-xs font-medium text-stone-500">{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M') }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Low stock alert --}}
        <div class="admin-card rounded-[18px] border border-stone-100 bg-white p-6">
            <h2 class="font-display text-lg font-semibold text-stone-800">Low Stock Alert</h2>
            <p class="mt-0.5 text-sm text-stone-500">Products at or below threshold</p>
            <ul class="mt-4 space-y-2 max-h-64 overflow-y-auto">
                @forelse($lowStockProductsList ?? [] as $p)
                    <li class="flex items-center justify-between rounded-xl border border-stone-100 bg-stone-50/50 px-3 py-2.5">
                        <a href="{{ route('admin.products.edit', $p) }}" class="truncate text-sm font-medium text-stone-800 hover:text-primary-600">{{ $p->name }}</a>
                        <span class="ml-2 flex-shrink-0 rounded-lg px-2 py-0.5 text-xs font-semibold {{ $p->stock < 1 ? 'bg-danger-100 text-danger-700' : 'bg-warning-100 text-warning-700' }}">{{ $p->stock }}</span>
                    </li>
                @empty
                    <li class="rounded-xl border border-stone-100 bg-stone-50/50 px-3 py-4 text-center text-sm text-stone-500">No low stock products</li>
                @endforelse
            </ul>
            @if(($lowStockProductsList ?? collect())->isNotEmpty())
                <a href="{{ route('admin.products.index') }}?low_stock=1" class="mt-3 inline-block text-sm font-medium text-primary-600 hover:text-primary-700">View all products →</a>
            @endif
        </div>
    </div>

    {{-- Recent orders table --}}
    <div class="admin-card overflow-hidden rounded-[18px] border border-stone-100 bg-white">
        <div class="border-b border-stone-100 px-6 py-4">
            <h2 class="font-display text-lg font-semibold text-stone-800">Recent Orders</h2>
            <p class="mt-0.5 text-sm text-stone-500">Latest 8 orders</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200">
                <thead class="bg-stone-50/80">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-stone-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse($recentOrders ?? [] as $order)
                        <tr class="hover:bg-stone-50/50 transition-colors">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-stone-800">#{{ $order->id }}</td>
                            <td class="px-6 py-4 text-sm text-stone-600">{{ $order->full_name ?? $order->user?->name ?? '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-stone-500">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-stone-800">LKR {{ number_format($order->total_amount, 0) }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-amber-100 text-amber-800',
                                        'processing' => 'bg-blue-100 text-blue-800',
                                        'shipped' => 'bg-primary-100 text-primary-700',
                                        'delivered' => 'bg-primary-100 text-primary-700',
                                        'cancelled' => 'bg-stone-100 text-stone-600',
                                    ];
                                @endphp
                                <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$order->status] ?? 'bg-stone-100 text-stone-600' }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-primary-600 hover:text-primary-700">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-stone-500">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 px-6 py-3 bg-stone-50/30">
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">View all orders →</a>
        </div>
    </div>
</div>
@endsection
