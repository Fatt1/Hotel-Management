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
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Xem thông tin khách hàng</h1>
        <p class="text-slate-500 font-medium">Thông tin chi tiết và lịch sử đặt phòng</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 space-y-8">

        {{-- Thông tin cá nhân --}}
        <div>
            <h2 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-5">Thông tin cá nhân</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-slate-500">Họ</label>
                    <div class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700">
                        {{ $viewModel->customer()->last_name }}
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-slate-500">Email</label>
                    <div class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700">
                        {{ $viewModel->customer()->email }}
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-slate-500">Tên</label>
                    <div class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700">
                        {{ $viewModel->customer()->first_name }}
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-slate-500">Quốc gia</label>
                    <div class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700">
                        {{ $viewModel->customer()->country }}
                    </div>
                </div>

                <div class="flex flex-col gap-1 md:col-span-2">
                    <label class="text-sm font-semibold text-slate-500">Số điện thoại</label>
                    <div class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700">
                        {{ $viewModel->customer()->phone_number }}
                    </div>
                </div>

            </div>
        </div>

        {{-- Lịch sử đặt phòng --}}
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

        {{-- Buttons --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.customers.index') }}"
                class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-colors">
                Quay lại
            </a>
            @can('customers.edit')
                <a href="{{ route('admin.customers.edit', $viewModel->customer()->id) }}"
                    class="flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined !text-base">edit</span>
                    Chỉnh sửa
                </a>
            @endcan
        </div>

    </div>
</div>
@endsection