@extends("layouts.admin")
@section('content')

<div class="p-8 space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Quản lý loại dịch vụ</h1>
            <p class="text-slate-500 font-medium">Trang quản lý nhóm dịch vụ (ServiceGroup) cho hệ thống khách sạn Urban Luxe.</p>
        </div>
        <button onclick="openCreateModal()"
            class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl text-sm font-bold shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all hover:-translate-y-0.5">
            <span class="material-symbols-outlined">add</span>
            Thêm loại dịch vụ
        </button>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div id="flash-success" data-message="{{ session('success') }}" class="hidden"></div>
    @endif
    @if(session('error'))
        <div id="flash-error" data-message="{{ session('error') }}" class="hidden"></div>
    @endif

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Search --}}
        <div class="p-5 border-b border-slate-100">
            <form action="{{ route('admin.service-groups.index') }}" method="GET"
                class="flex items-center gap-3">
                <div class="relative w-full sm:w-96">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <span class="material-symbols-outlined !text-lg">search</span>
                    </span>
                    <input name="search" value="{{ request('search') }}"
                        class="block w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none"
                        placeholder="Tìm tên nhóm dịch vụ..." type="text" />
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-primary text-white rounded-xl text-sm font-bold hover:bg-primary/90 transition-all">
                    Tìm kiếm
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.service-groups.index') }}"
                        class="px-4 py-2 border border-slate-200 rounded-xl text-sm text-slate-600 hover:bg-slate-50 transition-all">
                        Xóa bộ lọc
                    </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="table-header">Mã nhóm (ID)</th>
                        <th class="table-header">Tên nhóm dịch vụ (ServiceName)</th>
                        <th class="table-header text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="tableBody">
                    @forelse($serviceGroups as $group)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="table-cell font-bold text-primary">
                            GRP-{{ str_pad($group->id, 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="table-cell font-semibold text-slate-900">
                            {{ $group->service_name }}
                        </td>
                        <td class="table-cell text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    class="edit-group-btn action-btn text-amber-500 hover:bg-amber-50"
                                    title="Chỉnh sửa"
                                    data-group-id="{{ $group->id }}"
                                    data-group-name="{{ $group->service_name }}"
                                >
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                                <button
                                    class="delete-group-btn action-btn text-rose-500 hover:bg-rose-50"
                                    title="Xóa"
                                    data-group-id="{{ $group->id }}"
                                    data-group-name="{{ $group->service_name }}"
                                >
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-12 text-slate-400 text-sm">
                            @if(request('search'))
                                Không tìm thấy nhóm dịch vụ nào khớp với "<strong>{{ request('search') }}</strong>".
                                <a href="{{ route('admin.service-groups.index') }}" class="text-primary font-bold hover:underline ml-1">Xem tất cả</a>
                            @else
                                Chưa có loại dịch vụ nào.
                                <a href="javascript:void(0)" onclick="openCreateModal()" class="text-primary font-bold hover:underline ml-1">Thêm loại mới</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-5 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs font-medium text-slate-500">
                Hiển thị {{ $serviceGroups->count() }} trên {{ $serviceGroups->total() }} nhóm dịch vụ
            </span>
            <div>
                {{ $serviceGroups->withQueryString()->links('vendor.pagination.custom') }}
            </div>
        </div>

    </div>
</div>

@endsection
@push('scripts')
    @vite(['resources/js/admin/service-groups/index.js'])
@endpush
