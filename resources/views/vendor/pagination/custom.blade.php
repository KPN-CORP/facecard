@if ($paginator->hasPages())
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <div class="pagination-wrapper d-flex justify-content-between align-items-center">
        {{-- "Showing X to Y of Z" text --}}
        <div class="text-muted small">
            @if($paginator->total() > 0)
                Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
            @else
                No results
            @endif
        </div>

        {{-- Page Links --}}
        <nav>
            <ul class="pagination custom-pagination mb-0">
                {{-- "Previous" --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">&lsaquo;</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&lsaquo;</a></li>
                @endif

                @php
                    $current = $paginator->currentPage();
                    $last = $paginator->lastPage();
                    $start = 1;
                    $end = $last;

                    if ($last > 3) {
                        if ($current === 1) {
                            $start = 1;
                            $end = 3;
                        } elseif ($current === $last) {
                            $start = $last - 2;
                            $end = $last;
                        } else {
                            $start = $current - 1;
                            $end = $current + 1;
                        }
                    }
                @endphp

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page == $current)
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a></li>
                    @endif
                @endfor

                {{-- "Next" --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&rsaquo;</a></li>
                @else
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">&rsaquo;</span></li>
                @endif
            </ul>
        </nav>
    </div>
@endif