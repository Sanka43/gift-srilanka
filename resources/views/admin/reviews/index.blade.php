@extends('layouts.admin')

@section('title', 'Reviews')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="font-display text-2xl font-bold text-stone-800">Reviews</h1>
        <p class="mt-1 text-sm text-stone-500">Customer product reviews.</p>
    </div>

    <div class="admin-card overflow-hidden rounded-2xl border border-stone-100 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200">
                <thead class="bg-stone-50/80">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Comment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-stone-500">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-stone-50/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-stone-800">
                                <a href="{{ route('admin.products.edit', $review->product) }}" class="hover:text-primary-600">{{ $review->product->name }}</a>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-stone-600">{{ $review->user->name ?? '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center gap-0.5 text-warning-500">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="h-4 w-4" fill="{{ $i <= $review->rating ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-stone-600 max-w-xs truncate">{{ $review->comment ?? '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-stone-500">{{ $review->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-stone-500">No reviews yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-stone-100 px-6 py-3 bg-stone-50/30">{{ $reviews->links() }}</div>
    </div>
</div>
@endsection
