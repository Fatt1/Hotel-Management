@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-slate-50 py-8">
    <div class="max-w-5xl mx-auto px-4">
        <!-- Back Button -->
        <a href="{{ route('admin.layout-rooms.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium mb-6 transition-colors">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
            <span>Quay lại sơ đồ</span>
        </a>

        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
            <!-- Header -->
            <div class="px-8 py-6 border-b border-slate-200">
                <h1 class="text-2xl font-bold text-slate-900">
                    Chi tiết phòng {{ $booking->bookingDetails->first()->room->name }} - {{ $booking->bookingDetails->first()->room->roomType->name }}
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    MÃ PHÒNG: {{ $booking->bookingDetails->first()->room->roomType->code }}-{{ $booking->bookingDetails->first()->room->name }}
                </p>
            </div>

            <!-- Content -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8">
                <!-- Left: Customer Info -->
                <div>
                    <div class="flex items-center gap-2 mb-6">
                        <span class="material-symbols-outlined text-blue-600 text-xl">person</span>
                        <h2 class="text-base font-bold text-blue-600 uppercase">Thông tin khách đặt</h2>
                    </div>

                    <div class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Họ và Tên</label>
                            <p class="text-base font-bold text-slate-900 mt-1">
                                {{ $booking->customer->last_name }} {{ $booking->customer->first_name }} 
                            </p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Số điện thoại</label>
                            <p class="text-base font-bold text-slate-900 mt-1">
                                {{ $booking->customer->phone_number }}
                            </p>
                        </div>

                        <!-- Expected Check-in -->
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Ngày nhận phòng dự kiến</label>
                            <p class="text-base font-bold text-slate-900 mt-1">
                                {{ $booking->bookingDetails->first()->checkin_date->format('d/m/Y - H:i') }}
                            </p>
                        </div>

                        <!-- Booking Code -->
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Mã đặt chỗ</label>
                            <div class="flex items-center gap-2 mt-1">
                                <p class="text-base font-bold text-slate-900">UL-BK-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</p>
                                <button onclick="copyBookingCode('UL-BK-{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}')" 
                                    class="text-slate-400 hover:text-slate-600 transition-colors">
                                    <span class="material-symbols-outlined text-lg">content_copy</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Other Rooms -->
                <div>
                    <div class="flex items-center gap-2 mb-6">
                        <span class="material-symbols-outlined text-blue-600 text-xl">meeting_room</span>
                        <h2 class="text-base font-bold text-blue-600 uppercase">Các phòng khác của khách đã đặt</h2>
                    </div>

                    <div class="space-y-3">
                        @foreach($booking->bookingDetails as $detail)
                        <div class="flex items-center gap-3 p-3 rounded-lg border-2 border-slate-200 hover:border-blue-300 transition-colors">
                            @php
                                $iconColor = match($detail->room->roomType->code) {
                                    'DXK' => 'text-red-500 bg-red-50',
                                    'STT' => 'text-blue-500 bg-blue-50',
                                    'SUO' => 'text-green-500 bg-green-50',
                                    'DBQ' => 'text-orange-500 bg-orange-50',
                                    default => 'text-slate-500 bg-slate-50'
                                };
                            @endphp
                            <div class="p-2 rounded-lg {{ $iconColor }}">
                                <span class="material-symbols-outlined text-2xl">{{ $detail->room->roomType->code === 'SUO' ? 'king_bed' : 'hotel' }}</span>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-slate-900">Phòng {{ $detail->room->name }} - {{ $detail->room->roomType->name }}</h3>
                                <p class="text-xs text-slate-500 font-medium">{{ $detail->room->roomType->code }}-{{ $detail->room->name }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <a href="{{ route('admin.layout-rooms.index') }}" 
                    class="px-6 py-3 rounded-xl font-bold text-slate-600 hover:bg-slate-200 transition-all">
                    Đóng
                </a>
                
                <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST" class="inline" 
                    onsubmit="return confirm('Bạn có chắc chắn muốn hủy đặt lịch này?')">
                    @csrf
                    @method('POST')
                    <button type="submit" 
                        class="px-6 py-3 rounded-xl font-bold text-red-600 hover:bg-red-50 border-2 border-red-200 transition-all">
                        Hủy đặt lịch
                    </button>
                </form>
                
                <form action="{{ route('admin.bookings.checkin.confirm', $booking->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                        class="px-6 py-3 rounded-xl font-bold text-white bg-purple-600 hover:bg-purple-700 shadow-lg shadow-purple-600/30 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-xl">login</span>
                        <span>Check-in ngay</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-sm text-slate-500">© {{ date('Y') }} Urban Luxe Management System. All rights reserved.</p>
        </div>
    </div>
</div>

<script>
function copyBookingCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        // Show success message
        const msg = document.createElement('div');
        msg.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
        msg.textContent = 'Đã copy mã đặt chỗ!';
        document.body.appendChild(msg);
        
        setTimeout(() => {
            msg.remove();
        }, 2000);
    });
}
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endsection
