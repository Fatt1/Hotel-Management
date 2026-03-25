@extends('client.layouts.app')

@section('title', 'Lịch Đặt Phòng — Urban Luxe')

@push('styles')
<style>
  .profile-hero {
    background: linear-gradient(180deg, rgba(10,10,10,0) 0%, rgba(10,10,10,0.72) 100%),
                url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1600&q=80') center/cover no-repeat;
  }
  .sidebar-link.active {
    background: linear-gradient(135deg, #d4af37 0%, #c9a227 100%);
    color: #0a0a0a;
    font-weight: 600;
  }
  .booking-card {
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.03);
    border-radius: 14px;
  }
  .field-input {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 10px;
    color: #e8e0d0;
    padding: .55rem .75rem;
    width: 100%;
    font-size: .85rem;
  }
  .field-input:focus {
    outline: none;
    border-color: rgba(212,175,55,.6);
  }
  .btn-primary {
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #d4af37 0%, #b8952a 100%);
    color: #0a0a0a;
    padding: .58rem .9rem;
    font-size: .8rem;
    font-weight: 600;
    cursor: pointer;
  }
  .btn-secondary {
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 10px;
    background: rgba(255,255,255,.02);
    color: #e8e0d0;
    padding: .58rem .9rem;
    font-size: .8rem;
    font-weight: 600;
    cursor: pointer;
  }
  .btn-danger {
    border: none;
    border-radius: 10px;
    background: rgba(255,107,107,.15);
    color: #ff8a8a;
    padding: .58rem .9rem;
    font-size: .8rem;
    font-weight: 600;
    cursor: pointer;
  }
</style>
@endpush

@section('content')
<section class="profile-hero relative h-52 pt-16 flex flex-col items-center justify-center text-center">
  <p class="text-[0.65rem] font-semibold uppercase tracking-[0.22em] text-[#d4af37] mb-2">● THÀNH VIÊN</p>
  <h1 class="font-['Playfair_Display'] text-3xl sm:text-4xl font-bold text-[#e8e0d0]">Lịch Đặt Phòng</h1>
  <p class="mt-2 text-[0.8rem] text-[#9a9080]">Xem lịch sử đặt phòng và quản lý kỳ nghỉ của bạn.</p>
</section>

@if(session('success'))
  <div class="mx-auto mt-2 max-w-5xl px-4 sm:px-6">
    <div class="rounded-xl border border-emerald-500/25 bg-emerald-900/25 px-4 py-3 text-sm text-emerald-300">
      {{ session('success') }}
    </div>
  </div>
@endif

@if(session('error'))
  <div class="mx-auto mt-2 max-w-5xl px-4 sm:px-6">
    <div class="rounded-xl border border-red-500/25 bg-red-900/25 px-4 py-3 text-sm text-red-300">
      {{ session('error') }}
    </div>
  </div>
@endif

@if($errors->any())
  <div class="mx-auto mt-2 max-w-5xl px-4 sm:px-6">
    <div class="rounded-xl border border-red-500/25 bg-red-900/25 px-4 py-3 text-sm text-red-300">
      <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  </div>
@endif

<section class="mx-auto max-w-4xl px-4 sm:px-6 py-10">
  <div class="flex flex-col sm:flex-row gap-6">
    <aside class="w-full sm:w-56 flex-shrink-0">
      <div class="rounded-2xl border border-white/8 bg-white/3 p-3 flex flex-col gap-1">
        <a href="{{ route('client.profile') }}"
           class="sidebar-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm text-[#9a9080] no-underline transition-all hover:bg-white/5 hover:text-[#e8e0d0]">
          <span class="text-base">👤</span>
          <span>Hồ Sơ</span>
        </a>
        <a href="{{ route('client.bookings.index') }}"
           class="sidebar-link active flex items-center gap-3 rounded-xl px-4 py-3 text-sm no-underline transition-all">
          <span class="text-base">🎟</span>
          <span>Lịch Đặt Phòng</span>
        </a>
      </div>
    </aside>

    <div class="flex-1">
      <div class="rounded-2xl border border-white/8 bg-white/3 p-6 sm:p-8 space-y-8">
        <div class="mb-1 flex items-center justify-between">
          <h2 class="font-['Playfair_Display'] text-xl font-semibold text-[#e8e0d0]">Lịch Đặt Phòng Của Tôi</h2>
          <span class="text-[0.72rem] text-[#6b6050]">Xem chi tiết và quản lý lịch đặt phòng</span>
        </div>

        <div>
        <div class="mb-3 flex items-center justify-between">
          <h2 class="font-['Playfair_Display'] text-2xl font-semibold text-[#e8e0d0]">Sắp Tới</h2>
        </div>

        @forelse($upcomingBookings as $booking)
          @php
            $checkin = $booking->checkin_date ?? optional($booking->bookingDetails->sortBy('checkin_date')->first())->checkin_date;
            $checkout = $booking->checkout_date ?? optional($booking->bookingDetails->sortByDesc('checkout_date')->first())->checkout_date;
            $reservationCode = 'RES-' . str_pad((string) $booking->id, 7, '0', STR_PAD_LEFT);
          @endphp
          <article class="booking-card p-4 sm:p-5 mb-4" id="booking-{{ $booking->id }}">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
              <div>
                <p class="text-xs uppercase tracking-[0.16em] text-[#9a9080]">Mã đặt phòng</p>
                <h3 class="text-lg font-semibold text-[#e8e0d0]">#{{ $reservationCode }}</h3>
                <p class="text-sm text-[#9a9080]">Ngày đặt: {{ $booking->booking_date?->format('d/m/Y H:i') ?? '-' }}</p>
              </div>
              <div class="text-left sm:text-right">
                <p class="text-[0.7rem] uppercase tracking-[0.16em] text-[#9a9080]">Tổng tiền</p>
                <p class="text-xl font-bold text-white">{{ number_format((float) $booking->final_amount, 0, ',', '.') }}đ</p>
                <p class="mt-1 text-xs text-[#9a9080]">Trạng thái: {{ $booking->status }}</p>
              </div>
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-[#d5ccbc]">
              <p>Check-in: <span class="text-white">{{ $checkin?->format('d/m/Y') ?? '-' }}</span></p>
              <p>Check-out: <span class="text-white">{{ $checkout?->format('d/m/Y') ?? '-' }}</span></p>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
              <button
                class="btn-primary"
                type="button"
                onclick="loadBookingDetails({{ $booking->id }}, '{{ route('client.bookings.details', $booking->id) }}', 'booking-details-{{ $booking->id }}')"
              >
                Xem chi tiết
              </button>
              @if($booking->status === 'Đã đặt')
                <button class="btn-secondary" type="button" onclick="toggleBookingDetails('manage-booking-{{ $booking->id }}')">Quản lý booking</button>
              @endif
            </div>

            <div id="booking-details-{{ $booking->id }}" class="hidden mt-4 rounded-xl border border-white/8 bg-black/20 p-4">
              <div class="text-[#9a9080]">Nhấn "Xem chi tiết" để tải dữ liệu.</div>
            </div>

            @if($booking->status === 'Đã đặt')
              <div id="manage-booking-{{ $booking->id }}" class="hidden mt-4 rounded-xl border border-white/8 bg-black/20 p-4">
                <h4 class="text-sm font-semibold text-[#e8e0d0] mb-3">Quản lý booking</h4>
                <form method="POST" action="{{ route('client.bookings.update-dates', $booking->id) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                  @csrf
                  @method('PUT')
                  <div>
                    <label class="block mb-1 text-xs uppercase tracking-[0.12em] text-[#9a9080]">Check-in</label>
                    <input class="field-input" type="date" name="checkin_date" value="{{ $checkin?->format('Y-m-d') }}" required>
                  </div>
                  <div>
                    <label class="block mb-1 text-xs uppercase tracking-[0.12em] text-[#9a9080]">Check-out</label>
                    <input class="field-input" type="date" name="checkout_date" value="{{ $checkout?->format('Y-m-d') }}" required>
                  </div>
                  <button class="btn-secondary" type="submit">Cập nhật thời gian</button>
                </form>

                <form method="POST" action="{{ route('client.bookings.cancel', $booking->id) }}" class="mt-3"
                      onsubmit="return confirm('Bạn có chắc chắn muốn hủy booking này?');">
                  @csrf
                  <button class="btn-danger" type="submit">Hủy booking</button>
                </form>
              </div>
            @endif
          </article>
        @empty
          <div class="booking-card p-5 text-sm text-[#9a9080]">
            Bạn chưa có booking sắp tới.
          </div>
        @endforelse
      </div>

      <div>
        <div class="mb-3 flex items-center justify-between">
          <h2 class="font-['Playfair_Display'] text-2xl font-semibold text-[#e8e0d0]">Lịch Sử</h2>
        </div>

        @forelse($pastBookings as $booking)
          @php
            $checkin = $booking->checkin_date ?? optional($booking->bookingDetails->sortBy('checkin_date')->first())->checkin_date;
            $checkout = $booking->checkout_date ?? optional($booking->bookingDetails->sortByDesc('checkout_date')->first())->checkout_date;
            $reservationCode = 'RES-' . str_pad((string) $booking->id, 7, '0', STR_PAD_LEFT);
          @endphp
          <article class="booking-card p-4 sm:p-5 mb-4">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
              <div>
                <h3 class="text-base font-semibold text-[#e8e0d0]">#{{ $reservationCode }}</h3>
                <p class="text-sm text-[#9a9080]">Ngày đặt: {{ $booking->booking_date?->format('d/m/Y H:i') ?? '-' }}</p>
                <p class="mt-1 text-xs text-[#9a9080]">{{ $checkin?->format('d/m/Y') ?? '-' }} - {{ $checkout?->format('d/m/Y') ?? '-' }}</p>
              </div>
              <div class="text-left sm:text-right">
                <p class="text-base font-semibold text-white">{{ number_format((float) $booking->final_amount, 0, ',', '.') }}đ</p>
                <p class="mt-1 text-xs text-[#9a9080]">{{ $booking->status }}</p>
              </div>
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
              <button
                class="btn-secondary"
                type="button"
                onclick="loadBookingDetails({{ $booking->id }}, '{{ route('client.bookings.details', $booking->id) }}', 'history-details-{{ $booking->id }}')"
              >
                Xem chi tiết booking
              </button>
            </div>

            <div id="history-details-{{ $booking->id }}" class="hidden mt-4 rounded-xl border border-white/8 bg-black/20 p-4 text-sm text-[#d5ccbc]">
              <div class="text-[#9a9080]">Nhấn "Xem chi tiết booking" để tải dữ liệu.</div>
            </div>
          </article>
        @empty
          <div class="booking-card p-5 text-sm text-[#9a9080]">
            Chưa có lịch sử booking.
          </div>
        @endforelse

        @if($pastBookings->hasPages())
          <div class="mt-6">
            {{ $pastBookings->links() }}
          </div>
        @endif
      </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
  function toggleBookingDetails(elementId) {
    var element = document.getElementById(elementId);
    if (!element) {
      return;
    }
    element.classList.toggle('hidden');
  }

  async function loadBookingDetails(bookingId, detailsUrl, containerId) {
    var detailsContainer = document.getElementById(containerId);

    if (!detailsContainer) {
      return;
    }

    if (!detailsContainer.classList.contains('hidden')) {
      detailsContainer.classList.add('hidden');
      return;
    }

    detailsContainer.classList.remove('hidden');
    detailsContainer.innerHTML = '<div class="text-[#9a9080]">Đang tải chi tiết booking...</div>';

    try {
      var response = await fetch(detailsUrl, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      });

      if (!response.ok) {
        throw new Error('Request failed');
      }

      var payload = await response.json();
      detailsContainer.innerHTML = payload.html || '<div class="text-[#9a9080]">Không có dữ liệu chi tiết.</div>';
    } catch (error) {
      detailsContainer.innerHTML = '<div class="text-[#ff8a8a]">Không thể tải chi tiết booking. Vui lòng thử lại.</div>';
    }
  }
</script>
@endpush
