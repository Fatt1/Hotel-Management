@extends('client.layouts.app')

@section('title', 'Đặt Phòng Thành Công - Urban Luxe Hotel')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endpush

@section('content')

{{-- ============================================================
     HERO — Booking Confirmed
     ============================================================ --}}
<div class="relative overflow-hidden bg-[#0a0a0a] min-h-[280px] pt-[72px] pb-16">
  <img src="https://images.unsplash.com/photo-1617038220319-276d3cfab638?w=1920&q=80"
       alt="" aria-hidden="true"
       class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none opacity-30 grayscale-[15%]">
  <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(5,5,5,.55)_0%,rgba(5,5,5,.7)_100%)]"></div>

  <div class="relative z-10 max-w-2xl mx-auto px-8 text-center">
    {{-- Green checkmark circle --}}
    <div class="booking-anim-pop mx-auto mb-5 w-16 h-16 rounded-full bg-green-500 flex items-center justify-center shadow-lg shadow-green-500/30">
      <i class="fas fa-check text-white text-2xl"></i>
    </div>

    <div class="booking-anim-fadeup">
      <h1 class="font-['Playfair_Display'] text-4xl md:text-5xl font-bold text-white mb-3">
        Đặt Phòng Thành Công!
      </h1>
      <p class="text-stone-300/75 text-sm mb-6">
        Chào mừng đến Urban Luxe. Đặt phòng của bạn đã được xác nhận thành công.
      </p>

      {{-- Booking ID pill --}}
      <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur border border-white/20 rounded-full px-5 py-2 text-sm text-white/80 tracking-widest font-mono">
        <span class="text-white/40 text-[10px] font-bold uppercase tracking-[0.2em]">Mã Đặt Phòng</span>
        <span class="font-bold text-white text-base">#{{ $bookingRef }}</span>
        <button onclick="copyRef('{{ $bookingRef }}')" title="Sao chép" class="ml-1 text-white/50 hover:text-white transition-colors">
          <i id="copyIcon" class="far fa-copy text-sm"></i>
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ============================================================
     MAIN CONTENT — white card
     ============================================================ --}}
<div class="bg-[#f0f2f5] py-10 min-h-screen">
  <div class="max-w-3xl mx-auto px-8 booking-anim-fadeup2">

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

      {{-- Header row --}}
      <div class="flex items-start justify-between px-8 pt-7 pb-5 border-b border-gray-100">
        <div>
          <h2 class="text-xl font-bold text-gray-900">Tóm Tắt Đặt Phòng</h2>
          @if(!empty($bookingData['guest_email']))
            <p class="text-xs text-gray-400 mt-1">
              Xác nhận đã gửi tới
              <span class="text-gray-600 font-medium">{{ $bookingData['guest_email'] }}</span>
            </p>
          @endif
        </div>
        <div class="flex items-center gap-2">
          <button onclick="window.print()"
                  class="flex items-center gap-1.5 border border-gray-200 text-gray-600 hover:border-gray-400 hover:text-gray-800 text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-print text-[11px]"></i> In
          </button>
          <button class="flex items-center gap-1.5 border border-gray-200 text-gray-600 hover:border-gray-400 hover:text-gray-800 text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
            <i class="far fa-envelope text-[11px]"></i> Email
          </button>
        </div>
      </div>

      {{-- 4-column summary row --}}
      <div class="grid grid-cols-4 gap-0 divide-x divide-gray-100 px-0 border-b border-gray-100 bg-[#fafafa]">
        <div class="px-7 py-5">
          <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-1.5">Nhận Phòng</div>
          <div class="text-base font-bold text-gray-900">{{ $checkInDate->format('d/m/Y') }}</div>
          <div class="text-xs text-gray-400 mt-0.5">Sau 14:00 PM</div>
        </div>
        <div class="px-7 py-5">
          <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-1.5">Trả Phòng</div>
          <div class="text-base font-bold text-gray-900">{{ $checkOutDate->format('d/m/Y') }}</div>
          <div class="text-xs text-gray-400 mt-0.5">Trước 12:00 PM</div>
        </div>
        <div class="px-7 py-5">
          <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-1.5">Số Khách</div>
          <div class="text-base font-bold text-gray-900">
            {{ $bookingData['adults'] }} Người lớn
            @if($bookingData['children'] > 0)
              &amp; {{ $bookingData['children'] }} Trẻ em
            @endif
          </div>
          <div class="text-xs text-gray-400 mt-0.5">{{ $bookingData['nights'] }} đêm</div>
        </div>
        <div class="px-7 py-5">
          <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-1.5">Trạng Thái</div>
          <div class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-200 text-xs font-bold px-2.5 py-1 rounded-full">
            <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
            Đã Xác Nhận
          </div>
          <div class="text-xs text-blue-600 font-semibold mt-1">Đã Thanh Toán</div>
        </div>
      </div>

      {{-- Room Details --}}
      <div class="px-8 py-6 border-b border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-bold text-gray-900">Chi Tiết Phòng</h3>
          <a href="#" class="text-blue-600 hover:text-blue-700 text-xs font-semibold transition-colors">Xem Chính Sách</a>
        </div>

        {{-- Room row --}}
        @php $totalQty = array_sum(array_column($bookingData['rooms'], 'qty')); @endphp
        @foreach($bookingData['rooms'] as $idx => $room)
          @php
            $roomImageUrl = $room['image_url'] ?? '';
            if (
              is_string($roomImageUrl) &&
              $roomImageUrl !== '' &&
              !str_starts_with($roomImageUrl, 'http://') &&
              !str_starts_with($roomImageUrl, 'https://') &&
              !str_starts_with($roomImageUrl, '//')
            ) {
              $roomImageUrl = asset('storage/' . ltrim($roomImageUrl, '/'));
            }
          @endphp
          <div class="flex items-center gap-5 @if(!$loop->last) mb-4 pb-4 border-b border-gray-100 @endif">
            {{-- Room image --}}
            <div class="w-20 h-20 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
              @if(!empty($roomImageUrl))
                <img src="{{ $roomImageUrl }}" alt="{{ $room['name'] }}" class="w-full h-full object-cover">
              @else
                <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                  <i class="fas fa-bed text-gray-400 text-xl"></i>
                </div>
              @endif
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between">
                <div>
                  <div class="font-bold text-gray-900 text-sm">
                    @if($room['qty'] > 1) {{ $room['qty'] }}x @endif{{ $room['name'] }}
                  </div>
                  <div class="text-xs text-gray-400 mt-0.5">
                    Khách chính:
                    <span class="font-medium text-gray-600">{{ $bookingData['guest_name'] ?: 'Khách lưu trú' }}</span>
                  </div>
                  {{-- Room type badge row --}}
                  <div class="flex items-center gap-2 mt-2 flex-wrap">
                    <span class="inline-flex items-center gap-1 bg-gray-100 rounded-full px-3 py-1 text-[11px] font-medium text-gray-600">
                      <i class="fas fa-bed text-gray-400 text-[9px]"></i>
                      {{ $room['name'] }}
                    </span>
                    @if($room['width'] > 0)
                      <span class="inline-flex items-center gap-1 bg-gray-100 rounded-full px-3 py-1 text-[11px] font-medium text-gray-600">
                        <i class="fas fa-vector-square text-gray-400 text-[9px]"></i>
                        {{ $room['width'] }} m²
                      </span>
                    @endif
                  </div>
                </div>
                <div class="text-right flex-shrink-0 ml-4">
                  <div class="font-bold text-gray-900 text-base">
                    {{ number_format($room['line_total'], 0, ',', '.') }} ₫
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Total + Actions --}}
      <div class="px-8 py-6">
        {{-- Total --}}
        <div class="flex items-center justify-between mb-7">
          <div class="text-sm font-bold text-gray-700">Tổng Thanh Toán</div>
          <div class="text-2xl font-bold text-gray-900 font-[Playfair_Display,serif]">
            {{ number_format($bookingData['subtotal'], 0, ',', '.') }} ₫
          </div>
        </div>

        {{-- Action buttons --}}
        <div class="flex items-center justify-center gap-4">
          <button onclick="window.print()"
                  class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm tracking-wider uppercase py-3.5 px-7 rounded-xl transition-colors">
            <i class="fas fa-download text-sm"></i>
            Tải Biên Lai
          </button>
          <a href="{{ route('client.rooms.index') }}"
             class="flex items-center gap-2 border-2 border-gray-200 hover:border-gray-400 text-gray-700 hover:text-gray-900 font-bold text-sm tracking-wider uppercase py-3.5 px-7 rounded-xl transition-colors">
            <i class="far fa-calendar text-sm"></i>
            Quản Lý Đặt Phòng
          </a>
        </div>

        {{-- Security note --}}
        <div class="flex items-center justify-center gap-2 mt-5 text-[11px] text-gray-400">
          <i class="fas fa-circle-check text-green-400 text-sm"></i>
          Giao dịch được xử lý bảo mật vào {{ $bookingData['confirmed_at'] }}
        </div>
      </div>

    </div>

    {{-- What's next card --}}
    <div class="mt-6 grid grid-cols-3 gap-4">
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5 text-center">
        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center mx-auto mb-3">
          <i class="fas fa-envelope text-blue-500 text-sm"></i>
        </div>
        <p class="text-sm font-bold text-gray-800 mb-1">Email Xác Nhận</p>
        <p class="text-xs text-gray-400">Kiểm tra hộp thư đến để xem chi tiết đặt phòng.</p>
      </div>
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5 text-center">
        <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center mx-auto mb-3">
          <i class="fas fa-moon text-amber-500 text-sm"></i>
        </div>
        <p class="text-sm font-bold text-gray-800 mb-1">Miễn Phí Huỷ</p>
        <p class="text-xs text-gray-400">Huỷ miễn phí trước 48 giờ nhận phòng.</p>
      </div>
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5 text-center">
        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-3">
          <i class="fas fa-headset text-green-500 text-sm"></i>
        </div>
        <p class="text-sm font-bold text-gray-800 mb-1">Hỗ Trợ 24/7</p>
        <p class="text-xs text-gray-400">
          <a href="tel:+84281234567" class="text-blue-600 hover:underline">+84 28 1234 567</a>
        </p>
      </div>
    </div>

  </div>
</div>

@endsection

@push('scripts')
<script>
function copyRef(ref) {
  navigator.clipboard.writeText(ref).then(function() {
    var icon = document.getElementById('copyIcon');
    if (icon) {
      icon.className = 'fas fa-check text-green-400 text-sm';
      setTimeout(function(){ icon.className = 'far fa-copy text-sm'; }, 2000);
    }
  });
}
</script>
@endpush
