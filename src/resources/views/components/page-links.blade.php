<div class="page-links">
    @if ($paginator->onFirstPage())
    <div class="page-links__item">
        <svg fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
    </div>
    @else
    <a class="page-links__item page-links__link-item" href="{{ $paginator->previousPageUrl() }}">
        <svg fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
    </a>
    @endif

    @foreach ($leftPages as $page)
    @if ($page === $paginator->currentPage())
    <div class="page-links__item page-links__current-item">
        {{ $page }}
    </div>
    @else
    <a class="page-links__item page-links__link-item" href="{{ $paginator->url($page) }}">
        {{ $page }}
    </a>
    @endif
    @endforeach

    @if ($hasLeftDots)
    <div class="page-links__item">
        …
    </div>
    @endif

    @foreach ($centerPages as $page)
    @if ($page === $paginator->currentPage())
    <div class="page-links__item page-links__current-item">
        {{ $page }}
    </div>
    @else
    <a class="page-links__item page-links__link-item" href="{{ $paginator->url($page) }}">
        {{ $page }}
    </a>
    @endif
    @endforeach

    @if ($hasRightDots)
    <div class="page-links__item">
        …
    </div>
    @endif

    @foreach ($rightPages as $page)
    @if ($page === $paginator->currentPage())
    <div class="page-links__item page-links__current-item">
        {{ $page }}
    </div>
    @else
    <a class="page-links__item page-links__link-item" href="{{ $paginator->url($page) }}">
        {{ $page }}
    </a>
    @endif
    @endforeach

    @if ($paginator->hasMorePages())
    <a class="page-links__item page-links__link-item" href="{{ $paginator->nextPageUrl() }}">
        <svg fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
        </svg>
    </a>
    @else
    <div class="page-links__item">
        <svg fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
        </svg>
    </div>
    @endif
</div>