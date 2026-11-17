@extends('layouts.admin')

@section('title', 'Stock Notifications')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="font-display text-2xl font-bold text-stone-800">Stock Notifications</h1>
        <p class="mt-1 text-sm text-stone-500">Customers to notify when products are back in stock.</p>
    </div>

    <div class="admin-card overflow-hidden rounded-2xl border border-stone-100 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200">
                <thead class="bg-stone-50/80">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Requested</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse($notifications as $n)
                        <tr class="hover:bg-stone-50/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-stone-800">
                                <a href="{{ route('admin.products.edit', $n->product) }}" class="hover:text-primary-600">{{ $n->product->name }}</a>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-stone-600">{{ $n->email }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-stone-500">{{ $n->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-stone-500">No pending notifications.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 px-6 py-3 bg-stone-50/30">{{ $notifications->links() }}</div>
    </div>
</div>
@endsection
