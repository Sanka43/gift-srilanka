@extends('layouts.app')

@section('title', 'Shipping & Delivery')
@section('meta_description', 'Shipping and delivery information for Gifts Sri Lanka. Same-day and scheduled delivery across Colombo.')

@section('content')
<div class="max-w-3xl mx-auto max-md:min-w-0">
    <h1 class="font-display text-3xl sm:text-4xl max-md:text-2xl font-bold text-stone-800 tracking-tight mb-2">Shipping & Delivery</h1>
    <p class="text-stone-500 text-lg mb-10 max-md:mb-6">How we get your gifts to their destination on time.</p>

    <div class="prose prose-stone prose-lg max-w-none prose-headings:font-display prose-headings:text-stone-800 prose-a:text-primary-600 prose-a:no-underline hover:prose-a:underline space-y-8 max-md:space-y-6">
        <section>
            <h2 class="font-display text-xl font-semibold text-stone-800 mt-8 mb-3">Delivery Areas</h2>
            <p class="text-stone-600">
                We deliver across Colombo and selected areas. At checkout you can enter your delivery address to see if we serve your area and to view delivery time slots and any applicable charges.
            </p>
        </section>

        <section>
            <h2 class="font-display text-xl font-semibold text-stone-800 mt-8 mb-3">Same-Day Delivery</h2>
            <p class="text-stone-600">
                Orders placed before our cut-off time may be eligible for same-day delivery. Availability depends on your location and the time of order. We'll show the earliest available slot during checkout.
            </p>
        </section>

        <section>
            <h2 class="font-display text-xl font-semibold text-stone-800 mt-8 mb-3">Scheduled Delivery</h2>
            <p class="text-stone-600">
                You can choose a future date for delivery (e.g. for birthdays or anniversaries). We recommend ordering at least a day in advance to secure your preferred date.
            </p>
        </section>

        <section>
            <h2 class="font-display text-xl font-semibold text-stone-800 mt-8 mb-3">Delivery Charges</h2>
            <p class="text-stone-600">
                Delivery fees vary by area and delivery type. The exact amount will be displayed before you complete your order. Free delivery may apply on selected products or minimum order values—check the product or promotion details.
            </p>
        </section>

        <p class="text-stone-500 text-sm pt-4">
            Questions about delivery? <a href="{{ route('page.contact') }}">Contact us</a> or see our <a href="{{ route('page.faq') }}">FAQ</a>.
        </p>
    </div>
</div>
@endsection
