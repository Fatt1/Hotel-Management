@if ($paginator->hasPages())
    <div class="flex items-center gap-2">
        @if ($paginator->onFirstPage())
            <span
                class="p-2 rounded-lg border border-slate-200 opacity-50 cursor-not-allowed flex items-center justify-center">
                <span class="material-symbols-outlined !text-lg">chevron_left</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
                class="p-2 rounded-lg border border-slate-200 hover:bg-slate-50 flex items-center justify-center">
                <span class="material-symbols-outlined !text-lg">chevron_left</span>
            </a>
        @endif

        @foreach ($elements as $element)
            {{-- Nếu danh sách trang quá dài, nó tự động biến thành dấu "..." --}}
            @if (is_string($element))
                <span class="pagination-link cursor-not-allowed opacity-50">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pagination-link text-white bg-primary">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="pagination-link">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach
        {{-- Nút "Trang tiếp theo" (>) --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
                class="p-2 rounded-lg border border-slate-200 hover:bg-slate-50 flex items-center justify-center">
                <span class="material-symbols-outlined !text-lg">chevron_right</span>
            </a>
        @else
            <span
                class="p-2 rounded-lg border border-slate-200 opacity-50 cursor-not-allowed flex items-center justify-center">
                <span class="material-symbols-outlined !text-lg">chevron_right</span>
            </span>
        @endif
    </div>
@endif
