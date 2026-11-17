@php
    $mainCategories = $mainCategories ?? collect();
    $submitActionDebounced = $submitActionDebounced ?? $submitAction;
@endphp
<form method="GET" action="{{ route('shop.index') }}" id="{{ $formId }}" x-ref="{{ $formRef }}" class="filter-form space-y-0">
    <input type="hidden" name="search" value="{{ request('search') }}">
    <input type="hidden" name="min_price" :value="minPrice">
    <input type="hidden" name="max_price" :value="maxPrice">
    <input type="hidden" name="sort" :value="sortBy">

    {{-- Category --}}
    <div class="filter-section">
        <h3 class="filter-section-title">Category</h3>
        <div class="filter-options">
            <label class="filter-option">
                <input type="radio" name="category" value="" x-model="selectedCategory" @change="{{ $submitAction }}()" class="filter-radio">
                <span class="filter-option-label">All</span>
                @if(isset($categoryCounts['all']))
                    <span class="filter-count">{{ $categoryCounts['all'] }}</span>
                @endif
            </label>
            @foreach($mainCategories as $mainCat)
                @if($mainCat->children->isEmpty())
                    <label class="filter-option">
                        <input type="radio" name="category" value="{{ $mainCat->slug }}" x-model="selectedCategory" @change="{{ $submitAction }}()" class="filter-radio">
                        <span class="filter-option-label">{{ $mainCat->name }}</span>
                        @if(isset($categoryCounts[$mainCat->slug]))
                            <span class="filter-count">{{ $categoryCounts[$mainCat->slug] }}</span>
                        @endif
                    </label>
                @else
                    <div class="py-0.5" x-data="{ open: {{ $mainCat->children->contains('slug', request('category')) ? 'true' : 'false' }} }">
                        <button type="button" @click="open = !open" class="filter-option w-full text-left flex items-center gap-2">
                            <span class="text-stone-400 transition-transform" :class="open ? 'rotate-90' : ''">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </span>
                            <span class="text-sm font-medium text-stone-700">{{ $mainCat->name }}</span>
                        </button>
                        <div x-show="open" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             class="pl-4 border-l border-stone-200 ml-1 mt-0.5 space-y-0.5">
                            @foreach($mainCat->children as $sub)
                                <label class="filter-option">
                                    <input type="radio" name="category" value="{{ $sub->slug }}" x-model="selectedCategory" @change="{{ $submitAction }}()" class="filter-radio">
                                    <span class="filter-option-label">{{ $sub->name }}</span>
                                    @if(isset($categoryCounts[$sub->slug]))
                                        <span class="filter-count">{{ $categoryCounts[$sub->slug] }}</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Occasion --}}
    <div class="filter-section">
        <h3 class="filter-section-title">Occasion</h3>
        <div class="filter-chips">
            @foreach($occasions as $occ)
                <label class="filter-chip {{ in_array($occ, $currentOccasions) ? 'filter-chip-active' : '' }}">
                    <input type="checkbox" name="occasion[]" value="{{ $occ }}" {{ in_array($occ, $currentOccasions) ? 'checked' : '' }} @change="{{ $submitAction }}()" class="sr-only">
                    <span>{{ $occ }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Recipient --}}
    <div class="filter-section">
        <h3 class="filter-section-title">Recipient</h3>
        <div class="filter-chips">
            @foreach($recipients as $rec)
                <label class="filter-chip {{ in_array($rec, $currentRecipients) ? 'filter-chip-active' : '' }}">
                    <input type="checkbox" name="recipient[]" value="{{ $rec }}" {{ in_array($rec, $currentRecipients) ? 'checked' : '' }} @change="{{ $submitAction }}()" class="sr-only">
                    <span>{{ $rec }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Price --}}
    @if($maxPrice > $minPrice)
    <div class="filter-section">
        <h3 class="filter-section-title">Price (LKR)</h3>
        <div class="grid grid-cols-2 gap-2 mb-2">
            <input type="number" id="{{ $formId }}-min-price" x-model.number="minPrice" @input="{{ $submitActionDebounced }}()" @change="minPrice = Math.min(Math.max(minPrice, {{ $minPrice }}), maxPrice - 1); {{ $submitAction }}()" min="{{ $minPrice }}" max="{{ $maxPrice }}" step="100" class="filter-input" placeholder="Min">
            <input type="number" id="{{ $formId }}-max-price" x-model.number="maxPrice" @input="{{ $submitActionDebounced }}()" @change="maxPrice = Math.max(Math.min(maxPrice, {{ $maxPrice }}), minPrice + 1); {{ $submitAction }}()" min="{{ $minPrice }}" max="{{ $maxPrice }}" step="100" class="filter-input" placeholder="Max">
        </div>
        <div class="space-y-2">
            <input type="range" x-model.number="minPrice" @input="minPrice = Math.min(minPrice, maxPrice - 1)" @change="{{ $submitAction }}()" min="{{ $minPrice }}" max="{{ $maxPrice }}" step="100" class="price-slider w-full" aria-label="Min price">
            <input type="range" x-model.number="maxPrice" @input="maxPrice = Math.max(maxPrice, minPrice + 1)" @change="{{ $submitAction }}()" min="{{ $minPrice }}" max="{{ $maxPrice }}" step="100" class="price-slider w-full" aria-label="Max price">
        </div>
    </div>
    @endif

    {{-- Delivery --}}
    <div class="filter-section">
        <h3 class="filter-section-title">Delivery</h3>
        <div class="filter-options filter-options-compact">
            <label class="filter-option">
                <input type="radio" name="delivery_type" value="" {{ request('delivery_type') === '' ? 'checked' : '' }} @change="{{ $submitAction }}()" class="filter-radio">
                <span class="filter-option-label">Any</span>
            </label>
            @foreach($deliveryOptions as $value => $label)
                <label class="filter-option">
                    <input type="radio" name="delivery_type" value="{{ $value }}" {{ request('delivery_type') === $value ? 'checked' : '' }} @change="{{ $submitAction }}()" class="filter-radio">
                    <span class="filter-option-label">{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Rating --}}
    <div class="filter-section">
        <h3 class="filter-section-title">Rating</h3>
        <div class="filter-options filter-options-compact">
            <label class="filter-option">
                <input type="radio" name="rating" value="" {{ request('rating') === '' ? 'checked' : '' }} @change="{{ $submitAction }}()" class="filter-radio">
                <span class="filter-option-label">Any</span>
            </label>
            <label class="filter-option">
                <input type="radio" name="rating" value="4" {{ request('rating') === '4' ? 'checked' : '' }} @change="{{ $submitAction }}()" class="filter-radio">
                <span class="filter-option-label">4★+</span>
            </label>
            <label class="filter-option">
                <input type="radio" name="rating" value="3" {{ request('rating') === '3' ? 'checked' : '' }} @change="{{ $submitAction }}()" class="filter-radio">
                <span class="filter-option-label">3★+</span>
            </label>
        </div>
    </div>

    {{-- Availability --}}
    <div class="filter-section">
        <h3 class="filter-section-title">Availability</h3>
        <label class="filter-toggle">
            <span class="filter-option-label">In stock only</span>
            <button type="button" role="switch" @click="inStockOnly = !inStockOnly; {{ $submitAction }}()" :aria-checked="inStockOnly"
                class="filter-toggle-track"
                :class="inStockOnly ? 'filter-toggle-on' : ''">
                <span class="filter-toggle-thumb" :class="inStockOnly ? 'filter-toggle-thumb-on' : ''"></span>
            </button>
        </label>
        <input type="hidden" name="in_stock" :value="inStockOnly ? '1' : ''">
    </div>

    {{-- Deals --}}
    <div class="filter-section filter-section-last">
        <h3 class="filter-section-title">Deals</h3>
        <label class="filter-option">
            <input type="checkbox" name="on_sale" value="1" {{ request('on_sale') ? 'checked' : '' }} @change="{{ $submitAction }}()" class="filter-checkbox">
            <span class="filter-option-label">On sale</span>
            @if(request('on_sale'))
                <span class="filter-badge-sale">On</span>
            @endif
        </label>
    </div>
</form>
