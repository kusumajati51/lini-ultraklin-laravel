@if ($paginator->hasPages())
    <div class="el-pagination is-background">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <button class="btn-prev disabled" rel="prev">
                <i class="el-icon el-icon-arrow-left"></i>
            </button>
        @else
            <button class="btn-prev" onclick="window.location = '{{ $paginator->previousPageUrl() }}'" rel="prev">
                <i class="el-icon el-icon-arrow-left"></i>
            </button>
        @endif

        <ul class="el-pager">
            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="disabled">{{ $element }}</li>
                @endif
    
                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="number active">{{ $page }}</li>
                        @else
                            <li class="number" onclick="window.location = '{{ $url }}'">{{ $page }}</li>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </ul>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <button class="btn-next" onclick="window.location = '{{ $paginator->nextPageUrl() }}'" rel="next">
                <i class="el-icon el-icon-arrow-right"></i>
            </button>
        @else
            <button class="btn-next disabled" rel="next">
                <i class="el-icon el-icon-arrow-right"></i>
            </button>
        @endif
    </div>
@endif
