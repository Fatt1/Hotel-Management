@extends('client.layouts.app')

@section('title', 'Urban Luxe Hotel - Sanctuary in the City')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')

{{-- ============================================================
     HERO SECTION
     ============================================================ --}}
<section class="relative min-h-screen flex flex-col justify-center bg-[#080c10]">

  {{-- Background (overflow-hidden only on the bg wrapper, NOT on the section) --}}
  <div class="absolute inset-0 overflow-hidden pointer-events-none">
    <img src="https://images.unsplash.com/photo-1486325212027-8081e485255e?w=1920&q=80"
         alt=""
         class="w-full h-full object-cover object-center opacity-55">
    <div class="absolute inset-0 bg-[linear-gradient(to_bottom,rgba(8,12,16,.35)_0%,rgba(8,12,16,.8)_100%)]"></div>
  </div>

  {{-- Content --}}
  <div class="relative z-10 max-w-6xl mx-auto px-8 w-full pt-28 pb-16">

    {{-- Label --}}
    <p class="text-amber-400/80 text-[11px] font-bold tracking-[0.3em] uppercase mb-4">
      Urban Luxe · Ho Chi Minh City
    </p>

    {{-- Heading --}}
    <h1 class="font-['Playfair_Display'] text-5xl md:text-6xl lg:text-7xl font-bold text-white leading-tight mb-5 max-w-xl">
      Sanctuary<br>in the City.
    </h1>

    {{-- Subtitle --}}
    <p class="text-white/55 text-sm leading-relaxed mb-14 max-w-sm">
      Experience unparalleled luxury at Urban Luxe — where contemporary design meets timeless elegance in the heart of the city.
    </p>

    {{-- Search Bar --}}
    <form method="GET" action="{{ route('client.rooms.index') }}" id="heroSearchForm"
          class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl shadow-2xl max-w-4xl">
      <div class="grid grid-cols-4 divide-x divide-white/10">

        {{-- Check-in --}}
        <div class="px-5 py-4 group">
          <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/40 mb-1.5 flex items-center gap-1.5">
            <i class="far fa-calendar text-white/30 text-[10px]"></i> Nhận Phòng
          </div>
          <input type="text" name="check_in" id="heroCheckIn"
                 placeholder="dd/mm/yyyy"
                 autocomplete="off" readonly
                 class="w-full bg-transparent text-white text-sm font-semibold placeholder-white/30 outline-none cursor-pointer">
        </div>

        {{-- Check-out --}}
        <div class="px-5 py-4 group">
          <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/40 mb-1.5 flex items-center gap-1.5">
            <i class="far fa-calendar text-white/30 text-[10px]"></i> Trả Phòng
          </div>
          <input type="text" name="check_out" id="heroCheckOut"
                 placeholder="dd/mm/yyyy"
                 autocomplete="off" readonly
                 class="w-full bg-transparent text-white text-sm font-semibold placeholder-white/30 outline-none cursor-pointer">
        </div>

        {{-- Guests --}}
        <div class="px-5 py-4 relative" id="guestWrapper">
          <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/40 mb-1.5 flex items-center gap-1.5">
            <i class="fas fa-user text-white/30 text-[10px]"></i> Khách
          </div>
          <button type="button" id="guestTriggerHome"
                  class="w-full text-left text-white text-sm font-semibold outline-none"
                  onclick="toggleGuestHome()">
            <span id="guestLabelHome">2 Người lớn</span>
            <i class="fas fa-chevron-down text-white/40 text-[9px] ml-1"></i>
          </button>
          <input type="hidden" name="adults" id="adultsHome" value="2">
          <input type="hidden" name="children" id="childrenHome" value="0">

          {{-- Dropdown moved to body level below --}}
        </div>

        {{-- Search Button --}}
        <div class="px-5 py-3 flex items-center">
          <button type="submit"
                  class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold tracking-wider uppercase py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors">
            <i class="fas fa-search text-xs"></i>
            Tìm Phòng
          </button>
        </div>

      </div>
    </form>

  </div>

  {{-- Scroll indicator --}}
  <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 animate-bounce">
    <span class="text-white/30 text-[10px] tracking-widest uppercase">Khám Phá</span>
    <i class="fas fa-chevron-down text-white/30 text-xs"></i>
  </div>
</section>

{{-- Guest Dropdown Portal — position:absolute anchored to document (not viewport) --}}
<div id="guestDropdownHome"
     class="hidden absolute w-64 border border-white/15 rounded-xl shadow-2xl p-4"
     style="z-index:9999; background-color:#0d1117;">
  {{-- Adults --}}
  <div class="flex items-center justify-between mb-3">
    <div>
      <p class="text-white text-sm font-semibold">Người lớn</p>
      <p class="text-white/40 text-xs">Từ 12 tuổi trở lên</p>
    </div>
    <div class="flex items-center gap-3">
      <button type="button" onclick="changeGuestHome('adults',-1)"
              style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(255,255,255,.2);color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:transparent;cursor:pointer;font-size:16px;line-height:1;">−</button>
      <span id="adultsCountHome" class="text-white font-bold text-sm w-4 text-center">2</span>
      <button type="button" onclick="changeGuestHome('adults',1)"
              style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(255,255,255,.2);color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:transparent;cursor:pointer;font-size:16px;line-height:1;">+</button>
    </div>
  </div>
  {{-- Children --}}
  <div class="flex items-center justify-between mb-4">
    <div>
      <p class="text-white text-sm font-semibold">Trẻ em</p>
      <p class="text-white/40 text-xs">2–11 tuổi</p>
    </div>
    <div class="flex items-center gap-3">
      <button type="button" onclick="changeGuestHome('children',-1)"
              style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(255,255,255,.2);color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:transparent;cursor:pointer;font-size:16px;line-height:1;">−</button>
      <span id="childrenCountHome" class="text-white font-bold text-sm w-4 text-center">0</span>
      <button type="button" onclick="changeGuestHome('children',1)"
              style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(255,255,255,.2);color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:transparent;cursor:pointer;font-size:16px;line-height:1;">+</button>
    </div>
  </div>
  <button type="button" onclick="closeGuestHome()"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 rounded-lg transition-colors">Áp Dụng</button>
</div>

{{-- ============================================================
     AMENITIES SECTION — "World-Class Amenities"
     ============================================================ --}}
<section class="bg-[#0d1117] py-20">
  <div class="max-w-6xl mx-auto px-8">

    {{-- Header --}}
    <div class="flex items-end justify-between mb-10">
      <div>
        <p class="text-amber-400 text-[11px] font-bold tracking-[0.25em] uppercase mb-3">Đẳng Cấp Thượng Hạng</p>
        <h2 class="font-['Playfair_Display'] text-4xl font-bold text-white">World-Class Amenities</h2>
        <p class="text-white/40 text-sm mt-3 max-w-sm leading-relaxed">
          Trải nghiệm những tiện ích đẳng cấp thế giới được thiết kế để chiều chuộng mọi giác quan của bạn.
        </p>
      </div>
      <a href="{{ route('client.amenities.index') }}"
         class="flex items-center gap-2 text-white/50 hover:text-white text-sm font-semibold transition-colors whitespace-nowrap mb-1">
        Xem tất cả <i class="fas fa-arrow-right text-xs"></i>
      </a>
    </div>

    {{-- 2x2 Grid --}}
    <div class="grid grid-cols-2 gap-4">

      @php
        $amenityCards = [
          [
            'title' => 'Elite Spa & Wellness',
            'desc'  => 'Thư giãn tâm hồn với các liệu trình spa cao cấp, thiết kế riêng để phục hồi năng lượng và cân bằng nội tâm.',
            'img'   => 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?w=800&q=80',
            'icon'  => 'fas fa-spa',
          ],
          [
            'title' => 'Skyline Infinity Pool',
            'desc'  => 'Hồ bơi vô cực trên lầu thượng với tầm nhìn panorama toàn cảnh thành phố khi hoàng hôn buông xuống.',
            'img'   => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&q=80',
            'icon'  => 'fas fa-water',
          ],
          [
            'title' => 'State-of-the-Art Fitness',
            'desc'  => 'Phòng gym hiện đại với thiết bị tiên tiến nhất và huấn luyện viên cá nhân theo yêu cầu 24/7.',
            'img'   => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=800&q=80',
            'icon'  => 'fas fa-dumbbell',
          ],
          [
            'title' => '24/7 Bespoke Concierge',
            'desc'  => 'Dịch vụ concierge cá nhân hóa sẵn sàng thỏa mãn mọi yêu cầu đặc biệt của quý khách bất kể giờ giấc.',
            'img'   => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=800&q=80',
            'icon'  => 'fas fa-concierge-bell',
          ],
        ];
      @endphp

      @foreach($amenityCards as $card)
        <div class="relative overflow-hidden rounded-2xl group cursor-pointer"
             style="height:260px;">
          <img src="{{ $card['img'] }}" alt="{{ $card['title'] }}"
               class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
          {{-- dark gradient over image --}}
          <div class="absolute inset-0 bg-[linear-gradient(to_top,rgba(0,0,0,.85)_0%,rgba(0,0,0,.1)_60%)]"></div>
          {{-- Content --}}
          <div class="absolute bottom-0 left-0 right-0 p-6">
            <div class="flex items-center gap-2 mb-2">
              <div class="w-6 h-6 rounded-full bg-amber-400/20 flex items-center justify-center">
                <i class="{{ $card['icon'] }} text-amber-400 text-[10px]"></i>
              </div>
            </div>
            <h3 class="text-white font-bold text-lg leading-tight mb-1">{{ $card['title'] }}</h3>
            <p class="text-white/60 text-xs leading-relaxed line-clamp-2">{{ $card['desc'] }}</p>
          </div>
        </div>
      @endforeach

    </div>

  </div>
</section>

{{-- ============================================================
     CURATED STAYS — Room Cards
     ============================================================ --}}
<section class="bg-[#0d1117] py-16 border-t border-white/5">
  <div class="max-w-6xl mx-auto px-8">

    {{-- Header --}}
    <div class="mb-10">
      <p class="text-amber-400 text-[11px] font-bold tracking-[0.25em] uppercase mb-3">Lựa Chọn Tinh Tế</p>
      <h2 class="font-['Playfair_Display'] text-4xl font-bold text-white">Curated Stays</h2>
      <p class="text-white/40 text-sm mt-2">Những không gian nghỉ dưỡng được tuyển chọn kỹ lưỡng cho bạn.</p>
    </div>

    {{-- Room cards --}}
    <div class="grid grid-cols-3 gap-5">
      @forelse($featuredRooms as $room)
        @php
          $imgUrl = $room->images->first()?->image_url
                    ?? 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&q=80';
          $bedText = '';
          if ($room->double_bed_quantity > 0) $bedText = $room->double_bed_quantity . ' giường đôi';
          elseif ($room->single_bed_quantity > 0) $bedText = $room->single_bed_quantity . ' giường đơn';
        @endphp
        <div class="bg-[#141920] border border-white/8 rounded-2xl overflow-hidden group hover:border-white/20 transition-all duration-300">

          {{-- Image --}}
          <div class="relative h-52 overflow-hidden">
            <img src="{{ $imgUrl }}" alt="{{ $room->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            {{-- Best value badge --}}
            @if($loop->last)
              <div class="absolute top-3 right-3 bg-blue-600 text-white text-[10px] font-bold px-2 py-1 rounded-full tracking-wider uppercase">
                Best Value
              </div>
            @endif
          </div>

          {{-- Info --}}
          <div class="p-5">
            <h3 class="font-['Playfair_Display'] text-white font-bold text-lg mb-1">{{ $room->name }}</h3>
            <p class="text-white/40 text-xs leading-relaxed mb-4 line-clamp-2">
              {{ $room->description ?? 'Phòng cao cấp với thiết kế hiện đại, tầm nhìn tuyệt đẹp và đầy đủ tiện nghi sang trọng.' }}
            </p>

            {{-- Tags --}}
            <div class="flex flex-wrap items-center gap-2 mb-4">
              @if($room->width)
                <span class="flex items-center gap-1 text-white/40 text-[11px]">
                  <i class="fas fa-ruler-combined text-[9px]"></i> {{ $room->width }}m²
                </span>
              @endif
              @if($room->adult_quantity)
                <span class="flex items-center gap-1 text-white/40 text-[11px]">
                  <i class="fas fa-user text-[9px]"></i> {{ $room->adult_quantity }} khách
                </span>
              @endif
              @if($bedText)
                <span class="flex items-center gap-1 text-white/40 text-[11px]">
                  <i class="fas fa-bed text-[9px]"></i> {{ $bedText }}
                </span>
              @endif
            </div>

            {{-- Price + CTA --}}
            <div class="flex items-end justify-between">
              <div>
                <span class="text-white/30 text-[10px]">Từ</span>
                <div class="text-white font-bold text-lg">
                  {{ number_format($room->daily_price / 1000, 0, ',', '.') }}K
                  <span class="text-white/30 text-xs font-normal">/đêm</span>
                </div>
              </div>
              <a href="{{ route('client.rooms.index') }}"
                 class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition-colors">
                Đặt Ngay
              </a>
            </div>
          </div>
        </div>
      @empty
        {{-- Static fallback cards if no DB data --}}
        @foreach([
          ['Phòng Deluxe', 'Không gian sang trọng với cửa sổ toàn cảnh thành phố hiện đại.', '900K', 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&q=80'],
          ['The Premium Suite', 'Phòng suite cao cấp với phòng khách riêng biệt và bồn tắm jacuzzi.', '1.5M', 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=800&q=80'],
          ['The Urban Penthouse', 'Penthouse đỉnh cao tọa lạc tại tầng thượng với tầm nhìn 360°.', '3.2M', 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800&q=80'],
        ] as [$name, $desc, $price, $img])
          <div class="bg-[#141920] border border-white/8 rounded-2xl overflow-hidden group hover:border-white/20 transition-all duration-300">
            <div class="relative h-52 overflow-hidden">
              <img src="{{ $img }}" alt="{{ $name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            </div>
            <div class="p-5">
              <h3 class="font-['Playfair_Display'] text-white font-bold text-lg mb-1">{{ $name }}</h3>
              <p class="text-white/40 text-xs leading-relaxed mb-4">{{ $desc }}</p>
              <div class="flex items-end justify-between">
                <div>
                  <span class="text-white/30 text-[10px]">Từ</span>
                  <div class="text-white font-bold text-lg">{{ $price }}<span class="text-white/30 text-xs font-normal">/đêm</span></div>
                </div>
                <a href="{{ route('client.rooms.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition-colors">Đặt Ngay</a>
              </div>
            </div>
          </div>
        @endforeach
      @endforelse
    </div>

    {{-- View all rooms CTA --}}
    <div class="text-center mt-10">
      <a href="{{ route('client.rooms.index') }}"
         class="inline-flex items-center gap-2.5 border border-white/15 hover:border-white/35 text-white/70 hover:text-white text-sm font-semibold px-8 py-3.5 rounded-xl transition-colors">
        Xem Tất Cả Phòng <i class="fas fa-arrow-right text-xs"></i>
      </a>
    </div>

  </div>
</section>

{{-- ============================================================
     TESTIMONIAL QUOTE SECTION
     ============================================================ --}}
<section class="bg-[#080c10] py-24 relative overflow-hidden">

  {{-- Decorative blur circles --}}
  <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-600/5 rounded-full filter blur-3xl pointer-events-none"></div>
  <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-amber-400/5 rounded-full filter blur-3xl pointer-events-none"></div>

  <div class="relative z-10 max-w-3xl mx-auto px-8 text-center">

    {{-- Large quote mark --}}
    <div class="w-10 h-10 rounded-full bg-amber-400/15 flex items-center justify-center mx-auto mb-8">
      <i class="fas fa-quote-left text-amber-400 text-sm"></i>
    </div>

    <blockquote class="font-['Playfair_Display'] text-3xl md:text-4xl font-bold text-white leading-tight mb-8 italic">
      "An architectural marvel that redefines what a city hotel can be. The attention to detail is simply unmatched."
    </blockquote>

    {{-- Reviewer --}}
    <div class="flex items-center justify-center gap-3">
      <img src="https://i.pravatar.cc/48?img=12" alt="Ben Collins"
           class="w-10 h-10 rounded-full border-2 border-amber-400/30">
      <div class="text-left">
        <p class="text-white font-semibold text-sm">Ben Collins</p>
        <p class="text-white/40 text-xs">Travel + Leisure, Top 10 Hotels</p>
      </div>
    </div>

  </div>
</section>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
// ── Date pickers ──
var todayStr = new Date().toISOString().split('T')[0];

var checkInPicker = flatpickr('#heroCheckIn', {
  dateFormat: 'd/m/Y',
  altInput: false,
  minDate: 'today',
  locale: { firstDayOfWeek: 1 },
  onChange: function(selected) {
    if (selected[0]) {
      checkOutPicker.set('minDate', selected[0]);
      if (!checkOutPicker.selectedDates.length) {
        var next = new Date(selected[0]);
        next.setDate(next.getDate() + 1);
        checkOutPicker.setDate(next);
      }
    }
  }
});

var checkOutPicker = flatpickr('#heroCheckOut', {
  dateFormat: 'd/m/Y',
  minDate: 'today',
  locale: { firstDayOfWeek: 1 },
});

// ── Guests dropdown ── (fixed position portal, escapes all overflow clipping)
var gAdults = 2, gChildren = 0;
var guestOpen = false;

function toggleGuestHome() {
  if (guestOpen) { closeGuestHome(); return; }
  var trigger = document.getElementById('guestTriggerHome');
  var dd = document.getElementById('guestDropdownHome');
  var rect = trigger.getBoundingClientRect();
  // position:absolute relative to document — add scroll offset so it stays fixed when scrolling
  dd.style.left = (rect.left + window.scrollX) + 'px';
  dd.style.top  = (rect.bottom + window.scrollY + 8) + 'px';
  dd.classList.remove('hidden');
  guestOpen = true;
}
function closeGuestHome() {
  document.getElementById('guestDropdownHome').classList.add('hidden');
  guestOpen = false;
}
function changeGuestHome(type, delta) {
  if (type === 'adults') {
    gAdults = Math.max(1, gAdults + delta);
    document.getElementById('adultsCountHome').textContent = gAdults;
    document.getElementById('adultsHome').value = gAdults;
  } else {
    gChildren = Math.max(0, gChildren + delta);
    document.getElementById('childrenCountHome').textContent = gChildren;
    document.getElementById('childrenHome').value = gChildren;
  }
  var label = gAdults + ' Người lớn' + (gChildren > 0 ? ', ' + gChildren + ' Trẻ em' : '');
  document.getElementById('guestLabelHome').textContent = label;
}

// Close dropdown only when clicking outside BOTH the trigger wrapper AND the portal dropdown
document.addEventListener('click', function(e) {
  var wrapper = document.getElementById('guestWrapper');
  var dd = document.getElementById('guestDropdownHome');
  if (wrapper && !wrapper.contains(e.target) && dd && !dd.contains(e.target)) {
    closeGuestHome();
  }
});

// ── Form submit: convert dd/mm/yyyy → yyyy-mm-dd for query params ──
document.getElementById('heroSearchForm').addEventListener('submit', function(e) {
  var inField  = document.getElementById('heroCheckIn');
  var outField = document.getElementById('heroCheckOut');

  function parseDMY(str) {
    if (!str) return '';
    var parts = str.split('/');
    if (parts.length !== 3) return str;
    return parts[2] + '-' + parts[1] + '-' + parts[0];
  }

  inField.value  = parseDMY(inField.value);
  outField.value = parseDMY(outField.value);
});
</script>
@endpush
