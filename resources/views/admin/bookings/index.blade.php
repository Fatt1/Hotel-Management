@extends('layouts.admin')
@section('content')
    <div class="p-8 space-y-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Quản lý đặt lịch</h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Hệ thống quản trị khách sạn Urban Luxe - Quản lý
                    đặt lịch khách hàng.</p>
            </div>
            <button
                class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl text-sm font-bold shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all hover:-translate-y-0.5">
                <span class="material-symbols-outlined">add_circle</span>
                Tạo đặt phòng mới
            </button>
        </div>
        <div
            class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div
                class="p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col xl:flex-row gap-4 justify-between items-center">
                <div class="relative w-full xl:w-96">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <span class="material-symbols-outlined !text-lg">search</span>
                    </span>
                    <input
                        class="block w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all"
                        placeholder="Tìm theo Tên khách / Mã đặt phòng..." type="text" />
                </div>
                <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
                    <select
                        class="block w-full sm:w-48 px-4 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending">Chờ xác nhận</option>
                        <option value="confirmed">Đã xác nhận</option>
                        <option value="checkin">Đã nhận phòng</option>
                        <option value="checkout">Đã trả phòng</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                    <button class="p-2 text-slate-500 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">filter_alt</span>
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-800/30">
                            <th class="table-header">Mã đặt phòng</th>
                            <th class="table-header">Khách hàng</th>
                            <th class="table-header text-center">Ngày đặt</th>
                            
                            <th class="table-header text-right">Tổng tiền</th>
                            <th class="table-header text-center">Trạng thái</th>
                            <th class="table-header text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($bookings as $booking)
                            @php
                                $status_classes = [
                                    'Chờ xác nhận' => 'badge-pending-booking',
                                    'Đang ở' => 'badge-confirmed-booking',
                                    'Hoàn tất' => 'badge-completed-booking',
                                    'Hủy' => 'badge-cancelled-booking',
                                    'Không đến' => 'badge-no-show-booking',
                                ];
                            @endphp
                             <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors group">
                            <td class="table-cell">
                                <span class="font-mono font-bold text-primary tracking-wider">#UL-{{ $booking->id }}</span>
                            </td>
                            <td class="table-cell">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800 dark:text-slate-100">{{ $booking->customer->first_name }} {{ $booking->customer->last_name }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $booking->customer->phone_number }}</span>
                                </div>
                            </td>
                           
                            <td class="table-cell text-center">
                                <span class="text-xs font-medium">{{ $booking->booking_date->format('d/m/Y') }}</span>
                            </td>
                           
                            <td class="table-cell text-right font-black text-slate-800 dark:text-slate-200">{{ number_format($booking->final_amount, 0, ',', '.') }} đ</td>
                            <td class="table-cell text-center">
                                <span class="{{ $status_classes[$booking->status] ?? 'badge-default' }}">{{ $booking->status }}</span>
                            </td>
                            <td class="table-cell text-right">
                                <div
                                    class="flex items-center justify-end gap-1 ">
                                    <button class="action-btn text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20"
                                        title="Xem chi tiết">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                    <button class="action-btn text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20"
                                        title="Chỉnh sửa">
                                        <span class="material-symbols-outlined">edit</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                            
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Hiển thị {{ $bookings->lastItem() }} trên {{ $bookings->total() }} đặt lịch</span>
                <div class="flex items-center gap-1">
                    @if($bookings->currentPage() > 1) 
                    <a href="{{ route('admin.bookings.index', ['page_number' => $bookings->currentPage() - 1]) }}"
                        class="p-2 rounded-lg border border-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled="">
                        <span class="material-symbols-outlined !text-lg">chevron_left</span>
                    </a>
                    @else
                    <a href="#"
                        class="p-2 rounded-lg border border-slate-200  disabled:opacity-50 disabled:cursor-not-allowed cursor-not-allowed bg-gray-200"
                        disabled>
                        <span class="material-symbols-outlined !text-lg">chevron_left</span>
                    </a>
                    @endif
                    @for($i = 1; $i <= $bookings->lastPage(); $i++)
                        <a href="{{ route('admin.bookings.index', ['page_number' => $i]) }}"
                            class="w-8 flex items-center justify-center h-8 rounded-lg text-xs font-bold {{ $bookings->currentPage() == $i ? 'bg-primary text-white' : 'hover:bg-slate-100' }}">
                            {{ $i }}
                        </a>
                    @endfor
                    @if($bookings->currentPage() < $bookings->lastPage())
                    <a href="{{ route('admin.bookings.index', ['page_number' => $bookings->currentPage() + 1]) }}"
                        class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800">
                        <span class="material-symbols-outlined !text-lg">chevron_right</span>
                    </a>
                    @else
                    <a href="#"
                        class="p-2 rounded-lg border border-slate-200  disabled:opacity-50 disabled:cursor-not-allowed cursor-not-allowed bg-gray-200"
                        disabled>
                        <span class="material-symbols-outlined !text-lg">chevron_right</span>
                    </a>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
