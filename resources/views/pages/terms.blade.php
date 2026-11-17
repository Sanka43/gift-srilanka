@extends('layouts.app')

@section('title', 'Terms of Service')
@section('meta_description', 'Terms of Service for ' . config('app.name') . ': using our website, placing orders, delivery, payments, liability, and how we may update these terms.')

@section('content')
<div class="max-w-4xl mx-auto max-md:min-w-0 space-y-10 max-md:space-y-8">
    <header class="max-w-3xl">
        <h1 class="font-display text-3xl sm:text-4xl max-md:text-2xl font-bold text-stone-800 tracking-tight mb-2">Terms of Service</h1>
        <p class="text-stone-500 text-lg mb-2 max-md:mb-1 max-md:text-base">Rules for using our website, placing orders, and receiving deliveries.</p>
        <p class="text-stone-400 text-sm max-md:text-xs">Last updated <time datetime="{{ now()->format('Y-m-d') }}">{{ now()->format('F j, Y') }}</time> · {{ config('app.name') }}</p>
    </header>

    {{-- Quick reference --}}
    <div class="grid gap-4 sm:grid-cols-3 max-md:gap-3">
        <div class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-5 shadow-soft max-md:p-4">
            <div class="flex items-center gap-3 mb-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-50 text-primary-600" aria-hidden="true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </span>
                <h2 class="font-display font-semibold text-stone-800 text-sm uppercase tracking-wide">Agreement</h2>
            </div>
            <p class="text-sm text-stone-600 leading-relaxed">By using this site or placing an order, you agree to these terms and our <a href="{{ route('page.privacy') }}" class="text-primary-600 hover:underline">Privacy Policy</a>.</p>
        </div>
        <div class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-5 shadow-soft max-md:p-4">
            <div class="flex items-center gap-3 mb-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700" aria-hidden="true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
                <h2 class="font-display font-semibold text-stone-800 text-sm uppercase tracking-wide">Orders</h2>
            </div>
            <p class="text-sm text-stone-600 leading-relaxed">An order is an offer to buy. We may accept or decline it. Prices and slots shown at checkout apply once your order is confirmed.</p>
        </div>
        <div class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-5 shadow-soft max-md:p-4">
            <div class="flex items-center gap-3 mb-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-stone-100 text-stone-600" aria-hidden="true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <h2 class="font-display font-semibold text-stone-800 text-sm uppercase tracking-wide">Questions</h2>
            </div>
            <p class="text-sm text-stone-600 leading-relaxed">For returns, see <a href="{{ route('page.return') }}" class="text-primary-600 hover:underline">Returns &amp; refunds</a>. Otherwise <a href="{{ route('page.contact') }}" class="text-primary-600 hover:underline">contact us</a>.</p>
        </div>
    </div>

    <p class="text-stone-600 text-sm leading-relaxed max-w-3xl">
        Welcome to {{ config('app.name') }}. These Terms of Service (“Terms”) govern your access to our website and the purchase and delivery of products through it. If you do not agree, please do not use our services.
    </p>

    {{-- Policy sections --}}
    <div class="space-y-4 max-md:space-y-3">
        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">1. Who may use the service</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                You must be able to enter a binding contract under applicable law. If you use the site on behalf of a business, you confirm you are authorised to bind that business. You are responsible for the accuracy of information you provide (account details, delivery address, recipient phone numbers, and gift messages).
            </p>
            <p class="text-stone-600 text-sm leading-relaxed">
                You must keep your login credentials confidential and notify us promptly if you suspect unauthorised access to your account.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">2. Acceptable use</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                You agree to use the website only for lawful purposes. You must not:
            </p>
            <ul class="space-y-2 text-sm text-stone-600 mb-3">
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>Attempt to gain unauthorised access to our systems, other users’ accounts, or data</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>Use automated means (scraping, bots) in a way that overloads or harms the site, except as allowed by public search engines indexing pages</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>Upload malware, interfere with security features, or reverse engineer our software except where the law allows</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>Use the service to harass, defraud, or send unlawful or offensive content in gift messages</span></li>
            </ul>
            <p class="text-stone-600 text-sm leading-relaxed">
                We may suspend or terminate access if we reasonably believe you have breached these rules or pose a risk to us, other customers, or third parties.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">3. Products, descriptions, and pricing</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                We aim to describe products, images, and colours accurately. Natural variation can occur (e.g. fresh flowers, handmade items). If a product is unavailable or mispriced, we may refuse or cancel the order and refund any payment taken, or offer a reasonable substitute with your consent where possible.
            </p>
            <p class="text-stone-600 text-sm leading-relaxed">
                Prices are shown in the currency stated at checkout. Taxes or fees, if applicable, are included or shown before you pay. Promotional offers are subject to their stated terms, dates, and stock limits.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">4. Orders and payment</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                When you submit an order, you make an offer to purchase. A contract is formed when we send you an order confirmation (or when we clearly accept the order by starting fulfilment, if we do not send email). We may decline or limit orders for stock, delivery area, fraud prevention, or operational reasons.
            </p>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                Payment is due when you place the order unless we agree otherwise. You authorise us and our payment providers to charge your selected payment method. If payment fails, we may cancel the order. You are responsible for providing valid payment details and keeping billing information up to date.
            </p>
            <p class="text-stone-500 text-xs leading-relaxed border-t border-stone-100 pt-3">
                Chargebacks should be a last step after contacting us—we will work to resolve legitimate disputes.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">5. Delivery and risk</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                Delivery areas, time slots, and fees are shown at checkout and in our <a href="{{ route('page.shipping') }}" class="text-primary-600 font-medium hover:underline">Shipping &amp; delivery</a> information. You are responsible for correct address and contact details. If a recipient is unavailable, we will follow our delivery procedures (e.g. redelivery attempts or safe drop-off where permitted).
            </p>
            <p class="text-stone-600 text-sm leading-relaxed">
                Risk of loss or damage to the goods passes to you or the recipient when delivery is completed according to our process. We are not liable for delays due to events outside our reasonable control (e.g. severe weather, road closures, strikes, or incorrect information you provided).
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">6. Returns and refunds</h2>
            <p class="text-stone-600 text-sm leading-relaxed">
                Our <a href="{{ route('page.return') }}" class="text-primary-600 font-medium hover:underline">Returns &amp; refunds</a> policy forms part of these Terms. Perishable and personalised items are generally non-returnable once dispatched. Eligible refunds or replacements are handled as described there.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">7. Reviews and user content</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                If you submit reviews, photos, or other content, you grant us a non-exclusive licence to use, display, and adapt that content in connection with our services (for example on the product page or in marketing). You confirm you have the right to share it and that it is not illegal, misleading, or infringing.
            </p>
            <p class="text-stone-600 text-sm leading-relaxed">
                We may remove content that violates these Terms or applicable law, or that we consider harmful to our community.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">8. Intellectual property</h2>
            <p class="text-stone-600 text-sm leading-relaxed">
                The website, branding, product images, text, layout, and software are owned by {{ config('app.name') }} or our licensors and are protected by intellectual property laws. You may not copy, distribute, modify, or create derivative works without our prior written consent, except for temporary copying needed to browse or shop for personal use.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">9. Privacy</h2>
            <p class="text-stone-600 text-sm leading-relaxed">
                Our <a href="{{ route('page.privacy') }}" class="text-primary-600 font-medium hover:underline">Privacy Policy</a> explains how we collect and use personal data. By using the site, you acknowledge that we process data as described there.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">10. Disclaimers</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                We provide the website and services on an “as is” and “as available” basis to the extent permitted by law. We do not guarantee uninterrupted or error-free operation. Nothing in these Terms excludes or limits liability where such exclusion is unlawful (including liability for death or personal injury caused by negligence, or fraud).
            </p>
            <p class="text-stone-600 text-sm leading-relaxed">
                To the extent allowed by law, we disclaim implied warranties such as merchantability or fitness for a particular purpose for digital services, except where mandatory consumer rights apply.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">11. Limitation of liability</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                To the fullest extent permitted by law, we are not liable for indirect, incidental, special, consequential, or punitive damages, or for loss of profits, data, or goodwill, arising from your use of the site or our products.
            </p>
            <p class="text-stone-600 text-sm leading-relaxed">
                Our total aggregate liability for any claim arising out of or related to a specific order is limited to the amount you paid us for that order. These limitations apply whether the claim is in contract, tort (including negligence), or otherwise.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">12. Indemnity</h2>
            <p class="text-stone-600 text-sm leading-relaxed">
                You agree to indemnify and hold harmless {{ config('app.name') }} and our officers and staff from claims, losses, or costs (including reasonable legal fees) arising from your breach of these Terms, misuse of the site, or content you submit—except to the extent caused by our wilful misconduct or gross negligence.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">13. Changes to these Terms</h2>
            <p class="text-stone-600 text-sm leading-relaxed">
                We may update these Terms from time to time. The “Last updated” date at the top will change when we do. Material changes may be highlighted on the site or communicated by email where appropriate. Continued use of the site after changes constitutes acceptance, except where applicable law requires your explicit consent for certain changes.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">14. Governing law and disputes</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                These Terms are governed by the laws of Sri Lanka, without regard to conflict-of-law rules. Courts in Sri Lanka shall have non-exclusive jurisdiction over disputes, subject to any mandatory rights you have as a consumer in your country of residence.
            </p>
            <p class="text-stone-500 text-xs leading-relaxed">
                We encourage you to <a href="{{ route('page.contact') }}" class="text-primary-600 hover:underline">contact us</a> first to resolve concerns informally.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">15. General</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                If any provision of these Terms is held invalid, the remainder stays in effect. Failure to enforce a provision is not a waiver. You may not assign your rights under these Terms without our consent; we may assign them in connection with a merger, sale, or reorganisation.
            </p>
            <p class="text-stone-600 text-sm leading-relaxed">
                These Terms, together with our Privacy Policy and policies linked from them, constitute the entire agreement between you and {{ config('app.name') }} regarding the subject matter here.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">16. Contact</h2>
            <p class="text-stone-600 text-sm leading-relaxed">
                For questions about these Terms of Service, please <a href="{{ route('page.contact') }}" class="text-primary-600 font-medium hover:underline">contact us</a> using the details shown on our website.
            </p>
        </section>
    </div>

    {{-- CTA --}}
    <div class="rounded-2xl max-md:rounded-xl border border-warning-200/80 bg-gradient-to-br from-amber-50 to-orange-50/80 px-6 py-6 max-md:px-4 max-md:py-5 shadow-soft">
        <h2 class="font-display text-lg font-semibold text-stone-800 mb-2 max-md:text-base">Related policies</h2>
        <p class="text-stone-600 text-sm leading-relaxed mb-4 max-md:text-xs">
            <a href="{{ route('page.privacy') }}" class="text-primary-600 font-medium hover:underline">Privacy Policy</a>
            ·
            <a href="{{ route('page.return') }}" class="text-primary-600 font-medium hover:underline">Returns &amp; refunds</a>
            ·
            <a href="{{ route('page.shipping') }}" class="text-primary-600 font-medium hover:underline">Shipping &amp; delivery</a>
        </p>
        <a href="{{ route('page.contact') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-primary-600/25 hover:bg-primary-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-amber-50">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Contact us
        </a>
    </div>
</div>
@endsection
