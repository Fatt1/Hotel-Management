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
    @if (session('error') || $errors->any())
      <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        @if (session('error'))
          <div>{{ session('error') }}</div>
        @endif
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <div class="flex gap-7 items-start">

      {{-- ====================================================
           LEFT — Payment Method
           ==================================================== --}}
      <div class="flex-1 min-w-0">

        {{-- Payment Method Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 mb-5">
          <h2 class="text-lg font-bold text-gray-900 mb-1">Phương Thức Thanh Toán</h2>
          <p class="text-xs text-gray-400 mb-6">Tất cả giao dịch đều được mã hoá và bảo mật.</p>

          {{-- Tabs --}}
          <div class="mb-6 grid grid-cols-2 gap-4">

            {{-- MoMo Tab --}}
            <div id="tab-momo" onclick="switchMethod('momo')" class="method-tab active border-2 border-pink-500 bg-pink-50/50 p-4 rounded-xl text-center cursor-pointer transition-all">
              <div class="flex flex-col items-center justify-center">
                <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" alt="MoMo" class="h-10 mb-2 object-contain block">
                <div class="tab-label text-sm text-gray-800 font-bold">Thanh Toán Bằng Ví MoMo</div>
              </div>
            </div>

            {{-- Credit Card Tab --}}
            <div id="tab-cc" onclick="switchMethod('cc')" class="method-tab border-2 border-gray-100 bg-white hover:border-blue-500 p-4 rounded-xl text-center cursor-pointer transition-all">
              <div class="flex flex-col items-center justify-center">
                <div class="h-10 mb-2 flex items-center justify-center">
                  <i class="fas fa-credit-card text-3xl text-blue-600"></i>
                </div>
                <div class="tab-label text-sm text-gray-800 font-bold">Credit Card</div>
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

          {{-- ── Panel: Credit Card ── --}}
          <div id="panel-cc" class="payment-panel mt-2" style="display: none;">
            <!-- Form mock for credit card -->
            <div class="space-y-5">
              <!-- Card Number -->
              <div>
                <label class="block text-xs font-bold text-gray-700 tracking-wider uppercase mb-2">Số Thẻ <span class="text-red-500">*</span></label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="far fa-credit-card text-gray-400"></i>
                  </div>
                  <input type="text" placeholder="0000 0000 0000 0000" class="block w-full pl-10 pr-10 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium text-gray-900 placeholder-gray-400 outline-none transition-all">
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <i class="fas fa-lock text-green-500 text-sm"></i>
                  </div>
                </div>
              </div>

              <!-- Exp & CVV -->
              <div class="grid grid-cols-2 gap-5">
                <div>
                  <label class="block text-xs font-bold text-gray-700 tracking-wider uppercase mb-2">Ngày Hết Hạn <span class="text-red-500">*</span></label>
                  <input type="text" placeholder="MM / YY" class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium text-gray-900 placeholder-gray-400 outline-none transition-all">
                </div>
                <div>
                  <label class="block text-xs font-bold text-gray-700 tracking-wider uppercase mb-2">CVV / CVC <span class="text-red-500">*</span></label>
                  <div class="relative">
                    <input type="text" placeholder="123" class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium text-gray-900 placeholder-gray-400 outline-none transition-all">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-help">
                      <i class="far fa-question-circle text-gray-400"></i>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Cardholder Name -->
              <div>
                <label class="block text-xs font-bold text-gray-700 tracking-wider uppercase mb-2">Tên Chủ Thẻ <span class="text-red-500">*</span></label>
                <input type="text" placeholder="Tên in trên thẻ" class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium text-gray-900 placeholder-gray-400 outline-none transition-all">
              </div>

              <!-- Save card -->
              <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 flex items-start gap-3 mt-2">
                <input type="checkbox" id="saveCard" class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                <div>
                  <label for="saveCard" class="text-sm font-bold text-gray-800 cursor-pointer">Lưu thông tin thẻ cho lần sau</label>
                  <p class="text-xs text-gray-500 mt-1">Thông tin thẻ được lưu trữ an toàn. Chúng tôi không lưu mã CVV.</p>
                </div>
              </div>
            </div>
            
            {{-- Billing Address mock --}}
            <div class="mt-8 border-t border-gray-100 pt-6">
              <h3 class="text-base font-bold text-gray-900 mb-4">Địa Chỉ Hoá Đơn</h3>
              <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="billing" checked class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                  <span class="text-sm text-gray-700 font-medium">Giống thông tin người đặt</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" name="billing" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                  <span class="text-sm text-gray-700 font-medium">Sử dụng địa chỉ khác</span>
                </label>
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
            {{-- Hidden form that stores all booking data --}}
            <form id="confirmForm" class="hidden">
              @foreach($allInputs as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
              @endforeach
            </form>

            <button id="payBtn" disabled
                    onclick="if(!this.disabled){ processPayment(); }"
                    class="w-full bg-[#A50064] hover:bg-[#8A0053] disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed text-white font-bold text-sm tracking-wider uppercase py-4 rounded-xl flex items-center justify-center gap-2 transition-colors relative">
              <i id="payIcon" class="fas fa-wallet text-sm text-white"></i>
              <span id="payLabel">Xác Nhận & Thanh Toán</span>
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
// ── Switch Tabs ──
function switchMethod(method) {
  // Reset all tabs
  document.getElementById('tab-momo').className = 'method-tab border-2 border-gray-100 bg-white hover:border-pink-500 p-4 rounded-xl text-center cursor-pointer transition-all';
  document.getElementById('tab-cc').className = 'method-tab border-2 border-gray-100 bg-white hover:border-blue-500 p-4 rounded-xl text-center cursor-pointer transition-all';
  
  // Set active tab and panel
  if (method === 'momo') {
    document.getElementById('tab-momo').className = 'method-tab active border-2 border-pink-500 bg-pink-50/50 p-4 rounded-xl text-center cursor-pointer transition-all';
    
    // Using display style directly ensures high priority
    document.getElementById('panel-momo').style.display = 'block';
    document.getElementById('panel-cc').style.display = 'none';
  } else {
    document.getElementById('tab-cc').className = 'method-tab active border-2 border-blue-500 bg-blue-50/50 p-4 rounded-xl text-center cursor-pointer transition-all';
    
    // Using display style directly ensures high priority
    document.getElementById('panel-cc').style.display = 'block';
    document.getElementById('panel-momo').style.display = 'none';
  }
}

// ── Terms checkbox validation toggles button ──
function updatePayBtn() {
  var agreed = document.getElementById('agreeTerms').checked;
  document.getElementById('payBtn').disabled = !agreed;
}

// ── Xử lý thanh toán qua API thay vì Form Submit ──
async function processPayment() {
  const btn = document.getElementById('payBtn');
  const label = document.getElementById('payLabel');
  const icon = document.getElementById('payIcon');
  
  // Hiệu ứng Loading
  btn.disabled = true;
  label.innerText = 'Đang Xử Lý...';
  icon.className = 'fas fa-circle-notch fa-spin text-sm text-white';

  try {
    const form = document.getElementById('confirmForm');
    const formData = new FormData(form);
    const bookingDetails = [];

    // Lọc các field kiểu qty_{id} và tạo array cho API
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('qty_') && parseInt(value) > 0) {
            let rtId = key.replace('qty_', '');
            bookingDetails.push({
                room_type_id: parseInt(rtId),
                quantity: parseInt(value)
            });
        }
    }

    const phoneCode = String(formData.get('phone_code') || '+84').trim();
    const phoneValue = String(formData.get('phone') || '').trim().replace(/\s+/g, '');
    const fullPhoneNumber = phoneValue.startsWith('+')
      ? phoneValue
      : `${phoneCode}${phoneValue.replace(/^0+/, '')}`;

    const payload = {
        email: formData.get('email_verify'),
        phone_number: fullPhoneNumber,
        first_name: formData.get('first_name'),
        last_name: formData.get('last_name'),
        country: formData.get('country') || 'VN',
        booking_date: new Date().toISOString().split('T')[0],
        checkin_date: formData.get('check_in'),
        checkout_date: formData.get('check_out'),
        status: 'Đã đặt',
        booking_details: bookingDetails
    };

    // Gọi lên backend - Dùng fetch thuần cho Blade
    const response = await fetch("{{ route('client.booking.confirm') }}", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": "{{ csrf_token() }}"
      },
      body: JSON.stringify(payload)
    });

    const data = await response.json();

    if (response.ok && data.success) {
        // Chuyển luôn sang trang thành công (bùm)
        window.location.href = "{{ route('client.booking.confirmation') }}?booking_id=" + data.booking_id;
    } else {
        alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
        resetBtn();
    }
  } catch (error) {
    console.error('Lỗi khi gọi API:', error);
    alert('Không thể kết nối đến máy chủ, vui lòng thử lại sau.');
    resetBtn();
  }
}

function resetBtn() {
    const btn = document.getElementById('payBtn');
    const label = document.getElementById('payLabel');
    const icon = document.getElementById('payIcon');
    btn.disabled = false;
    label.innerText = 'Xác Nhận & Thanh Toán';
    icon.className = 'fas fa-wallet text-sm text-white';
}
</script>
@endpush
