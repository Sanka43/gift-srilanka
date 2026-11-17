@extends('layouts.app')

@section('title', 'Privacy Policy')
@section('meta_description', 'Privacy Policy for ' . config('app.name') . ': what we collect, how we use and share personal data, cookies, security, retention, and your rights.')

@section('content')
<div class="max-w-4xl mx-auto max-md:min-w-0 space-y-10 max-md:space-y-8">
    <header class="max-w-3xl">
        <h1 class="font-display text-3xl sm:text-4xl max-md:text-2xl font-bold text-stone-800 tracking-tight mb-2">Privacy Policy</h1>
        <p class="text-stone-500 text-lg mb-2 max-md:mb-1 max-md:text-base">How we collect, use, store, and protect your personal information.</p>
        <p class="text-stone-400 text-sm max-md:text-xs">Last updated <time datetime="{{ now()->format('Y-m-d') }}">{{ now()->format('F j, Y') }}</time> · {{ config('app.name') }}</p>
    </header>

    {{-- Quick reference --}}
    <div class="grid gap-4 sm:grid-cols-3 max-md:gap-3">
        <div class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-5 shadow-soft max-md:p-4">
            <div class="flex items-center gap-3 mb-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-50 text-primary-600" aria-hidden="true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </span>
                <h2 class="font-display font-semibold text-stone-800 text-sm uppercase tracking-wide">No data sales</h2>
            </div>
            <p class="text-sm text-stone-600 leading-relaxed">We do not sell your personal information. We use data to run our shop, deliver orders, and improve our service.</p>
        </div>
        <div class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-5 shadow-soft max-md:p-4">
            <div class="flex items-center gap-3 mb-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700" aria-hidden="true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
                <h2 class="font-display font-semibold text-stone-800 text-sm uppercase tracking-wide">Your choices</h2>
            </div>
            <p class="text-sm text-stone-600 leading-relaxed">You can ask to access, correct, or delete your data where applicable, and opt out of marketing at any time.</p>
        </div>
        <div class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-5 shadow-soft max-md:p-4">
            <div class="flex items-center gap-3 mb-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-stone-100 text-stone-600" aria-hidden="true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                <h2 class="font-display font-semibold text-stone-800 text-sm uppercase tracking-wide">Contact</h2>
            </div>
            <p class="text-sm text-stone-600 leading-relaxed">Questions or requests? <a href="{{ route('page.contact') }}" class="text-primary-600 hover:underline">Contact us</a>. See also our <a href="{{ route('page.terms') }}" class="text-primary-600 hover:underline">Terms of Service</a>.</p>
        </div>
    </div>

    <p class="text-stone-600 text-sm leading-relaxed max-w-3xl">
        {{ config('app.name') }} We respects your privacy. This Privacy Policy explains what personal data we collect when you visit our website, create an account, place an order, or contact us; how we use and share it; how long we keep it; and the rights you may have. By using our services, you acknowledge the practices described here (and our <a href="{{ route('page.terms') }}" class="text-primary-600 font-medium hover:underline">Terms of Service</a>).
    </p>

    {{-- Policy sections --}}
    <div class="space-y-4 max-md:space-y-3">
        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">1. Who is responsible for your data</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                The data controller for personal data processed through this website is {{ config('app.name') }}, operating the site and related order and delivery services. Contact details used for privacy requests are those published on our <a href="{{ route('page.contact') }}" class="text-primary-600 font-medium hover:underline">Contact</a> page.
            </p>
            <p class="text-stone-500 text-xs leading-relaxed border-t border-stone-100 pt-3">
                If we use processors (e.g. hosting, payments), they process data only on our instructions and under appropriate agreements.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">2. Information we collect</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                Depending on how you interact with us, we may collect:
            </p>
            <ul class="space-y-2 text-sm text-stone-600 mb-4">
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Account &amp; identity:</strong> name, email address, phone number, password (stored in hashed form), and profile preferences.</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Orders &amp; delivery:</strong> billing and delivery addresses, recipient name and contact details, delivery instructions, gift messages, order contents, and transaction references.</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Payments:</strong> payment is handled by our payment providers; we typically receive limited information (e.g. last four digits, status, token)—not your full card number on our servers.</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Communications:</strong> messages you send us (contact form, email, chat), and service emails we send you (order updates, support).</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Reviews &amp; content:</strong> product reviews, ratings, or photos you choose to submit.</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Technical &amp; usage:</strong> IP address, browser type, device type, approximate location derived from IP, pages viewed, referring URL, and timestamps—often via cookies or similar technologies (see below).</span></li>
            </ul>
            <p class="text-stone-600 text-sm leading-relaxed">
                Providing certain data is necessary to fulfil orders; optional fields are marked where possible or are clearly voluntary (e.g. marketing sign-up).
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">3. Why we use your data (legal bases)</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                We process personal data where:
            </p>
            <ul class="space-y-2 text-sm text-stone-600 mb-3">
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Contract:</strong> to register you, process and deliver your order, take payment, and provide customer support.</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Legitimate interests:</strong> to secure our site, prevent fraud, analyse usage to improve our shop, and measure marketing effectiveness—balanced against your rights.</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Consent:</strong> where required—for example non-essential cookies or marketing emails—you can withdraw consent at any time.</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Legal obligation:</strong> to comply with tax, accounting, or lawful requests from authorities.</span></li>
            </ul>
            <p class="text-stone-500 text-xs leading-relaxed">
                Exact legal bases can vary by jurisdiction; we apply the above in line with applicable law in Sri Lanka and, where relevant, your location.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">4. How we use your information</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                We use personal data to:
            </p>
            <ul class="space-y-2 text-sm text-stone-600">
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>Create and manage accounts; authenticate logins</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>Process payments, fulfil orders, and coordinate delivery (including sharing delivery details with couriers)</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>Send transactional messages (confirmations, delivery updates)—these are part of the service</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>Send marketing only where permitted; you may unsubscribe via the link in emails or by contacting us</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>Operate wishlists, reviews, and features you choose to use</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>Detect abuse, protect security, and enforce our <a href="{{ route('page.terms') }}" class="text-primary-600 hover:underline">Terms</a></span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span>Comply with law and respond to valid legal requests</span></li>
            </ul>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">5. Sharing of information</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                <strong class="text-stone-800">We do not sell your personal information.</strong> We may share data with:
            </p>
            <ul class="space-y-2 text-sm text-stone-600 mb-3">
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Service providers:</strong> hosting, email delivery, analytics, payment processing, fraud screening, and delivery partners—only what they need to perform their services.</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Professional advisers:</strong> lawyers or accountants where required, under confidentiality.</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Authorities:</strong> when required by law or to protect rights, safety, and security.</span></li>
            </ul>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                If our business is involved in a merger, acquisition, or asset sale, your data may be transferred as part of that transaction; we will require the successor to honour this policy or notify you of changes.
            </p>
            <p class="text-stone-600 text-sm leading-relaxed">
                Some providers may process data outside your country. Where we transfer data internationally, we use appropriate safeguards (e.g. contracts or provider certifications) where required by law.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">6. Data security</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                We implement technical and organisational measures appropriate to the risk: access controls, encryption in transit where standard (e.g. HTTPS), secure handling of credentials, and limited internal access on a need-to-know basis.
            </p>
            <p class="text-stone-600 text-sm leading-relaxed">
                Payment card data is processed by PCI-aware payment partners; we do not store full card numbers on our own systems. No online system is perfectly secure—please use a strong, unique password for your account and contact us if you suspect misuse.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">7. Cookies and similar technologies</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                We use cookies and similar tools for:
            </p>
            <ul class="space-y-2 text-sm text-stone-600 mb-3">
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Strictly necessary:</strong> session, security, cart, and login—required for the site to work</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Functional:</strong> remember preferences (e.g. language) where we offer them</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Analytics:</strong> understand traffic and improve pages (may use aggregated or pseudonymous data)</span></li>
            </ul>
            <p class="text-stone-600 text-sm leading-relaxed">
                You can control cookies through your browser settings. Blocking all cookies may limit cart, checkout, or login. Where we use non-essential cookies subject to consent, we will reflect your choices via our cookie banner or settings when implemented.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">8. Retention</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                We keep personal data only as long as necessary for the purposes above, including:
            </p>
            <ul class="space-y-2 text-sm text-stone-600 mb-3">
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Orders &amp; tax:</strong> typically for the period required by commercial and tax law (often several years for invoices and transaction records)</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Account:</strong> until you close your account, then a short period to resolve disputes unless law requires longer retention</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Marketing:</strong> until you unsubscribe or object, and briefly after to honour your choice</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Logs &amp; security:</strong> for a limited period for troubleshooting and security</span></li>
            </ul>
            <p class="text-stone-600 text-sm leading-relaxed">
                When retention ends, we delete or anonymise data where feasible.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">9. Your rights</h2>
            <p class="text-stone-600 text-sm leading-relaxed mb-3">
                Depending on applicable law, you may have the right to:
            </p>
            <ul class="space-y-2 text-sm text-stone-600 mb-3">
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Access</strong> a copy of your personal data</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Rectify</strong> inaccurate or incomplete data</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Erase</strong> data in certain circumstances</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Restrict</strong> processing in certain cases</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Object</strong> to processing based on legitimate interests or to direct marketing</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Data portability</strong> for data you provided, where technically feasible</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Withdraw consent</strong> where processing was consent-based, without affecting prior lawful processing</span></li>
                <li class="flex gap-2"><span class="text-primary-500 font-bold shrink-0" aria-hidden="true">·</span><span><strong class="text-stone-800">Lodge a complaint</strong> with a supervisory authority where you live or work, if applicable</span></li>
            </ul>
            <p class="text-stone-600 text-sm leading-relaxed">
                To exercise these rights, <a href="{{ route('page.contact') }}" class="text-primary-600 font-medium hover:underline">contact us</a> with your name, email, and a description of your request. We may need to verify your identity before fulfilling certain requests.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">10. Children</h2>
            <p class="text-stone-600 text-sm leading-relaxed">
                Our services are directed at adults who may purchase gifts for others. We do not knowingly collect personal data from children under 16 (or the age required in your jurisdiction) without parental consent. If you believe we have collected a child’s data in error, please contact us and we will delete it promptly.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">11. Automated decisions</h2>
            <p class="text-stone-600 text-sm leading-relaxed">
                We do not use fully automated decision-making that produces legal or similarly significant effects solely by automated means. Some fraud checks may use automated scoring; you may contact us for human review if this affects you unfairly.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">12. Changes to this policy</h2>
            <p class="text-stone-600 text-sm leading-relaxed">
                We may update this Privacy Policy to reflect legal, technical, or business changes. The “Last updated” date at the top will change when we do. For material changes, we may provide additional notice (e.g. on the site or by email). We encourage you to review this page periodically.
            </p>
        </section>

        <section class="rounded-2xl max-md:rounded-xl bg-white border border-stone-200 p-6 max-md:p-4 shadow-soft">
            <h2 class="font-display text-lg font-semibold text-stone-800 mb-3 max-md:text-base">13. Contact us</h2>
            <p class="text-stone-600 text-sm leading-relaxed">
                For privacy-related questions, concerns, or requests, please <a href="{{ route('page.contact') }}" class="text-primary-600 font-medium hover:underline">contact us</a> using the details on our website.
            </p>
        </section>
    </div>

    {{-- CTA --}}
    <div class="rounded-2xl max-md:rounded-xl border border-warning-200/80 bg-gradient-to-br from-amber-50 to-orange-50/80 px-6 py-6 max-md:px-4 max-md:py-5 shadow-soft">
        <h2 class="font-display text-lg font-semibold text-stone-800 mb-2 max-md:text-base">Related policies</h2>
        <p class="text-stone-600 text-sm leading-relaxed mb-4 max-md:text-xs">
            <a href="{{ route('page.terms') }}" class="text-primary-600 font-medium hover:underline">Terms of Service</a>
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
