@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="font-display text-2xl font-bold text-stone-800">Settings</h1>
        <p class="mt-1 text-sm text-stone-500">Store and application settings.</p>
    </div>

    <div class="admin-card rounded-2xl border border-stone-100 bg-white p-6 max-w-2xl">
        <h2 class="font-display text-lg font-semibold text-stone-800">General</h2>
        <p class="mt-2 text-sm text-stone-500">Configuration is managed via environment and config files. Future settings UI can be added here.</p>
    </div>
</div>
@endsection
