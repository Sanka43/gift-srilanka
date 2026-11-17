@extends('layouts.app')

@section('title', 'Blog')
@section('meta_description', 'Gift ideas, occasions, and stories from Gifts Sri Lanka. Tips for choosing the perfect gift in Colombo.')

@section('content')
<div class="max-w-3xl mx-auto max-md:min-w-0">
    <h1 class="font-display text-3xl sm:text-4xl max-md:text-2xl font-bold text-stone-800 tracking-tight mb-2">Blog</h1>
    <p class="text-stone-500 text-lg mb-10 max-md:mb-6">Gift ideas, occasions, and stories from {{ config('app.name') }}.</p>

    <div class="prose prose-stone prose-lg max-w-none prose-headings:font-display prose-headings:text-stone-800 prose-a:text-primary-600 prose-a:no-underline hover:prose-a:underline space-y-10 max-md:space-y-6">
        <p class="text-stone-600 leading-relaxed">
            Welcome to our blog. Here we share gift ideas, seasonal inspiration, and stories to help you celebrate every occasion—from birthdays and anniversaries to corporate events and festivals.
        </p>

        <section>
            <h2 class="font-display text-xl font-semibold text-stone-800 mt-8 mb-3">Coming Soon</h2>
            <p class="text-stone-600">
                We're preparing articles on the best gifts for Sri Lankan occasions, same-day delivery tips, and how to choose the perfect bouquet or hamper. Check back soon for updates, or <a href="{{ route('page.contact') }}">contact us</a> if you have a topic you'd like us to cover.
            </p>
        </section>

        <p class="text-stone-500 text-sm pt-4">
            In the meantime, browse our <a href="{{ route('shop.index') }}">shop</a> for flowers, cakes, hampers, and more—delivered across Colombo and beyond.
        </p>
    </div>
</div>
@endsection
