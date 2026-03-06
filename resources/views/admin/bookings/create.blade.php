@extends('layouts.admin')
@section('title', 'Tạo booking mới')
@section('content')
    <div class="flex flex-col xl:flex-row gap-6">
        <div class="flex-1 space-y-6">
            {{-- Thông tin khách hàng --}}
            <div
                class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark p-6">
                <div class="flex items-center mb-4">
                    <span class="material-symbols-outlined text-primary dark:text-blue-400 mr-2">person_outline</span>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Thông tin khách hàng
                    </h2>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Email khách
                            hàng</label>
                        <div class="flex gap-2">
                            <input type="email" id="customer-email-input" placeholder="Nhập email khách hàng"
                                class="flex-1 px-3 py-2 text-sm border border-border-light dark:border-border-dark rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary" />
                            <button type="button" id="search-customer-btn"
                                class="flex items-center gap-1 px-4 py-2 text-sm font-medium bg-primary text-white rounded-lg hover:bg-blue-800 transition-colors">
                                <span id="search-customer-icon" class="material-symbols-outlined text-base">search</span>
                                Tìm
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Khách hàng mới sẽ được tự động tạo tài
                            khoản.</p>
                    </div>

                    {{-- Customer info section (shown after search) --}}
                    <div id="customer-info-section" class="hidden">
                        <div id="customer-info-card" class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <div id="customer-status-header" class="flex items-center justify-between gap-2 text-amber-700 mb-3">
                                <div class="flex items-center gap-2">
                                    <span id="customer-status-icon" class="material-symbols-outlined text-base">person_add</span>
                                    <span id="customer-status-text" class="text-xs font-bold uppercase tracking-wider">Khách hàng chưa tồn tại — Nhập thông tin để tạo mới</span>
                                </div>
                                <a id="customer-edit-link" href="#" target="_blank"
                                    class="hidden flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-primary bg-white border border-primary/30 rounded-lg hover:bg-primary/5 transition-colors">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                    Chỉnh sửa
                                </a>
                            </div>
                           
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                {{-- Họ  --}}
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Họ</label>
                                    <input id="nc-first-name" name="customer_first_name" placeholder="Nhập họ"
                                        type="text"
                                        class="w-full px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/30" />
                                    <span id="nc-first-name-error" class="hidden text-xs text-red-500"></span>
                                </div>
                                {{-- Tên  --}}
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Tên</label>
                                    <input id="nc-last-name" name="customer_last_name" placeholder="Nhập tên" type="text"
                                        class="w-full px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/30" />
                                    <span id="nc-last-name-error" class="hidden text-xs text-red-500"></span>
                                </div>
                                {{-- Email  --}}
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Email</label>
                                    <input id="nc-email" name="customer_email" placeholder="Email" type="email"
                                        class="w-full px-3 py-2 text-sm border rounded-lg bg-white text-gray-500" />
                                    <span id="nc-email-error" class="hidden text-xs text-red-500"></span>
                                </div>
                                {{-- Số điện thoại  --}}
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Số điện
                                        thoại</label>
                                    <input id="nc-phone" name="customer_phone" placeholder="VD: 0901234567" type="text"
                                        class="w-full px-3 py-2 text-sm border rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/30" />
                                    <span id="nc-phone-error" class="hidden text-xs text-red-500"></span>
                                </div>
                                {{-- Quốc gia  --}}
                                <div id="nc-country-field" class="flex flex-col gap-1 md:col-span-2">
                                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Quốc gia</label>
                                    @include('admin.customers._country_picker', [
                                        'inputName' => 'customer_country',
                                        'selectedValue' => '',
                                        'pickerCountries' => $countries,
                                    ])
                                    <span id="nc-country-error" class="hidden text-xs text-red-500"></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Thời gian lưu trú --}}
            <div
                class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark p-6">
                <div class="flex items-center mb-4">
                    <span class="material-symbols-outlined text-primary dark:text-blue-400 mr-2">date_range</span>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Thời gian lưu trú
                    </h2>
                </div>
                {{-- Date Range Picker --}}
                <div id="date-range-bar"
                    class="flex items-center gap-2 p-1 border border-border-light dark:border-border-dark rounded-xl bg-white dark:bg-gray-800 shadow-sm">
                    {{-- Check-in display --}}
                    <div
                        class="flex-1 flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <span class="material-symbols-outlined text-primary text-lg flex-shrink-0">flight_land</span>
                        <div class="flex-1">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Nhận phòng</p>
                            <div class="flex items-center gap-2">
                                <p id="checkin-display"
                                    class="text-sm font-semibold text-gray-900 dark:text-white cursor-pointer"
                                    id="checkin-date-trigger">--</p>
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
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Chọn phòng
                        </h2>
                    </div>
                    <button type="button" id="add-room-btn"
                        class="flex items-center text-xs font-medium text-primary dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-3 py-1.5 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors">
                        <span class="material-symbols-outlined text-sm mr-1">add</span>
                        Thêm phòng
                    </button>
                </div>

                {{-- Danh sách phòng đã chọn --}}
                <div id="selectedRoomsList" class="space-y-3">
                    <div
                        class="flex flex-col items-center justify-center py-8 text-gray-400 dark:text-gray-600 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
                        <span class="material-symbols-outlined text-3xl mb-2">bed</span>
                        <p class="text-sm">Chưa có phòng nào được chọn</p>
                    </div>
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
                <div id="paymentDetails" class="space-y-4 mb-6">
                    <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">Chưa có phòng nào được chọn</p>
                </div>
                <div
                    class="bg-gray-50 dark:bg-gray-800/50 -mx-6 px-6 py-4 border-t border-gray-100 dark:border-gray-700 mb-6">
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Tổng cộng</span>
                        <div class="flex items-baseline justify-between">
                            <span id="totalAmount" class="text-2xl font-bold text-gray-900 dark:text-white">0 đ</span>
                        </div>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 text-right italic">Đã bao gồm thuế &amp;
                            phí</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <button type="button" id="confirm-booking-btn"
                        class="w-full bg-primary hover:bg-blue-800 text-white font-medium py-3 rounded-lg shadow-lg shadow-blue-500/30 transition-all transform active:scale-95">
                        Xác nhận đặt phòng
                    </button>
                    <button type="button"
                        class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 font-medium py-3 rounded-lg transition-colors">
                        Hủy bỏ
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @vite(['resources/js/admin/bookings/create-booking.js'])
@endpush
