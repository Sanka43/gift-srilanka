@extends('layouts.app')

@section('title', 'Meet The Team')
@section('meta_description', 'Meet the people behind Gifts Sri Lanka—your local gift delivery experts in Colombo.')

@section('content')
<div class="max-w-3xl mx-auto max-md:min-w-0">
    <h1 class="font-display text-3xl sm:text-4xl max-md:text-2xl font-bold text-stone-800 tracking-tight mb-2">Meet The Team</h1>
    <p class="text-stone-500 text-lg mb-10 max-md:mb-6">The people behind {{ config('app.name') }}.</p>

    <div class="prose prose-stone prose-lg max-w-none prose-headings:font-display prose-headings:text-stone-800 prose-a:text-primary-600 prose-a:no-underline hover:prose-a:underline space-y-8 max-md:space-y-6">
        <p class="text-stone-600 leading-relaxed">
            We're a small team based in Colombo, passionate about helping you send happiness—whether it's a birthday bouquet, an anniversary cake, or a corporate hamper. Every order is handled with care so your recipient feels special.
        </p>

        <section>
            <h2 class="font-display text-xl font-semibold text-stone-800 mt-8 mb-3">Our Mission</h2>
            <p class="text-stone-600">
                To make gifting easy and memorable. We source quality products, pack them with care, and deliver on time so you can focus on the occasion, not the logistics.
            </p>
        </section>

        <section>
            <h2 class="font-display text-xl font-semibold text-stone-800 mt-8 mb-3">Get in Touch</h2>
            <p class="text-stone-600">
                Have a question or a special request? We'd love to hear from you. <a href="{{ route('page.contact') }}">Contact us</a> and we'll get back to you as soon as we can.
            </p>
        </section>
    </div>
</div>
@endsection
