@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="{{ __('ui.pagination.aria_label') }}" class="flex items-center justify-between">
            <div class="flex flex-1 justify-between sm:hidden">
                <span>
                    @if ($paginator->onFirstPage())
                        <span class="btn btn-sm btn-disabled">
                            {!! __('pagination.previous') !!}
                        </span>
                    @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="btn btn-sm btn-outline">
                            {!! __('pagination.previous') !!}
                        </button>
                    @endif
                </span>

                <span>
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="btn btn-sm btn-outline ml-3">
                            {!! __('pagination.next') !!}
                        </button>
                    @else
                        <span class="btn btn-sm btn-disabled ml-3">
                            {!! __('pagination.next') !!}
                        </span>
                    @endif
                </span>
            </div>

            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm leading-5 text-base-content/70">
                        {{ __('ui.pagination.showing', [
                            'first' => $paginator->firstItem(),
                            'last' => $paginator->lastItem(),
                            'total' => $paginator->total(),
                        ]) }}
                    </p>
                </div>

                <div>
                    <span class="join">
                        @if ($paginator->onFirstPage())
                            <span class="join-item btn btn-sm btn-disabled" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                                <x-app-icon icon="heroicons:chevron-left-20-solid" class="h-5 w-5 text-current" />
                            </span>
                        @else
                            <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" class="join-item btn btn-sm btn-outline" aria-label="{{ __('pagination.previous') }}">
                                <x-app-icon icon="heroicons:chevron-left-20-solid" class="h-5 w-5 text-current" />
                            </button>
                        @endif

                        @foreach ($elements as $element)
                            @if (is_string($element))
                                <span class="join-item btn btn-sm btn-disabled">{{ $element }}</span>
                            @endif

                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                        @if ($page == $paginator->currentPage())
                                            <span class="join-item btn btn-sm btn-primary pointer-events-none" aria-current="page">{{ $page }}</span>
                                        @else
                                            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="join-item btn btn-sm btn-outline" aria-label="{{ __('ui.pagination.go_to_page', ['page' => $page]) }}">
                                                {{ $page }}
                                            </button>
                                        @endif
                                    </span>
                                @endforeach
                            @endif
                        @endforeach

                        @if ($paginator->hasMorePages())
                            <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" class="join-item btn btn-sm btn-outline" aria-label="{{ __('pagination.next') }}">
                                <x-app-icon icon="heroicons:chevron-right-20-solid" class="h-5 w-5 text-current" />
                            </button>
                        @else
                            <span class="join-item btn btn-sm btn-disabled" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                                <x-app-icon icon="heroicons:chevron-right-20-solid" class="h-5 w-5 text-current" />
                            </span>
                        @endif
                    </span>
                </div>
            </div>
        </nav>
    @endif
</div>
