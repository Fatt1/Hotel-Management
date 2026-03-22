@extends('client.layouts.app')

@section('title', 'Thanh Toán - Urban Luxe Hotel')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endpush

@section('content')

{{-- ============================================================
     HERO — STEP 3 / 3
     ============================================================ --}}
<div class="relative overflow-hidden bg-[#0a0a0a] min-h-[220px] pt-24 pb-14">
  <img src="https://images.unsplash.com/photo-1617038220319-276d3cfab638?w=1920&q=80"
       alt="" aria-hidden="true"
       class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none opacity-25 grayscale-[20%]">
  <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(5,5,5,.6)_0%,rgba(5,5,5,.75)_100%)]"></div>
  <div class="relative z-10 max-w-3xl mx-auto px-8 text-center">
    <div class="flex items-center justify-center gap-2 mb-4">
      <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>
      <span class="text-[11px] font-bold tracking-[0.25em] uppercase text-white/60">Bước 3 / 3</span>
    </div>
    <h1 class="font-['Playfair_Display'] text-4xl font-bold text-white mb-3">Thanh Toán An Toàn</h1>
    <p class="text-stone-300/70 text-sm">Hoàn tất đặt phòng của bạn một cách bảo mật.</p>
  </div>
</div>

{{-- ============================================================
     MAIN — 2 columns
     ============================================================ --}}
<div class="bg-[#f0f2f5] min-h-screen py-10">
  <div class="max-w-6xl mx-auto px-8">
    <div class="flex gap-7 items-start">

      {{-- ====================================================
           LEFT — Payment Method
           ==================================================== --}}
      <div class="flex-1 min-w-0">

        {{-- Payment Method Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 mb-5">
          <h2 class="text-lg font-bold text-gray-900 mb-1">Phương Thức Thanh Toán</h2>
          <p class="text-xs text-gray-400 mb-6">Tất cả giao dịch đều được mã hoá và bảo mật.</p>

          {{-- 1 tab (MoMo Only) --}}
          <div class="mb-6 grid grid-cols-1">

            {{-- MoMo --}}
            <div class="method-tab active border-2 border-pink-500 bg-pink-50/50 p-4 rounded-xl text-center cursor-default">
              <div class="flex flex-col items-center justify-center">
                <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" alt="MoMo" class="h-10 mb-2 object-contain block">
                <div class="tab-label text-sm text-gray-800 font-bold">Thanh Toán Bằng Ví MoMo</div>
              </div>
            </div>

          </div>

          {{-- ── Panel: MoMo ── --}}
          <div id="panel-momo" class="payment-panel active">
            <div class="flex flex-col items-center py-6 px-4 text-center border border-pink-100 bg-white rounded-xl shadow-sm">
              <div class="w-16 h-16 rounded-2xl bg-pink-50 flex items-center justify-center mb-4 p-2 border border-pink-100">
                <i class="fas fa-qrcode text-3xl text-pink-600"></i>
              </div>
              <h3 class="font-bold text-gray-800 mb-2 text-lg">Quét Mã QR MoMo</h3>
              <p class="text-sm text-gray-500 max-w-sm mb-6 leading-relaxed">Sau khi bấm thanh toán, bạn sẽ được chuyển hướng an toàn đến cổng thanh toán MoMo để quét mã QR và hoàn tất quá trình đặt phòng.</p>
              
              <div class="flex items-start gap-3 bg-pink-50 border border-pink-200 rounded-xl px-5 py-4 text-left w-full">
                 <i class="fas fa-circle-info text-pink-600 text-base mt-0.5 flex-shrink-0"></i>
                 <div class="text-[13px] text-gray-700 leading-relaxed">
                   <p class="font-bold text-gray-900 mb-1">Hướng dẫn thanh toán an toàn:</p>
                   <ul class="list-disc pl-4 text-gray-600 space-y-1">
                     <li>Mở ứng dụng <b>MoMo</b> trên điện thoại.</li>
                     <li>Chọn chức năng <b>Quét Mã</b> ở góc phải màn hình.</li>
                     <li>Quét mã QR hiển thị ở trang tiếp theo để thanh toán.</li>
                   </ul>
                 </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      {{-- ====================================================
           RIGHT — Stay Summary (same as checkout, + terms checkbox)
           ==================================================== --}}
      <div class="w-[340px] flex-shrink-0">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">

          {{-- Header --}}
          <div class="px-6 pt-6 pb-4 flex items-center justify-between border-b border-gray-100">
            <h3 class="font-bold text-gray-900 text-base">Tóm Tắt Đặt Phòng</h3>
            <a href="javascript:history.back()" class="text-blue-600 hover:text-blue-700 text-xs font-semibold transition-colors">
              Sửa
            </a>
          </div>

          {{-- Room list --}}
          <div class="divide-y divide-gray-100">
            @foreach($selectedRooms as $item)
              @php
                $rt     = $item['room_type'];
                $imgUrl = $rt->images->first()?->image_url;
                if (
                  is_string($imgUrl) &&
                  $imgUrl !== '' &&
                  !str_starts_with($imgUrl, 'http://') &&
                  !str_starts_with($imgUrl, 'https://') &&
                  !str_starts_with($imgUrl, '//')
                ) {
                  $imgUrl = asset('storage/' . ltrim($imgUrl, '/'));
                }
                $imgUrl = $imgUrl ?: 'https://picsum.photos/seed/room' . $rt->id . '/120/80';
                $badges = [
                  'STD' => ['Giá Tốt Nhất', 'text-green-600 bg-green-50'],
                  'DLX' => ['Bữa Sáng Kèm',  'text-amber-600 bg-amber-50'],
                  'STE' => ['Bữa Sáng Kèm',  'text-amber-600 bg-amber-50'],
                  'PH'  => ['Luxury Pick',    'text-purple-600 bg-purple-50'],
                ];
                [$badgeText, $badgeCls] = $badges[$rt->code] ?? ['Phổ Biến', 'text-blue-600 bg-blue-50'];
              @endphp
              <div class="flex items-start gap-3 px-6 py-4">
                <div class="w-14 h-14 rounded-xl overflow-hidden flex-shrink-0">
                  <img src="{{ $imgUrl }}" alt="{{ $rt->name }}" class="w-full h-full object-cover"
                       onerror="this.src='https://picsum.photos/seed/room{{ $rt->id }}/120/80'">
                </div>
                <div class="flex-1 min-w-0">
                  <div class="text-sm font-semibold text-gray-800 truncate">{{ $rt->name }}</div>
                  <div class="text-[11px] text-gray-400 mt-0.5">
                    {{ $rt->width ?? '–' }} m²
                    @if($item['qty'] > 1)&nbsp;·&nbsp; <span class="font-medium">×{{ $item['qty'] }}</span>@endif
                  </div>
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

          {{-- Pricing --}}
          <div class="px-6 py-5 border-t border-gray-100">
            <div class="flex items-center justify-between mb-1">
              <span class="text-sm text-gray-500">Phòng ({{ $nights }} đêm)</span>
              <span class="text-sm font-semibold text-gray-800">{{ number_format($subtotal, 0, ',', '.') }} ₫</span>
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

          {{-- Terms checkbox --}}
          <div class="px-6 pb-4">
            <label class="flex items-start gap-2.5 cursor-pointer">
              <input type="checkbox" id="agreeTerms"
                     class="mt-0.5 w-4 h-4 rounded border-gray-300 accent-blue-600 flex-shrink-0"
                     onchange="updatePayBtn()">
              <span class="text-xs text-gray-500 leading-relaxed">
                Tôi đồng ý với
                <a href="#" class="text-blue-600 hover:underline font-medium">Điều khoản &amp; Điều kiện</a>
                và
                <a href="#" class="text-blue-600 hover:underline font-medium">Chính sách Bảo mật</a>.
              </span>
            </label>
          </div>

          {{-- CTA --}}
          <div class="px-6 pb-6">
            {{-- Hidden form that submits all booking data to confirm route --}}
            <form id="confirmForm" method="POST" action="{{ route('client.booking.confirm') }}" class="hidden">
              @csrf
              {{-- Pass all booking inputs through --}}
              @foreach($allInputs as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
              @endforeach
              <input type="hidden" id="paymentMethodInput" name="payment_method" value="momo">
            </form>

            <button id="payBtn" disabled
                    onclick="if(!this.disabled){ document.getElementById('confirmForm').submit(); }"
                    class="w-full bg-[#A50064] hover:bg-[#8A0053] disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed text-white font-bold text-sm tracking-wider uppercase py-4 rounded-xl flex items-center justify-center gap-2 transition-colors">
              <i id="payIcon" class="fas fa-wallet text-sm text-white"></i>
              <span id="payLabel">Thanh Toán Bằng MoMo</span>
            </button>
            <div class="flex items-center justify-center gap-1.5 mt-3 text-[10px] text-gray-400">
              <i class="fas fa-lock text-gray-300"></i>
              Đặt phòng an toàn &nbsp;·&nbsp; Mã hoá SSL
            </div>
          </div>


        </div>

        {{-- Safe Checkout card --}}
        <div class="mt-4 bg-blue-50 border border-blue-100 rounded-2xl px-6 py-4 flex items-start gap-3">
          <i class="fas fa-shield-halved text-blue-500 text-lg mt-0.5"></i>
          <div>
            <p class="text-sm font-bold text-blue-800">Đảm Bảo Thanh Toán An Toàn</p>
            <p class="text-xs text-blue-600 mt-0.5 leading-relaxed">
              Thông tin thanh toán của bạn được mã hoá và xử lý bảo mật. Chúng tôi không lưu trữ thông tin thẻ đầy đủ.
            </p>
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
<script>
// ── Terms checkbox validation toggles button ──
function updatePayBtn() {
  var agreed = document.getElementById('agreeTerms').checked;
  document.getElementById('payBtn').disabled = !agreed;
}
</script>
@endpush
