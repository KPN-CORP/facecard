@if ($paginator->hasPages())
    <div class="d-flex justify-content-between align-items-center">

        {{-- Teks "Showing X to Y of Z" --}}
        <div class="text-muted small">
            @if($paginator->total() > 0)
                Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
            @else
                No results
            @endif
        </div>

        <nav>
            <ul class="pagination mb-0 align-items-center">

                {{-- "Previous" Button --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link border-0 bg-transparent">&lsaquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link border-0 text-primary" href="{{ $paginator->previousPageUrl() }}" rel="prev">&lsaquo;</a>
                    </li>
                @endif

                @php
                    $current = $paginator->currentPage();
                    $last = $paginator->lastPage();
                    $start = max(1, $current - 1);
                    $end = min($last, $current + 1);

                    if ($current === 1 && $last > 2) {
                        $end = 3;
                    }
                    if ($current === $last && $last > 2) {
                        $start = $last - 2;
                    }
                @endphp

                {{-- Page Number --}}
                @for ($page = $start; $page <= $end; $page++)
                    @if ($page == $current)
                        <li class="page-item mx-1" aria-current="page">
                            <span class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 32px; height: 32px;">
                                {{ $page }}
                            </span>
                        </li>
                    @else
                        <li class="page-item mx-1">
                            <a class="page-link border-0 text-secondary" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endfor

                {{-- "Next" --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link border-0 text-danger" href="{{ $paginator->nextPageUrl() }}" rel="next">&rsaquo;</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link border-0">&rsaquo;</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif