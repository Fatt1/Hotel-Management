@extends("layouts.admin")
@section('content')

<div class="p-8 space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Quản lý dịch vụ</h1>
            <p class="text-slate-500 font-medium">Hệ thống quản trị khách sạn Urban Luxe - Quản lý danh mục dịch vụ dựa trên mô hình dữ liệu.</p>
        </div>
        <button onclick="openCreateServiceModal()"
            class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl text-sm font-bold shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all hover:-translate-y-0.5">
            <span class="material-symbols-outlined">add</span>
            Thêm dịch vụ mới
        </button>
    </div>

  

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Search & Filter --}}
        <div class="p-5 border-b border-slate-100">
            <form action="{{ route('admin.services.index') }}" method="GET"
                class="flex flex-col sm:flex-row items-center gap-3">

                {{-- Search --}}
                <div class="relative w-full sm:w-96">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <span class="material-symbols-outlined !text-lg">search</span>
                    </span>
                    <input name="search" value="{{ request('search') }}"
                        class="block w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none"
                        placeholder="Tìm tên dịch vụ..." type="text" />
                </div>

                {{-- Group Filter --}}
                <select name="group_id"
                    onchange="this.form.submit()"
                    class="px-4 py-2 border border-slate-200 rounded-xl text-sm bg-white text-slate-600 focus:ring-2 focus:ring-primary/20 outline-none">
                    <option value="">Nhóm dịch vụ (Tất cả)</option>
                    @foreach($viewModel->serviceGroups() as $group)
                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                            {{ $group->service_name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                    class="px-4 py-2 bg-primary text-white rounded-xl text-sm font-bold hover:bg-primary/90 transition-all">
                    Tìm kiếm
                </button>

                @if(request('search') || request('group_id'))
                    <a href="{{ route('admin.services.index') }}"
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
                        <th class="table-header">Mã dịch vụ (ID)</th>
                        <th class="table-header">Tên dịch vụ (Name)</th>
                        <th class="table-header">Nhóm dịch vụ (ServiceName)</th>
                        <th class="table-header">Đơn giá (UnitPrice)</th>
                        <th class="table-header">Đơn vị tính (Unit)</th>
                        <th class="table-header text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($services as $service)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="table-cell font-bold text-primary">
                            SRV-{{ str_pad($service->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="table-cell font-semibold text-slate-900">
                            {{ $service->name }}
                        </td>
                        <td class="table-cell">
                            <span class="inline-block px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-medium">
                                {{ $service->group->service_name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="table-cell font-bold text-slate-900">
                            {{ number_format($service->unit_price, 0, '.', '.') }} VND
                        </td>
                        <td class="table-cell text-slate-700">
                            {{ $service->unit }}
                        </td>
                        <td class="table-cell text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    class="edit-service-btn action-btn text-amber-500 hover:bg-amber-50"
                                    title="Chỉnh sửa"
                                    data-service-id="{{ $service->id }}"
                                    data-service-name="{{ $service->name }}"
                                    data-service-group-id="{{ $service->group_id }}"
                                    data-service-unit-price="{{ $service->unit_price }}"
                                    data-service-unit="{{ $service->unit }}"
                                >
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                                <button
                                    class="delete-service-btn action-btn text-rose-500 hover:bg-rose-50"
                                    title="Xóa"
                                    data-service-id="{{ $service->id }}"
                                    data-service-name="{{ $service->name }}"
                                >
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-slate-400 text-sm">
                            @if(request('search') || request('group_id'))
                                Không tìm thấy dịch vụ nào khớp với bộ lọc hiện tại.
                                <a href="{{ route('admin.services.index') }}" class="text-primary font-bold hover:underline ml-1">Xem tất cả</a>
                            @else
                                Chưa có dịch vụ nào.
                                <a href="javascript:void(0)" onclick="openCreateServiceModal()" class="text-primary font-bold hover:underline ml-1">Thêm dịch vụ mới</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

         {{ $services->withQueryString()->links('vendor.pagination.custom')}}
    </div>
  </div>
</div>

{{-- Inject service groups for JS dropdown --}}
<script>
    window.SERVICE_GROUPS = @json($viewModel->serviceGroups()->map(fn($g) => ['id' => $g->id, 'service_name' => $g->service_name]));
</script>

@endsection
@push('scripts')
    @vite(['resources/js/admin/services/index.js'])
@endpush
