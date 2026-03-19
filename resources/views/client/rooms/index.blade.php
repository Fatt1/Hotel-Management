@extends('client.layouts.app')

@section('title', 'Chọn Phòng - Urban Luxe Hotel')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
/* ── Date input ─────────────────────────────────────────── */
input[type="date"] { color-scheme: light; }
input[type="date"]::-webkit-calendar-picker-indicator { opacity: 0.45; cursor: pointer; }

/* ── Qty select custom arrow ────────────────────────────── */
.qty-select {
  appearance: none; -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 0.5rem center; padding-right: 2rem;
}

/* ── Lightbox ───────────────────────────────────────────── */
#lightboxOverlay { display: none; }
#lightboxOverlay.lb-active { display: flex; }

/* ── Total panel sticky (desktop only) ─────────────────── */
@media (min-width: 768px) {
  #totalPanel.is-fixed { position: fixed !important; bottom: 24px; right: 32px; width: 300px; z-index: 40; }
}

/* ── Room row desktop grid ──────────────────────────────── */
@media (min-width: 1024px) {
  .room-row-grid {
    display: grid;
    grid-template-columns: 220px 1fr 100px 190px 130px;
    gap: 1rem;
    align-items: start;
  }
  .room-col-mobile-header { display: none; }
}

/* ── Search bar desktop layout ──────────────────────────── */
@media (min-width: 768px) {
  .search-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1.8fr auto;
  }
  .search-divider { border-right: 1px solid #f3f4f6; }
}

/* ── Room image height ──────────────────────────────────── */
.room-thumb { height: 180px; }
@media (min-width: 1024px) {
  .room-thumb { height: 148px; }
}
</style>
@endpush

@section('content')

@php
  $fallbackImages = [
    'STD' => ['https://picsum.photos/seed/std-room-1/800/560','https://picsum.photos/seed/std-room-2/800/560','https://picsum.photos/seed/std-room-3/800/560','https://picsum.photos/seed/std-room-4/800/560'],
    'DLX' => ['https://picsum.photos/seed/dlx-suite-1/800/560','https://picsum.photos/seed/dlx-suite-2/800/560','https://picsum.photos/seed/dlx-suite-3/800/560','https://picsum.photos/seed/dlx-suite-4/800/560','https://picsum.photos/seed/dlx-suite-5/800/560'],
    'FAM' => ['https://picsum.photos/seed/fam-room-1/800/560','https://picsum.photos/seed/fam-room-2/800/560','https://picsum.photos/seed/fam-room-3/800/560','https://picsum.photos/seed/fam-room-4/800/560'],
    'PRE' => ['https://picsum.photos/seed/pre-suite-1/800/560','https://picsum.photos/seed/pre-suite-2/800/560','https://picsum.photos/seed/pre-suite-3/800/560','https://picsum.photos/seed/pre-suite-4/800/560','https://picsum.photos/seed/pre-suite-5/800/560','https://picsum.photos/seed/pre-suite-6/800/560','https://picsum.photos/seed/pre-suite-7/800/560','https://picsum.photos/seed/pre-suite-8/800/560'],
  ];
  $defaultFallback = ['https://picsum.photos/seed/hotel-room-a/800/560','https://picsum.photos/seed/hotel-room-b/800/560'];

  // Convert DB image path (e.g. room-types/x.jpg) to a public URL for client pages.
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
@endphp

{{-- ============================================================
     HERO — dark city skyline section
     ============================================================ --}}
<div class="relative overflow-hidden" style="background: #0a0a0a; padding-top: 64px; padding-bottom: 80px; min-height: 280px;">
  {{-- Background city image --}}
  <img src="https://images.unsplash.com/photo-1617038220319-276d3cfab638?w=1920&q=80"
       alt="" aria-hidden="true"
       class="absolute inset-0 w-full h-full object-cover object-center opacity-30 grayscale-[20%] pointer-events-none">
  {{-- Dark gradient overlay --}}
  <div class="absolute inset-0" style="background: linear-gradient(180deg, rgba(5,5,5,0.55) 0%, rgba(5,5,5,0.72) 100%);"></div>

  {{-- Hero text --}}
  <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-14">
    <div class="flex items-center gap-3 mb-3">
      <div class="h-px w-8 bg-[#d4af37]/60"></div>
      <p class="text-[10px] font-bold tracking-[0.3em] uppercase text-[#d4af37]">Danh Sách Phòng</p>
    </div>
    <h1 class="font-['Playfair_Display'] text-[clamp(1.8rem,5vw,3.5rem)] font-bold text-stone-100 leading-tight">
      Khám Phá Không Gian<br>Nghỉ Dưỡng Đẳng Cấp
    </h1>
  </div>

  {{-- Search widget — floats at bottom of hero --}}
  <div class="relative z-20 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <form method="GET" action="{{ route('client.rooms.index') }}" id="searchForm">
      <div class="search-grid bg-white shadow-xl rounded-xl overflow-hidden">

        {{-- Check In --}}
        <div class="px-4 py-4 search-divider hover:bg-gray-50 transition-colors cursor-pointer border-b md:border-b-0 border-gray-100">
          <label class="block text-[9px] font-bold tracking-[0.2em] uppercase text-gray-500 mb-1.5" for="check_in">
            <i class="far fa-calendar mr-1"></i>Nhận Phòng
          </label>
          <input type="date" id="check_in" name="check_in"
                 value="{{ $checkIn }}"
                 min="{{ now()->format('Y-m-d') }}"
                 onchange="syncMinCheckout()"
                 class="w-full bg-transparent border-none outline-none text-gray-900 text-sm font-semibold cursor-pointer p-0">
        </div>

        {{-- Check Out --}}
        <div class="px-4 py-4 search-divider hover:bg-gray-50 transition-colors cursor-pointer border-b md:border-b-0 border-gray-100">
          <label class="block text-[9px] font-bold tracking-[0.2em] uppercase text-gray-500 mb-1.5" for="check_out">
            <i class="far fa-calendar mr-1"></i>Trả Phòng
          </label>
          <input type="date" id="check_out" name="check_out"
                 value="{{ $checkOut }}"
                 min="{{ \Carbon\Carbon::parse($checkIn)->addDay()->format('Y-m-d') }}"
                 class="w-full bg-transparent border-none outline-none text-gray-900 text-sm font-semibold cursor-pointer p-0">
        </div>

        {{-- Guests --}}
        <div class="px-4 py-4 search-divider hover:bg-gray-50 transition-colors select-none border-b md:border-b-0 border-gray-100" id="guestField">
          <label class="block text-[9px] font-bold tracking-[0.2em] uppercase text-gray-500 mb-1.5">
            <i class="far fa-user mr-1"></i>Khách
          </label>
          <div class="flex items-center justify-between cursor-pointer" onclick="toggleGuestDropdown(event)">
            <span class="text-gray-900 text-sm font-semibold" id="guestSummary">
              {{ $adults }} Người lớn, {{ $children }} Trẻ em, {{ $roomsCount }} phòng
            </span>
            <i class="fas fa-chevron-down text-gray-400 text-[10px] ml-2" id="guestChevron"></i>
          </div>
          <input type="hidden" name="adults"      id="inp_adults"      value="{{ $adults }}">
          <input type="hidden" name="children"    id="inp_children"    value="{{ $children }}">
          <input type="hidden" name="rooms_count" id="inp_rooms_count" value="{{ $roomsCount }}">
        </div>
        {{-- Guest dropdown teleported to body in JS below --}}

        {{-- Submit --}}
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold tracking-[0.14em] uppercase px-8 py-4 md:py-0 flex items-center justify-center gap-2 transition-colors whitespace-nowrap rounded-b-xl md:rounded-b-none md:rounded-r-xl">
          <i class="fas fa-magnifying-glass text-xs"></i>
          <span class="hidden sm:inline">Cập Nhật</span> Tìm Kiếm
        </button>

      </div>
    </form>
  </div>
</div>

{{-- ============================================================
     ROOM LISTING
     ============================================================ --}}
<div class="bg-[#f0f2f5] pb-16 pt-6">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Rounded white card container --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">

      {{-- Card header --}}
      <div class="px-5 sm:px-7 pt-7 pb-4 flex items-start sm:items-end justify-between flex-wrap gap-3">
        <div>
          <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Hãy chọn phòng</h2>
          <p class="text-sm text-gray-500 mt-0.5">
            @if($roomTypes->count() > 0)
              Hiện có <span class="font-semibold text-gray-700">{{ $roomTypes->count() }} loại phòng</span>
              phù hợp &nbsp;·&nbsp;
              {{ \Carbon\Carbon::parse($checkIn)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($checkOut)->format('d/m/Y') }}
              &nbsp;·&nbsp; {{ $nights }} đêm
            @else
              Không tìm thấy phòng phù hợp
            @endif
          </p>
        </div>
        <div class="flex gap-2">
          <button class="flex items-center gap-1.5 px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-gray-600 text-xs font-semibold tracking-wide uppercase hover:border-gray-400 transition-colors">
            <i class="fas fa-sliders text-xs"></i> Lọc
          </button>
          <button class="flex items-center gap-1.5 px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-gray-600 text-xs font-semibold tracking-wide uppercase hover:border-gray-400 transition-colors">
            <i class="fas fa-sort text-xs"></i> Sắp Xếp
          </button>
        </div>
      </div>

      {{-- Thin divider --}}
      <div class="h-px bg-gray-100 mx-5 sm:mx-7 mb-5"></div>

    @if($roomTypes->isEmpty())
      {{-- Empty state --}}
      <div class="flex flex-col items-center text-center py-16 px-6 mx-5 sm:mx-7 mb-7">
        <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mb-5">
          <i class="fas fa-magnifying-glass text-2xl text-red-400"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-800 mb-2 max-w-md">
          Rất tiếc, không tìm thấy phòng nào phù hợp với yêu cầu của bạn
        </h3>
        <p class="text-sm text-gray-400 mb-7 max-w-sm">
          Hãy thử thay đổi ngày hoặc bộ lọc để xem thêm các lựa chọn khác.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-3">
          <a href="{{ route('client.rooms.index') }}"
             class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
            Xóa bộ lọc
          </a>
          <a href="tel:+842812345678"
             class="px-5 py-2.5 border border-gray-300 hover:border-gray-400 text-gray-600 text-sm font-semibold rounded-lg transition-colors">
            Liên hệ đặt phòng
          </a>
        </div>
      </div>
    @else

      {{-- ── Desktop table header (hidden on mobile/tablet) ─────── --}}
      <div class="hidden lg:grid border-b border-gray-100 bg-gray-50 px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-400 mx-5 sm:mx-7 rounded-t-xl border border-gray-100"
           style="grid-template-columns: 220px 1fr 100px 190px 130px; gap: 1rem;">
        <span>Thông Tin Phòng</span>
        <span>Tiện Nghi</span>
        <span>Sức Chứa</span>
        <span>Giá &amp; Giá Trị</span>
        <span>Đặt Phòng</span>
      </div>

      {{-- ── Room rows ────────────────────────────────────────────── --}}
      <div class="mx-5 sm:mx-7 mb-2 border border-gray-100 rounded-xl overflow-hidden">
        @foreach($roomTypes as $rt)
          @php
            $dbImgs = $rt->images
              ->pluck('image_url')
              ->map($resolveImageUrl)
              ->filter()
              ->values()
              ->toArray();
            $imgs   = count($dbImgs) > 0 ? $dbImgs : ($fallbackImages[$rt->code] ?? $defaultFallback);
            $imgCnt = count($imgs);

            $bedParts = [];
            if ($rt->single_bed_quantity > 0) $bedParts[] = $rt->single_bed_quantity . ' giường đơn';
            if ($rt->double_bed_quantity > 0) $bedParts[] = $rt->double_bed_quantity . ' giường đôi';
            $bedStr = implode(', ', $bedParts) ?: 'Đang cập nhật';

            $area    = (float)$rt->width * (float)$rt->height;
            $areaSqm = number_format($area, 0, ',', '.') . ' m²';
            $areaFt  = number_format($area * 10.7639, 0, ',', '.') . ' ft²';

            $showAms   = $rt->amenities->take(6);
            $moreCount = max(0, $rt->amenities->count() - 6);
            $avail     = $rt->available_count ?? 0;
            $priceFmt  = number_format((float)$rt->daily_price, 0, ',', '.') . ' đ';
          @endphp

          {{-- ─── Mobile / Tablet: stacked card layout ─────────────── --}}
          <div class="lg:hidden border-b border-gray-100 last:border-b-0 hover:bg-blue-50/20 transition-colors p-4 sm:p-5">

            {{-- Top: image + basic info side by side on sm --}}
            <div class="flex gap-4">
              {{-- Image --}}
              <div class="relative flex-shrink-0 overflow-hidden rounded-xl cursor-pointer group bg-gray-100"
                   style="width: 130px; height: 100px;"
                   onclick="openLightbox({{ $rt->id }}, 0)">
                <img src="{{ $imgs[0] }}"
                     alt="{{ $rt->name }}"
                     loading="lazy"
                     onerror="this.src='https://picsum.photos/seed/hotel-default/800/560'"
                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                @if($imgCnt > 1)
                  <span class="absolute bottom-1 left-1.5 bg-black/60 text-white text-[10px] px-1.5 py-0.5 font-medium rounded">
                    1 / {{ $imgCnt }}
                  </span>
                @endif
              </div>

              {{-- Name + meta --}}
              <div class="flex-1 min-w-0">
                <h3 class="text-sm font-bold text-gray-900 leading-snug">{{ $rt->name }}</h3>
                <div class="mt-1.5 space-y-0.5">
                  @if($area > 0)
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                      <i class="fas fa-expand-arrows-alt text-[10px] w-3"></i>
                      {{ $areaSqm }}
                    </div>
                  @endif
                  <div class="flex items-center gap-1.5 text-xs text-gray-500">
                    <i class="fas fa-user text-[10px] w-3"></i>
                    Tối đa {{ $rt->adult_quantity }} người lớn
                  </div>
                  @if(count($bedParts) > 0)
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                      <i class="fas fa-bed text-[10px] w-3"></i>
                      {{ $bedStr }}
                    </div>
                  @endif
                </div>
                @if($imgCnt > 1)
                  <button onclick="openLightbox({{ $rt->id }}, 0)"
                          class="mt-2 text-xs font-semibold text-blue-500 hover:text-blue-700 flex items-center gap-1 transition-colors bg-transparent border-none cursor-pointer p-0">
                    Xem {{ $imgCnt }} ảnh <i class="fas fa-arrow-right text-[9px]"></i>
                  </button>
                @endif
              </div>
            </div>

            {{-- Amenities (collapsible strip) --}}
            @if($showAms->count() > 0)
              <div class="mt-3 flex flex-wrap gap-x-3 gap-y-1">
                @foreach($showAms as $am)
                  <div class="flex items-center gap-1.5">
                    <i class="fas {{ $am->icon ?: 'fa-circle-check' }} text-[10px] text-gray-400"></i>
                    <span class="text-xs text-gray-600">{{ $am->name }}</span>
                  </div>
                @endforeach
                @if($moreCount > 0)
                  <span class="text-[11px] text-gray-400">+{{ $moreCount }} tiện ích khác</span>
                @endif
              </div>
            @else
              <p class="mt-3 text-xs text-gray-400 italic">Tiện nghi đang được cập nhật...</p>
            @endif

            {{-- Bottom: price + qty + book --}}
            <div class="mt-4 flex items-center justify-between flex-wrap gap-3">
              <div>
                <div class="text-lg font-bold text-gray-900">{!! $priceFmt !!}</div>
                <div class="text-xs text-gray-400">mỗi đêm</div>
              </div>
              <div class="flex items-center gap-3">
                @if($avail > 0)
                  <div class="relative">
                    <select class="qty-select border border-gray-300 text-gray-800 text-sm font-semibold bg-white px-3 py-2 rounded-lg cursor-pointer focus:outline-none focus:border-blue-500 w-[78px]"
                            data-price="{{ $rt->daily_price }}"
                            data-room-id="{{ $rt->id }}"
                            onchange="updateTotal()">
                      @for($q = 0; $q <= min($avail, 5); $q++)
                        <option value="{{ $q }}">{{ $q }}</option>
                      @endfor
                    </select>
                  </div>
                  <div class="text-xs">
                    @if($avail <= 2)
                      <span class="text-orange-500 font-semibold">
                        <i class="fas fa-triangle-exclamation text-[9px]"></i> Còn {{ $avail }} phòng!
                      </span>
                    @else
                      <span class="text-gray-400">Còn {{ $avail }} phòng</span>
                    @endif
                  </div>
                @else
                  <span class="text-red-400 text-xs font-semibold"><i class="fas fa-ban text-[9px]"></i> Hết phòng</span>
                @endif
              </div>
            </div>

          </div>{{-- /mobile card --}}

          {{-- ─── Desktop: 5-column grid row ─────────────────────── --}}
          <div class="hidden lg:grid border-b border-gray-100 last:border-b-0 hover:bg-blue-50/20 transition-colors px-5 py-5 gap-4"
               style="grid-template-columns: 220px 1fr 100px 190px 130px; align-items: start;">

            {{-- 1. Thông tin phòng --}}
            <div>
              <div class="relative w-full overflow-hidden cursor-pointer group bg-gray-100 rounded-lg room-thumb"
                   onclick="openLightbox({{ $rt->id }}, 0)">
                <img src="{{ $imgs[0] }}"
                     alt="{{ $rt->name }}"
                     loading="lazy"
                     onerror="this.src='https://picsum.photos/seed/hotel-default/800/560'"
                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                @if($imgCnt > 1)
                  <span class="absolute bottom-1.5 left-2 bg-black/60 text-white text-[10px] px-1.5 py-0.5 font-medium">
                    1 / {{ $imgCnt }}
                  </span>
                @endif
              </div>

              <h3 class="mt-2.5 text-sm font-bold text-gray-900 leading-snug">{{ $rt->name }}</h3>
              <div class="mt-1.5 space-y-0.5">
                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                  <i class="fas fa-expand-arrows-alt text-[10px] w-3"></i>
                  {{ $areaSqm }} / {{ $areaFt }}
                </div>
                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                  <i class="fas fa-user text-[10px] w-3"></i>
                  Tối đa {{ $rt->adult_quantity }} người lớn
                </div>
                @if(count($bedParts) > 0)
                  <div class="flex items-center gap-1.5 text-xs text-gray-500">
                    <i class="fas fa-bed text-[10px] w-3"></i>
                    {{ $bedStr }}
                  </div>
                @endif
              </div>

              @if($imgCnt > 1)
                <button onclick="openLightbox({{ $rt->id }}, 0)"
                        class="mt-2 text-xs font-semibold text-blue-500 hover:text-blue-700 flex items-center gap-1 transition-colors bg-transparent border-none cursor-pointer p-0">
                  Xem ảnh <i class="fas fa-arrow-right text-[9px]"></i>
                </button>
              @endif
            </div>

            {{-- 2. Tiện nghi --}}
            <div class="pt-0.5">
              @forelse($showAms as $am)
                <div class="flex items-center gap-2 mb-1.5">
                  <i class="fas {{ $am->icon ?: 'fa-circle-check' }} text-[11px] text-gray-400 w-3.5"></i>
                  <span class="text-xs text-gray-600">{{ $am->name }}</span>
                </div>
              @empty
                <span class="text-xs text-gray-400 italic">Đang cập nhật...</span>
              @endforelse
              @if($moreCount > 0)
                <div class="text-[11px] text-gray-400 mt-1">+{{ $moreCount }} tiện ích khác</div>
              @endif
            </div>

            {{-- 3. Sức chứa --}}
            <div class="flex items-start flex-wrap gap-0.5 pt-0.5">
              @for($i = 0; $i < $rt->adult_quantity; $i++)
                <i class="fas fa-person text-base text-gray-500" title="Người lớn"></i>
              @endfor
              @for($i = 0; $i < $rt->child_quantity; $i++)
                <i class="fas fa-child text-sm text-gray-400" title="Trẻ em"></i>
              @endfor
            </div>

            {{-- 4. Giá & Giá trị --}}
            <div class="pt-0.5">
              <div class="text-xl font-bold text-gray-900">{!! $priceFmt !!}</div>
              <div class="text-xs text-gray-400 mb-2.5">mỗi đêm</div>
              @if($rt->description)
                <p class="text-[11px] text-gray-400 mt-2 leading-relaxed max-w-[180px]">
                  {{ \Illuminate\Support\Str::limit($rt->description, 80) }}
                </p>
              @endif
            </div>

            {{-- 5. Đặt phòng --}}
            <div class="pt-0.5">
              <div class="text-[9px] font-bold tracking-widest uppercase text-gray-400 mb-1.5">Số Lượng</div>
              @if($avail > 0)
                <div class="relative inline-block">
                  <select class="qty-select border border-gray-300 text-gray-800 text-sm font-semibold bg-white px-3 py-2 cursor-pointer focus:outline-none focus:border-blue-500 w-[78px]"
                          data-price="{{ $rt->daily_price }}"
                          data-room-id="{{ $rt->id }}"
                          onchange="updateTotal()">
                    @for($q = 0; $q <= min($avail, 5); $q++)
                      <option value="{{ $q }}">{{ $q }}</option>
                    @endfor
                  </select>
                </div>
                @if($avail <= 2)
                  <div class="text-[11px] text-orange-500 font-semibold mt-1">
                    <i class="fas fa-triangle-exclamation text-[9px]"></i> Chỉ còn {{ $avail }} phòng!
                  </div>
                @else
                  <div class="text-[11px] text-gray-400 mt-1">Còn {{ $avail }} phòng</div>
                @endif
              @else
                <div class="relative inline-block">
                  <select class="qty-select border border-gray-200 text-gray-400 text-sm bg-gray-50 px-3 py-2 w-[78px]" disabled>
                    <option>0</option>
                  </select>
                </div>
                <div class="text-[11px] text-red-400 font-semibold mt-1">
                  <i class="fas fa-ban text-[9px]"></i> Hết phòng
                </div>
              @endif
            </div>

          </div>{{-- /desktop row --}}

          {{-- Inject image data for lightbox --}}
          <script>
            window.__roomData = window.__roomData || {};
            window.__roomData[{{ $rt->id }}] = { name: @json($rt->name), images: @json($imgs) };
          </script>
        @endforeach
      </div>{{-- /room rows --}}

      {{-- Total Panel --}}
      <div id="totalSentinel"></div>
      <div id="totalPanel" class="flex justify-end px-4 sm:px-7 py-7">
        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 sm:p-7 w-full sm:w-auto sm:min-w-[300px]">
          <div class="text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-1" id="totalLabel">
            TỔNG GIÁ ({{ $nights }} ĐÊM)
          </div>
          <div class="text-3xl sm:text-4xl font-bold text-gray-900 mb-5" style="font-family: 'Playfair Display', serif;" id="totalPrice">
            0 &#8363;
          </div>
          <button id="bookBtn" disabled onclick="handleBookNow()"
                  class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed text-white font-bold text-sm tracking-wider uppercase py-4 rounded-xl flex items-center justify-center gap-2 transition-colors">
            Đặt phòng ngay <i class="fas fa-arrow-right text-xs"></i>
          </button>
          <p class="text-[10px] text-gray-400 text-center mt-3 leading-relaxed">
            Miễn phí huỷ trước 48 giờ &nbsp;·&nbsp; Không phụ thu ẩn
          </p>
        </div>
      </div>

    @endif

    </div>{{-- /white card --}}
  </div>
</div>

{{-- ============================================================
     LIGHTBOX
     ============================================================ --}}
<div id="lightboxOverlay"
     class="fixed inset-0 z-[9999] lb-active:flex flex-col items-center justify-center"
     style="background: rgba(0,0,0,0.96);">

  <div class="absolute top-0 left-0 right-0 flex items-center justify-between px-4 sm:px-8 py-5 z-10">
    <span class="text-sm text-white/40 tracking-widest font-medium" id="lbCounter">01 / 01</span>
    <button onclick="closeLightbox()" title="Đóng (Esc)"
            class="text-white/50 hover:text-white text-2xl bg-transparent border-none cursor-pointer transition-colors leading-none">
      <i class="fas fa-xmark"></i>
    </button>
  </div>

  <div class="flex items-center justify-center w-full flex-1 px-4 sm:px-20 pt-16 pb-2 gap-3 sm:gap-6">
    <button id="lbPrev" onclick="lbNavigate(-1)"
            class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-full border border-white/20 flex items-center justify-center text-white/50 hover:border-white/60 hover:text-white bg-transparent cursor-pointer transition-all">
      <i class="fas fa-chevron-left text-sm"></i>
    </button>
    <div class="flex-1 flex items-center justify-center" style="max-width: 680px; max-height: calc(100vh - 280px);">
      <img id="lbMainImg" src="" alt="" class="max-w-full max-h-full object-contain shadow-2xl">
    </div>
    <button id="lbNext" onclick="lbNavigate(1)"
            class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-full border border-white/20 flex items-center justify-center text-white/50 hover:border-white/60 hover:text-white bg-transparent cursor-pointer transition-all">
      <i class="fas fa-chevron-right text-sm"></i>
    </button>
  </div>

  <div class="text-center px-4 sm:px-8 py-2">
    <div class="font-['Playfair_Display',serif] text-base text-stone-200" id="lbTitle"></div>
    <div class="text-[10px] font-bold tracking-[0.28em] uppercase text-amber-400/70 mt-0.5">URBAN LUXE COLLECTION</div>
  </div>

  <div id="lbThumbs" class="flex gap-2 px-4 sm:px-8 pb-6 overflow-x-auto justify-center max-w-full" style="scrollbar-width:none;"></div>

</div>

@endsection

@push('scripts')
<script>
// ==========================================================
// SEARCH — Date sync
// ==========================================================
function syncMinCheckout() {
  var ci = document.getElementById('check_in');
  var co = document.getElementById('check_out');
  if (!ci || !co) return;
  var d = new Date(ci.value);
  d.setDate(d.getDate() + 1);
  var minVal = d.toISOString().split('T')[0];
  co.setAttribute('min', minVal);
  if (co.value <= ci.value) co.value = minVal;
}

// ==========================================================
// SEARCH — Guest dropdown (teleported to body to escape z-context)
// ==========================================================
var guestState = { adults: {{ $adults }}, children: {{ $children }}, rooms_count: {{ $roomsCount }} };
var guestMin   = { adults: 1, children: 0, rooms_count: 1 };

(function buildGuestDropdown() {
  var dd = document.createElement('div');
  dd.id = 'guestDropdown';
  dd.className = 'hidden bg-white border border-gray-200 shadow-2xl p-5 w-80 rounded-xl';
  dd.style.cssText = 'position:fixed; z-index:9999; top:0; left:0;';
  dd.innerHTML = `
    <div class="flex items-center justify-between py-3 border-b border-gray-100">
      <div><div class="text-sm font-semibold text-gray-800">Người lớn</div><div class="text-xs text-gray-400">Từ 13 tuổi trở lên</div></div>
      <div class="flex items-center gap-3">
        <button type="button" id="btn_adults_minus" onclick="changeGuest('adults',-1)" class="w-8 h-8 rounded-full border border-gray-300 text-gray-600 flex items-center justify-center text-lg leading-none hover:border-blue-500 hover:text-blue-500 transition-colors">−</button>
        <span class="w-5 text-center text-sm font-bold text-gray-800" id="disp_adults">${guestState.adults}</span>
        <button type="button" onclick="changeGuest('adults',1)" class="w-8 h-8 rounded-full border border-gray-300 text-gray-600 flex items-center justify-center text-lg leading-none hover:border-blue-500 hover:text-blue-500 transition-colors">+</button>
      </div>
    </div>
    <div class="flex items-center justify-between py-3 border-b border-gray-100">
      <div><div class="text-sm font-semibold text-gray-800">Trẻ em</div><div class="text-xs text-gray-400">Từ 0 - 12 tuổi</div></div>
      <div class="flex items-center gap-3">
        <button type="button" id="btn_children_minus" onclick="changeGuest('children',-1)" class="w-8 h-8 rounded-full border border-gray-300 text-gray-600 flex items-center justify-center text-lg leading-none hover:border-blue-500 hover:text-blue-500 transition-colors">−</button>
        <span class="w-5 text-center text-sm font-bold text-gray-800" id="disp_children">${guestState.children}</span>
        <button type="button" onclick="changeGuest('children',1)" class="w-8 h-8 rounded-full border border-gray-300 text-gray-600 flex items-center justify-center text-lg leading-none hover:border-blue-500 hover:text-blue-500 transition-colors">+</button>
      </div>
    </div>
    <div class="flex items-center justify-between py-3">
      <div class="text-sm font-semibold text-gray-800">Số phòng</div>
      <div class="flex items-center gap-3">
        <button type="button" id="btn_rooms_minus" onclick="changeGuest('rooms_count',-1)" class="w-8 h-8 rounded-full border border-gray-300 text-gray-600 flex items-center justify-center text-lg leading-none hover:border-blue-500 hover:text-blue-500 transition-colors">−</button>
        <span class="w-5 text-center text-sm font-bold text-gray-800" id="disp_rooms_count">${guestState.rooms_count}</span>
        <button type="button" onclick="changeGuest('rooms_count',1)" class="w-8 h-8 rounded-full border border-gray-300 text-gray-600 flex items-center justify-center text-lg leading-none hover:border-blue-500 hover:text-blue-500 transition-colors">+</button>
      </div>
    </div>
  `;
  document.body.appendChild(dd);
  ['adults','children','rooms_count'].forEach(function(k) {
    setTimeout(function() {
      var btn = document.getElementById('btn_' + k + '_minus');
      if (btn) btn.disabled = (guestState[k] <= (guestMin[k] || 0));
    }, 0);
  });
})();

function toggleGuestDropdown(e) {
  e.stopPropagation();
  var dd    = document.getElementById('guestDropdown');
  var field = document.getElementById('guestField');
  var chevron = document.getElementById('guestChevron');
  if (!dd) return;
  var isOpen = !dd.classList.contains('hidden');
  if (isOpen) {
    dd.classList.add('hidden');
    if (chevron) chevron.style.transform = '';
    return;
  }
  var rect = field.getBoundingClientRect();
  var ddWidth = 320;
  var leftPos = Math.min(rect.left, window.innerWidth - ddWidth - 8);
  dd.style.top  = (rect.bottom + 4) + 'px';
  dd.style.left = Math.max(8, leftPos) + 'px';
  dd.classList.remove('hidden');
  if (chevron) chevron.style.transform = 'rotate(180deg)';
}
document.addEventListener('click', function(e) {
  var field = document.getElementById('guestField');
  var dd    = document.getElementById('guestDropdown');
  var chevron = document.getElementById('guestChevron');
  if (dd && field && !field.contains(e.target) && !dd.contains(e.target)) {
    dd.classList.add('hidden');
    if (chevron) chevron.style.transform = '';
  }
});
function changeGuest(type, delta) {
  var min = guestMin[type] !== undefined ? guestMin[type] : 0;
  guestState[type] = Math.max(min, guestState[type] + delta);
  document.getElementById('disp_' + type).textContent = guestState[type];
  document.getElementById('inp_'  + type).value       = guestState[type];
  var mb = document.getElementById('btn_' + type + '_minus');
  if (mb) mb.disabled = (guestState[type] <= min);
  var s = document.getElementById('guestSummary');
  if (s) s.textContent = guestState.adults + ' Người lớn, ' + guestState.children + ' Trẻ em, ' + guestState.rooms_count + ' phòng';
}

// ==========================================================
// PRICE CALCULATOR
// ==========================================================
var NIGHTS = {{ $nights }};
function updateTotal() {
  var selects = document.querySelectorAll('.qty-select:not([disabled])');
  var total   = 0;
  var hasAny  = false;
  selects.forEach(function(sel) {
    var qty = parseInt(sel.value) || 0;
    total  += qty * (parseFloat(sel.dataset.price) || 0) * NIGHTS;
    if (qty > 0) hasAny = true;
  });
  var priceEl = document.getElementById('totalPrice');
  var btnEl   = document.getElementById('bookBtn');
  if (priceEl) priceEl.innerHTML = new Intl.NumberFormat('vi-VN').format(total) + ' &#8363;';
  if (btnEl)   btnEl.disabled   = !hasAny;
}
function handleBookNow() {
  var selects = document.querySelectorAll('.qty-select');
  var hasAny  = false;
  selects.forEach(function(s){ if (parseInt(s.value) > 0) hasAny = true; });
  if (!hasAny) return;

  var form = document.createElement('form');
  form.method = 'POST';
  form.action = '{{ route("client.booking.checkout") }}';
  form.style.display = 'none';

  var csrf = document.createElement('input');
  csrf.type = 'hidden'; csrf.name = '_token';
  csrf.value = '{{ csrf_token() }}';
  form.appendChild(csrf);

  var params = {
    check_in:    document.getElementById('check_in').value,
    check_out:   document.getElementById('check_out').value,
    adults:      document.getElementById('inp_adults').value,
    children:    document.getElementById('inp_children').value,
    rooms_count: document.getElementById('inp_rooms_count').value,
  };
  Object.keys(params).forEach(function(k) {
    var i = document.createElement('input');
    i.type = 'hidden'; i.name = k; i.value = params[k];
    form.appendChild(i);
  });

  selects.forEach(function(sel) {
    var qty = parseInt(sel.value) || 0;
    if (qty > 0) {
      var i = document.createElement('input');
      i.type = 'hidden';
      i.name  = 'qty_' + sel.dataset.roomId;
      i.value = qty;
      form.appendChild(i);
    }
  });

  document.body.appendChild(form);
  form.submit();
}
document.addEventListener('DOMContentLoaded', function() { updateTotal(); });

// ==========================================================
// TOTAL PANEL — Sticky-fixed at bottom on desktop
// ==========================================================
function updateTotalPanelState() {
  if (window.innerWidth < 768) return; // no sticky on mobile
  var sentinel = document.getElementById('totalSentinel');
  var panel    = document.getElementById('totalPanel');
  if (!sentinel || !panel) return;
  var rect = sentinel.getBoundingClientRect();
  if (rect.top <= window.innerHeight) {
    panel.classList.remove('is-fixed');
  } else {
    panel.classList.add('is-fixed');
  }
}
window.addEventListener('scroll', updateTotalPanelState, { passive: true });
window.addEventListener('resize', updateTotalPanelState, { passive: true });

// Close guest dropdown on scroll
window.addEventListener('scroll', function() {
  var dd = document.getElementById('guestDropdown');
  var chevron = document.getElementById('guestChevron');
  if (dd && !dd.classList.contains('hidden')) {
    dd.classList.add('hidden');
    if (chevron) chevron.style.transform = '';
  }
}, { passive: true });
document.addEventListener('DOMContentLoaded', updateTotalPanelState);

// ==========================================================
// LIGHTBOX
// ==========================================================
var lbRoomId = null, lbIndex = 0;
function openLightbox(roomId, startIdx) {
  var data = window.__roomData && window.__roomData[roomId];
  if (!data || !data.images || !data.images.length) return;
  lbRoomId = roomId; lbIndex = startIdx || 0;
  renderLightbox();
  document.getElementById('lightboxOverlay').classList.add('lb-active');
  document.body.style.overflow = 'hidden';
}
function closeLightbox() {
  document.getElementById('lightboxOverlay').classList.remove('lb-active');
  document.body.style.overflow = '';
}
function lbNavigate(dir) {
  var data = window.__roomData[lbRoomId];
  if (!data) return;
  lbIndex = (lbIndex + dir + data.images.length) % data.images.length;
  renderLightbox();
}
function lbGoTo(idx) { lbIndex = idx; renderLightbox(); }
function renderLightbox() {
  var data   = window.__roomData[lbRoomId];
  if (!data) return;
  var imgs   = data.images, total = imgs.length, i = lbIndex;
  var counter = document.getElementById('lbCounter');
  if (counter) counter.textContent = String(i + 1).padStart(2,'0') + ' / ' + String(total).padStart(2,'0');
  var mainImg = document.getElementById('lbMainImg');
  if (mainImg) { mainImg.src = imgs[i]; mainImg.alt = data.name; }
  var title = document.getElementById('lbTitle');
  if (title) title.textContent = data.name;
  var thumbsWrap = document.getElementById('lbThumbs');
  if (thumbsWrap) {
    thumbsWrap.innerHTML = '';
    imgs.forEach(function(src, idx) {
      var div = document.createElement('div');
      div.style.cssText = 'flex-shrink:0;width:68px;height:48px;overflow:hidden;cursor:pointer;border:2px solid ' + (idx===i ? '#d4af37':'transparent') + ';opacity:' + (idx===i ? '1':'0.45') + ';transition:all .2s;border-radius:4px;';
      div.onclick = (function(n){ return function(){ lbGoTo(n); }; })(idx);
      var img = document.createElement('img');
      img.src = src; img.alt = ''; img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';
      div.appendChild(img); thumbsWrap.appendChild(div);
    });
    var active = thumbsWrap.children[i];
    if (active) active.scrollIntoView({ inline: 'nearest', block: 'nearest' });
  }
  var show = total > 1;
  var prev = document.getElementById('lbPrev'), next = document.getElementById('lbNext');
  if (prev) prev.style.visibility = show ? 'visible' : 'hidden';
  if (next) next.style.visibility = show ? 'visible' : 'hidden';
}
document.getElementById('lightboxOverlay').addEventListener('click', function(e) {
  if (e.target === this) closeLightbox();
});
document.addEventListener('keydown', function(e) {
  if (!document.getElementById('lightboxOverlay').classList.contains('lb-active')) return;
  if (e.key === 'ArrowLeft')  lbNavigate(-1);
  if (e.key === 'ArrowRight') lbNavigate(1);
  if (e.key === 'Escape')     closeLightbox();
});
</script>
@endpush
