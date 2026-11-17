@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="font-display text-2xl font-bold text-stone-800">Customers</h1>
        <p class="mt-1 text-sm text-stone-500">Registered users (customers).</p>
    </div>

    <div class="admin-card overflow-hidden rounded-2xl border border-stone-100 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200">
                <thead class="bg-stone-50/80">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Total Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse($customers as $user)
                        <tr class="hover:bg-stone-50/50 transition-colors">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-stone-800">{{ $user->name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-stone-600">{{ $user->email }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-stone-600">{{ $user->phone ?? '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-stone-800">{{ $user->orders_count ?? 0 }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-stone-500">{{ $user->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-stone-500">No customers yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 px-6 py-3 bg-stone-50/30">{{ $customers->links() }}</div>
    </div>
</div>
@endsection
