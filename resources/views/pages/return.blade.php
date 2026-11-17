@extends('layouts.app')

@section('title', 'Returns & Refunds')
@section('meta_description', 'Returns and refunds policy for Gifts Sri Lanka: eligibility, cancellations, damaged orders, how to request a refund, and timelines.')

@section('content')
<div class="max-w-4xl mx-auto max-md:min-w-0 space-y-10 max-md:space-y-8">
    <header class="max-w-3xl">
        <h1 class="font-display text-3xl sm:text-4xl max-md:text-2xl font-bold text-stone-800 tracking-tight mb-2">Returns &amp; refunds</h1>
        <p class="text-stone-500 text-lg mb-2 max-md:mb-1 max-md:text-base">Cancellations, damaged deliveries, and how we process refunds.</p>
        <p class="text-stone-400 text-sm max-md:text-xs">Last updated <time datetime="{{ now()->format('Y-m-d') }}">{{ now()->format('F j, Y') }}</time> · {{ config('app.name') }}</p>
    </header>

    {{-- Quick reference --}}
    <div class="grid gap-4 sm:grid-cols-3 max-md:gap-3">
        <div class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-5 shadow-soft max-md:p-4">
            <div class="flex items-center gap-3 mb-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-50 text-primary-600" aria-hidden="true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <h2 class="font-display font-semibold text-stone-800 text-sm uppercase tracking-wide">We can help</h2>
            </div>
            <p class="text-sm text-stone-600 leading-relaxed">Wrong item, damage in transit, major quality issues, or eligible non-perishable returns—contact us with your order number.</p>
        </div>
        <div class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-5 shadow-soft max-md:p-4">
            <div class="flex items-center gap-3 mb-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700" aria-hidden="true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
                <h2 class="font-display font-semibold text-stone-800 text-sm uppercase tracking-wide">Usually final</h2>
            </div>
            <p class="text-sm text-stone-600 leading-relaxed">Fresh flowers, cakes, food hampers, and personalised items cannot be returned once dispatched or delivered—nature of the product.</p>
        </div>
        <div class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-5 shadow-soft max-md:p-4 sm:col-span-1">
            <div class="flex items-center gap-3 mb-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-stone-100 text-stone-600" aria-hidden="true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <h2 class="font-display font-semibold text-stone-800 text-sm uppercase tracking-wide">Act quickly</h2>
            </div>
            <p class="text-sm text-stone-600 leading-relaxed">Report problems within <strong class="font-semibold text-stone-700">24 hours</strong> of delivery when possible. Cancellations only before prep or dispatch.</p>
        </div>
    </div>

    {{-- How to request --}}
    <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 shadow-soft overflow-hidden" aria-labelledby="how-to-heading">
        <div class="border-b border-stone-100 px-6 py-5 max-md:px-4 max-md:py-4 bg-stone-50/80">
            <h2 id="how-to-heading" class="font-display text-xl font-semibold text-stone-800 max-md:text-lg">How to request a return or refund</h2>
            <p class="text-stone-500 text-sm mt-1 max-md:text-xs">Follow these steps so we can resolve your case faster.</p>
        </div>
        <ol class="divide-y divide-stone-100 not-prose">
            <li class="flex gap-4 px-6 py-5 max-md:px-4 max-md:py-4">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-500 text-white text-sm font-display font-bold">1</span>
                <div>
                    <p class="font-display font-semibold text-stone-800 text-sm max-md:text-sm">Gather your details</p>
                    <p class="text-stone-600 text-sm mt-1 leading-relaxed">Have your order number, delivery date, and a short description of the issue (e.g. “wrong cake flavour”, “damaged bouquet”).</p>
                </div>
            </li>
            <li class="flex gap-4 px-6 py-5 max-md:px-4 max-md:py-4">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-500 text-white text-sm font-display font-bold">2</span>
                <div>
                    <p class="font-display font-semibold text-stone-800 text-sm">Add photos if relevant</p>
                    <p class="text-stone-600 text-sm mt-1 leading-relaxed">For damage or wrong items, clear photos of the product and packaging help us approve a replacement or refund without delay.</p>
                </div>
            </li>
            <li class="flex gap-4 px-6 py-5 max-md:px-4 max-md:py-4">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-500 text-white text-sm font-display font-bold">3</span>
                <div>
                    <p class="font-display font-semibold text-stone-800 text-sm"><a href="{{ route('page.contact') }}" class="text-primary-600 hover:underline">Contact us</a></p>
                    <p class="text-stone-600 text-sm mt-1 leading-relaxed">Email or message us through our contact page. We aim to respond within <strong class="font-semibold text-stone-700">1–2 business days</strong> and will confirm the next step (replacement, partial credit, or refund).</p>
                </div>
            </li>
        </ol>
    </section>

    {{-- Policy detail cards --}}
    <div class="space-y-4 max-md:space-y-3">
        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">What cannot be returned</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-4">
                Many of our products are time-sensitive or made to order. For hygiene, safety, and quality reasons, the following are generally <strong class="text-stone-800">not eligible</strong> for return after they have left our care or been delivered:
            </p>
            <ul class="space-y-2 text-sm text-stone-600">
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>Fresh flowers, plants, and perishable floral arrangements</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>Cakes, desserts, chocolates, and food or beverage hampers</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>Personalised or custom items (engraving, printed messages, bespoke packaging)</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>Items marked final sale or non-returnable on the product page</span></li>
            </ul>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">Non-perishable gifts</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                For eligible non-perishable items (for example sealed accessories or gift items without personalisation), we may accept a return if:
            </p>
            <ul class="space-y-2 text-sm text-stone-600 mb-4">
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>The item is unused, in original packaging, with tags intact where applicable</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>You contact us within <strong class="font-semibold text-stone-700">7 days</strong> of delivery (unless a different period is stated at checkout)</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>We confirm the return by email before you send anything back</span></li>
            </ul>
            <p class="text-stone-500 text-xs leading-relaxed border-t border-stone-100 pt-3">Return shipping may be your responsibility unless we sent the wrong item or the product was faulty. We will confirm address and instructions when your return is approved.</p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">Wrong item, damage, or quality issues</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                If something arrives incorrect, visibly damaged, or clearly not up to the standard described, tell us as soon as you can—ideally within <strong class="font-semibold text-stone-700">24 hours</strong> of delivery—with your order number and photos if possible.
            </p>
            <p class="text-stone-600 text-sm leading-relaxed">
                Depending on stock and your preference, we may offer a <strong class="font-semibold text-stone-700">replacement</strong> (including redelivery where appropriate), a <strong class="font-semibold text-stone-700">partial refund</strong>, or a <strong class="font-semibold text-stone-700">full refund</strong> for the affected items. We may decline claims where issues are reported very late without good reason, or where the product was acceptable but personal taste differed (e.g. flower colour shade in natural products).
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">Cancellations &amp; changes</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                You can cancel or request changes <strong class="font-semibold text-stone-700">before</strong> your order is prepared or dispatched. Same-day and scheduled orders move quickly—contact us immediately if you need to cancel.
            </p>
            <p class="text-stone-600 text-sm leading-relaxed">
                Once preparation, custom work, or delivery has started, we may be unable to cancel or may only offer a partial refund to cover costs already incurred. Seasonal peaks (e.g. Valentine’s Day) may have stricter cut-off times; we will communicate any special terms on the site or at checkout when they apply.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">Delivery problems</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                If the recipient is unavailable and we cannot complete delivery per our <a href="{{ route('page.shipping') }}" class="text-primary-600 hover:underline font-medium">shipping &amp; delivery</a> guidelines, we may attempt redelivery where possible. Additional fees can apply for repeated attempts or address changes made after dispatch.
            </p>
            <p class="text-stone-600 text-sm leading-relaxed">
                Refunds for failed delivery are assessed case by case. We are not responsible for delays caused by events outside our control (severe weather, traffic, incorrect address supplied at checkout), but we will always try to find a fair solution.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">Refunds &amp; processing time</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                Approved refunds are returned to your <strong class="font-semibold text-stone-700">original payment method</strong> unless we agree otherwise (e.g. store credit). You will receive an email confirmation when a refund is initiated.
            </p>
            <p class="text-stone-600 text-sm leading-relaxed">
                Banks and card issuers typically show the credit within <strong class="font-semibold text-stone-700">5–10 business days</strong>; some institutions take longer. If you do not see it after that window, contact your bank first, then reach out to us with your order number.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">Exchanges</h2>
            <p class="text-stone-600 text-sm leading-relaxed">
                We do not operate a formal exchange service for most products. If you need a different item, the usual path is to return an eligible product (where applicable) and place a new order, or we may arrange a replacement directly when resolving a wrong or faulty delivery.
            </p>
        </section>
    </div>

    {{-- CTA --}}
    <div class="rounded-2xl max-md:rounded-xl border border-warning-200/80 bg-gradient-to-br from-amber-50 to-orange-50/80 px-6 py-6 max-md:px-4 max-md:py-5 shadow-soft">
        <h2 class="font-display text-lg font-semibold text-stone-800 mb-2 max-md:text-base">Need help with an order?</h2>
        <p class="text-stone-600 text-sm leading-relaxed mb-4 max-md:text-xs">
            Send your order number and a brief explanation. For damaged or wrong items, photos speed things up. You can also read our <a href="{{ route('page.faq') }}" class="text-primary-600 font-medium hover:underline">FAQ</a> or review our <a href="{{ route('page.terms') }}" class="text-primary-600 font-medium hover:underline">Terms of Service</a>.
        </p>
        <a href="{{ route('page.contact') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-primary-600/25 hover:bg-primary-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-amber-50">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Contact us
        </a>
    </div>
</div>
@endsection
