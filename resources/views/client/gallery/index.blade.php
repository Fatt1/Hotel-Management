@extends('client.layouts.app')

@section('title', 'Thư Viện Ảnh — Urban Luxe Hotel')

@push('styles')
<style>
/* Minimal custom CSS for Animation and fallback states */
@keyframes gal-fadein {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
.animate-gal-fadein {
    animation: gal-fadein 0.4s ease forwards;
}
.gal-item[hidden] {
    display: none !important;
}
</style>
@endpush

@section('content')

{{-- ============================================================
     HEADER SECTION
     ============================================================ --}}
<section class="pt-36 pb-16 bg-[#0a0a0a]">
    <div class="max-w-7xl mx-auto px-8">

        {{-- Title row --}}
        <div class="flex flex-wrap items-end justify-between gap-8 mb-14">
            <div class="max-w-[480px]">
                {{-- Eyebrow label --}}
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-px w-10 bg-[#d4af37]"></div>
                    <span class="text-[10px] font-bold tracking-[0.3em] uppercase text-[#d4af37]">Thư Viện Hình Ảnh</span>
                </div>
                <h1 class="font-['Playfair_Display'] text-[clamp(2.5rem,5vw,3.75rem)] font-bold text-[#f0ead8] leading-[1.1] mb-4">
                    Bộ Sưu Tập
                </h1>
                <p class="text-sm text-[#6b6050] leading-relaxed max-w-[380px]">
                    Đắm chìm trong trải nghiệm Urban Luxe. Hành trình thị giác qua những
                    không gian được thiết kế cho người lữ hành hiện đại.
                </p>
            </div>

            {{-- Filter tabs --}}
            <div class="flex flex-wrap gap-2" id="gal-tabs">
                <button class="gal-tab-btn inline-block py-2 px-5 text-[11px] font-bold tracking-[0.15em] uppercase border transition-all duration-200 whitespace-nowrap bg-[#d4af37] border-[#d4af37] text-[#0a0a0a]" data-cat="all">Tất Cả</button>
                <button class="gal-tab-btn inline-block py-2 px-5 text-[11px] font-bold tracking-[0.15em] uppercase border transition-all duration-200 whitespace-nowrap border-[#d4af37]/25 text-[#9a9080] bg-transparent hover:text-[#d4af37] hover:border-[#d4af37]/50" data-cat="room">Phòng</button>
                <button class="gal-tab-btn inline-block py-2 px-5 text-[11px] font-bold tracking-[0.15em] uppercase border transition-all duration-200 whitespace-nowrap border-[#d4af37]/25 text-[#9a9080] bg-transparent hover:text-[#d4af37] hover:border-[#d4af37]/50" data-cat="dining">Ẩm Thực</button>
                <button class="gal-tab-btn inline-block py-2 px-5 text-[11px] font-bold tracking-[0.15em] uppercase border transition-all duration-200 whitespace-nowrap border-[#d4af37]/25 text-[#9a9080] bg-transparent hover:text-[#d4af37] hover:border-[#d4af37]/50" data-cat="exterior">Ngoại Thất</button>
                <button class="gal-tab-btn inline-block py-2 px-5 text-[11px] font-bold tracking-[0.15em] uppercase border transition-all duration-200 whitespace-nowrap border-[#d4af37]/25 text-[#9a9080] bg-transparent hover:text-[#d4af37] hover:border-[#d4af37]/50" data-cat="interior">Nội Thất</button>
                <button class="gal-tab-btn inline-block py-2 px-5 text-[11px] font-bold tracking-[0.15em] uppercase border transition-all duration-200 whitespace-nowrap border-[#d4af37]/25 text-[#9a9080] bg-transparent hover:text-[#d4af37] hover:border-[#d4af37]/50" data-cat="event">Sự Kiện</button>
            </div>
        </div>

        {{-- Decorative divider --}}
        <div class="h-px bg-gradient-to-r from-transparent via-[#d4af37]/30 to-transparent mb-12"></div>

        {{-- ============================================================
             MASONRY GALLERY GRID
             ============================================================ --}}

        {{-- Build a flat list of all images across all categories --}}
        @php
            /* Static mock data for Gallery */
            $allItems = collect([
                (object)['category' => 'room', 'title' => 'Phòng Superior', 'image_path' => 'https://picsum.photos/seed/room-superior/800/600'],
                (object)['category' => 'room', 'title' => 'Phòng Deluxe Hướng Biển', 'image_path' => 'https://picsum.photos/seed/room-deluxe/600/800'],
                (object)['category' => 'room', 'title' => 'Phòng Suite Góc', 'image_path' => 'https://picsum.photos/seed/room-suite/800/500'],
                (object)['category' => 'dining', 'title' => 'Nhà Hàng Cung Đình', 'image_path' => 'https://picsum.photos/seed/dining-cung/800/800'],
                (object)['category' => 'dining', 'title' => 'Ocean Breeze Buffet', 'image_path' => 'https://picsum.photos/seed/dining-ocean/800/500'],
                (object)['category' => 'dining', 'title' => 'Skyline Bar', 'image_path' => 'https://picsum.photos/seed/dining-sky/600/800'],
                (object)['category' => 'exterior', 'title' => 'Kiến Trúc Mặt Tiền', 'image_path' => 'https://picsum.photos/seed/ext-facade/800/600'],
                (object)['category' => 'exterior', 'title' => 'Khuôn Viên Vườn Đêm', 'image_path' => 'https://picsum.photos/seed/ext-garden/800/800'],
                (object)['category' => 'interior', 'title' => 'Sảnh Lễ Tân Sang Trọng', 'image_path' => 'https://picsum.photos/seed/int-lobby/800/600'],
                (object)['category' => 'interior', 'title' => 'Hành Lang Nghệ Thuật', 'image_path' => 'https://picsum.photos/seed/int-hall/600/800'],
                (object)['category' => 'event', 'title' => 'Phòng Hội Nghị Grand', 'image_path' => 'https://picsum.photos/seed/event-grand/800/500'],
                (object)['category' => 'event', 'title' => 'Tiệc Cưới Ngoài Trời', 'image_path' => 'https://picsum.photos/seed/event-wedding/800/800'],
            ]);
        @endphp

        @if($allItems->isEmpty())
            {{-- ---- Empty state ---- --}}
            <div class="text-center py-24">
                <p class="text-[14.5px] text-[#4a4040] italic">Chưa có hình ảnh nào trong thư viện.</p>
            </div>
        @else
            <div class="columns-1 sm:columns-2 md:columns-3 gap-3" id="gal-grid">
                @foreach($allItems as $item)
                    @php
                        $cat = $item->category ?? 'room';
                        $imgSrc = ($item->image_path && file_exists(public_path($item->image_path)))
                            ? asset($item->image_path)
                            : ('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800&q=80');

                        $catLabels = [
                            'room'     => 'Phòng Nghỉ',
                            'dining'   => 'Ẩm Thực',
                            'exterior' => 'Ngoại Thất',
                            'interior' => 'Nội Thất',
                            'event'    => 'Sự Kiện',
                        ];
                        $catLabel = $catLabels[$cat] ?? ucfirst($cat);
                    @endphp
                    <div
                        class="gal-item animate-gal-fadein break-inside-avoid mb-3 overflow-hidden relative cursor-pointer block group"
                        data-cat="{{ $cat }}"
                        onclick="openLightbox('{{ $imgSrc }}', '{{ addslashes($item->title) }}')"
                        title="{{ $item->title }}"
                    >
                        <img
                            src="{{ $imgSrc }}"
                            alt="{{ $item->title }}"
                            loading="lazy"
                            class="w-full h-auto block transition-transform duration-500 group-hover:scale-[1.04]"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-[#050505]/80 via-transparent to-transparent opacity-0 transition-opacity duration-300 flex items-end p-5 group-hover:opacity-100">
                            <div>
                                <p class="text-[9px] font-bold tracking-[0.2em] uppercase text-[#d4af37] mb-1">{{ $catLabel }}</p>
                                <p class="text-[12.5px] font-semibold tracking-[0.06em] text-[#f0ead8]">{{ $item->title }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Item count --}}
            <p id="gal-count" class="text-center mt-10 text-[11.5px] text-[#4a4040] tracking-[0.1em]">
                Hiển thị <span id="gal-count-num">{{ $allItems->count() }}</span> / {{ $allItems->count() }} hình ảnh
            </p>
        @endif

    </div>
</section>

{{-- ============================================================
     LIGHTBOX
     ============================================================ --}}
<div id="gal-lightbox" class="hidden fixed inset-0 z-[9999] bg-[#050505]/95 items-center justify-center p-8" onclick="closeLightbox(event)" role="dialog" aria-modal="true" aria-label="Xem ảnh lớn">
    <button id="gal-lb-close" class="absolute top-5 right-6 text-3xl text-[#9a9080] cursor-pointer bg-none border-none leading-none transition-colors duration-200 hover:text-[#d4af37]" onclick="closeLightbox()" aria-label="Đóng">&times;</button>
    <img id="gal-lb-img" class="max-w-[90vw] max-h-[88vh] object-contain shadow-[0_0_80px_rgba(0,0,0,0.6)]" src="" alt="">
    <span id="gal-lb-caption" class="absolute bottom-6 left-1/2 -translate-x-1/2 text-xs text-[#9a9080] tracking-[0.08em] whitespace-nowrap"></span>
</div>

@push('scripts')
<script>
/* ============================================================
   GALLERY FILTER + LIGHTBOX
   ============================================================ */

(function () {
    const tabs      = document.querySelectorAll('.gal-tab-btn');
    const items     = document.querySelectorAll('.gal-item');
    const countNum  = document.getElementById('gal-count-num');
    const totalAll  = items.length;

    /* Tab click */
    tabs.forEach(btn => {
        btn.addEventListener('click', () => {
            const cat = btn.dataset.cat;

            /* Toggle active state */
            tabs.forEach(t => {
                 // reset classes to inactive
                 t.classList.remove('bg-[#d4af37]', 'border-[#d4af37]', 'text-[#0a0a0a]');
                 t.classList.add('bg-transparent', 'border-[#d4af37]/25', 'text-[#9a9080]', 'hover:text-[#d4af37]', 'hover:border-[#d4af37]/50');
            });
            // set active classes
            btn.classList.add('bg-[#d4af37]', 'border-[#d4af37]', 'text-[#0a0a0a]');
            btn.classList.remove('bg-transparent', 'border-[#d4af37]/25', 'text-[#9a9080]', 'hover:text-[#d4af37]', 'hover:border-[#d4af37]/50');

            /* Show / hide items */
            let visible = 0;
            items.forEach(item => {
                const match = (cat === 'all') || (item.dataset.cat === cat);
                if (match) {
                    item.removeAttribute('hidden');
                    /* Re-trigger fade animation */
                    item.classList.remove('animate-gal-fadein');
                    void item.offsetWidth; /* reflow */
                    item.classList.add('animate-gal-fadein');
                    visible++;
                } else {
                    item.setAttribute('hidden', '');
                }
            });

            if (countNum) {
                countNum.textContent = visible;
            }
        });
    });
})();

/* ---- Lightbox ---- */
function openLightbox(src, title) {
    const lb   = document.getElementById('gal-lightbox');
    const img  = document.getElementById('gal-lb-img');
    const cap  = document.getElementById('gal-lb-caption');
    img.src    = src;
    img.alt    = title;
    cap.textContent = title;
    lb.classList.remove('hidden');
    lb.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeLightbox(event) {
    const lb = document.getElementById('gal-lightbox');
    const cls = document.getElementById('gal-lb-close');
    if (event && event.target !== lb && event.target !== cls) return;
    lb.classList.remove('flex');
    lb.classList.add('hidden');
    document.getElementById('gal-lb-img').src = '';
    document.body.style.overflow = '';
}

/* Close on Escape key */
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeLightbox({ target: document.getElementById('gal-lightbox') });
});
</script>
@endpush

@endsection
