@extends('layouts.app')

@section('title', 'Frequently Asked Questions')
@section('meta_description', 'FAQ for Gifts Sri Lanka: orders, delivery, payments, returns, and more.')

@section('content')
<div class="max-w-3xl mx-auto max-md:min-w-0">
    <h1 class="font-display text-3xl sm:text-4xl max-md:text-2xl font-bold text-stone-800 tracking-tight mb-2">Frequently Asked Questions</h1>
    <p class="text-stone-500 text-lg mb-10 max-md:mb-6">Quick answers to common questions.</p>

    <div class="space-y-6 max-md:space-y-4 not-prose">
        <details class="group rounded-2xl max-md:rounded-xl bg-white border border-stone-200 shadow-soft overflow-hidden">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-4 max-md:px-4 max-md:py-3 font-display font-semibold text-stone-800 hover:bg-stone-50 transition-colors max-md:text-sm">
                How do I place an order?
                <span class="shrink-0 transition-transform group-open:rotate-180">
                    <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </span>
            </summary>
            <div class="border-t border-stone-100 px-6 py-4 max-md:px-4 max-md:py-3 text-stone-600">
                Browse our shop, add items to your cart, and proceed to checkout. You'll need to create an account or sign in. Enter your delivery details and payment information to complete the order.
            </div>
        </details>

        <details class="group rounded-2xl max-md:rounded-xl bg-white border border-stone-200 shadow-soft overflow-hidden">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-4 max-md:px-4 max-md:py-3 font-display font-semibold text-stone-800 hover:bg-stone-50 transition-colors max-md:text-sm">
                Do you deliver on the same day?
                <span class="shrink-0 transition-transform group-open:rotate-180">
                    <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </span>
            </summary>
            <div class="border-t border-stone-100 px-6 py-4 max-md:px-4 max-md:py-3 text-stone-600">
                Yes, same-day delivery is available in many areas when you order before our cut-off time. At checkout you'll see the available delivery slots for your address.
            </div>
        </details>

        <details class="group rounded-2xl max-md:rounded-xl bg-white border border-stone-200 shadow-soft overflow-hidden">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-4 max-md:px-4 max-md:py-3 font-display font-semibold text-stone-800 hover:bg-stone-50 transition-colors max-md:text-sm">
                What payment methods do you accept?
                <span class="shrink-0 transition-transform group-open:rotate-180">
                    <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </span>
            </summary>
            <div class="border-t border-stone-100 px-6 py-4 max-md:px-4 max-md:py-3 text-stone-600">
                We accept major credit and debit cards and other payment options shown at checkout. All payments are processed securely.
            </div>
        </details>

        <details class="group rounded-2xl max-md:rounded-xl bg-white border border-stone-200 shadow-soft overflow-hidden">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-4 max-md:px-4 max-md:py-3 font-display font-semibold text-stone-800 hover:bg-stone-50 transition-colors max-md:text-sm">
                Can I cancel or change my order?
                <span class="shrink-0 transition-transform group-open:rotate-180">
                    <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </span>
            </summary>
            <div class="border-t border-stone-100 px-6 py-4 max-md:px-4 max-md:py-3 text-stone-600">
                Contact us as soon as possible with your order number. We can often cancel or amend orders before they are dispatched. Once preparation or delivery has started, changes may not be possible.
            </div>
        </details>

        <details class="group rounded-2xl max-md:rounded-xl bg-white border border-stone-200 shadow-soft overflow-hidden">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-4 max-md:px-4 max-md:py-3 font-display font-semibold text-stone-800 hover:bg-stone-50 transition-colors max-md:text-sm">
                What if my gift arrives damaged or wrong?
                <span class="shrink-0 transition-transform group-open:rotate-180">
                    <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </span>
            </summary>
            <div class="border-t border-stone-100 px-6 py-4 max-md:px-4 max-md:py-3 text-stone-600">
                Please contact us immediately with your order number and, if possible, a photo. We will arrange a replacement or refund as appropriate. See our <a href="{{ route('page.return') }}" class="text-primary-600 hover:underline">Returns & Refunds</a> page for more details.
            </div>
        </details>

        <details class="group rounded-2xl max-md:rounded-xl bg-white border border-stone-200 shadow-soft overflow-hidden">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-4 max-md:px-4 max-md:py-3 font-display font-semibold text-stone-800 hover:bg-stone-50 transition-colors max-md:text-sm">
                Do you deliver outside Colombo?
                <span class="shrink-0 transition-transform group-open:rotate-180">
                    <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </span>
            </summary>
            <div class="border-t border-stone-100 px-6 py-4 max-md:px-4 max-md:py-3 text-stone-600">
                Our main delivery area is Colombo and surrounding areas. Enter your address at checkout to see if we can deliver to you. We may expand to more areas in the future.
            </div>
        </details>
    </div>

    <p class="text-stone-500 text-sm mt-10">
        Still have questions? <a href="{{ route('page.contact') }}" class="text-primary-600 hover:underline">Contact us</a>.
    </p>
</div>
@endsection
