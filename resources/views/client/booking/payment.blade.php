@extends('client.layouts.app')

@section('title', 'Thanh Toán - Urban Luxe Hotel')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
  .method-tab { cursor:pointer; border:2px solid #e5e7eb; border-radius:12px; padding:18px 12px; text-align:center; transition:all .2s; }
  .method-tab:hover { border-color:#93c5fd; background:#f0f7ff; }
  .method-tab.active { border-color:#3b82f6; background:#eff6ff; }
  .method-tab.active .tab-label { color:#2563eb; font-weight:700; }
  .method-tab .tab-dot { display:none; }
  .method-tab.active .tab-dot { display:block; }
  .payment-panel { display:none; }
  .payment-panel.active { display:block; }
</style>
@endpush

@section('content')

{{-- ============================================================
     HERO — STEP 3 / 3
     ============================================================ --}}
<div class="relative overflow-hidden" style="background:#0a0a0a; min-height:220px; padding-top:96px; padding-bottom:56px;">
  <img src="https://images.unsplash.com/photo-1617038220319-276d3cfab638?w=1920&q=80"
       alt="" aria-hidden="true"
       class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none"
       style="opacity:.25; filter:grayscale(20%);">
  <div class="absolute inset-0" style="background:linear-gradient(180deg,rgba(5,5,5,.6) 0%,rgba(5,5,5,.75) 100%);"></div>
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

          {{-- 3 tabs --}}
          <div class="grid grid-cols-3 gap-3 mb-6" id="methodTabs">

            {{-- Credit Card --}}
            <div class="method-tab active" data-method="credit" onclick="switchMethod('credit')">
              <div class="relative">
                <div class="tab-dot absolute top-0 right-0 w-2.5 h-2.5 rounded-full bg-blue-600"></div>
              </div>
              <i class="far fa-credit-card text-2xl text-gray-400 mb-2 block"></i>
              <div class="tab-label text-sm text-gray-600">Thẻ Tín Dụng</div>
            </div>

            {{-- PayPal --}}
            <div class="method-tab" data-method="paypal" onclick="switchMethod('paypal')">
              <div class="relative">
                <div class="tab-dot absolute top-0 right-0 w-2.5 h-2.5 rounded-full bg-blue-600"></div>
              </div>
              <i class="fab fa-paypal text-2xl text-gray-400 mb-2 block"></i>
              <div class="tab-label text-sm text-gray-600">PayPal</div>
            </div>

            {{-- Bank Transfer --}}
            <div class="method-tab" data-method="bank" onclick="switchMethod('bank')">
              <div class="relative">
                <div class="tab-dot absolute top-0 right-0 w-2.5 h-2.5 rounded-full bg-blue-600"></div>
              </div>
              <i class="fas fa-landmark text-2xl text-gray-400 mb-2 block"></i>
              <div class="tab-label text-sm text-gray-600">Chuyển Khoản</div>
            </div>

          </div>

          {{-- ── Panel: Credit Card ── --}}
          <div id="panel-credit" class="payment-panel active">
            {{-- Card number --}}
            <div class="mb-4">
              <div class="flex items-center justify-between mb-1.5">
                <label class="text-[11px] font-bold tracking-widest uppercase text-gray-500">
                  Số thẻ <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-1.5">
                  <span class="text-[10px] font-bold px-2 py-0.5 bg-gray-100 text-gray-500 rounded tracking-widest">VISA</span>
                  <span class="text-[10px] font-bold px-2 py-0.5 bg-gray-100 text-gray-500 rounded tracking-widest">MC</span>
                  <span class="text-[10px] font-bold px-2 py-0.5 bg-gray-100 text-gray-500 rounded tracking-widest">AMEX</span>
                </div>
              </div>
              <div class="relative">
                <i class="far fa-credit-card absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="cardNumber" placeholder="0000 0000 0000 0000" maxlength="19"
                       class="w-full border border-gray-200 rounded-lg py-3 pl-10 pr-10 text-sm font-mono text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-100 transition-all"
                       oninput="formatCardNumber(this); updatePayBtn()">
                <i class="fas fa-lock absolute right-3.5 top-1/2 -translate-y-1/2 text-green-400 text-xs"></i>
              </div>
            </div>

            {{-- Expiry + CVV --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-[11px] font-bold tracking-widest uppercase text-gray-500 mb-1.5">
                  Ngày hết hạn <span class="text-red-500">*</span>
                </label>
                <input type="text" id="expiryInput" placeholder="MM / YY" maxlength="7"
                       class="w-full border border-gray-200 rounded-lg py-3 px-4 text-sm font-mono text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-100 transition-all"
                       oninput="formatExpiry(this); updatePayBtn()">
              </div>
              <div>
                <label class="block text-[11px] font-bold tracking-widest uppercase text-gray-500 mb-1.5 flex items-center gap-1">
                  CVV / CVC <span class="text-red-500">*</span>
                  <i class="fas fa-circle-info text-gray-300 text-[10px] ml-1 cursor-help" title="3 số ở mặt sau thẻ"></i>
                </label>
                <input type="text" id="cvvInput" placeholder="123" maxlength="4"
                       class="w-full border border-gray-200 rounded-lg py-3 px-4 text-sm font-mono text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-100 transition-all"
                       oninput="updatePayBtn()">
              </div>
            </div>

            {{-- Cardholder name --}}
            <div class="mb-5">
              <label class="block text-[11px] font-bold tracking-widest uppercase text-gray-500 mb-1.5">
                Tên chủ thẻ <span class="text-red-500">*</span>
              </label>
              <input type="text" id="holderInput" placeholder="Tên như trên thẻ"
                     class="w-full border border-gray-200 rounded-lg py-3 px-4 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-100 transition-all"
                     oninput="updatePayBtn()">
            </div>


            {{-- Save card --}}
            <label class="flex items-start gap-3 cursor-pointer">
              <div class="flex-shrink-0 mt-0.5">
                <input type="checkbox" class="w-4 h-4 rounded border-gray-300 accent-blue-600">
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-700">Lưu thẻ cho lần đặt phòng sau</p>
                <p class="text-xs text-gray-400">Thông tin thẻ được bảo mật. Chúng tôi không lưu CVV.</p>
              </div>
            </label>
          </div>

          {{-- ── Panel: PayPal ── --}}
          <div id="panel-paypal" class="payment-panel">
            <div class="flex flex-col items-center py-8 text-center">
              <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                <i class="fab fa-paypal text-3xl text-blue-600"></i>
              </div>
              <h3 class="font-bold text-gray-800 mb-2">Thanh toán qua PayPal</h3>
              <p class="text-sm text-gray-400 max-w-xs">Bạn sẽ được chuyển đến trang PayPal để hoàn tất thanh toán một cách an toàn.</p>
            </div>
          </div>

          {{-- ── Panel: Bank Transfer ── --}}
          <div id="panel-bank" class="payment-panel">
            @php $bookingRef = 'UL-' . strtoupper(substr(md5(now()->timestamp), 0, 6)); @endphp

            {{-- 2-column: bank info + QR --}}
            <div class="flex gap-4 mb-4">

              {{-- Bank info fields --}}
              <div class="flex-1 space-y-3">

                <div>
                  <label class="block text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-1">Tên Ngân Hàng</label>
                  <div class="flex items-center gap-2.5 border border-gray-200 rounded-lg px-3 py-2.5 bg-gray-50">
                    <i class="fas fa-landmark text-gray-400 text-sm"></i>
                    <span class="text-sm font-semibold text-gray-800">Techcombank</span>
                  </div>
                </div>

                <div>
                  <label class="block text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-1">Chủ Tài Khoản</label>
                  <div class="flex items-center gap-2.5 border border-gray-200 rounded-lg px-3 py-2.5 bg-gray-50">
                    <i class="fas fa-user text-gray-400 text-sm"></i>
                    <span class="text-sm font-semibold text-gray-800">URBAN LUXE HOTELS JSC</span>
                  </div>
                </div>

                <div>
                  <label class="block text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-1">Số Tài Khoản</label>
                  <div class="flex items-center justify-between border border-gray-200 rounded-lg px-3 py-2.5 bg-gray-50">
                    <div class="flex items-center gap-2.5">
                      <i class="fas fa-hashtag text-gray-400 text-sm"></i>
                      <span class="text-sm font-mono font-semibold text-gray-800">1903 4829 1200 18</span>
                    </div>
                    <button type="button" onclick="copyAccNum()" title="Sao chép" class="text-gray-400 hover:text-blue-500 transition-colors">
                      <i class="far fa-copy text-sm"></i>
                    </button>
                  </div>
                </div>

                <div>
                  <label class="block text-[10px] font-bold tracking-widest uppercase text-gray-400 mb-1">Chi Nhánh</label>
                  <div class="flex items-center gap-2.5 border border-gray-200 rounded-lg px-3 py-2.5 bg-gray-50">
                    <i class="fas fa-location-dot text-gray-400 text-sm"></i>
                    <span class="text-sm text-gray-700">Hội sở — Hồ Chí Minh</span>
                  </div>
                </div>

              </div>

              {{-- QR Code --}}
              <div class="flex-shrink-0 w-36 flex flex-col items-center">
                <div class="w-full bg-gray-50 border border-gray-200 rounded-xl overflow-hidden p-2">
                  <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=00020101021238520010A000000727012700069704160113190348291200180208QRIBFTTA5303704540{{ number_format($subtotal, 0, '', '') }}5802VN5915URBAN+LUXE+HOTEL6008HOCHIMINH62180814{{ $bookingRef }}6304"
                       alt="QR Chuyển Khoản" class="w-full rounded-lg">
                </div>
                <p class="text-[10px] text-gray-400 text-center mt-2 leading-tight">Quét bằng app ngân hàng<br>hoặc ví điện tử</p>
              </div>

            </div>

            {{-- Important instruction --}}
            <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
              <i class="fas fa-circle-info text-blue-500 text-sm mt-0.5 flex-shrink-0"></i>
              <p class="text-xs text-blue-700 leading-relaxed">
                <span class="font-bold">Hướng dẫn quan trọng:</span>
                Vui lòng ghi rõ mã đặt phòng
                <span class="font-bold text-blue-800 bg-blue-100 px-1.5 py-0.5 rounded font-mono">#{{ $bookingRef }}</span>
                trong nội dung chuyển khoản để chúng tôi xử lý nhanh hơn.
              </p>
            </div>
          </div>
        </div>

        {{-- Billing Address Card — only visible for credit card tab --}}
        <div id="billingCard" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
          <h2 class="text-base font-bold text-gray-900 mb-5">Địa Chỉ Thanh Toán</h2>
          <div class="flex items-center gap-6">
            <label class="flex items-center gap-2.5 cursor-pointer">
              <input type="radio" name="billing_addr" value="same" checked
                     class="w-4 h-4 accent-blue-600">
              <span class="text-sm font-semibold text-gray-700">Giống thông tin khách</span>
            </label>
            <label class="flex items-center gap-2.5 cursor-pointer">
              <input type="radio" name="billing_addr" value="different"
                     class="w-4 h-4 accent-blue-600">
              <span class="text-sm text-gray-600">Địa chỉ khác</span>
            </label>
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
                $imgUrl = $rt->images->first()?->image_url
                          ?? 'https://picsum.photos/seed/room' . $rt->id . '/120/80';
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
              <div class="text-2xl font-bold text-gray-900" style="font-family:'Playfair Display',serif;">
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
            <form id="confirmForm" method="POST" action="{{ route('client.booking.confirm') }}" style="display:none;">
              @csrf
              {{-- Pass all booking inputs through --}}
              @foreach($allInputs as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
              @endforeach
              <input type="hidden" id="paymentMethodInput" name="payment_method" value="credit">
            </form>

            <button id="payBtn" disabled
                    onclick="if(!this.disabled){ document.getElementById('confirmForm').submit(); }"
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed text-white font-bold text-sm tracking-wider uppercase py-4 rounded-xl flex items-center justify-center gap-2 transition-colors">
              <i id="payIcon" class="far fa-credit-card text-sm"></i>
              <span id="payLabel">Hoàn Tất Thanh Toán</span>
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
// ── Active payment method ──
var activeMethod = 'credit';

// ── Payment method switcher ──
var methodLabels = {
  credit: { label: 'Hoàn Tất Thanh Toán', icon: 'far fa-credit-card' },
  paypal: { label: 'Thanh Toán Với PayPal', icon: 'fab fa-paypal' },
  bank:   { label: 'Xác Nhận Chuyển Khoản', icon: 'fas fa-landmark' },
};

function switchMethod(method) {
  activeMethod = method;
  // tabs
  document.querySelectorAll('.method-tab').forEach(function(tab) {
    tab.classList.toggle('active', tab.dataset.method === method);
  });
  // panels
  document.querySelectorAll('.payment-panel').forEach(function(p) {
    p.classList.toggle('active', p.id === 'panel-' + method);
  });
  // billing address card: only show for credit card
  var billingCard = document.getElementById('billingCard');
  if (billingCard) billingCard.style.display = (method === 'credit') ? '' : 'none';
  // sync hidden payment method input
  var pmInput = document.getElementById('paymentMethodInput');
  if (pmInput) pmInput.value = method;
  // update CTA label/icon
  var m = methodLabels[method];
  document.getElementById('payLabel').textContent = m.label;
  document.getElementById('payIcon').className = m.icon + ' text-sm';
  // re-evaluate button state
  updatePayBtn();
}

// ── Terms checkbox + card field validation toggles button ──
function updatePayBtn() {
  var agreed = document.getElementById('agreeTerms').checked;
  var canPay = false;
  if (activeMethod === 'credit') {
    var cardNum  = (document.getElementById('cardNumber').value.replace(/\s/g,'').length >= 16);
    var expiry   = document.getElementById('expiryInput').value.trim().length >= 4;
    var cvv      = document.getElementById('cvvInput').value.trim().length >= 3;
    var holder   = document.getElementById('holderInput').value.trim().length > 0;
    canPay = agreed && cardNum && expiry && cvv && holder;
  } else {
    // PayPal and Bank Transfer — only need terms
    canPay = agreed;
  }
  document.getElementById('payBtn').disabled = !canPay;
}

// ── Card number auto-format ──
function formatCardNumber(el) {
  var v = el.value.replace(/\D/g, '').substring(0, 16);
  el.value = v.replace(/(.{4})/g, '$1 ').trim();
}

// ── Expiry auto-format ──
function formatExpiry(el) {
  var v = el.value.replace(/\D/g, '').substring(0, 4);
  if (v.length >= 3) v = v.substring(0, 2) + ' / ' + v.substring(2);
  el.value = v;
}

// ── Copy account number ──
function copyAccNum() {
  navigator.clipboard.writeText('1903482912001 8').then(function() {
    var icon = document.querySelector('[onclick="copyAccNum()"] i');
    if (icon) { icon.className = 'fas fa-check text-green-500 text-sm'; setTimeout(function(){ icon.className = 'far fa-copy text-sm'; }, 1500); }
  });
}
</script>
@endpush
