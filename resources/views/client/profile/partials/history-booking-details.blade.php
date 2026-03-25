@php
  $reservationCode = 'RES-' . str_pad((string) $booking->id, 7, '0', STR_PAD_LEFT);
@endphp

<div class="space-y-2 text-sm text-[#d5ccbc]">
  <p>Mã đặt phòng: <span class="text-white">#{{ $reservationCode }}</span></p>
  <p>Ngày đặt: <span class="text-white">{{ $booking->booking_date?->format('d/m/Y H:i') ?? '-' }}</span></p>
  <p>Trạng thái: <span class="text-white">{{ $booking->status }}</span></p>
</div>

<div class="mt-3 divide-y divide-white/10 border border-white/8 rounded-lg overflow-hidden">
  @forelse($booking->bookingDetails as $detail)
    @php
      $serviceTotal = (float) $detail->serviceUsages->sum(fn($usage) => $usage->quantity * $usage->unit_price);
    @endphp
    <div class="p-3 text-sm text-[#d5ccbc] space-y-2">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
        <span class="font-semibold text-[#e8e0d0]">{{ $detail->room->name ?? 'N/A' }} - {{ $detail->room->roomType->name ?? 'N/A' }}</span>
        <span>{{ $detail->checkin_date?->format('d/m/Y H:i') }} → {{ $detail->checkout_date?->format('d/m/Y H:i') }}</span>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 text-xs text-[#b9ac99]">
        <p>Tiền phòng: {{ number_format((float) $detail->room_amount, 0, ',', '.') }}đ</p>
        <p>Dịch vụ: {{ number_format($serviceTotal, 0, ',', '.') }}đ</p>
        <p>Phụ thu: {{ number_format((float) $detail->surcharge_amount, 0, ',', '.') }}đ</p>
      </div>
      <div class="text-xs text-[#9a9080]">
        @if($detail->serviceUsages->isNotEmpty())
          Dịch vụ đã dùng:
          @foreach($detail->serviceUsages as $usage)
            <span class="text-[#d5ccbc]">{{ $usage->service->name }} x{{ $usage->quantity }}@if(!$loop->last), @endif</span>
          @endforeach
        @else
          Chưa có dịch vụ sử dụng.
        @endif
      </div>
    </div>
  @empty
    <div class="p-3 text-sm text-[#9a9080]">Không có chi tiết phòng cho booking này.</div>
  @endforelse
</div>

<div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-[#d5ccbc]">
  <p>Tổng tiền phòng: <span class="text-white">{{ number_format((float) $booking->total_room_amount, 0, ',', '.') }}đ</span></p>
  <p>Tổng tiền dịch vụ: <span class="text-white">{{ number_format((float) $booking->total_service_amount, 0, ',', '.') }}đ</span></p>
  <p>Phụ thu: <span class="text-white">{{ number_format((float) $booking->surcharge_amount, 0, ',', '.') }}đ</span></p>
  <p>Tổng thanh toán: <span class="text-white">{{ number_format((float) $booking->final_amount, 0, ',', '.') }}đ</span></p>
</div>
