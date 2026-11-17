@extends('layouts.app')

@section('title', 'My Wishlist')
@section('noindex', '1')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 max-md:px-5 py-8 max-md:py-6">
    <h1 class="font-display text-3xl max-md:text-2xl font-semibold text-luxury-navy mb-2">My Wishlist</h1>
    <p class="text-stone-600 mb-8 max-md:mb-6">Products you've saved for later.</p>

    @if(session('success'))
        <div class="rounded-xl bg-primary-50 border border-primary-200 text-primary-800 px-4 py-3 mb-6">{{ session('success') }}</div>
    @endif

    @if($items->isEmpty())
        <div class="bg-white rounded-2xl border border-stone-200 p-12 max-md:p-8 text-center">
            <p class="text-stone-500 mb-6">Your wishlist is empty.</p>
            <a href="{{ route('shop.index') }}" class="inline-flex items-center justify-center px-6 py-3 max-md:min-h-[2.75rem] max-md:w-full max-md:max-w-xs rounded-xl bg-primary-600 text-white font-medium hover:bg-primary-700 transition">Browse Shop</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 max-md:gap-4">
            @foreach($items as $wish)
                @php $product = $wish->product; @endphp
                <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-soft hover:shadow-luxury transition-all duration-300 flex flex-col">
                    <a href="{{ route('products.show', $product->slug) }}" class="block aspect-[5/4] bg-cream-100 overflow-hidden">
                        @if($product->images->isNotEmpty())
                            <img src="{{ $product->images->first()->public_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                        @else
                            <img src="{{ asset('images/logo.webp') . '?v=' . config('app.logo_version', 2) }}" alt="" class="w-full h-full object-contain opacity-40 grayscale p-3" aria-hidden="true">
                        @endif
                    </a>
                    <div class="p-4 max-md:p-3 flex-1 flex flex-col min-w-0">
                        <a href="{{ route('products.show', $product->slug) }}" class="font-display font-semibold text-luxury-navy hover:text-primary-600 line-clamp-2">{{ $product->name }}</a>
                        <p class="mt-1 text-lg max-md:text-base font-semibold text-primary-600">LKR {{ number_format($product->effective_price, 0) }}</p>
                        <div class="mt-4 max-md:mt-3 flex gap-2 max-md:flex-col max-md:gap-2">
                            <a href="{{ route('products.show', $product->slug) }}" class="flex-1 text-center py-2.5 max-md:min-h-[2.75rem] rounded-xl border border-stone-200 text-sm font-medium text-stone-700 hover:bg-stone-50 transition">View</a>
                            <form action="{{ route('wishlist.remove', $product) }}" method="POST" class="flex-1 min-w-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full py-2.5 max-md:min-h-[2.75rem] rounded-xl border border-danger-200 text-danger-600 text-sm font-medium hover:bg-danger-50 transition">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8">{{ $items->links() }}</div>
    @endif
</div>
@endsection
