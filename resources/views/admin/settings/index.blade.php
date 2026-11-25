@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
@php
    $inputClass = 'w-full h-11 px-3.5 rounded-xl border border-stone-300 bg-white text-stone-800 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 text-sm';
    $d = $delivery;
    $val = fn ($key) => old($key, $d[$key]);
@endphp
<div class="space-y-6">
    <div>
        <h1 class="font-display text-2xl font-bold text-stone-800">Settings</h1>
        <p class="mt-1 text-sm text-stone-500">Store and application settings.</p>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.delivery.update') }}" class="admin-card rounded-2xl border border-stone-100 bg-white p-6 max-w-4xl space-y-6 !p-4 sm:!p-6">
        @csrf
        @method('PUT')

        <div>
            <h2 class="font-display text-lg font-semibold text-stone-800">Delivery charge</h2>
            <p class="mt-1 text-sm text-stone-500">
                Charge = base fee for the first km, then the per-km fee for every extra km (rounded up to a full km). Distance is road distance from the start location to the customer's pin.
            </p>
        </div>

        <label class="flex items-center gap-3 text-sm font-medium text-stone-700">
            <input type="hidden" name="enabled" value="0">
            <input type="checkbox" name="enabled" value="1" @checked((bool) $val('enabled')) class="h-4 w-4 rounded border-stone-300 text-primary-600">
            Enable distance based delivery charge (when off, customers pay a flat Rs. {{ number_format((float) config('shop.delivery_charge', 350), 0) }} and no map pin is required)
        </label>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-1.5">Base distance (km)</label>
                <input type="number" step="0.1" min="0" name="base_km" id="f-base-km" value="{{ $val('base_km') }}" class="{{ $inputClass }}" required>
                <p class="text-xs text-stone-500 mt-1">The first X km are covered by the base fee.</p>
                @error('base_km')<p class="text-danger-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-1.5">Base fee (Rs.)</label>
                <input type="number" step="1" min="0" name="base_fee" id="f-base-fee" value="{{ $val('base_fee') }}" class="{{ $inputClass }}" required>
                @error('base_fee')<p class="text-danger-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-1.5">Fee per extra km (Rs.)</label>
                <input type="number" step="1" min="0" name="per_km_fee" id="f-per-km" value="{{ $val('per_km_fee') }}" class="{{ $inputClass }}" required>
                @error('per_km_fee')<p class="text-danger-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-1.5">Max delivery radius (km)</label>
                <input type="number" step="0.1" min="0" name="max_radius_km" value="{{ $val('max_radius_km') }}" class="{{ $inputClass }}" required>
                <p class="text-xs text-stone-500 mt-1">Orders beyond this road distance are blocked. 0 = no limit.</p>
                @error('max_radius_km')<p class="text-danger-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-1.5">Free delivery above (Rs.)</label>
                <input type="number" step="1" min="0" name="free_above" id="f-free" value="{{ $val('free_above') }}" class="{{ $inputClass }}" required>
                <p class="text-xs text-stone-500 mt-1">Cart subtotal at or above this gets free delivery. 0 = off.</p>
                @error('free_above')<p class="text-danger-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-stone-700 mb-2">Delivery start location</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-3">
                <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-stone-600 mb-1">Name / address label</label>
                    <input type="text" name="start_name" value="{{ $val('start_name') }}" class="{{ $inputClass }}" required>
                    @error('start_name')<p class="text-danger-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-600 mb-1">Latitude</label>
                    <input type="number" step="any" name="start_lat" id="f-lat" value="{{ $val('start_lat') }}" class="{{ $inputClass }}" required>
                    @error('start_lat')<p class="text-danger-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-600 mb-1">Longitude</label>
                    <input type="number" step="any" name="start_lng" id="f-lng" value="{{ $val('start_lng') }}" class="{{ $inputClass }}" required>
                    @error('start_lng')<p class="text-danger-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 mb-2">
                <input type="text" id="dm-search" placeholder="Search a place to move the pin" autocomplete="off" class="{{ $inputClass }} flex-1">
                <div class="grid grid-cols-2 sm:flex gap-2">
                    <button type="button" id="dm-search-btn" class="h-11 px-4 rounded-xl border border-stone-300 bg-white text-sm font-semibold text-stone-700 hover:bg-stone-50">Search</button>
                    <button type="button" id="dm-locate" class="h-11 px-4 rounded-xl border border-stone-300 bg-white text-sm font-semibold text-stone-700 hover:bg-stone-50">My location</button>
                </div>
            </div>
            <div id="dm-map" style="height: 18rem;" class="w-full rounded-xl border border-stone-300 bg-stone-100 z-0"></div>
            <p class="text-xs text-stone-500 mt-1">Click the map or drag the pin to set the start location. <span class="lg:hidden">On phones use two fingers to move the map.</span></p>
        </div>

        <div class="rounded-xl border border-stone-200 bg-stone-50 p-4">
            <h3 class="text-sm font-semibold text-stone-700 mb-2">Test calculator</h3>
            <div class="flex flex-wrap items-center gap-3 text-sm">
                <label class="text-stone-600">Distance (km)</label>
                <input type="number" step="0.1" min="0" id="t-km" value="12.3" class="{{ $inputClass }} !w-28">
                <label class="text-stone-600">Cart subtotal (Rs.)</label>
                <input type="number" step="1" min="0" id="t-sub" value="5000" class="{{ $inputClass }} !w-32">
                <span class="font-semibold text-stone-800">= <span id="t-result">-</span></span>
            </div>
        </div>

        <div>
            <button type="submit" class="rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-2.5 text-sm">Save delivery settings</button>
        </div>
    </form>
</div>

<script src="{{ asset('js/delivery-map.js') }}"></script>
<script>
(function () {
    var latEl = document.getElementById('f-lat'), lngEl = document.getElementById('f-lng');
    var start = { lat: parseFloat(latEl.value) || 6.9271, lng: parseFloat(lngEl.value) || 79.8612 };
    var googleKey = @json(config('shop.maps.google_browser_key'));

    DeliveryMap.init({
        el: document.getElementById('dm-map'),
        provider: googleKey ? 'google' : 'osm',
        googleKey: googleKey,
        center: start, zoom: 13, initial: start,
        searchInput: document.getElementById('dm-search'),
        searchButton: document.getElementById('dm-search-btn'),
        locateButton: document.getElementById('dm-locate'),
        onPick: function (lat, lng) { latEl.value = lat.toFixed(7); lngEl.value = lng.toFixed(7); }
    });

    function calc() {
        var km = parseFloat(document.getElementById('t-km').value) || 0;
        var sub = parseFloat(document.getElementById('t-sub').value) || 0;
        var baseKm = parseFloat(document.getElementById('f-base-km').value) || 0;
        var baseFee = parseFloat(document.getElementById('f-base-fee').value) || 0;
        var perKm = parseFloat(document.getElementById('f-per-km').value) || 0;
        var free = parseFloat(document.getElementById('f-free').value) || 0;
        var fee = (free > 0 && sub >= free) ? 0 : baseFee + Math.ceil(Math.max(0, km - baseKm)) * perKm;
        document.getElementById('t-result').textContent = fee === 0 ? 'Free' : 'Rs. ' + Math.round(fee).toLocaleString('en-US');
    }
    ['t-km', 't-sub', 'f-base-km', 'f-base-fee', 'f-per-km', 'f-free'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', calc);
    });
    calc();
})();
</script>
@endsection
