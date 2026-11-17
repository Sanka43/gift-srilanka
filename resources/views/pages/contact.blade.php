@extends('layouts.app')

@section('title', 'Contact Us')
@section('meta_description', 'Contact Gifts Sri Lanka for orders, support, or enquiries. We deliver across Colombo and beyond.')

@section('content')
<div class="max-w-3xl mx-auto max-md:min-w-0">
    <h1 class="font-display text-3xl sm:text-4xl max-md:text-2xl font-bold text-stone-800 tracking-tight mb-2">Contact Us</h1>
    <p class="text-stone-500 text-lg mb-10 max-md:mb-6">We'd love to hear from you. Get in touch for orders, support, or general enquiries.</p>

    @auth
    <section class="rounded-2xl bg-white border border-stone-200 shadow-soft p-6 sm:p-8 max-md:p-5 max-md:rounded-xl mb-8 max-md:mb-6">
        <h2 class="font-display text-xl font-semibold text-stone-800 mb-4">Send a message</h2>
        <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="subject" class="block text-sm font-medium text-stone-700 mb-1">Subject</label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                       class="w-full rounded-xl border border-stone-300 px-4 py-2.5 text-stone-800 placeholder-stone-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error('subject') border-danger-500 focus:border-danger-500 focus:ring-danger-500/20 @enderror"
                       placeholder="e.g. Order enquiry, Product question">
                @error('subject')
                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="message" class="block text-sm font-medium text-stone-700 mb-1">Message</label>
                <textarea name="message" id="message" rows="5" required
                          class="w-full rounded-xl border border-stone-300 px-4 py-2.5 text-stone-800 placeholder-stone-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error('message') border-danger-500 focus:border-danger-500 focus:ring-danger-500/20 @enderror"
                          placeholder="Your message...">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 max-md:min-h-[2.75rem] max-md:w-full text-sm font-semibold text-white shadow-soft hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                Send message
            </button>
        </form>
    </section>
    @else
    <div class="rounded-2xl bg-primary-50 border border-primary-200 p-6 mb-8">
        <p class="text-primary-800 font-medium">Please <a href="{{ route('login') }}" class="underline hover:no-underline">log in</a> or <a href="{{ route('register') }}" class="underline hover:no-underline">register</a> to send us a message.</p>
    </div>
    @endauth

    <div class="prose prose-stone prose-lg max-w-none prose-headings:font-display prose-headings:text-stone-800 prose-a:text-primary-600 prose-a:no-underline hover:prose-a:underline space-y-8 max-md:space-y-6">
        <section class="rounded-2xl bg-white border border-stone-200 shadow-soft p-6 sm:p-8 max-md:p-5 max-md:rounded-xl not-prose">
            <h2 class="font-display text-xl font-semibold text-stone-800 mb-4">Contact Details</h2>
            <ul class="space-y-3 text-stone-600">
                <li class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-100 text-primary-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <span><strong class="text-stone-800">Email</strong><br>{{ config('contact.email') }}</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-100 text-primary-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </span>
                    <span><strong class="text-stone-800">Phone</strong><br>{{ config('contact.phone') }}</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-100 text-primary-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </span>
                    <span><strong class="text-stone-800">Address</strong><br>{{ config('contact.address') }}</span>
                </li>
            </ul>
        </section>

        <p class="text-stone-600">
            For order-related questions, include your order number so we can help you faster. We aim to respond within 24 hours on business days.
        </p>
    </div>
</div>
@endsection
