@extends("layouts.admin")

@section('content')
<div class="p-8 space-y-6">

    {{-- Back link --}}
    <a href="{{ route('admin.customers.index') }}"
        class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-primary transition-colors">
        <span class="material-symbols-outlined !text-base">arrow_back</span>
        Quay lại danh sách
    </a>

    {{-- Page Header --}}
    <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Chỉnh sửa khách hàng</h1>
        <p class="text-slate-500 font-medium">Cập nhật thông tin chi tiết và lịch sử khách hàng Urban Luxe</p>
    </div>

    {{-- Flash error --}}
    @if(session('error'))
        <div id="flash-error" data-message="{{ session('error') }}" class="hidden"></div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-8">

        {{-- Form thông tin cá nhân --}}
        <div>
            <h2 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-5">Thông tin cá nhân</h2>

            <form action="{{ route('admin.customers.update', $viewModel->customer()->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $viewModel->customer()->id }}" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Họ --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-semibold text-slate-700">Họ <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name"
                            value="{{ old('first_name', $viewModel->customer()->first_name) }}"
                            class="w-full rounded-xl border @error('first_name') border-red-400 @else border-slate-200 @enderror bg-slate-50 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 outline-none" />
                        @error('first_name')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-semibold text-slate-700">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email"
                            value="{{ old('email', $viewModel->customer()->email) }}"
                            class="w-full rounded-xl border @error('email') border-red-400 @else border-slate-200 @enderror bg-slate-50 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 outline-none" />
                        @error('email')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tên --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-semibold text-slate-700">Tên <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name"
                            value="{{ old('last_name', $viewModel->customer()->last_name) }}"
                            class="w-full rounded-xl border @error('last_name') border-red-400 @else border-slate-200 @enderror bg-slate-50 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 outline-none" />
                        @error('last_name')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Quốc gia --}}
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-semibold text-slate-700">Quốc gia <span class="text-red-500">*</span></label>
                        @include('admin.customers._country_picker', ['selectedValue' => old('country', $viewModel->customer()->country)])
                        @error('country')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Số điện thoại --}}
                    <div class="flex flex-col gap-1 md:col-span-2">
                        <label class="text-sm font-semibold text-slate-700">Số điện thoại <span class="text-red-500">*</span></label>
                        <input type="text" name="phone_number"
                            value="{{ old('phone_number', $viewModel->customer()->phone_number) }}"
                            class="w-full rounded-xl border @error('phone_number') border-red-400 @else border-slate-200 @enderror bg-slate-50 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 outline-none" />
                        @error('phone_number')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                {{-- Buttons --}}
                <div class="flex justify-end gap-3 mt-8">
                    <a href="{{ route('admin.customers.index') }}"
                        class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-colors">
                        Hủy
                    </a>
                    <button type="submit"
                        class="flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">
                        <span class="material-symbols-outlined !text-base">save</span>
                        Lưu thông tin
                    </button>
                </div>

            </form>
        </div>

        {{-- Lịch sử đặt phòng (read-only) --}}
        <div>
            <h2 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-5">Lịch sử đặt phòng</h2>
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="table-header">Mã đặt phòng</th>
                            <th class="table-header">Ngày đến</th>
                            <th class="table-header">Ngày đi</th>
                            <th class="table-header text-right">Tiền phòng</th>
                            <th class="table-header text-right">Tiền dịch vụ</th>
                            <th class="table-header text-right">Tổng tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($viewModel->bookingHistory() as $booking)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="table-cell font-bold text-primary">{{ $booking['code'] }}</td>
                                <td class="table-cell">{{ $booking['checkin_date'] }}</td>
                                <td class="table-cell">{{ $booking['checkout_date'] }}</td>
                                <td class="table-cell text-right">{{ number_format($booking['total_room_amount'], 0, ',', '.') }} đ</td>
                                <td class="table-cell text-right">{{ number_format($booking['total_service_amount'], 0, ',', '.') }} đ</td>
                                <td class="table-cell text-right font-bold text-slate-900">{{ number_format($booking['final_amount'], 0, ',', '.') }} đ</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-slate-400 text-sm">
                                    Chưa có lịch sử đặt phòng.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/admin/customers/edit.js'])
@endpush