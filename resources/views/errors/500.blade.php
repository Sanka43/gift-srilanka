@extends('layouts.app')

@section('title', 'Server Error')

@section('content')
<div class="max-w-xl mx-auto px-4 py-16 text-center">
    <h1 class="font-display text-2xl font-bold text-stone-800 mb-2">Something went wrong</h1>
    <p class="text-stone-600 mb-6">We're sorry. Please try again later.</p>
    <a href="{{ route('home') }}" class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700">
        Back to home
    </a>
</div>
@endsection
