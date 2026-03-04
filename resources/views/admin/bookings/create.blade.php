@extends('layouts.admin')
@section('content')
    <div class="flex flex-col xl:flex-row gap-6">
        <div class="flex-1 space-y-6">
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
                            <input
                                class="flex-1 rounded-lg border border-gray-300 bg-white pl-2"
                                placeholder="Nhập email khách hàng" type="email" value="new.guest@gmail.com" />
                            <button
                                class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Xác thực
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Khách hàng mới sẽ được tự động tạo tài
                            khoản.</p>
                    </div>
                </div>
            </div>
                        <div
                class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark p-6">
                <div class="flex items-center mb-4">
                    <span class="material-symbols-outlined text-primary dark:text-blue-400 mr-2">date_range</span>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Thời gian lưu trú
                    </h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Ngày nhận phòng
                            (Check-in)</label>
                        <div class="relative">
                            <input
                                class="w-full pl-10 pr-4 py-2.5 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm focus:border-primary focus:ring focus:ring-primary/20 dark:text-white"
                                placeholder="mm/dd/yyyy" type="text" />
                            <span
                                class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-lg">calendar_today</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Ngày trả phòng
                            (Check-out)</label>
                        <div class="relative">
                            <input
                                class="w-full pl-10 pr-4 py-2.5 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm focus:border-primary focus:ring focus:ring-primary/20 dark:text-white"
                                placeholder="mm/dd/yyyy" type="text" />
                            <span
                                class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-lg">event_busy</span>
                        </div>
                    </div>
                </div>
            </div>
            <div
                class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-border-light dark:border-border-dark p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <span class="material-symbols-outlined text-primary dark:text-blue-400 mr-2">meeting_room</span>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Chọn phòng</h2>
                    </div>
                    <button
                        class="flex items-center text-xs font-medium text-primary dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-3 py-1.5 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors">
                        <span class="material-symbols-outlined text-sm mr-1">add</span>
                        Thêm phòng
                    </button>
                </div>
                <div class="space-y-3">
                    <div
                        class="flex items-center justify-between p-4 border border-border-light dark:border-border-dark rounded-lg bg-white dark:bg-gray-800 hover:border-primary dark:hover:border-blue-500 transition-colors group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-primary dark:text-blue-400 font-bold text-sm">
                                201
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Urban Deluxe Twin</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Tầng 2 - View phố</p>
                            </div>
                        </div>
                        <button
                            class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors opacity-0 group-hover:opacity-100">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                    <div
                        class="flex items-center justify-between p-4 border border-border-light dark:border-border-dark rounded-lg bg-white dark:bg-gray-800 hover:border-primary dark:hover:border-blue-500 transition-colors group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-lg bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400 font-bold text-sm">
                                501
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Executive Suite</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Tầng 5 - View biển</p>
                            </div>
                        </div>
                        <button
                            class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors opacity-0 group-hover:opacity-100">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                </div>
            </div>

      
        </div>
        <div class="w-full xl:w-96 flex-shrink-0">
            <div
                class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-lg border border-border-light dark:border-border-dark p-6 xl:sticky xl:top-6">
                <h2
                    class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">
                    Chi tiết thanh toán</h2>
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Urban Deluxe (x2 đêm)</span>
                        <span class="font-medium text-gray-900 dark:text-white">2,400,000 đ</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Executive Suite (x2 đêm)</span>
                        <span class="font-medium text-gray-900 dark:text-white">4,000,000 đ</span>
                    </div>
                    <div class="border-t border-dashed border-gray-200 dark:border-gray-700 my-4"></div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Tổng tiền phòng</span>
                        <span class="font-bold text-gray-900 dark:text-white">6,400,000 đ</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Phí dịch vụ</span>
                        <span class="font-medium text-gray-900 dark:text-white">30,000 đ</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Thuế VAT (8%)</span>
                        <span class="font-medium text-gray-900 dark:text-white">514,400 đ</span>
                    </div>
                </div>
                <div
                    class="bg-gray-50 dark:bg-gray-800/50 -mx-6 px-6 py-4 border-t border-gray-100 dark:border-gray-700 mb-6">
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Tổng cộng</span>
                        <div class="flex items-baseline justify-between">
                            <span class="text-2xl font-bold text-primary dark:text-blue-400">6,944,400 đ</span>
                        </div>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 text-right italic">Đã bao gồm thuế &amp;
                            phí</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <button
                        class="w-full bg-primary hover:bg-blue-800 text-white font-medium py-3 rounded-lg shadow-lg shadow-blue-500/30 transition-all transform active:scale-95">
                        Xác nhận đặt phòng
                    </button>
                    <button
                        class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 font-medium py-3 rounded-lg transition-colors">
                        Hủy bỏ
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
