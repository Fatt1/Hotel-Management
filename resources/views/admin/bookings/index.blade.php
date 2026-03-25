@extends('layouts.admin')
@section('title', 'Quản lý đặt lịch')
@section('content')
@php
    $status = ["Đã đặt", "Đang ở", "Hoàn tất", "Hủy"];
    $fromDate = request('from_date');
    $toDate = request('to_date');
@endphp
    <div class="p-8 space-y-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Quản lý đặt lịch</h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Hệ thống quản trị khách sạn Urban Luxe - Quản lý
                    đặt lịch khách hàng.</p>
            </div>
           <form action="{{ route('admin.bookings.create') }}">
                <button type="submit"
                    class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl text-sm font-bold shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all hover:-translate-y-0.5">
                    <span class="material-symbols-outlined">add_circle</span>
                    Tạo đặt phòng mới
                </button>
           </form>
        </div>
        <div
            class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div
                class="p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col xl:flex-row gap-4 justify-between items-center">
                <form action="{{ route('admin.bookings.index') }}" method="GET"
                    class="w-full flex flex-col xl:flex-row gap-4 justify-between items-center">
                    <div class="relative w-full xl:w-96">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <span class="material-symbols-outlined text-lg!">search</span>
                        </span>
                        <input name="search" value="{{ request('search') }}"
                            class="block w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all"
                            placeholder="Tìm theo Tên khách / Mã đặt phòng..." type="text" />
                    </div>
                    <div class="relative w-full xl:w-72">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">
                            <span class="material-symbols-outlined text-lg!">date_range</span>
                        </span>
                        <input
                            id="booking-date-range"
                            type="text"
                            class="block w-full pl-10 pr-10 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all"
                            placeholder="Từ ngày - Đến ngày"
                            autocomplete="off"
                            value="{{ $fromDate && $toDate ? \Carbon\Carbon::parse($fromDate)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($toDate)->format('d/m/Y') : '' }}" />
                        <button type="button" id="clear-date-range"
                            class="absolute inset-y-0 right-0 pr-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hidden"
                            title="Xóa ngày lọc">
                            <span class="material-symbols-outlined text-lg!">close</span>
                        </button>
                        <input type="hidden" name="from_date" id="from_date" value="{{ $fromDate }}">
                        <input type="hidden" name="to_date" id="to_date" value="{{ $toDate }}">
                    </div>
                    <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
                        <select name="status" onchange="this.form.submit()"
                            class="block w-full sm:w-48 px-4 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all">
                            <option value="">Tất cả trạng thái</option>
                            @foreach ($status as $st)
                                <option value="{{ $st }}" @selected(request('status') === $st)>{{ $st }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="p-2 text-slate-500 hover:text-primary transition-colors" title="Lọc">
                            <span class="material-symbols-outlined">filter_alt</span>
                        </button>
                    </div>
                </form>
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
                                    'Đã đặt' => 'badge-pending-booking',
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
                                    <a href="{{ route('admin.bookings.print-invoice', $booking->id) }}" target="_blank" rel="noopener"
                                        class="action-btn text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20"
                                        title="In hóa đơn">
                                        <span class="material-symbols-outlined">print</span>
                                    </a>
                                    <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="action-btn text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20"
                                        title="Xem chi tiết">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </a>
                                    <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST" class="cancel-booking-form">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="action-btn text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20"
                                            title="Hủy đặt phòng">
                                            <span class="material-symbols-outlined">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                            
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $bookings->withQueryString()->links('vendor.pagination.custom') }}
        </div>
    </div>
@endsection
@push('scripts')
   @vite(['resources/js/admin/bookings/index.js'])
@endpush
