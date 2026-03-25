@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between flex-wrap gap-3">
        <div class="text-xs text-[#9a9080]">
            Hiển thị {{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }} / {{ $paginator->total() }} booking
        </div>

        <div class="flex items-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 rounded-lg border border-white/10 bg-white/5 text-[#6b6050] cursor-not-allowed">Trước</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 rounded-lg border border-white/10 bg-white/5 text-[#e8e0d0] no-underline hover:bg-white/10 transition-colors">Trước</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-3 py-2 rounded-lg border border-white/10 bg-white/5 text-[#6b6050]">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="px-3 py-2 rounded-lg border border-[#d4af37]/40 bg-[linear-gradient(135deg,#d4af37_0%,#b8952a_100%)] text-[#0a0a0a] font-semibold">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-2 rounded-lg border border-white/10 bg-white/5 text-[#e8e0d0] no-underline hover:bg-white/10 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 rounded-lg border border-white/10 bg-white/5 text-[#e8e0d0] no-underline hover:bg-white/10 transition-colors">Sau</a>
            @else
                <span class="px-3 py-2 rounded-lg border border-white/10 bg-white/5 text-[#6b6050] cursor-not-allowed">Sau</span>
            @endif
        </div>
    </nav>
@endif
