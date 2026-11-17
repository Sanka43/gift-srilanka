@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between max-md:gap-3">
        <div>
            <h1 class="font-display text-2xl max-md:text-xl font-bold text-stone-800">Products</h1>
            <p class="mt-1 text-sm text-stone-500">Manage your product catalog.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-600 transition">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Product
        </a>
    </div>

    <div class="admin-card rounded-2xl border border-stone-100 bg-white p-5 max-md:rounded-xl max-md:p-4">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1.3fr)_12rem_12rem_auto]">
            <div>
                <label for="search" class="mb-1.5 block text-sm font-medium text-stone-700">Search</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by product name or slug"
                    class="admin-input w-full rounded-xl border border-stone-200 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none"
                >
            </div>
            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-stone-700">Status</label>
                <select id="status" name="status" class="admin-input w-full rounded-xl border border-stone-200 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                    <option value="">All statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <label for="min_rating" class="mb-1.5 block text-sm font-medium text-stone-700">Minimum rating</label>
                <select id="min_rating" name="min_rating" class="admin-input w-full rounded-xl border border-stone-200 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none">
                    <option value="">Any rating</option>
                    @foreach([5, 4, 3, 2, 1] as $rating)
                        <option value="{{ $rating }}" {{ (string) request('min_rating') === (string) $rating ? 'selected' : '' }}>
                            {{ $rating }} stars & up
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-wrap items-end gap-3">
                <label class="flex min-h-[44px] items-center gap-2 rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm font-medium text-stone-700">
                    <input type="checkbox" name="low_stock" value="1" {{ request()->boolean('low_stock') ? 'checked' : '' }} class="rounded border-stone-300 text-primary-600 focus:ring-primary-500">
                    Low stock only
                </label>
                <button type="submit" class="min-h-[44px] rounded-xl bg-primary-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600">Apply</button>
                @if(request()->hasAny(['search', 'status', 'min_rating', 'low_stock']))
                    <a href="{{ route('admin.products.index') }}" class="min-h-[44px] rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="admin-card overflow-hidden rounded-2xl border border-stone-100 bg-white max-md:rounded-xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200">
                <thead class="bg-stone-50/80">
                    <tr>
                        <th class="px-6 py-3 max-md:px-4 max-md:py-2.5 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Product</th>
                        <th class="px-6 py-3 max-md:px-4 max-md:py-2.5 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Category</th>
                        <th class="px-6 py-3 max-md:px-4 max-md:py-2.5 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Price</th>
                        <th class="px-6 py-3 max-md:px-4 max-md:py-2.5 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Rating</th>
                        <th class="px-6 py-3 max-md:px-4 max-md:py-2.5 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Stock</th>
                        <th class="px-6 py-3 max-md:px-4 max-md:py-2.5 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Status</th>
                        <th class="px-6 py-3 max-md:px-4 max-md:py-2.5 text-right text-xs font-medium uppercase tracking-wider text-stone-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse($products as $product)
                        <tr class="hover:bg-stone-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($product->primaryImage?->image_path)
                                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="" class="h-12 w-12 rounded-xl object-cover border border-stone-100">
                                    @else
                                        <img src="{{ asset('images/logo.webp') . '?v=' . config('app.logo_version', 2) }}" alt="" class="h-12 w-12 rounded-xl object-contain opacity-40 grayscale p-1.5 border border-stone-100" aria-hidden="true">
                                    @endif
                                    <span class="font-medium text-stone-800">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-stone-600">{{ $product->category?->name ?? '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-stone-800">LKR {{ number_format($product->price, 0) }}</td>
                            <td class="px-6 py-4">
                                @if($product->rating !== null)
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-stone-800">{{ number_format((float) $product->rating, 1) }}</span>
                                        <span class="text-xs text-amber-500">★</span>
                                        <span class="text-xs text-stone-500">({{ $product->reviews_count }})</span>
                                    </div>
                                @else
                                    <span class="text-sm text-stone-400">No rating</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium {{ $product->stock <= 5 ? 'text-danger-600' : 'text-stone-600' }}">{{ $product->stock }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-semibold {{ $product->status ? 'bg-primary-100 text-primary-700' : 'bg-stone-100 text-stone-600' }}">{{ $product->status ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                <a href="{{ route('admin.products.edit', $product) }}" class="font-medium text-primary-600 hover:text-primary-700">Edit</a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-danger-600 hover:text-danger-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-stone-500">No products matched your filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 px-6 py-3 max-md:px-4 max-md:py-2 bg-stone-50/30">{{ $products->links() }}</div>
    </div>
</div>
@endsection
