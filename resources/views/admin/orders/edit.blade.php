@extends('layouts.admin')

@section('title', 'Edit Order #' . $order->id)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.orders.show', $order) }}" class="text-sm text-stone-500 dark:text-stone-400 hover:text-stone-700 dark:hover:text-stone-200">← Back to Order</a>
</div>
<div class="max-w-xl">
    <h2 class="text-xl font-bold text-stone-800 dark:text-stone-100 mb-6">Edit Order #{{ $order->id }}</h2>
    <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div class="admin-card bg-white dark:bg-stone-800/90 rounded-xl border border-stone-200 dark:border-stone-700 p-6 space-y-4">
            <div>
                <label for="status" class="block text-sm font-medium text-stone-700 dark:text-stone-200 mb-1">Order status</label>
                <select name="status" id="status" class="admin-select w-full rounded-lg border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 text-stone-800 dark:text-stone-100 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    @foreach(\App\Models\Order::STATUSES as $s)
                        <option value="{{ $s }}" {{ old('status', $order->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                @error('status')<p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="payment_status" class="block text-sm font-medium text-stone-700 dark:text-stone-200 mb-1">Payment status</label>
                <select name="payment_status" id="payment_status" class="admin-select w-full rounded-lg border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 text-stone-800 dark:text-stone-100 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="pending" {{ old('payment_status', $order->payment_status) === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ old('payment_status', $order->payment_status) === 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
                @error('payment_status')<p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Save changes</button>
            <a href="{{ route('admin.orders.show', $order) }}" class="px-4 py-2 border border-stone-300 dark:border-stone-600 rounded-lg text-stone-700 dark:text-stone-200 hover:bg-stone-50 dark:hover:bg-stone-700/50">Cancel</a>
        </div>
    </form>
</div>
@endsection
