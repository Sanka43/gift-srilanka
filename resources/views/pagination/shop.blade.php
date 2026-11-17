@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-wrap items-center justify-center gap-2 max-md:gap-1.5 max-md:px-1">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center w-10 h-10 max-md:min-h-[2.75rem] max-md:min-w-[2.75rem] rounded-xl text-sm font-medium text-stone-400 bg-stone-100 cursor-default transition-colors duration-300" aria-hidden="true">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-10 h-10 max-md:min-h-[2.75rem] max-md:min-w-[2.75rem] rounded-xl text-sm font-medium text-stone-600 bg-white border border-stone-200 shadow-soft hover:bg-stone-50 hover:border-primary-200 hover:text-primary-600 transition-all duration-300" aria-label="{{ __('pagination.previous') }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
            </a>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="inline-flex items-center justify-center w-10 h-10 max-md:min-h-[2.75rem] max-md:min-w-[2.75rem] rounded-xl text-sm text-stone-400" aria-disabled="true">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="inline-flex items-center justify-center min-w-[2.5rem] h-10 max-md:min-h-[2.75rem] max-md:min-w-[2.75rem] px-4 max-md:px-3 rounded-xl text-sm font-semibold text-white bg-primary-500 shadow-soft transition-all duration-300">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex items-center justify-center min-w-[2.5rem] h-10 max-md:min-h-[2.75rem] max-md:min-w-[2.75rem] px-4 max-md:px-3 rounded-xl text-sm font-medium text-stone-600 bg-white border border-stone-200 shadow-soft hover:bg-primary-50 hover:border-primary-200 hover:text-primary-600 transition-all duration-300" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-10 h-10 max-md:min-h-[2.75rem] max-md:min-w-[2.75rem] rounded-xl text-sm font-medium text-stone-600 bg-white border border-stone-200 shadow-soft hover:bg-stone-50 hover:border-primary-200 hover:text-primary-600 transition-all duration-300" aria-label="{{ __('pagination.next') }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
            </a>
        @else
            <span class="inline-flex items-center justify-center w-10 h-10 max-md:min-h-[2.75rem] max-md:min-w-[2.75rem] rounded-xl text-sm font-medium text-stone-400 bg-stone-100 cursor-default transition-colors duration-300" aria-hidden="true">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
            </span>
        @endif
    </nav>
@endif
