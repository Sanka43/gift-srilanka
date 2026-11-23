@extends('layouts.app')

@section('title', 'Redirecting to Payment')
@section('noindex', '1')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-3xl border border-stone-200 shadow-soft p-8 text-center">
        <!-- Animated Spinner -->
        <div class="relative mx-auto w-16 h-16 mb-6">
            <div class="w-16 h-16 rounded-full border-4 border-primary-100 border-t-primary-600 animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
        </div>

        <h1 class="font-display text-xl font-bold text-stone-800 mb-2">Connecting to Secure Payment</h1>
        <p class="text-sm text-stone-600 mb-6">
            Please wait while we redirect you to PayHere to complete your payment of 
            <span class="font-bold text-stone-900">Rs. {{ number_format($order->total_amount, 2) }}</span>.
        </p>

        <!-- Hidden PayHere Auto-Submit Form -->
        <form id="payhere_form" action="{{ $checkoutUrl }}" method="POST">
            @foreach ($payload as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach

            <noscript>
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold shadow-md transition-all">
                    Proceed to PayHere
                </button>
            </noscript>
        </form>

        <div class="mt-4 pt-4 border-t border-stone-100">
            <p class="text-xs text-stone-400 mb-3">If you are not redirected automatically within 5 seconds:</p>
            <button type="button" onclick="document.getElementById('payhere_form').submit();" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-stone-200 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition-all">
                <span>Click here to continue</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </div>

        <div class="mt-6 flex items-center justify-center gap-2 text-[11px] text-stone-400">
            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>256-Bit SSL Encrypted | PayHere Certified</span>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            var form = document.getElementById('payhere_form');
            if (form) {
                form.submit();
            }
        }, 800);
    });
</script>
@endsection
