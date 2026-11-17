@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-wrap items-center justify-between gap-4 max-md:flex-col max-md:gap-3 max-md:items-stretch">
        <p class="text-sm text-stone-600">
            @if ($paginator->firstItem())
                Showing <span class="font-medium text-stone-800">{{ $paginator->firstItem() }}</span>–<span class="font-medium text-stone-800">{{ $paginator->lastItem() }}</span> of <span class="font-medium">{{ $paginator->total() }}</span>
            @else
                {{ $paginator->count() }} {{ __('results') }}
            @endif
        </p>
        <span class="inline-flex rounded-2xl border border-stone-200 bg-white shadow-soft overflow-hidden max-md:w-full max-md:justify-center max-md:flex-wrap">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-stone-400 cursor-default border-r border-stone-100" aria-hidden="true">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-3 py-2 text-sm font-medium text-stone-600 hover:text-primary-600 hover:bg-primary-50/50 border-r border-stone-100 transition-colors" aria-label="{{ __('pagination.previous') }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center px-3 py-2 text-sm text-stone-400 border-r border-stone-100">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-primary-500 border-r border-primary-600">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-stone-600 hover:text-primary-600 hover:bg-primary-50/50 border-r border-stone-100 transition-colors" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-3 py-2 text-sm font-medium text-stone-600 hover:text-primary-600 hover:bg-primary-50/50 transition-colors" aria-label="{{ __('pagination.next') }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                </a>
            @else
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-stone-400 cursor-default" aria-hidden="true">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                </span>
            @endif
        </span>
    </nav>
@endif
