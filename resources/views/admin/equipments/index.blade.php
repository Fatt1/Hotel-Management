@extends("layouts.admin")
@section('content')

<div class="flex-1 flex flex-col">
  <div class="p-8 max-w-7xl mx-auto w-full">
    <!-- Page Header -->
    <div class="mb-4">
      <a href="{{ route('admin.dashboard') }}" class="text-blue-900 text-sm font-bold flex items-center gap-1 mb-4">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Quay lại tổng quan
      </a>
    </div>

    <!-- Title -->
    <div class="flex items-end justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-slate-900">Quản lý trang thiết bị</h1>
        <p class="text-slate-500 text-sm mt-1">Hệ thống quản lý khách sạn Urban Luxe - Quản lý trang thiết bị dùng trên mô hình dữ liệu.</p>
      </div>
      <a href="{{ route('admin.equipments.create') }}" class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 transition-all shadow-lg shadow-blue-900/20 active:scale-95">
        <span class="material-symbols-outlined">add</span>
        Thêm thiết bị mới
      </a>
    </div>

    <!-- Search & Filter -->
    <form action="{{ route('admin.equipments.index') }}" method="GET"
          class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6 flex items-center gap-4">
      <div class="relative flex-1">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
        <input
          type="text"
          name="search"
          value="{{ request('search') }}"
          placeholder="Tìm theo tên thiết bị..."
          class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-900/20 outline-none"
        />
      </div>
      <select name="category_id" onchange="this.form.submit()"
              class="px-4 py-2 border border-slate-200 rounded-lg text-sm bg-white">
        <option value="">Tất cả phân loại</option>
        @foreach($categories as $cat)
          <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
            {{ $cat->name }}
          </option>
        @endforeach
      </select>
    </form>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mã thiết bị</th>
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tên thiết bị</th>
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Phân loại</th>
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Giá nhập (VNĐ)</th>
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Hành động</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100" id="tableBody">
            @forelse($equipments as $equipment)
            <tr class="hover:bg-slate-50/50 transition-colors group">
              <td class="px-6 py-4">
                <p class="font-bold text-blue-900">EQ-{{ str_pad($equipment->id, 3, '0', STR_PAD_LEFT) }}</p>
              </td>
              <td class="px-6 py-4">
                <p class="font-bold text-slate-900">{{ $equipment->name }}</p>
              </td>
              <td class="px-6 py-4">
                <span class="inline-block px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-medium">
                  {{ $equipment->category->name ?? 'N/A' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <p class="font-bold text-slate-900">{{ number_format($equipment->import_price ?? 0, 0, '.', '.') }}đ</p>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('admin.equipments.edit', $equipment->id) }}" class="p-2 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-all" title="Chỉnh sửa">
                    <span class="material-symbols-outlined text-xl">edit</span>
                  </a>
                  <button class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all delete-equipment-btn" title="Xóa" data-equipment-id="{{ $equipment->id }}" data-equipment-name="{{ $equipment->name }}">
                    <span class="material-symbols-outlined text-xl">delete</span>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="px-6 py-12 text-center">
                <p class="text-slate-500 text-sm">Chưa có thiết bị nào. <a href="{{ route('admin.equipments.create') }}" class="text-blue-900 font-bold hover:underline">Thêm thiết bị mới</a></p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Table Footer with Pagination -->
      <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
        <span class="text-xs font-medium text-slate-500">Hiển thị {{ $equipments->lastItem() ?? 0 }} trên {{ $equipments->total() }} thiết bị</span>
        <div class="mt-5">
          {{ $equipments->withQueryString()->links('vendor.pagination.custom') }}
        </div>
      </div>
    </div>
  </div>
</div>


@push('scripts')
  @vite(['resources/js/admin/equipments/index.js'])
@endpush

@endsection
