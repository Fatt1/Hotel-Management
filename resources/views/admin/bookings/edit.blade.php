@extends('layouts.admin')
@section('title', 'Cập nhật booking')
@section('content')
    @php
        $isCompleted = $booking->status === 'Hoàn tất';
        $isOccupied = $booking->status === 'Đang ở';
        function formatDuration(float $hour) {
                                $totalMinutes = (int) floor($hour * 60);
                                $days = intdiv($totalMinutes, 24 * 60);
                                $remainingMinutes = $totalMinutes % (24 * 60);
                                $h = intdiv($remainingMinutes, 60);
                                $m = $remainingMinutes % 60;

                                $parts = [];
                                if ($days > 0) {
                                    $parts[] = $days . ' ngày';
                                }
                                if ($h > 0) {
                                    $parts[] = $h . ' giờ';
                                }
                                if ($m > 0) {
                                    $parts[] = $m . ' phút';
                                }

                                return empty($parts) ? '0 phút' : implode(' ', $parts);
                            }
    @endphp
    
    @if($isCompleted)
        <div class="mb-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400">warning</span>
                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                    Booking này đã hoàn tất. Không thể chỉnh sửa.
                </p>
            </div>
        </div>
    @endif

    <div class="flex flex-col xl:flex-row gap-6">
        <div class="flex-1 space-y-6">
            {{-- Thông tin khách hàng (READ-ONLY) --}}
            <div
                class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark p-6">
                <div class="flex items-center mb-4">
                    <span class="material-symbols-outlined text-primary dark:text-blue-400 mr-2">person_outline</span>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Thông tin khách hàng
                    </h2>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    {{-- Customer info section (always shown, read-only) --}}
                    <div id="customer-info-section">
                        <div id="customer-info-card" class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                            <div id="customer-status-header" class="flex items-center justify-between gap-2 text-blue-700 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-base">person</span>
                                    <span class="text-xs font-bold uppercase tracking-wider">Thông tin khách hàng</span>
                                </div>
                            </div>
                           
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                {{-- Họ  --}}
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Họ</label>
                                    <input id="nc-first-name" name="customer_first_name" 
                                        value="{{ $booking->customer->first_name }}"
                                        type="text" readonly
                                        class="w-full px-3 py-2 text-sm border rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed" />
                                </div>
                                {{-- Tên  --}}
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Tên</label>
                                    <input id="nc-last-name" name="customer_last_name" 
                                        value="{{ $booking->customer->last_name }}"
                                        type="text" readonly
                                        class="w-full px-3 py-2 text-sm border rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed" />
                                </div>
                                {{-- Email  --}}
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Email</label>
                                    <input id="nc-email" name="customer_email" 
                                        value="{{ $booking->customer->email }}"
                                        type="email" readonly
                                        class="w-full px-3 py-2 text-sm border rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed" />
                                </div>
                                {{-- Số điện thoại  --}}
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Số điện
                                        thoại</label>
                                    <input id="nc-phone" name="customer_phone" 
                                        value="{{ $booking->customer->phone_number }}"
                                        type="text" readonly
                                        class="w-full px-3 py-2 text-sm border rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed" />
                                </div>
                                {{-- Quốc gia  --}}
                                <div class="flex flex-col gap-1 md:col-span-2">
                                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Quốc gia</label>
                                    <input type="text" 
                                        value="{{ $booking->customer->country }}"
                                        readonly
                                        class="w-full px-3 py-2 text-sm border rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Thời gian lưu trú --}}
            <div
                class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark p-6 {{ $isCompleted ? 'opacity-60' : '' }}">
                <div class="flex items-center mb-4">
                    <span class="material-symbols-outlined text-primary dark:text-blue-400 mr-2">date_range</span>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Thời gian lưu trú
                    </h2>
                    @if($isCompleted)
                        <span class="ml-2 text-xs text-gray-500">(Không thể chỉnh sửa)</span>
                    @endif
                </div>
                {{-- Date Range Picker --}}
                <div id="date-range-bar"
                    class="flex items-center gap-2 p-1 border border-border-light dark:border-border-dark rounded-xl bg-white dark:bg-gray-800 shadow-sm {{ $isCompleted ? 'pointer-events-none' : '' }}">
                    {{-- Check-in display --}}
                    <div
                        class="flex-1 flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <span class="material-symbols-outlined text-primary text-lg flex-shrink-0">flight_land</span>
                        <div class="flex-1">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Nhận phòng</p>
                            <div class="flex items-center gap-2">
                                <p id="checkin-display"
                                    class="text-sm font-semibold text-gray-900 dark:text-white cursor-pointer">--</p>
                                <span class="text-gray-300">|</span>
                                <input type="time" id="checkin-time" value="14:00"
                                    class="text-sm font-semibold text-primary bg-transparent outline-none border-none cursor-pointer w-[72px] [&::-webkit-calendar-picker-indicator]:hidden" />
                            </div>
                        </div>
                    </div>

                    {{-- Arrow --}}
                    <div class="flex-shrink-0 flex items-center gap-1">
                        <div class="w-6 h-px bg-gray-200 dark:bg-gray-600"></div>
                        <span class="material-symbols-outlined text-base text-gray-400">arrow_forward</span>
                        <div class="w-6 h-px bg-gray-200 dark:bg-gray-600"></div>
                    </div>

                    {{-- Check-out display --}}
                    <div
                        class="flex-1 flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <span class="material-symbols-outlined text-primary text-lg flex-shrink-0">flight_takeoff</span>
                        <div class="flex-1">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Trả phòng</p>
                            <div class="flex items-center gap-2">
                                <p id="checkout-display"
                                    class="text-sm font-semibold text-gray-900 dark:text-white cursor-pointer">--</p>
                                <span class="text-gray-300">|</span>
                                <input type="time" id="checkout-time" value="12:00"
                                    class="text-sm font-semibold text-primary bg-transparent outline-none border-none cursor-pointer w-[72px] [&::-webkit-calendar-picker-indicator]:hidden" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hidden input flatpickr attach vào --}}
                <input type="text" id="flatpickr-range" class="hidden" />
                {{-- Actual form values --}}
                <input type="hidden" name="check_in" id="check_in" />
                <input type="hidden" name="check_out" id="check_out" />

                {{-- Badge hiển thị số đêm --}}
                <div id="stayDuration"
                    class="hidden mt-3 flex items-center gap-2 text-xs text-primary dark:text-blue-400 font-medium">
                    <span class="material-symbols-outlined text-sm">nights_stay</span>
                    <span id="durationText"></span>
                </div>
            </div>

            {{-- Chọn phòng --}}
            <div
                class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-primary dark:text-blue-400 mr-2">meeting_room</span>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Danh sách phòng
                        </h2>
                    </div>
                    @if(!$isCompleted)
                        <button type="button" id="add-room-btn"
                            class="flex items-center text-xs font-medium text-primary dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-3 py-1.5 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors">
                            <span class="material-symbols-outlined text-sm mr-1">add</span>
                            Thêm phòng
                        </button>
                    @endif
                </div>

                {{-- Danh sách phòng --}}
                <div class="space-y-3">
                    @forelse($booking->bookingDetails as $detail)
                        @php
                            $room = $detail->room;
                            $isCheckedOut = $detail->checkout_status;
                            $canEdit = !$isCompleted && !$isCheckedOut;
                            $checkinDate = \Carbon\Carbon::parse($detail->checkin_date);
                            $checkoutDate = \Carbon\Carbon::parse($detail->checkout_date);
                            
                            $hours = $checkinDate->diffInSeconds($checkoutDate) / 3600;
                            $roomCost = $detail->room_amount;

                            
                        @endphp
                        
                        <div class="p-4 bg-white dark:bg-gray-800 border border-border-light dark:border-border-dark rounded-xl space-y-3 {{ $isCheckedOut ? 'opacity-60' : '' }}">
                            
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary shrink-0">
                                        <span class="material-symbols-outlined text-lg">bed</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-bold text-gray-900 dark:text-white uppercase">{{ $room->name }}</p>
                                            @if($isCheckedOut)
                                                <span class="px-2 py-0.5 bg-green-100 text-green-600 text-[10px] font-black uppercase rounded">Đã checkout</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $room->roomType->name }} · {{ formatDuration($hours) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-primary">{{ number_format($roomCost, 0, ',', '.') }} đ</p>
                                        <p class="text-[10px] text-gray-400">{{ number_format($detail->daily_price, 0, ',', '.') }} đ/ngày</p>
                                        <p class="text-[10px] text-gray-400">{{ number_format($detail->hourly_price, 0, ',', '.') }} đ/giờ</p>
                                    </div>
                                    @if($canEdit)
                                        <button type="button"
                                            data-action="remove-room"
                                            data-booking-id="{{ $booking->id }}"
                                            data-room-id="{{ $room->id }}"
                                            class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors shrink-0">
                                            <span class="material-symbols-outlined text-base">delete</span>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Thời gian phòng này --}}
                            <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                        - Thời gian lưu trú
                                    </span>
                                    @if($canEdit)
                                        <button type="button"
                                            data-action="edit-room-dates"
                                            data-booking-id="{{ $booking->id }}"
                                            data-room-id="{{ $room->id }}"
                                            data-checkin-date="{{ $detail->checkin_date }}"
                                            data-checkout-date="{{ $detail->checkout_date }}"
                                            data-booking-status="{{ $booking->status }}"
                                            class="text-[11px] font-semibold text-primary hover:text-blue-800 dark:hover:text-blue-400 transition-colors">
                                            <span class="material-symbols-outlined text-sm">edit</span>
                                        </button>
                                    @endif
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Check-in:</span>
                                        <span class="text-gray-900 dark:text-white font-medium">{{ $checkinDate->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Check-out:</span>
                                        <span class="text-gray-900 dark:text-white font-medium">{{ $checkoutDate->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Dịch vụ --}}
                            <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                        - Dịch vụ đã dùng
                                    </span>
                                    @if($canEdit)
                                        <button type="button"
                                            data-action="add-room-service"
                                            data-room-id="{{ $room->id }}"
                                            class="flex items-center gap-0.5 text-[11px] font-semibold text-primary hover:text-blue-800 dark:hover:text-blue-400 transition-colors">
                                            <span class="material-symbols-outlined text-sm">add</span>
                                            Thêm dịch vụ
                                        </button>
                                    @endif
                                </div>
                                <div class="space-y-0.5">
                                    @forelse($detail->serviceUsages as $usage)
                                        <div class="flex items-center justify-between py-0.5">
                                            <span class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $usage->service->name }}
                                                <span class="text-gray-400 dark:text-gray-500">×{{ $usage->quantity }}</span>
                                            </span>
                                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300 shrink-0 ml-4">
                                                {{ number_format($usage->unit_price * $usage->quantity, 0, ',', '.') }} đ
                                            </span>
                                        </div>
                                    @empty
                                        <p class="text-xs text-gray-400 dark:text-gray-500 italic">Chưa có dịch vụ nào</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 text-gray-400 dark:text-gray-600 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
                            <span class="material-symbols-outlined text-3xl mb-2">bed</span>
                            <p class="text-sm">Chưa có phòng nào</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar thanh toán --}}
        <div class="w-full xl:w-96 flex-shrink-0">
            <div
                class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-lg border border-border-light dark:border-border-dark p-6 xl:sticky xl:top-6">
                <h2
                    class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">
                    Chi tiết thanh toán
                </h2>
                <div class="space-y-4 mb-6">
                    @php
                        $totalRoomAmount = 0;
                        $totalServiceAmount = 0;
                        $totalSurchargeAmount = 0;
                    @endphp
                    
                    @foreach($booking->bookingDetails as $detail)
                        @php
                            // Lấy dữ liệu trực tiếp từ DB
                            $checkinDate = \Carbon\Carbon::parse($detail->checkin_date);
                            $checkoutDate = \Carbon\Carbon::parse($detail->checkout_date);
                            $days = (int) max($checkinDate->diffInDays($checkoutDate), 1);
                            $roomAmount = $detail->room_amount;
                            $serviceAmount = $detail->service_amount ?? 0;
                            $surchargeAmount = $detail->surcharge_amount ?? 0;
                            
                            $totalRoomAmount += $roomAmount;
                            $totalServiceAmount += $serviceAmount;
                            $totalSurchargeAmount += $surchargeAmount;
                        @endphp
                        
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-3">
                            <p class="font-bold text-sm text-gray-900 dark:text-white mb-2">{{ $detail->room->name }}</p>
                            <div class="space-y-1 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Phòng</span>
                                    <span class="text-gray-900 dark:text-white">{{ number_format($roomAmount, 0, ',', '.') }} đ</span>
                                </div>
                                @if($serviceAmount > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Dịch vụ</span>
                                        <span class="text-gray-900 dark:text-white">{{ number_format($serviceAmount, 0, ',', '.') }} đ</span>
                                    </div>
                                @endif
                                @if($detail->checkout_status && $surchargeAmount > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Phụ thu</span>
                                        <span class="text-gray-900 dark:text-white">{{ number_format($surchargeAmount, 0, ',', '.') }} đ</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div
                    class="bg-gray-50 dark:bg-gray-800/50 -mx-6 px-6 py-4 border-t border-gray-100 dark:border-gray-700 mb-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-600 dark:text-gray-400">Tổng tiền phòng</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($totalRoomAmount, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-600 dark:text-gray-400">Tổng tiền dịch vụ</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($totalServiceAmount, 0, ',', '.') }} đ</span>
                        </div>
                        @if($totalSurchargeAmount > 0)
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-600 dark:text-gray-400">Tổng phụ thu</span>
                                <span class="font-semibold text-orange-600 dark:text-orange-400">{{ number_format($totalSurchargeAmount, 0, ',', '.') }} đ</span>
                            </div>
                        @endif
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Tổng cộng</span>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalRoomAmount + $totalServiceAmount + $totalSurchargeAmount, 0, ',', '.') }} đ</span>
                            </div>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500 text-right block mt-1 italic">Đã bao gồm thuế &amp; phí</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-3">
                    {{-- Lịch sử thanh toán --}}
                    @if($booking->payments->isNotEmpty())
                        @php
                            $methodLabels = ['cash' => 'Tiền mặt', 'bank_transfer' => 'Chuyển khoản', 'card' => 'Thẻ tín dụng'];
                            $totalPaid = $booking->payments->sum('amount');
                        @endphp
                        <div class="border border-border-light dark:border-border-dark rounded-xl p-4 space-y-2">
                            <span class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Lịch sử thanh toán</span>
                            @foreach($booking->payments as $payment)
                                <div class="flex items-center justify-between gap-2">
                                    <div>
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                            {{ $methodLabels[$payment->payment_method] ?? $payment->payment_method }}
                                        </span>
                                        @if($payment->staff)
                                            <span class="text-[10px] text-gray-400 block">{{ $payment->staff->first_name }} {{ $payment->staff->last_name }}</span>
                                        @endif
                                    </div>
                                    <span class="text-sm font-bold text-emerald-600">{{ number_format($payment->amount, 0, ',', '.') }} đ</span>
                                </div>
                            @endforeach
                            <div class="pt-1 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <span class="text-xs text-gray-500">Đã thanh toán</span>
                                <span class="text-sm font-bold text-emerald-600">{{ number_format($totalPaid, 0, ',', '.') }} đ</span>
                            </div>
                        </div>
                    @else
                        @php
                            $totalPaid = 0;
                        @endphp
                    @endif

                    <div class="flex items-center justify-between px-1">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Còn lại</span>
                        <span class="text-sm font-bold text-rose-500">{{ number_format(($totalRoomAmount + $totalServiceAmount + $totalSurchargeAmount) - $totalPaid, 0, ',', '.') }} đ</span>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-sm">info</span>
                            <p class="text-xs text-blue-700 dark:text-blue-300">
                                Thay đổi sẽ được lưu tự động khi thêm/xóa phòng hoặc dịch vụ.
                            </p>
                        </div>
                    </div>

                    <a href="{{ route('admin.bookings.index') }}"
                        class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 font-medium py-3 rounded-lg transition-colors flex items-center justify-center">
                        Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Pass booking context to update-booking.js --}}
    <div id="booking-edit-context"
        data-booking='@json($booking)'
        data-is-completed="{{ $isCompleted ? 'true' : 'false' }}"
        class="hidden"></div>
@endsection
@push('scripts')
    @vite(['resources/js/admin/bookings/update-booking.js'])
@endpush
