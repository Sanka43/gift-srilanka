@extends('layouts.app')

@section('title', 'Profile')
@section('noindex', '1')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 max-md:px-0 max-md:min-w-0">
    {{-- Profile header: avatar or initial + greeting + subtitle --}}
    @php
        $avatarUrl = $user->avatar_path ? asset('storage/' . str_replace('\\', '/', $user->avatar_path)) : null;
    @endphp
    <header class="flex flex-wrap items-center gap-4 sm:gap-5 max-md:gap-3 animate-fade-in">
        <div class="flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 max-md:w-14 max-md:h-14 rounded-full border-2 border-stone-200 bg-stone-50 text-stone-700 overflow-hidden shrink-0 ring-2 ring-white shadow-soft" aria-hidden="true">
            @if($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="" class="w-full h-full object-cover" />
            @else
                <span class="text-2xl sm:text-3xl font-display font-semibold">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</span>
            @endif
        </div>
        <div>
            <h1 class="font-display text-2xl sm:text-3xl max-md:text-xl font-bold text-stone-800 tracking-tight">
                Hello, {{ $user->name }}
            </h1>
            <p class="mt-1 text-sm text-stone-600">
                Manage your account and delivery details.
            </p>
        </div>
    </header>

    {{-- Inline success message (in addition to global flash) --}}
    @if(session('success'))
        <div x-data="{ visible: true }" x-show="visible" x-init="setTimeout(() => visible = false, 4000)" x-transition:leave="transition ease-in duration-300"
             class="rounded-2xl bg-primary-50 border border-primary-200 text-primary-800 px-4 py-3 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- Personal information card --}}
    <section class="bg-white rounded-2xl border border-stone-200 shadow-soft p-6 sm:p-8 max-md:p-5 max-md:rounded-xl animate-fade-in-up">
        <p class="text-xs font-semibold text-stone-500 uppercase tracking-[0.2em] mb-1">Account</p>
        <h2 class="font-display text-xl font-bold text-stone-800 mb-6">Personal information</h2>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PATCH')

            {{-- Profile picture --}}
            <div x-data="{ preview: @js($avatarUrl) }">
                <label class="block text-sm font-medium text-stone-700 mb-2">Profile picture</label>
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center justify-center w-20 h-20 rounded-full border-2 border-stone-200 bg-stone-50 text-stone-600 overflow-hidden shrink-0">
                        <img x-show="preview" :src="preview" alt="" class="w-full h-full object-cover" />
                        <span x-show="!preview" class="text-2xl font-display font-semibold">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 min-w-0 max-md:flex-col max-md:items-stretch">
                        <label class="inline-flex items-center gap-2 px-4 py-2.5 border border-stone-300 rounded-xl text-stone-700 font-medium text-sm hover:bg-stone-50 cursor-pointer focus-within:ring-2 focus-within:ring-primary-500 focus-within:ring-offset-2 transition ease-in-out duration-200">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v4a2 2 0 002 2h12a2 2 0 002-2v-4"/></svg>
                            <span>{{ $avatarUrl ? 'Change photo' : 'Add photo' }}</span>
                            <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/jpg,image/webp" class="sr-only"
                                x-on:change="const f = $event.target.files[0]; if (f) { const r = new FileReader(); r.onload = e => { preview = e.target.result; }; r.readAsDataURL(f); }">
                        </label>
                        @if($avatarUrl)
                            <label class="inline-flex items-center gap-2 px-4 py-2.5 border border-danger-200 rounded-xl text-danger-700 font-medium text-sm hover:bg-danger-50 cursor-pointer transition ease-in-out duration-200">
                                <input type="checkbox" name="remove_avatar" value="1" class="rounded border-stone-300 text-danger-600 focus:ring-danger-500">
                                <span>Remove photo</span>
                            </label>
                        @endif
                    </div>
                </div>
                <p class="text-xs text-stone-500 mt-2">JPG, PNG or WebP. Max 4 MB.</p>
                @error('avatar')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-stone-700 mb-1">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                    class="w-full rounded-xl border border-stone-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-stone-800">
                @error('name')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-stone-700 mb-1">Email</label>
                <input type="email" id="email" value="{{ $user->email }}" readonly
                    class="w-full rounded-xl bg-stone-100 cursor-not-allowed border border-stone-200 text-stone-600" tabindex="-1">
                <p class="text-xs text-stone-500 mt-1">Email cannot be changed.</p>
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-stone-700 mb-1">Phone</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone ?? '') }}"
                    class="w-full rounded-xl border border-stone-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-stone-800">
                @error('phone')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-stone-700 mb-1">Address</label>
                <input type="text" name="address" id="address" value="{{ old('address', $user->address ?? '') }}"
                    class="w-full rounded-xl border border-stone-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-stone-800">
                @error('address')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="district" class="block text-sm font-medium text-stone-700 mb-1">District</label>
                <input type="text" name="district" id="district" value="{{ old('district', $user->district ?? '') }}"
                    class="w-full rounded-xl border border-stone-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-stone-800">
                @error('district')<p class="text-danger-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 max-md:min-h-[2.75rem] max-md:w-full bg-primary-600 text-white rounded-xl font-semibold text-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-200">
                    Update Profile
                </button>
            </div>
        </form>
    </section>

    {{-- Account actions card --}}
    <section class="bg-white rounded-2xl border border-stone-200 shadow-soft p-6 sm:p-8 max-md:p-5 max-md:rounded-xl">
        <p class="text-xs font-semibold text-stone-500 uppercase tracking-[0.2em] mb-1">Account</p>
        <h2 class="font-display text-xl max-md:text-lg font-bold text-stone-800 mb-4">Quick actions</h2>
        <div class="flex flex-wrap gap-3 max-md:flex-col max-md:gap-2">
            <a href="{{ route('orders.my') }}" class="inline-flex items-center justify-center px-4 py-2.5 max-md:min-h-[2.75rem] max-md:w-full border border-stone-300 rounded-xl text-stone-700 font-medium text-sm hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-200">
                My Orders
            </a>
            <a href="{{ route('wishlist.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 max-md:min-h-[2.75rem] max-md:w-full border border-stone-300 rounded-xl text-stone-700 font-medium text-sm hover:bg-amber-50 hover:text-amber-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-200">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                Wishlist
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline max-md:w-full">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-2.5 max-md:min-h-[2.75rem] border border-stone-300 rounded-xl text-stone-700 font-medium text-sm hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-stone-400 focus:ring-offset-2 transition ease-in-out duration-200">
                    Logout
                </button>
            </form>
        </div>
    </section>

    {{-- Delete account card --}}
    <section class="bg-white rounded-2xl border border-stone-200 shadow-soft p-6 sm:p-8 max-md:p-5 max-md:rounded-xl">
        @include('profile.partials.delete-user-form')
    </section>
</div>
@endsection
