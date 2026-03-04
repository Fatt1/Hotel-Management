@extends('client.layouts.app')

@section('title', 'Thư Viện Ảnh — Urban Luxe Hotel')

@push('styles')
<style>
/* =========================================
   GALLERY PAGE — CUSTOM STYLES
   ========================================= */

/* --- Filter tabs --- */
.gal-tab-btn {
    display: inline-block;
    padding: 0.45rem 1.25rem;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    border: 1px solid rgba(212,175,55,0.25);
    color: #9a9080;
    background: transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.gal-tab-btn:hover {
    color: #d4af37;
    border-color: rgba(212,175,55,0.5);
}
.gal-tab-btn.active {
    background-color: #d4af37;
    border-color: #d4af37;
    color: #0a0a0a;
}

/* --- Masonry grid --- */
.gal-masonry {
    columns: 3;
    column-gap: 0.85rem;
}
@media (max-width: 900px) {
    .gal-masonry { columns: 2; }
}
@media (max-width: 540px) {
    .gal-masonry { columns: 1; }
}

.gal-item {
    break-inside: avoid;
    margin-bottom: 0.85rem;
    overflow: hidden;
    position: relative;
    cursor: pointer;
    display: block; /* shown by default */
}
.gal-item[hidden] {
    display: none !important;
}

.gal-item img {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.5s ease;
}
.gal-item:hover img {
    transform: scale(1.04);
}

/* Caption overlay */
.gal-caption {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(5,5,5,0.82) 0%, transparent 55%);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    align-items: flex-end;
    padding: 1.2rem 1rem;
}
.gal-item:hover .gal-caption {
    opacity: 1;
}
.gal-caption-title {
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    color: #f0ead8;
}
.gal-caption-cat {
    font-size: 0.58rem;
    font-weight: 700;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: #d4af37;
    margin-bottom: 0.3rem;
}

/* Lightbox */
#gal-lightbox {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(5,5,5,0.95);
    align-items: center;
    justify-content: center;
    padding: 2rem;
}
#gal-lightbox.open {
    display: flex;
}
#gal-lightbox img {
    max-width: 90vw;
    max-height: 88vh;
    object-fit: contain;
    box-shadow: 0 0 80px rgba(0,0,0,0.6);
}
#gal-lb-close {
    position: absolute;
    top: 1.25rem;
    right: 1.5rem;
    font-size: 1.8rem;
    color: #9a9080;
    cursor: pointer;
    background: none;
    border: none;
    line-height: 1;
    transition: color 0.2s;
}
#gal-lb-close:hover { color: #d4af37; }
#gal-lb-caption {
    position: absolute;
    bottom: 1.5rem;
    left: 50%;
    transform: translateX(-50%);
    font-size: 0.75rem;
    color: #9a9080;
    letter-spacing: 0.08em;
    white-space: nowrap;
}

/* Fade-in on appear */
@keyframes gal-fadein {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
.gal-item.appeared {
    animation: gal-fadein 0.4s ease forwards;
}
</style>
@endpush

@section('content')

{{-- ============================================================
     HEADER SECTION
     ============================================================ --}}
<section style="padding: 9rem 0 4rem; background-color: #0a0a0a;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">

        {{-- Title row --}}
        <div style="display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 2rem; margin-bottom: 3.5rem;">
            <div style="max-width: 480px;">
                {{-- Eyebrow label --}}
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                    <div style="height: 1px; width: 40px; background-color: #d4af37;"></div>
                    <span style="font-size: 0.6rem; font-weight: 700; letter-spacing: 0.3em; text-transform: uppercase; color: #d4af37;">Thư Viện Hình Ảnh</span>
                </div>
                <h1 style="font-family: 'Playfair Display', serif; font-size: clamp(2.5rem, 5vw, 3.75rem); font-weight: 700; color: #f0ead8; line-height: 1.1; margin-bottom: 1rem;">
                    Bộ Sưu Tập
                </h1>
                <p style="font-size: 0.875rem; color: #6b6050; line-height: 1.8; max-width: 380px;">
                    Đắm chìm trong trải nghiệm Urban Luxe. Hành trình thị giác qua những
                    không gian được thiết kế cho người lữ hành hiện đại.
                </p>
            </div>

            {{-- Filter tabs --}}
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;" id="gal-tabs">
                <button class="gal-tab-btn active" data-cat="all">Tất Cả</button>
                <button class="gal-tab-btn" data-cat="room">Phòng</button>
                <button class="gal-tab-btn" data-cat="dining">Ẩm Thực</button>
                <button class="gal-tab-btn" data-cat="exterior">Ngoại Thất</button>
                <button class="gal-tab-btn" data-cat="interior">Nội Thất</button>
                <button class="gal-tab-btn" data-cat="event">Sự Kiện</button>
            </div>
        </div>

        {{-- Decorative divider --}}
        <div style="height: 1px; background: linear-gradient(to right, transparent, rgba(212,175,55,0.3), transparent); margin-bottom: 3rem;"></div>

        {{-- ============================================================
             MASONRY GALLERY GRID
             ============================================================ --}}

        {{-- Build a flat list of all images across all categories --}}
        @php
            $allItems = collect();
            foreach ($galleries as $cat => $items) {
                $allItems = $allItems->merge($items);
            }

            /* Unsplash fallbacks per category */
            $fallbacks = [
                'room'     => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&q=80',
                'dining'   => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&q=80',
                'exterior' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800&q=80',
                'interior' => 'https://images.unsplash.com/photo-1610641818989-c2051b5e2cfd?w=800&q=80',
                'event'    => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800&q=80',
            ];
        @endphp

        @if($allItems->isEmpty())
            {{-- ---- Empty state ---- --}}
            <div style="text-align: center; padding: 6rem 0;">
                <p style="font-size: 0.9rem; color: #4a4040; font-style: italic;">Chưa có hình ảnh nào trong thư viện.</p>
            </div>
        @else
            <div class="gal-masonry" id="gal-grid">
                @foreach($allItems as $item)
                    @php
                        $cat = $item->category ?? 'room';
                        $imgSrc = ($item->image_path && file_exists(public_path($item->image_path)))
                            ? asset($item->image_path)
                            : ($fallbacks[$cat] ?? 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800&q=80');

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
                        class="gal-item appeared"
                        data-cat="{{ $cat }}"
                        onclick="openLightbox('{{ $imgSrc }}', '{{ addslashes($item->title) }}')"
                        title="{{ $item->title }}"
                    >
                        <img
                            src="{{ $imgSrc }}"
                            alt="{{ $item->title }}"
                            loading="lazy"
                        >
                        <div class="gal-caption">
                            <div>
                                <p class="gal-caption-cat">{{ $catLabel }}</p>
                                <p class="gal-caption-title">{{ $item->title }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Item count --}}
            <p id="gal-count" style="text-align: center; margin-top: 2.5rem; font-size: 0.72rem; color: #4a4040; letter-spacing: 0.1em;">
                Hiển thị <span id="gal-count-num">{{ $allItems->count() }}</span> / {{ $allItems->count() }} hình ảnh
            </p>
        @endif

    </div>
</section>

{{-- ============================================================
     LIGHTBOX
     ============================================================ --}}
<div id="gal-lightbox" onclick="closeLightbox(event)" role="dialog" aria-modal="true" aria-label="Xem ảnh lớn">
    <button id="gal-lb-close" onclick="closeLightbox()" aria-label="Đóng">&times;</button>
    <img id="gal-lb-img" src="" alt="">
    <span id="gal-lb-caption"></span>
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
            tabs.forEach(t => t.classList.remove('active'));
            btn.classList.add('active');

            /* Show / hide items */
            let visible = 0;
            items.forEach(item => {
                const match = (cat === 'all') || (item.dataset.cat === cat);
                if (match) {
                    item.removeAttribute('hidden');
                    /* Re-trigger fade animation */
                    item.classList.remove('appeared');
                    void item.offsetWidth; /* reflow */
                    item.classList.add('appeared');
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
    lb.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeLightbox(event) {
    if (event && event.target !== document.getElementById('gal-lightbox') && event.target !== document.getElementById('gal-lb-close')) return;
    document.getElementById('gal-lightbox').classList.remove('open');
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
