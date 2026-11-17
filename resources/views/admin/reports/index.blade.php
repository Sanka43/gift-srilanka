@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="font-display text-2xl font-bold text-stone-800">Reports</h1>
        <p class="mt-1 text-sm text-stone-500">Revenue and order insights by date range.</p>
    </div>

    <form action="{{ route('admin.reports.index') }}" method="GET" class="admin-card flex flex-wrap items-center gap-3 rounded-2xl border border-stone-100 bg-white p-4">
        <label class="text-sm font-medium text-stone-700">From</label>
        <input type="date" name="from" value="{{ $from }}" class="admin-input rounded-xl border border-stone-200 py-2 px-4 text-sm focus:border-primary-400 focus:outline-none">
        <label class="text-sm font-medium text-stone-700">To</label>
        <input type="date" name="to" value="{{ $to }}" class="admin-input rounded-xl border border-stone-200 py-2 px-4 text-sm focus:border-primary-400 focus:outline-none">
        <button type="submit" class="rounded-xl bg-primary-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-600 transition">Apply</button>
    </form>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="admin-card rounded-2xl border border-stone-100 bg-white p-6">
            <h2 class="font-display text-lg font-semibold text-stone-800">Revenue summary</h2>
            <p class="mt-1 text-sm text-stone-500">Delivered orders in selected period</p>
            <p class="mt-4 text-3xl font-bold text-primary-600">LKR {{ number_format($revenue, 0) }}</p>
        </div>
        <div class="admin-card rounded-2xl border border-stone-100 bg-white p-6">
            <h2 class="font-display text-lg font-semibold text-stone-800">Orders by status</h2>
            <p class="mt-1 text-sm text-stone-500">In selected period</p>
            <ul class="mt-4 space-y-2">
                @foreach(\App\Models\Order::STATUSES as $status)
                    <li class="flex justify-between text-sm">
                        <span class="text-stone-600">{{ ucfirst($status) }}</span>
                        <span class="font-semibold text-stone-800">{{ $ordersByStatus[$status] ?? 0 }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="admin-card rounded-2xl border border-stone-100 bg-white p-6">
        <h2 class="font-display text-lg font-semibold text-stone-800">Top selling products</h2>
        <p class="mt-1 text-sm text-stone-500">By quantity sold (delivered orders)</p>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200">
                <thead class="bg-stone-50/80">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Product</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-stone-500">Units sold</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200">
                    @forelse($topProducts as $p)
                        <tr>
                            <td class="px-6 py-3 text-sm font-medium text-stone-800">{{ $p->name }}</td>
                            <td class="px-6 py-3 text-right text-sm font-semibold text-stone-800">{{ $p->sold }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-8 text-center text-stone-500">No data in this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
