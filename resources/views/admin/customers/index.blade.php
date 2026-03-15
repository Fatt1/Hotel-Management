@extends('layouts.admin')

@section('content')
    <div class="p-8 space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Quản lý khách hàng</h1>
                <p class="text-slate-500 font-medium">Hệ thống quản trị khách sạn Urban Luxe - Quản lý danh sách khách hàng
                    chuyên nghiệp.</p>
            </div>
            @can('customers.create')
                <a href="{{ route('admin.customers.create') }}"
                    class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl text-sm font-bold shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all hover:-translate-y-0.5">
                    <span class="material-symbols-outlined">person_add</span>
                    Thêm khách hàng mới
                </a>
            @endcan
        </div>

        {{-- Flash data cho JS đọc --}}
        @if (session('success'))
            <div id="flash-success" data-message="{{ session('success') }}" class="hidden"></div>
        @endif
        @if (session('error'))
            <div id="flash-error" data-message="{{ session('error') }}" class="hidden"></div>
        @endif

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            {{-- Search & Filter --}}
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row gap-4 justify-between items-center">
                <form action="{{ route('admin.customers.index') }}" method="GET" id="filter-form"
                    class="flex items-center gap-3 w-full">

                    {{-- Search - bên trái --}}
                    <div class="relative w-full sm:w-80">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <span class="material-symbols-outlined !text-lg">search</span>
                        </span>
                        <input name="search" value="{{ request('search') }}"
                            class="block w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none"
                            placeholder="Tìm theo Tên/Email/SĐT..." type="text" />
                    </div>

                    {{-- Dropdown + Sort - bên phải --}}
                    <div class="flex items-center gap-2 ml-auto">
                        @include('admin.customers._country_picker', [
                            'selectedValue' => request('country', ''),
                            'formId' => 'filter-form',
                            'placeholder' => 'Quốc gia (Tất cả)',
                            'autoWidth' => true,
                            'pickerCountries' => $countries,
                        ])

                        {{-- Nút sort --}}
                        <button type="button" id="sort-btn" title="Sắp xếp"
                            class="p-2 border border-slate-200 bg-slate-50 rounded-xl text-slate-400 hover:text-primary hover:border-primary/30 transition-colors">
                            <span class="material-symbols-outlined !text-lg">sort</span>
                        </button>
                    </div>

                    {{-- Hidden sort fields --}}
                    <input type="hidden" name="sort_by" value="{{ request('sort_by', 'id') }}" id="sort-by-input" />
                    <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}" id="sort-dir-input" />

                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="table-header">ID</th>
                            <th class="table-header">Họ</th>
                            <th class="table-header">Tên</th>
                            <th class="table-header">Số điện thoại</th>
                            <th class="table-header">Quốc gia</th>
                            <th class="table-header">Email</th>
                            <th class="table-header text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="table-cell font-bold text-primary">
                                    CUS-{{ str_pad($customer->id + 1000, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="table-cell">{{ $customer->last_name }}</td>
                                <td class="table-cell font-semibold text-slate-900">{{ $customer->first_name }}</td>
                                <td class="table-cell">{{ $customer->phone_number }}</td>
                                <td class="table-cell">{{ $customer->country }}</td>
                                <td class="table-cell">{{ $customer->email }}</td>
                                <td class="table-cell text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('customers.view')
                                            <a href="{{ route('admin.customers.show', $customer->id) }}"
                                                class="action-btn text-primary hover:bg-blue-50" title="Xem chi tiết">
                                                <span class="material-symbols-outlined">visibility</span>
                                            </a>
                                        @endcan
                                        @can('customers.edit')
                                            <a href="{{ route('admin.customers.edit', $customer->id) }}"
                                                class="action-btn text-amber-500 hover:bg-amber-50" title="Chỉnh sửa">
                                                <span class="material-symbols-outlined">edit</span>
                                            </a>
                                        @endcan
                                        @can('customers.delete')
                                            <button data-customer-id="{{ $customer->id }}"
                                                class="btn-delete-customer action-btn text-rose-500 hover:bg-rose-50"
                                                title="Xóa">
                                                <span class="material-symbols-outlined">delete</span>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-slate-400 text-sm">
                                    Không có khách hàng nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

      
           <div class="p-5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Hiển thị {{ $customers->lastItem() }} trên {{ $customers->total() }} khách hàng</span>
                <div class="mt-4 md:mt-0">
                    {{ $customers->withQueryString()->links('vendor.pagination.custom') }}
                 
                </div>

        </div>

    </div>
@endsection

@push('scripts')
    @vite(['resources/js/admin/customers/index.js'])
@endpush
