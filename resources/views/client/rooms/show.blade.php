@extends('client.layouts.app')

@section('title', $roomType->name . ' - Urban Luxe Hotel')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
/* Lightbox */
#lbOverlay { display: none; }
#lbOverlay.lb-active { display: flex; }

/* Gallery grid thumbs on mobile: 2 cols, tablet: 3 */
.gallery-thumb { aspect-ratio: 4/3; }
</style>
@endpush

@section('content')

@php
  $resolveImageUrl = static function (?string $imageUrl): ?string {
    if (!is_string($imageUrl) || trim($imageUrl) === '') {
      return null;
    }

    $imageUrl = trim($imageUrl);
    if (
      str_starts_with($imageUrl, 'http://') ||
      str_starts_with($imageUrl, 'https://') ||
      str_starts_with($imageUrl, '//')
    ) {
      return $imageUrl;
    }

    return asset('storage/' . ltrim($imageUrl, '/'));
  };

  $images    = $roomType->images
    ->pluck('image_url')
    ->map($resolveImageUrl)
    ->filter()
    ->values()
    ->toArray();
  $fallback  = 'https://picsum.photos/seed/hotel-room-show/800/560';
  if (empty($images)) {
    $images = [$fallback, $fallback, $fallback];
  }
  $mainImg   = $images[0];
  $thumbs    = array_slice($images, 1, 4);

  $area      = (float)$roomType->width * (float)$roomType->height;
  $areaSqm   = $area > 0 ? number_format($area, 0, ',', '.') . ' m²' : null;

  $bedParts  = [];
  if ($roomType->single_bed_quantity > 0) $bedParts[] = $roomType->single_bed_quantity . ' giường đơn';
  if ($roomType->double_bed_quantity > 0) $bedParts[] = $roomType->double_bed_quantity . ' giường đôi';
  $bedStr    = implode(', ', $bedParts);

  $price     = number_format((float)$roomType->daily_price, 0, ',', '.') . ' đ';
  $amenities = $roomType->amenities;
@endphp

{{-- ── HERO / GALLERY ─────────────────────────────────────────────── --}}
<div class="bg-[#0a0a0a] pt-16">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6" aria-label="Breadcrumb">
      <a href="{{ route('client.home') }}" class="hover:text-[#d4af37] transition-colors text-gray-400">Trang chủ</a>
      <i class="fas fa-chevron-right text-[9px] text-gray-600"></i>
      <a href="{{ route('client.rooms.index') }}" class="hover:text-[#d4af37] transition-colors text-gray-400">Chọn phòng</a>
      <i class="fas fa-chevron-right text-[9px] text-gray-600"></i>
      <span class="text-gray-300 font-semibold">{{ $roomType->name }}</span>
    </nav>

    {{-- Gallery grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 rounded-2xl overflow-hidden" style="max-height: 520px;">
      {{-- Main image --}}
      <div class="relative overflow-hidden cursor-pointer group bg-gray-900 row-span-2"
           style="min-height: 280px;"
           onclick="openLb(0)">
        <img src="{{ $mainImg }}"
             alt="{{ $roomType->name }}"
             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
             style="max-height: 520px;">
        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/15 transition-colors flex items-center justify-center">
          <span class="opacity-0 group-hover:opacity-100 bg-black/60 text-white text-xs font-bold px-3 py-1.5 rounded-full transition-opacity flex items-center gap-1.5">
            <i class="fas fa-expand text-[10px]"></i> Xem lớn
          </span>
        </div>
        {{-- Photo count badge --}}
        <div class="absolute bottom-3 right-3 bg-black/70 text-white text-xs px-3 py-1 rounded-full font-semibold">
          <i class="fas fa-images text-[10px] mr-1"></i> {{ count($images) }} ảnh
        </div>
      </div>

      {{-- Secondary thumbnails (up to 4, shown in 2x2 on md+) --}}
      @foreach($thumbs as $idx => $thumb)
        <div class="relative overflow-hidden cursor-pointer group bg-gray-800 gallery-thumb hidden md:block"
             onclick="openLb({{ $idx + 1 }})">
          <img src="{{ $thumb }}" alt="Ảnh {{ $idx + 2 }}"
               class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
          @if($loop->last && count($images) > 5)
            <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
              <span class="text-white font-bold text-lg">+{{ count($images) - 5 }}</span>
            </div>
          @endif
        </div>
      @endforeach
    </div>

    {{-- "See all photos" button (mobile) --}}
    <button onclick="openLb(0)"
            class="md:hidden mt-3 w-full flex items-center justify-center gap-2 border border-gray-600 text-gray-300 hover:border-gray-400 hover:text-white text-sm font-semibold py-2.5 rounded-xl transition-colors">
      <i class="fas fa-images text-xs"></i> Xem tất cả {{ count($images) }} ảnh
    </button>

  </div>
</div>

{{-- ── MAIN CONTENT ────────────────────────────────────────────────── --}}
<div class="bg-[#f0f2f5] py-10">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-8 items-start">

      {{-- ── LEFT: Info + Amenities ──────────────────────────────────── --}}
      <div>
        {{-- Room name + badges --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 mb-6">
          <div class="flex items-start justify-between flex-wrap gap-4 mb-5">
            <div>
              <p class="text-[10px] font-bold tracking-[0.3em] uppercase text-blue-500 mb-1.5">Urban Luxe Hotel</p>
              <h1 class="font-['Playfair_Display'] text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">{{ $roomType->name }}</h1>
            </div>
            @if($roomType->is_active)
              <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 text-xs font-bold px-3 py-1.5 rounded-full border border-green-200">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Còn phòng
              </span>
            @endif
          </div>

          {{-- Key specs --}}
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pb-5 border-b border-gray-100">
            @if($areaSqm)
              <div class="text-center">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mx-auto mb-2">
                  <i class="fas fa-expand-arrows-alt text-blue-500 text-sm"></i>
                </div>
                <div class="text-sm font-bold text-gray-800">{{ $areaSqm }}</div>
                <div class="text-xs text-gray-400">Diện tích</div>
              </div>
            @endif
            @if($roomType->adult_quantity)
              <div class="text-center">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center mx-auto mb-2">
                  <i class="fas fa-user text-purple-500 text-sm"></i>
                </div>
                <div class="text-sm font-bold text-gray-800">{{ $roomType->adult_quantity }} người lớn</div>
                <div class="text-xs text-gray-400">Sức chứa</div>
              </div>
            @endif
            @if($bedStr)
              <div class="text-center">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mx-auto mb-2">
                  <i class="fas fa-bed text-amber-500 text-sm"></i>
                </div>
                <div class="text-sm font-bold text-gray-800">{{ $bedStr }}</div>
                <div class="text-xs text-gray-400">Loại giường</div>
              </div>
            @endif
            @if($roomType->view_type)
              <div class="text-center">
                <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center mx-auto mb-2">
                  <i class="fas fa-mountain-sun text-teal-500 text-sm"></i>
                </div>
                <div class="text-sm font-bold text-gray-800">{{ $roomType->view_type }}</div>
                <div class="text-xs text-gray-400">Tầm nhìn</div>
              </div>
            @endif
          </div>

          {{-- Description --}}
          @if($roomType->description)
            <div class="mt-5">
              <h2 class="text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">Mô tả</h2>
              <p class="text-sm text-gray-600 leading-relaxed">{{ $roomType->description }}</p>
            </div>
          @endif
        </div>

        {{-- Amenities --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 mb-6">
          <h2 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
            <i class="fas fa-star text-amber-400 text-sm"></i>
            Tiện Nghi Phòng
          </h2>

          @if($amenities->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              @foreach($amenities as $am)
                <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-b-0">
                  <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="fas {{ $am->icon ?: 'fa-circle-check' }} text-blue-500 text-xs"></i>
                  </div>
                  <span class="text-sm text-gray-700 font-medium">{{ $am->name }}</span>
                </div>
              @endforeach
            </div>
          @else
            <div class="flex flex-col items-center text-center py-8 text-gray-400">
              <i class="fas fa-info-circle text-2xl mb-3 text-gray-300"></i>
              <p class="text-sm">Thông tin tiện nghi đang được cập nhật.</p>
              <p class="text-xs mt-1">Vui lòng liên hệ để biết thêm chi tiết.</p>
            </div>
          @endif
        </div>

        {{-- Policies --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
          <h2 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
            <i class="fas fa-shield-halved text-green-500 text-sm"></i>
            Chính Sách
          </h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex items-start gap-3">
              <i class="fas fa-clock text-blue-400 mt-0.5 text-sm w-4"></i>
              <div>
                <div class="text-sm font-semibold text-gray-800">Nhận phòng / Trả phòng</div>
                <div class="text-xs text-gray-500 mt-0.5">Check-in: 14:00 · Check-out: 12:00</div>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <i class="fas fa-rotate-left text-green-400 mt-0.5 text-sm w-4"></i>
              <div>
                <div class="text-sm font-semibold text-gray-800">Huỷ phòng miễn phí</div>
                <div class="text-xs text-gray-500 mt-0.5">Trả phòng miễn phí trước 48 giờ</div>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <i class="fas fa-ban text-red-400 mt-0.5 text-sm w-4"></i>
              <div>
                <div class="text-sm font-semibold text-gray-800">Không hút thuốc</div>
                <div class="text-xs text-gray-500 mt-0.5">Phòng không hút thuốc hoàn toàn</div>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <i class="fas fa-paw text-orange-400 mt-0.5 text-sm w-4"></i>
              <div>
                <div class="text-sm font-semibold text-gray-800">Không mang thú cưng</div>
                <div class="text-xs text-gray-500 mt-0.5">Không nhận thú cưng tại khách sạn</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ── RIGHT: Booking panel ─────────────────────────────────── --}}
      <div class="lg:sticky lg:top-24">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sm:p-7">
          {{-- Price --}}
          <div class="mb-5">
            <span class="text-xs text-gray-400">Giá từ</span>
            <div class="text-3xl font-bold text-gray-900 mt-0.5" style="font-family: 'Playfair Display', serif;">
              {!! $price !!}
            </div>
            <span class="text-xs text-gray-400">mỗi đêm · chưa gồm thuế</span>
          </div>

          <div class="h-px bg-gray-100 mb-5"></div>

          {{-- Quick booking note --}}
          <p class="text-xs text-gray-500 leading-relaxed mb-5">
            Để đặt phòng này, vui lòng trở về trang danh sách phòng và chọn ngày nhận/trả phòng phù hợp.
          </p>

          {{-- CTA: go to booking page --}}
          <a href="{{ route('client.rooms.index') }}"
             class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm tracking-wider uppercase py-4 rounded-xl flex items-center justify-center gap-2 transition-colors no-underline">
            <i class="fas fa-calendar-check text-xs"></i> Đặt Phòng Ngay
          </a>

          <div class="mt-4 flex flex-col gap-2.5">
            <div class="flex items-center gap-2 text-xs text-gray-500">
              <i class="fas fa-rotate-left text-green-400 w-4 text-center"></i>
              Huỷ miễn phí trước 48 giờ
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-500">
              <i class="fas fa-lock text-blue-400 w-4 text-center"></i>
              Thanh toán an toàn & bảo mật
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-500">
              <i class="fas fa-headset text-amber-400 w-4 text-center"></i>
              Hỗ trợ 24/7 qua hotline
            </div>
          </div>

          <div class="mt-5 border-t border-gray-100 pt-5">
            <p class="text-xs text-gray-400 text-center">Cần hỗ trợ?</p>
            <a href="tel:+842812345678"
               class="mt-2 w-full flex items-center justify-center gap-2 border border-gray-200 hover:border-gray-300 text-gray-600 hover:text-gray-800 text-sm font-semibold py-2.5 rounded-xl transition-colors no-underline">
              <i class="fas fa-phone text-xs"></i> +84 28 1234 5678
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

{{-- ── LIGHTBOX ─────────────────────────────────────────────────────── --}}
<script>
  window.__showImages = @json($images);
  window.__showRoomName = @json($roomType->name);
</script>
<div id="lbOverlay"
     class="fixed inset-0 z-[9999] lb-active:flex flex-col items-center justify-center"
     style="background: rgba(0,0,0,0.96);">
  <div class="absolute top-0 left-0 right-0 flex items-center justify-between px-4 sm:px-8 py-5 z-10">
    <span class="text-sm text-white/40 tracking-widest font-medium" id="lbShowCounter">01 / 01</span>
    <button onclick="closeLbShow()" class="text-white/50 hover:text-white text-2xl bg-transparent border-none cursor-pointer transition-colors leading-none">
      <i class="fas fa-xmark"></i>
    </button>
  </div>
  <div class="flex items-center justify-center w-full flex-1 px-4 sm:px-20 pt-16 pb-2 gap-3 sm:gap-6">
    <button onclick="lbShowNav(-1)" class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-full border border-white/20 flex items-center justify-center text-white/50 hover:border-white/60 hover:text-white bg-transparent cursor-pointer transition-all">
      <i class="fas fa-chevron-left text-sm"></i>
    </button>
    <div class="flex-1 flex items-center justify-center" style="max-width:680px; max-height:calc(100vh - 280px);">
      <img id="lbShowImg" src="" alt="" class="max-w-full max-h-full object-contain shadow-2xl">
    </div>
    <button onclick="lbShowNav(1)" class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-full border border-white/20 flex items-center justify-center text-white/50 hover:border-white/60 hover:text-white bg-transparent cursor-pointer transition-all">
      <i class="fas fa-chevron-right text-sm"></i>
    </button>
  </div>
  <div class="text-center px-4 py-2">
    <div class="font-['Playfair_Display',serif] text-base text-stone-200">{{ $roomType->name }}</div>
    <div class="text-[10px] font-bold tracking-[0.28em] uppercase text-amber-400/70 mt-0.5">URBAN LUXE COLLECTION</div>
  </div>
  <div id="lbShowThumbs" class="flex gap-2 px-4 sm:px-8 pb-6 overflow-x-auto justify-center max-w-full" style="scrollbar-width:none;"></div>
</div>

@endsection

@push('scripts')
<script>
var lbShowIdx = 0;
var lbShowImgs = window.__showImages || [];

function openLb(idx) {
  lbShowIdx = idx || 0;
  renderLbShow();
  document.getElementById('lbOverlay').classList.add('lb-active');
  document.body.style.overflow = 'hidden';
}
function closeLbShow() {
  document.getElementById('lbOverlay').classList.remove('lb-active');
  document.body.style.overflow = '';
}
function lbShowNav(dir) {
  lbShowIdx = (lbShowIdx + dir + lbShowImgs.length) % lbShowImgs.length;
  renderLbShow();
}
function renderLbShow() {
  var i = lbShowIdx, total = lbShowImgs.length;
  var counter = document.getElementById('lbShowCounter');
  if (counter) counter.textContent = String(i+1).padStart(2,'0') + ' / ' + String(total).padStart(2,'0');
  var img = document.getElementById('lbShowImg');
  if (img) { img.src = lbShowImgs[i]; img.alt = window.__showRoomName; }
  var thumbsWrap = document.getElementById('lbShowThumbs');
  if (thumbsWrap) {
    thumbsWrap.innerHTML = '';
    lbShowImgs.forEach(function(src, idx) {
      var div = document.createElement('div');
      div.style.cssText = 'flex-shrink:0;width:68px;height:48px;overflow:hidden;cursor:pointer;border:2px solid ' + (idx===i ? '#d4af37':'transparent') + ';opacity:' + (idx===i ? '1':'0.45') + ';transition:all .2s;border-radius:4px;';
      div.onclick = (function(n){ return function(){ lbShowIdx=n; renderLbShow(); }; })(idx);
      var img = document.createElement('img');
      img.src = src; img.alt = ''; img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';
      div.appendChild(img); thumbsWrap.appendChild(div);
    });
    var active = thumbsWrap.children[i];
    if (active) active.scrollIntoView({ inline:'nearest', block:'nearest' });
  }
  var show = total > 1;
  ['lbOverlay .fa-chevron-left', 'lbOverlay .fa-chevron-right'].forEach(function(s){});
}
document.getElementById('lbOverlay').addEventListener('click', function(e) {
  if (e.target === this) closeLbShow();
});
document.addEventListener('keydown', function(e) {
  if (!document.getElementById('lbOverlay').classList.contains('lb-active')) return;
  if (e.key==='ArrowLeft')  lbShowNav(-1);
  if (e.key==='ArrowRight') lbShowNav(1);
  if (e.key==='Escape')     closeLbShow();
});
</script>
@endpush
