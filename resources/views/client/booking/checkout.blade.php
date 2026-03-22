@extends('client.layouts.app')

@section('title', 'Thông Tin Khách - Urban Luxe Hotel')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endpush

@section('content')

{{-- ============================================================
     HERO – dark city background with step indicator
     ============================================================ --}}
<div class="relative overflow-hidden bg-[#0a0a0a] min-h-[220px] pt-24 pb-14">
  <img src="https://images.unsplash.com/photo-1617038220319-276d3cfab638?w=1920&q=80"
       alt="" aria-hidden="true"
       class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none"
       class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none opacity-25 grayscale-[20%]">
  <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(5,5,5,0.6)_0%,rgba(5,5,5,0.75)_100%)]"></div>

  <div class="relative z-10 max-w-3xl mx-auto px-4 sm:px-8 text-center">
    {{-- Step indicator --}}
    <div class="flex items-center justify-center gap-2 mb-4">
      <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>
      <span class="text-[11px] font-bold tracking-[0.25em] uppercase text-white/60">Bước 2 / 3</span>
    </div>
    <h1 class="font-['Playfair_Display'] text-4xl font-bold text-white mb-3">Thông Tin Khách</h1>
    <p class="text-stone-300/70 text-sm">Vui lòng điền thông tin để hoàn tất đặt phòng của bạn.</p>
  </div>
</div>

{{-- ============================================================
     MAIN CONTENT — 2 columns: form left, summary right
     ============================================================ --}}
<div class="bg-[#f0f2f5] min-h-screen py-10">
  <div class="max-w-6xl mx-auto px-4 md:px-8">
    <div class="flex flex-col lg:flex-row gap-7 items-start">

      {{-- ======================================================
           LEFT — Guest Information Form
           ====================================================== --}}
      <div class="flex-1 bg-white rounded-2xl shadow-lg border border-gray-100 p-5 md:p-8 min-w-0">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-0 border-b border-gray-100 sm:border-none pb-4 sm:pb-0 mb-6">
          <h2 class="text-xl font-bold text-gray-900">Nhập thông tin của bạn</h2>
          <span class="text-xs text-gray-400">Các trường có dấu <span class="text-red-500">*</span> là bắt buộc</span>
        </div>

        {{-- Email verify --}}
        <div class="bg-gray-50 border border-gray-100 rounded-xl p-5 mb-6">
          <p class="text-sm font-semibold text-gray-700 mb-3">
            <i class="fas fa-circle-check text-blue-500 mr-2"></i>Xác thực email để tự điền thông tin
          </p>
          <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
              <i class="far fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
              <input type="email" id="emailVerify" placeholder="Nhập địa chỉ email của bạn"
                     class="w-full border border-gray-200 rounded-lg py-3 pl-10 pr-4 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-100 transition-all">
            </div>
            <button type="button" id="verifyEmailBtn"
                    class="w-full sm:w-auto px-5 py-3 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors whitespace-nowrap">
              Xác Thực
            </button>
          </div>
          <p id="verifyMessage" class="hidden text-[11px] text-red-500 mt-2 flex items-center gap-1.5">
            <i class="fas fa-circle-info text-[10px]"></i>
            Không tìm thấy tài khoản. Vui lòng điền thông tin bên dưới để tạo mới.
          </p>
        </div>

        {{-- The actual booking form —uses POST to /booking/checkout but we reuse the same route --}}
        <form method="POST" action="{{ route('client.booking.payment') }}" id="checkoutForm">
          @csrf

          {{-- Pass through all booking params as hidden fields --}}
          <input type="hidden" name="check_in"    value="{{ $checkIn }}">
          <input type="hidden" name="check_out"   value="{{ $checkOut }}">
          <input type="hidden" name="adults"       value="{{ $adults }}">
          <input type="hidden" name="children"     value="{{ $children }}">
          <input type="hidden" name="rooms_count"  value="{{ $roomsCount }}">
          @foreach($selectedRooms as $item)
            <input type="hidden" name="qty_{{ $item['room_type']->id }}" value="{{ $item['qty'] }}">
          @endforeach
          <input type="hidden" name="email_verify" id="emailVerifyHidden" value="">

          {{-- First + Last Name --}}
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-[11px] font-bold tracking-widest uppercase text-gray-500 mb-1.5">
                Họ <span class="text-red-500">*</span>
              </label>
              <input type="text" name="last_name" id="lastNameInput" placeholder="vd. Nguyễn"
                     class="w-full border border-gray-200 rounded-lg py-3 px-4 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-100 transition-all"
                     required>
            </div>
            <div>
              <label class="block text-[11px] font-bold tracking-widest uppercase text-gray-500 mb-1.5">
                Tên <span class="text-red-500">*</span>
              </label>
              <input type="text" name="first_name" id="firstNameInput" placeholder="vd. Văn An"
                     class="w-full border border-gray-200 rounded-lg py-3 px-4 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-100 transition-all"
                     required>
            </div>
          </div>

          {{-- Country --}}
          <div class="mb-4">
            <label class="block text-[11px] font-bold tracking-widest uppercase text-gray-500 mb-1.5">
              Quốc gia / Khu vực <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                @php
                  $clientAuthVM = new \App\ViewModels\ClientAuthViewModel();
                @endphp
                @include('admin.customers._country_picker', [
                    'inputName' => 'country',
                    'inputId' => 'countryInput',
                    'selectedValue' => 'Việt Nam',
                    'pickerCountries' => $clientAuthVM->countries(),
                    'placeholder' => 'Chọn quốc gia của bạn'
                ])
            </div>
          </div>

          {{-- Phone --}}
          <div class="mb-6">
            <label class="block text-[11px] font-bold tracking-widest uppercase text-gray-500 mb-1.5">
              Số điện thoại <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-2">
              <div class="relative">
                <select name="phone_code"
                        class="border border-gray-200 rounded-lg py-3 pl-3 pr-8 text-sm text-gray-700 bg-white focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-100 transition-all appearance-none">
                  <option value="+84" selected>🇻🇳 +84</option>
                  <option value="+1">🇺🇸 +1</option>
                  <option value="+81">🇯🇵 +81</option>
                  <option value="+82">🇰🇷 +82</option>
                  <option value="+65">🇸🇬 +65</option>
                  <option value="+61">🇦🇺 +61</option>
                  <option value="+44">🇬🇧 +44</option>
                </select>
                <i class="fas fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
              </div>
              <input type="tel" name="phone" id="phoneInput" placeholder="(012) 345-6789"
                     class="flex-1 border border-gray-200 rounded-lg py-3 px-4 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-100 transition-all"
                     required>

            </div>
          </div>{{-- /phone --}}

        </form>
      </div>{{-- /left card --}}

      {{-- ======================================================
           RIGHT — Stay Summary Sidebar
           ====================================================== --}}
      <div class="w-full lg:w-[340px] flex-shrink-0">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">

          {{-- Header --}}
          <div class="px-6 pt-6 pb-4 flex items-center justify-between border-b border-gray-100">
            <h3 class="font-bold text-gray-900 text-base">Tóm Tắt Đặt Phòng</h3>
            <a href="{{ url()->previous() }}" class="text-blue-600 hover:text-blue-700 text-xs font-semibold transition-colors">
              Sửa
            </a>
          </div>

          {{-- Room list --}}
          <div class="divide-y divide-gray-100">
            @foreach($selectedRooms as $item)
              @php
                $rt      = $item['room_type'];
                $imgUrl  = $rt->images->first()?->image_url;
                if (
                  is_string($imgUrl) &&
                  $imgUrl !== '' &&
                  !str_starts_with($imgUrl, 'http://') &&
                  !str_starts_with($imgUrl, 'https://') &&
                  !str_starts_with($imgUrl, '//')
                ) {
                  $imgUrl = asset('storage/' . ltrim($imgUrl, '/'));
                }
                $imgUrl  = $imgUrl ?: 'https://picsum.photos/seed/room' . $rt->id . '/120/80';
                $priceFmt = number_format($item['line_total'], 0, ',', '.');
              @endphp
              <div class="flex items-start gap-3 px-6 py-4">
                <div class="w-14 h-14 rounded-xl overflow-hidden flex-shrink-0">
                  <img src="{{ $imgUrl }}" alt="{{ $rt->name }}"
                       class="w-full h-full object-cover"
                       onerror="this.src='https://picsum.photos/seed/room{{ $rt->id }}/120/80'">
                </div>
                <div class="flex-1 min-w-0">
                  <div class="text-sm font-semibold text-gray-800 truncate">{{ $rt->name }}</div>
                  <div class="text-[11px] text-gray-400 mt-0.5">
                    {{ $rt->width ?? '–' }} m²
                    @if($item['qty'] > 1)
                      &nbsp;·&nbsp; <span class="font-medium">×{{ $item['qty'] }}</span>
                    @endif
                  </div>
                  {{-- Badge --}}
                  @php
                    $badges = [
                      'STD'  => ['Giá Tốt Nhất', 'text-green-600 bg-green-50'],
                      'DLX'  => ['Bữa Sáng Kèm', 'text-amber-600 bg-amber-50'],
                      'STE'  => ['Bữa Sáng Kèm', 'text-amber-600 bg-amber-50'],
                      'PH'   => ['Luxury Pick',   'text-purple-600 bg-purple-50'],
                    ];
                    [$badgeText, $badgeCls] = $badges[$rt->code] ?? ['Phổ Biến', 'text-blue-600 bg-blue-50'];
                  @endphp
                  <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-bold tracking-wide px-2 py-0.5 rounded-full {{ $badgeCls }}">
                    <i class="fas fa-check text-[8px]"></i> {{ $badgeText }}
                  </span>
                </div>
                <div class="text-sm font-bold text-gray-800 whitespace-nowrap">
                  {{ number_format($item['line_total'] / 1000000, 1) }}M
                </div>
              </div>
            @endforeach
          </div>

          {{-- Dates & Guests --}}
          <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <div class="grid grid-cols-2 gap-4 mb-3">
              <div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-0.5">Nhận Phòng</div>
                <div class="text-sm font-bold text-gray-800">{{ $checkInDate->format('d M') }}</div>
                <div class="text-xs text-gray-400">{{ $checkInDate->translatedFormat('l') }}</div>
              </div>
              <div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-0.5">Trả Phòng</div>
                <div class="text-sm font-bold text-gray-800">{{ $checkOutDate->format('d M') }}</div>
                <div class="text-xs text-gray-400">{{ $checkOutDate->translatedFormat('l') }}</div>
              </div>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] text-gray-500">
              <i class="fas fa-user text-gray-400 text-[10px]"></i>
              {{ $adults }} Người lớn{{ $children > 0 ? ', '.$children.' Trẻ em' : '' }}
              &nbsp;·&nbsp; {{ $nights }} đêm
              &nbsp;·&nbsp;
              @php $totalQty = array_sum(array_column($selectedRooms, 'qty')); @endphp
              {{ $totalQty }} phòng
            </div>
          </div>

          {{-- Pricing —NO tax, NO discount per user request --}}
          <div class="px-6 py-5 border-t border-gray-100">
            <div class="flex items-center justify-between mb-1">
              <span class="text-sm text-gray-500">Phòng ({{ $nights }} đêm)</span>
              <span class="text-sm font-semibold text-gray-800">
                {{ number_format($subtotal, 0, ',', '.') }} ₫
              </span>
            </div>

            <div class="h-px bg-gray-100 my-4"></div>

            <div class="flex items-end justify-between">
              <div>
                <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Tổng Thanh Toán</div>
                <div class="text-[10px] text-gray-400">Thanh toán ngay</div>
              </div>
              <div class="text-2xl font-bold text-gray-900 font-[Playfair_Display,serif]">
                {{ number_format($subtotal, 0, ',', '.') }} ₫
              </div>
            </div>
          </div>

          {{-- CTA --}}
          <div class="px-6 pb-6">
            <button id="checkoutBtn" disabled
                    onclick="if(!this.disabled){ document.getElementById('checkoutForm').submit(); }"
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed text-white font-bold text-sm tracking-wider uppercase py-3.5 sm:py-4 rounded-xl flex items-center justify-center gap-2 transition-colors">
              Tiếp Tục Thanh Toán <i class="fas fa-arrow-right text-xs"></i>
            </button>
            <div class="flex items-center justify-center gap-1.5 mt-3 text-[10px] text-gray-400">
              <i class="fas fa-lock text-gray-300"></i>
              Đặt phòng an toàn &nbsp;·&nbsp; Mã hoá SSL
            </div>
          </div>

        </div>

        {{-- Help --}}
        <div class="mt-4 bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5 flex items-start gap-3">
          <div class="w-9 h-9 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-headset text-blue-500 text-sm"></i>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-800">Cần hỗ trợ?</p>
            <p class="text-xs text-gray-400 mt-0.5">
              Liên hệ đội ngũ 24/7 tại
              <a href="tel:+84281234567" class="text-blue-600 hover:underline font-medium">+84 28 1234 567</a>
            </p>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

@endsection

@push('scripts')
@vite('resources/js/client/checkout.js')
@endpush
