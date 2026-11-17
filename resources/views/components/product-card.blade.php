@props(['product', 'trending' => false, 'largeFont' => false])

@php
    $images = $product->images;
    $image = $images->first();
    $fallbackImageUrl = asset('images/logo.webp') . '?v=' . config('app.logo_version', 2);
    $imageUrl = $image?->public_url ?: $fallbackImageUrl;
    $hasNoImage = !$image;
    $price = $product->effective_price;
    $originalPrice = $product->discount_price ? $product->price : null;
    $description = $product->description ? Str::limit(strip_tags($product->description), 70) : '';
    $discountPercent = $originalPrice ? round((1 - $price / $originalPrice) * 100) : 0;
    $stockQty = (int) $product->stock;
    $outOfStock = ($stockQty <= 0) || ($product->in_stock === false);
    $userEmail = auth()->user()?->email ?? '';
    $rating = (float) ($product->rating ?? $product->reviews_avg_rating ?? 0);
    $reviewCount = (int) ($product->reviews_count ?? $product->reviews->count() ?? 0);
    $reviewCountFormatted = $reviewCount >= 1000 ? round($reviewCount / 1000, 1) . 'k' : (string) $reviewCount;
@endphp

<article x-data='{"wishlisted": typeof localStorage !== "undefined" && localStorage.getItem("wishlist_{{ $product->id }}") === "1", "notifyModalOpen": false, "notifyEmail": @json($userEmail), "notifySubmitting": false, "notifySuccess": false}'
         class="group h-full flex flex-col bg-white overflow-hidden transition-all duration-300 focus-within:ring-2 focus-within:ring-primary-500/20 focus-within:border-primary-300
                rounded-2xl border border-stone-200/80 shadow-soft
                md:rounded-[1.125rem] md:border md:border-stone-200 md:shadow-[0_2px_12px_-4px_rgba(0,0,0,0.06),0_6px_20px_-6px_rgba(0,0,0,0.04)]
                max-md:rounded-2xl max-md:border max-md:border-stone-100 max-md:shadow-[0_2px_8px_-2px_rgba(0,0,0,0.06),0_4px_16px_-4px_rgba(0,0,0,0.04)]
                {{ $outOfStock ? 'hover:border-stone-300 hover:shadow-soft md:hover:shadow-[0_4px_16px_-4px_rgba(0,0,0,0.08)]' : 'max-md:active:scale-[0.98] md:hover:shadow-[0_12px_28px_-8px_rgba(0,0,0,0.1),0_0_0_1px_rgba(16,185,129,0.12)] md:hover:border-primary-200/80 md:hover:-translate-y-1' }}
                {{ $trending ? ' product-card--trending' : '' }}">
    <a href="{{ route('products.show', $product->slug) }}" class="flex flex-col flex-1 min-w-0 outline-none">
        <div class="relative aspect-[5/4] max-md:aspect-square bg-stone-100 overflow-hidden md:rounded-t-[0.875rem] max-md:rounded-t-2xl">
            {{-- Top-left: status badges — compact on mobile --}}
            <div class="absolute top-2 left-2 z-10 flex flex-col gap-1 items-start max-md:top-2 max-md:left-2 max-md:gap-1">
                @if($outOfStock)
                    <span class="px-2 py-1 rounded-md text-white text-[10px] font-semibold bg-stone-700/95 backdrop-blur-sm max-md:px-2 max-md:py-0.5 max-md:rounded max-md:text-[10px] max-md:font-bold">Out of Stock</span>
                @endif
                @if($discountPercent > 0)
                    <span class="px-2 py-0.5 rounded-md bg-danger-500 text-white text-[10px] font-bold shadow-sm max-md:rounded max-md:px-1.5 max-md:py-0.5 max-md:text-[10px]">{{ $discountPercent }}%</span>
                @endif
            </div>

            {{-- Top-right: wishlist — larger tap target on mobile --}}
            <div class="absolute top-2 right-2 z-10 max-md:top-2 max-md:right-2">
                <div>
                    @auth
                        @if($inWishlist ?? false)
                            <form action="{{ route('wishlist.remove', $product) }}" method="POST" class="inline" @click.stop>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-full bg-white/95 shadow-soft border border-stone-200/80 text-warning-500 hover:bg-warning-50 focus:ring-2 focus:ring-warning-400/40 transition-all max-md:p-2.5 max-md:min-w-[44px] max-md:min-h-[44px] max-md:flex max-md:items-center max-md:justify-center" aria-label="Remove from wishlist">
                                    <svg class="w-5 h-5 max-md:w-5 max-md:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('wishlist.add', $product) }}" method="POST" class="inline" @click.stop>
                                @csrf
                                <button type="submit" class="p-2 rounded-full bg-white/95 shadow-soft border border-stone-200/80 text-stone-400 hover:text-warning-500 hover:bg-warning-50 hover:border-warning-400/60 focus:ring-2 focus:ring-warning-400/40 transition-all max-md:p-2.5 max-md:min-w-[44px] max-md:min-h-[44px] max-md:flex max-md:items-center max-md:justify-center active:scale-95" aria-label="Add to wishlist">
                                    <svg class="w-5 h-5 max-md:w-5 max-md:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                </button>
                            </form>
                        @endif
                    @else
                        <button type="button" @click.stop.prevent="wishlisted = !wishlisted; localStorage.setItem('wishlist_{{ $product->id }}', wishlisted ? '1' : '0')"
                                class="p-2 rounded-full bg-white/95 shadow-soft border border-stone-200/80 transition-all focus:ring-2 focus:ring-warning-400/40 max-md:p-2.5 max-md:min-w-[44px] max-md:min-h-[44px] max-md:flex max-md:items-center max-md:justify-center active:scale-95"
                                :class="wishlisted ? 'text-warning-500 border-warning-400/80 bg-warning-50' : 'text-stone-400 hover:text-warning-500 hover:bg-warning-50 hover:border-warning-400/60'"
                                aria-label="Wishlist">
                            <svg x-show="!wishlisted" class="w-5 h-5 max-md:w-5 max-md:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            <svg x-show="wishlisted" x-cloak class="w-5 h-5 max-md:w-5 max-md:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </button>
                    @endauth
                </div>
            </div>

            <img src="{{ $imageUrl }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full transition-transform duration-300 {{ $hasNoImage ? 'object-contain opacity-40 grayscale bg-stone-100 p-6 max-md:p-12 md:p-6' : 'object-cover' }} {{ $outOfStock ? 'group-hover:scale-100' : 'md:group-hover:scale-[1.05] max-md:group-active:scale-[1.02]' }}"
                 loading="lazy"
                 onerror="this.onerror=null;this.src='{{ $fallbackImageUrl }}';this.classList.remove('object-cover');this.classList.add('object-contain','opacity-40','grayscale','bg-stone-100','p-6');"
                 style="{{ $outOfStock ? 'opacity: 0.82; filter: grayscale(15%);' : '' }}">
        </div>

        {{-- Content: tighter on mobile, clearer hierarchy; desktop: refined padding & typography --}}
        <div class="product-card-content flex flex-col flex-1 p-4 sm:p-5 max-md:p-3.5 max-md:pt-3 bg-white md:p-5 md:pb-5 md:rounded-b-[0.875rem] max-md:rounded-b-2xl">
            <h3 class="font-display font-bold text-stone-900 leading-snug line-clamp-2 group-hover:text-primary-600 transition-colors {{ $largeFont ? 'text-lg max-md:text-base md:text-lg md:leading-snug' : 'text-base sm:text-lg max-md:text-[13px] max-md:leading-tight md:text-lg md:leading-snug' }}">{{ $product->name }}</h3>
            @if($rating > 0 || $reviewCount > 0)
                <div class="mt-2 max-md:mt-1.5 flex items-center gap-1.5 flex-wrap" aria-label="Rating {{ $rating }} out of 5">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 max-md:w-3 max-md:h-3 {{ $i <= round($rating) ? 'text-amber-400' : 'text-stone-200' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                    <span class="font-medium text-stone-600 {{ $largeFont ? 'text-sm max-md:text-xs' : 'text-sm max-md:text-[11px] max-md:font-semibold' }}">{{ number_format($rating, 1) }}</span>
                    @if($reviewCount > 0)
                        <span class="text-stone-500 max-md:sr-only {{ $largeFont ? 'text-sm max-md:text-xs' : 'text-sm max-md:text-[11px]' }}">({{ $reviewCountFormatted }} {{ $reviewCount === 1 ? 'review' : 'reviews' }})</span>
                    @endif
                </div>
            @endif
            <div class="mt-auto pt-3 max-md:pt-2 flex flex-wrap items-baseline gap-2 md:pt-4">
                <span class="font-bold text-stone-900 tabular-nums {{ $largeFont ? 'text-xl max-md:text-lg md:text-xl' : 'text-xl max-md:text-[15px] max-md:font-bold md:text-xl' }}">LKR {{ number_format($price, 0) }}</span>
                @if($originalPrice)
                    <span class="text-stone-400 line-through tabular-nums {{ $largeFont ? 'text-sm max-md:text-xs' : 'text-sm max-md:text-[11px]' }}">LKR {{ number_format($originalPrice, 0) }}</span>
                @endif
            </div>
            {{-- Actions: single full-width primary on mobile, side-by-side with clear primary; desktop: refined buttons; trending: 2 lines on desktop --}}
            <div class="product-card-actions mt-4 flex gap-2 max-md:mt-3 max-md:flex-col max-md:gap-2 md:mt-5 md:gap-3 {{ $trending ? 'md:flex-col md:gap-2' : 'md:gap-3' }}">
                @if($outOfStock)
                    <div @click.stop class="flex-1 max-md:w-full">
                        <button type="button"
                                @click.stop.prevent='notifyModalOpen = true; notifySuccess = false; notifyEmail = @json($userEmail)'
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-stone-200 bg-stone-50 text-stone-700 text-sm font-medium hover:bg-stone-100 hover:border-stone-300 transition-all duration-200 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 max-md:min-h-[44px] max-md:py-3 max-md:text-sm max-md:rounded-lg max-md:font-semibold max-md:border-stone-200 max-md:bg-stone-100/80 active:scale-[0.98]">
                            Notify when back
                        </button>
                    </div>
                @else
                    <form action="{{ route('cart.add', $product) }}" method="POST" @click.stop class="flex-1 min-w-0 max-md:w-full">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-stone-200 bg-white text-stone-700 text-sm font-semibold hover:bg-stone-50 hover:border-stone-300 transition-all duration-200 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 max-md:min-h-[44px] max-md:py-3 max-md:text-sm max-md:rounded-lg max-md:border-stone-200 max-md:bg-stone-50 max-md:font-semibold active:scale-[0.98] md:rounded-lg md:py-2.5 md:hover:border-primary-200 md:hover:bg-primary-50/50 md:hover:text-primary-700">
                            Add to Cart
                        </button>
                    </form>
                    <form action="{{ route('cart.add', $product) }}" method="POST" @click.stop class="flex-1 min-w-0 max-md:w-full">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="redirect" value="checkout">
                        <input type="hidden" name="buy_now" value="1">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 focus:ring-2 focus:ring-primary-400 focus:ring-offset-2 transition-all duration-200 max-md:min-h-[44px] max-md:py-3 max-md:text-sm max-md:rounded-lg max-md:font-bold max-md:shadow-[0_2px_8px_-2px_rgba(16,185,129,0.4)] active:scale-[0.98] md:rounded-lg md:py-2.5 md:shadow-[0_2px_10px_-2px_rgba(16,185,129,0.35)] md:hover:shadow-[0_4px_14px_-2px_rgba(16,185,129,0.4)] md:hover:-translate-y-0.5">
                            Buy Now
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </a>

    {{-- Notify Me When Available modal (teleport to body so it shows above everything) --}}
    @if($outOfStock)
    <template x-teleport="body">
    <div x-show="notifyModalOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4"
         aria-modal="true"
         role="dialog"
         aria-labelledby="notify-modal-title">
        <div class="fixed inset-0 bg-stone-900/40" @click="notifyModalOpen = false" x-show="notifyModalOpen"></div>
        <div x-show="notifyModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative z-50 w-full max-w-sm bg-white rounded-2xl shadow-luxury border border-stone-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 id="notify-modal-title" class="font-display font-semibold text-stone-800 text-lg">Notify when available</h2>
                    <button type="button" @click="notifyModalOpen = false" class="p-2 rounded-xl text-stone-400 hover:text-stone-600 hover:bg-stone-100 transition-colors" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <p class="text-sm text-stone-500 mb-4">We'll email you when <span class="font-medium text-stone-700">{{ $product->name }}</span> is back in stock.</p>
                <template x-if="!notifySuccess">
                    <form @submit.prevent="
                        notifySubmitting = true;
                        fetch('{{ route('notify.when-available', $product) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ email: notifyEmail })
                        })
                        .then(r => r.ok ? r.json() : Promise.reject(r))
                        .then(() => { notifySuccess = true; })
                        .catch(() => { alert('Something went wrong. Please try again.'); })
                        .finally(() => { notifySubmitting = false; });
                    ">
                        <label for="notify-email-{{ $product->id }}" class="sr-only">Email address</label>
                        <input type="email"
                               id="notify-email-{{ $product->id }}"
                               x-model="notifyEmail"
                               required
                               placeholder="your@email.com"
                               class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm text-stone-800 placeholder-stone-400 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors">
                        <div class="mt-4 flex gap-2">
                            <button type="button" @click="notifyModalOpen = false" class="flex-1 py-2.5 rounded-xl border-2 border-stone-200 text-stone-600 font-medium text-sm hover:bg-stone-50 transition-colors">Cancel</button>
                            <button type="submit" class="flex-1 py-2.5 rounded-xl bg-primary-500 text-white font-semibold text-sm hover:bg-primary-600 transition-colors" :disabled="notifySubmitting">
                                <span x-show="!notifySubmitting">Notify me</span>
                                <span x-show="notifySubmitting" x-cloak>Sending…</span>
                            </button>
                        </div>
                    </form>
                </template>
                <template x-if="notifySuccess">
                    <div class="py-2 text-center">
                        <p class="text-sm font-medium text-primary-700">We'll notify you when it's back in stock.</p>
                        <button type="button" @click="notifyModalOpen = false" class="mt-4 px-5 py-2.5 rounded-xl bg-stone-100 text-stone-700 font-medium text-sm hover:bg-stone-200 transition-colors">Close</button>
                    </div>
                </template>
            </div>
        </div>
    </div>
    </template>
    @endif
</article>
