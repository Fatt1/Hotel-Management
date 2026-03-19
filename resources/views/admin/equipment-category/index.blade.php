@extends("layouts.admin")
@section('content')

<div class="flex-1 flex flex-col">
  <!-- Page Content -->
  <div class="p-8 max-w-7xl mx-auto w-full">
    <!-- Header -->
    <div class="flex items-end justify-between mb-8">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">Quản lý nhóm thiết bị</h2>
        <p class="text-slate-500 text-sm mt-1">Hệ thống quản lý khách sạn Urban Luxe - Cấu hình danh mục trang bị.</p>
      </div>
      <button onclick="openCreateModal()" class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 transition-all shadow-lg shadow-blue-900/20 active:scale-95">
        <span class="material-symbols-outlined">add</span>
        Thêm nhóm mới
      </button>
    </div>

    <!-- Search Filter -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6 flex items-center gap-4">
      <form action="{{ route('admin.equipment-categories.index') }}" method="GET" class="relative flex-1">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
        <input 
          type="text" 
          name="search"
          value="{{ request('search') }}"
          placeholder="Tìm theo tên nhóm..." 
          class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-900/20 outline-none"
        />
      </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mã nhóm (ID)</th>
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tên nhóm thiết bị (Name)</th>
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Hành động</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100" id="tableBody">
            @forelse($equipmentCategories as $category)
            <tr class="hover:bg-slate-50/50 transition-colors group">
              <td class="px-6 py-4">
                <p class="font-bold text-blue-900">CAT-{{ str_pad($category->id, 3, '0', STR_PAD_LEFT) }}</p>
              </td>
              <td class="px-6 py-4">
                <p class="font-bold text-slate-900">{{ $category->name }}</p>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-3">
                  <button class="edit-category-btn p-2 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-all" title="Chỉnh sửa" data-category-id="{{ $category->id }}" data-category-name="{{ $category->name }}">
                    <span class="material-symbols-outlined text-xl">edit</span>
                  </button>
                  <button class="delete-category-btn p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Xóa" data-category-id="{{ $category->id }}" data-category-name="{{ $category->name }}">
                    <span class="material-symbols-outlined text-xl">delete</span>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="px-6 py-12 text-center">
                <p class="text-slate-500 text-sm">Chưa có nhóm thiết bị nào. <a href="javascript:openCreateModal()" class="text-blue-900 font-bold hover:underline">Thêm nhóm mới</a></p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{ $equipmentCategories->withQueryString()->links('vendor.pagination.custom') }}
    </div>
  </div>
</div>

<script>

  function openDeleteModal(url, categoryName) {
    // Logic in resources/js/admin/equipment-categories/index.js
  }
</script>

@endsection
@push('scripts')
  @vite(['resources/js/admin/equipment-categories/index.js'])
@endpush

