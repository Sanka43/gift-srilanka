@php
    $messages = $adMessages ?? [];
    $messagesDuplicated = !empty($messages) ? array_merge($messages, $messages) : [];
@endphp
@if(!empty($messagesDuplicated))
<div class="ad-bar h-10 flex items-center overflow-hidden bg-primary-600 text-white text-xs sm:text-sm font-medium" aria-hidden="true">
    <div class="ad-marquee flex items-center whitespace-nowrap animate-marquee gap-x-12">
        @foreach($messagesDuplicated as $msg)
            <span class="ad-message shrink-0">{{ $msg }}</span>
            <span class="ad-sep shrink-0 text-white/80 mx-10">•</span>
        @endforeach
    </div>
</div>
<style>
@keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.animate-marquee {
    animation: marquee 25s linear infinite;
}
</style>
@endif
