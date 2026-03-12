@extends('layouts.admin')

@section('title', 'Checkout phòng')

@section('content')

    @php

        $alreadyPaid = $booking->payments->sum('amount');

        $bookingCode = 'UL-BK-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);

        $badgeColorList = [
            'text-orange-600 bg-orange-100',

            'text-blue-600 bg-blue-100',

            'text-green-600 bg-green-100',

            'text-purple-600 bg-purple-100',

            'text-red-600 bg-red-100',
        ];

    @endphp



    <div class="px-6 py-6">

        <div id="checkout-meta" class="hidden" data-booking-id="{{ $booking->id }}" data-already-paid="{{ $alreadyPaid }}">
        </div>



        {{-- ===== TOP BAR ===== --}}

        <div class="flex items-center justify-between mb-6">

            {{-- Thông tin khách hàng (bên trái) --}}

            <div class="flex items-center gap-4">

                <div class="bg-orange-100 p-2 rounded-full">

                    <span class="material-symbols-outlined text-primary text-xl">groups</span>

                </div>

                <div>

                    <h1 class="text-lg font-bold text-slate-900">

                        Khách hàng: {{ $booking->customer->full_name }}

                    </h1>

                    <div class="flex items-center gap-2 mt-0.5">

                        <span
                            class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded font-medium flex items-center gap-1">

                            <span class="material-symbols-outlined text-[14px]">hotel</span>

                            {{ $booking->bookingDetails->count() }} PHÒNG

                        </span>

                        <span class="text-xs text-slate-500 font-mono">{{ $bookingCode }}</span>

                    </div>

                </div>

            </div>



            {{-- Nút Checkout (bên phải) --}}

            <form id="checkout-form">

                @csrf

                <button type="submit" id="checkout-btn"
                    class="bg-primary hover:bg-orange-600 text-white px-6 py-2.5 rounded-lg font-bold text-sm shadow-md transition-colors flex items-center gap-2">

                    <span class="material-symbols-outlined text-lg">playlist_add_check</span>

                    Checkout ngay

                </button>

            </form>

        </div>



        {{-- ===== BODY ===== --}}

        <div class="flex flex-col lg:flex-row gap-6 items-start">



            {{-- LEFT: Room list + Invoice --}}

            <div class="flex-1 min-w-0 space-y-6">



                {{-- Room list --}}

                <div class="bg-white border border-slate-200 rounded-2xl p-6">

                    <div class="flex justify-between items-center mb-4">

                        <h2 class="text-blue-700 font-bold flex items-center gap-2">

                            <span class="material-symbols-outlined">bed</span> DANH SÁCH PHÒNG

                        </h2>

                        <button type="button" onclick="toggleSelectAll()" id="select-all-btn"
                            class="text-xs text-blue-600 hover:text-blue-800 font-semibold hover:underline">

                            Bỏ chọn tất cả

                        </button>

                    </div>



                    <div class="grid grid-cols-12 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2">

                        <div class="col-span-5">PHÒNG</div>

                        <div class="col-span-3">LOẠI</div>

                        <div class="col-span-3 text-right">Tổng tiền</div>

                        <div class="col-span-1 text-center">CHỌN</div>

                    </div>



                    @foreach ($booking->bookingDetails as $index => $detail)

                        @php
                            $isCheckedOut = $detail->checkout_status;

                            $checkinDate = $detail->checkin_date;

                            $checkoutDate = $detail->checkout_date ?? now();

                            $nights = max(1, (int) $checkoutDate->diffInDays($checkinDate));

                            $roomCharge = (float) $detail->daily_price * $nights;

                            $isEarlyCI = $checkinDate->hour < 14;

                            $isLateCO = $checkoutDate->hour > 12;

                            $surchargeLabel = match (true) {
                                $isEarlyCI && $isLateCO => 'Check-in sớm + Checkout muộn',

                                $isEarlyCI => 'Check-in sớm (trước 14h)',

                                $isLateCO => 'Checkout muộn (sau 12h)',

                                default => '',
                            };

                            $serviceTotal = $detail->serviceUsages->sum(fn($u) => $u->quantity * $u->unit_price);

                            $lineTotal = $roomCharge + $serviceTotal + (float) $detail->surcharge_amount;

                            $servicesJson = $detail->serviceUsages
                                ->map(
                                    fn($u) => [
                                        'name' => $u->service->name,

                                        'quantity' => (int) $u->quantity,

                                        'unit_price' => (float) $u->unit_price,
                                    ],
                                )
                                ->values()
                                ->toArray();

                            $badgeColor = $badgeColorList[$index % count($badgeColorList)];

                        @endphp



                        <div class="room-checkout-card border {{ $isCheckedOut ? 'border-green-300 bg-green-50/40' : 'border-slate-200' }} rounded-xl mb-4 transition-all {{ $isCheckedOut ? '' : 'hover:border-blue-300 hover:shadow-sm' }}"
                            data-index="{{ $index }}" data-room-name="P.{{ $detail->room->name }}"
                            data-room-type="{{ $detail->room->roomType->name }}" data-room-charge="{{ $roomCharge }}"
                            data-service-amount="{{ $serviceTotal }}"
                            data-surcharge-amount="{{ (float) $detail->surcharge_amount }}"
                            data-surcharge-label="{{ $surchargeLabel }}"
                            data-services="{{ json_encode($servicesJson, JSON_UNESCAPED_UNICODE) }}">

                            {{-- Main row --}}
                            <div class="p-4 bg-white rounded-t-xl">
                                <div class="grid grid-cols-12 items-center gap-1">
                                    {{-- Room info --}}
                                    <div class="col-span-5">
                                        <div class="flex items-center gap-3">
                                            <span
                                                class="font-black px-2 py-1 rounded-lg text-sm min-w-[2.5rem] text-center {{ $badgeColor }}">
                                                {{ $detail->room->name }}
                                            </span>
                                            <div class="min-w-0">
                                                <div
                                                    class="font-bold text-slate-900 flex items-center gap-1 flex-wrap text-sm">

                                                    P.{{ $detail->room->name }}
                                                </div>

                                                <div class="text-[11px] text-gray-400 mt-0.5 truncate">

                                                    {{ $checkinDate->format('H:i d/m') }} ->
                                                    {{ $checkoutDate->format('H:i d/m/Y') }}

                                                </div>

                                                <div class="text-[11px] text-blue-400 font-semibold">{{ $nights }}
                                                    đêm</div>

                                            </div>

                                        </div>

                                    </div>

                                    {{-- Room type --}}

                                    <div class="col-span-3 text-xs text-gray-500 font-medium">
                                        {{ $detail->room->roomType->name }}</div>
                                    {{-- Subtotal --}}
                                    <div class="col-span-3 text-right font-bold text-slate-800 text-sm">
                                        {{ number_format($lineTotal, 0, ',', '.') }}đ
                                    </div>
                                    {{-- Checkbox --}}
                                    <div class="col-span-1 flex justify-center items-center">
                                        @if ($isCheckedOut)
                                            <span
                                                class="text-[9px] bg-green-100 text-green-700 px-1.5 py-0.5 rounded font-bold uppercase tracking-wide">Đã
                                                CO</span>
                                        @else
                                            <input type="checkbox"
                                                class="room-checkbox w-4 h-4 rounded accent-blue-600 cursor-pointer"
                                                data-index="{{ $index }}"
                                                data-booking-detail-id="{{ $detail->id }}"
                                                data-room-id="{{ $detail->room_id }}" onchange="onRoomToggle()" checked>
                                        @endif
                                    </div>

                                </div>

                            </div>

                            {{-- Services + surcharge --}}
                            @if ($detail->serviceUsages->isNotEmpty() || ($detail->surcharge_amount > 0 && $surchargeLabel))
                                <div class="bg-slate-50 px-4 py-3 rounded-b-xl border-t border-slate-100 space-y-1">

                                    @if ($detail->serviceUsages->isNotEmpty())
                                        <div
                                            class="text-[10px] font-bold text-gray-400 uppercase mb-1.5 flex items-center gap-1">

                                            <span class="material-symbols-outlined text-[12px]">room_service</span> Dịch vụ
                                            đã dùng

                                        </div>

                                        @foreach ($detail->serviceUsages as $usage)
                                            <div class="flex justify-between text-xs text-gray-500">

                                                <span>{{ $usage->service->name }}- {{ $usage->quantity }}</span>

                                                <span
                                                    class="font-medium">{{ number_format($usage->unit_price * $usage->quantity, 0, ',', '.') }}đ</span>

                                            </div>
                                        @endforeach
                                    @endif

                                    @if ($detail->surcharge_amount > 0 && $surchargeLabel)
                                        <div class="flex justify-between text-xs text-red-500 font-semibold pt-1">

                                            <span class="flex items-center gap-1">

                                                <span class="material-symbols-outlined text-[12px]">alarm_off</span>

                                                Phụ phí {{ $surchargeLabel }}

                                            </span>
                                            <span>{{ number_format($detail->surcharge_amount, 0, ',', '.') }}đ</span>

                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Invoice breakdown --}}
                <div class="relative bg-white border border-slate-200 rounded-2xl p-6">
                    {{-- Loading spinner overlay --}}
                    <div id="invoice-loading"
                        class="hidden absolute inset-0 z-10 flex items-center justify-center bg-white/70 rounded-2xl">
                        <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                            </path>
                        </svg>
                    </div>

                    <div id="invoice-content">
                        <h2 class="text-gray-800 font-bold flex items-center gap-2 mb-5">
                            <span class="material-symbols-outlined text-gray-500">receipt_long</span> HÓA ĐƠN
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                            {{-- Tiền phòng --}}
                            <div>
                                <h3 class="text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">Tiền phòng
                                </h3>
                                <div class="space-y-1.5 text-sm" id="invoice-rooms-list"></div>
                                <div
                                    class="flex justify-between text-xs font-semibold text-slate-600 mt-2 pt-2 border-t border-slate-200">
                                    <span>Tổng tiền phòng</span>
                                    <span id="total-room-charge">0đ</span>
                                </div>
                            </div>

                            {{-- Dịch vụ --}}
                            <div>
                                <h3 class="text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">Dịch vụ</h3>
                                <div class="space-y-1.5 text-sm" id="invoice-services-list"></div>
                                <div
                                    class="flex justify-between text-xs font-semibold text-slate-600 mt-2 pt-2 border-t border-slate-200">
                                    <span>Tổng tiền dịch vụ</span>
                                    <span id="total-service-charge">0đ</span>
                                </div>
                            </div>

                            {{-- Phụ phí --}}
                            <div>
                                <h3 class="text-[11px] font-bold text-gray-400 uppercase mb-2 tracking-wider">Phụ phí</h3>
                                <div class="space-y-1.5 text-sm" id="invoice-surcharges-list"></div>
                                <div
                                    class="flex justify-between text-xs font-semibold text-slate-600 mt-2 pt-2 border-t border-slate-200">
                                    <span>Tổng phí phát sinh</span>
                                    <span id="total-surcharge">0đ</span>
                                </div>
                            </div>
                        </div>

                        {{-- Grand total --}}
                        <div class="py-3 border-t-2 border-slate-300">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="text-sm font-bold text-slate-700 uppercase">Tổng cần thanh toán</div>
                                    <div class="text-[10px] text-slate-400">Đã bao gồm tất cả phí</div>
                                </div>
                                <span class="text-xl font-black text-blue-700" id="grand-total">0đ</span>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- END Invoice --}}

            </div>
            {{-- END LEFT --}}

            {{-- RIGHT: Payment history + payment input (sticky) --}}
            <div class="w-full lg:w-[360px] shrink-0 lg:sticky lg:top-4">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-4">

                    {{-- Already paid history --}}
                    @if ($alreadyPaid > 0)
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-[11px] font-bold text-green-600 uppercase tracking-wider">Đã thanh
                                    toán</span>
                                <span class="text-sm font-bold text-green-600">-
                                    {{ number_format($alreadyPaid, 0, ',', '.') }}đ</span>
                            </div>
                            <div class="space-y-2.5 pl-3 border-l-2 border-green-200">
                                @foreach ($booking->payments as $payment)
                                    <div class="flex justify-between items-start text-xs">
                                        <div>
                                            <div class="text-gray-700 font-semibold">
                                                {{ $bookingCode }}
                                                ({{ match ($payment->payment_method) {
                                                    'cash' => 'Tiền mặt',
                                                    'bank_transfer' => 'Chuyển khoản NH',
                                                    'card' => 'Thẻ tín dụng',
                                                    default => $payment->payment_method,
                                                } }})
                                            </div>
                                            <div class="text-gray-400 mt-0.5">
                                                {{ $payment->created_at?->format('H:i · d/m/Y') ?? 'Không rõ thời gian' }}
                                                @if ($payment->staff)
                                                    · {{ $payment->staff->first_name }} {{ $payment->staff->last_name }}
                                                @endif
                                            </div>
                                        </div>
                                        <span class="text-green-600 font-semibold whitespace-nowrap ml-3">+
                                            {{ number_format($payment->amount, 0, ',', '.') }}đ</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Payment input --}}
                    <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 space-y-3">
                        <h3 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">payments</span> Ghi nhận
                        </h3>

                        {{-- Radio: Thanh toán / Hoàn tiền --}}
                        <div class="flex gap-5">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="payment-type" id="type-payment" value="payment" checked
                                    class="accent-blue-600 w-4 h-4" onchange="onPaymentTypeChange()">
                                <span class="text-sm font-semibold text-slate-700">Thanh toán</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="payment-type" id="type-refund" value="refund"
                                    class="accent-rose-500 w-4 h-4" onchange="onPaymentTypeChange()">
                                <span class="text-sm font-semibold text-rose-600">Hoàn tiền</span>
                            </label>
                        </div>

                        <div>
                            <label class="text-xs text-gray-500 mb-1 block">Số tiền</label>
                            <input type="number" id="payment-amount" min="0" step="1000"
                                class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white transition-all"
                                placeholder="Nhập số tiền">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Phương thức</label>
                                <select id="payment-method"
                                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white">
                                    <option value="cash">Tiền mặt</option>
                                    <option value="bank_transfer">Chuyển khoản</option>
                                    <option value="card">Thẻ tín dụng</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Tiền tệ</label>
                                <select id="payment-currency"
                                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white">
                                    <option value="VND">VND</option>
                                    <option value="USD">USD</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-1 border-t border-slate-200">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide"
                                id="remaining-label">Còn lại</span>
                            <span id="payment-remaining" class="text-sm font-bold text-rose-500">0đ</span>
                        </div>

                        <button type="button" onclick="submitPayment()" id="submit-payment-btn"
                            class="w-full py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-bold text-sm transition-colors flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-base">check_circle</span>
                            <span id="submit-payment-label">Ghi nhận thanh toán</span>
                        </button>
                    </div>

                </div>
            </div>
            {{-- END RIGHT --}}

        </div>
        {{-- END BODY --}}

        {{-- ===== FOOTER ===== --}}
        <div class="mt-8 flex justify-end py-4 border-t border-slate-200">
            <a href="{{ route('admin.layout-rooms.index') }}"
                class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium text-sm transition-colors">
                Đóng
            </a>
        </div>

    </div>

    @push('scripts')
        @vite(['resources/js/admin/bookings/checkout.js'])
    @endpush
@endsection
