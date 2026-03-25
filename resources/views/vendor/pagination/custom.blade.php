@php
    $pageSizeOptions = [5, 10, 25, 50, 100];
    $currentPageSize = (int) request()->query('page_size', $paginator->perPage());
    if (!in_array($currentPageSize, $pageSizeOptions, true)) {
        $currentPageSize = (int) $paginator->perPage();
    }

    $query = request()->query();
    unset($query['page']);
@endphp

<div class="p-5 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
    <div class="flex items-center gap-4 flex-wrap">
        <span class="text-xs font-medium text-slate-500">Hiển thị {{ $paginator->lastItem() ?? 0 }} trên {{ $paginator->total() }} mục</span>
    </div>

    <div class="flex items-center gap-3 flex-wrap">
        <div class="flex items-center gap-2">
            <label for="page-size-select" class="text-xs text-slate-500">Số dòng/trang</label>
            <select id="page-size-select"
                class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-sm focus:ring-2 focus:ring-primary/20"
                onchange="window.location.href = this.value">
                @foreach ($pageSizeOptions as $size)
                    @php
                        $pageSizeUrl = request()->url() . '?' . http_build_query(array_merge($query, ['page_size' => $size]));
                    @endphp
                    <option value="{{ $pageSizeUrl }}" @selected($currentPageSize === $size)>{{ $size }}</option>
                @endforeach
            </select>
        </div>

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
    </div>
</div>
