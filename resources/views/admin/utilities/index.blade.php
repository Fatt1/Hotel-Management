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
        <h1 class="text-3xl font-bold text-slate-900">Quản lý tiện ích</h1>
        <p class="text-slate-500 text-sm mt-1">Hệ thống quản lý khách sạn Urban Luxe - Quản lý danh mục tiện ích khách hàng.</p>
      </div>
      <a href="{{ route('admin.utilities.create') }}" class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 transition-all shadow-lg shadow-blue-900/20 active:scale-95">
        <span class="material-symbols-outlined">add</span>
        Thêm tiện ích mới
      </a>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6 flex items-center gap-4">
      <div class="relative flex-1">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
        <input 
          type="text" 
          id="searchInput" 
          placeholder="Tìm theo tên tiện ích..." 
          class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-900/20 outline-none"
          onkeyup="filterTable()"
        />
      </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">ID</th>
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Biểu tượng</th>
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tên tiện ích</th>
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Hành động</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100" id="tableBody">
            @forelse($utilities as $utility)
            <tr class="hover:bg-slate-50/50 transition-colors group">
              <td class="px-6 py-4">
                <p class="font-bold text-blue-900">{{ $utility->id }}</p>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center">
                  @if($utility->icon)
                    <span class="material-symbols-outlined text-2xl text-slate-600">{{ $utility->icon }}</span>
                  @else
                    <span class="material-symbols-outlined text-2xl text-slate-300">help</span>
                  @endif
                </div>
              </td>
              <td class="px-6 py-4">
                <p class="font-bold text-slate-900">{{ $utility->name }}</p>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2">
                  <a href="{{ route('admin.utilities.edit', $utility->id) }}" class="p-2 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-all" title="Chỉnh sửa">
                    <span class="material-symbols-outlined text-xl">edit</span>
                  </a>
                  <button class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all delete-utility-btn" title="Xóa" data-utility-id="{{ $utility->id }}" data-utility-name="{{ $utility->name }}">
                    <span class="material-symbols-outlined text-xl">delete</span>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="px-6 py-12 text-center">
                <p class="text-slate-500 text-sm">Chưa có tiện ích nào. <a href="{{ route('admin.utilities.create') }}" class="text-blue-900 font-bold hover:underline">Thêm tiện ích mới</a></p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Table Footer with Pagination -->
      <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
        <p class="text-xs text-slate-500 font-medium">
          Hiển thị {{ $utilities->count() }} / {{ $utilities->total() }} tiện ích
        </p>
        <div class="flex items-center gap-2">
          @if ($utilities->onFirstPage())
            <button class="p-2 text-slate-400 rounded transition-all disabled:opacity-50 cursor-not-allowed" disabled>
              <span class="material-symbols-outlined">chevron_left</span>
            </button>
          @else
            <a href="{{ $utilities->previousPageUrl() }}" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded transition-all">
              <span class="material-symbols-outlined">chevron_left</span>
            </a>
          @endif

          @foreach ($utilities->getUrlRange(1, $utilities->lastPage()) as $page => $url)
            @if ($page == $utilities->currentPage())
              <button class="px-3 py-1 bg-blue-900 text-white rounded text-sm font-bold">{{ $page }}</button>
            @else
              <a href="{{ $url }}" class="text-slate-600 text-sm font-bold hover:bg-slate-100 px-3 py-1 rounded">{{ $page }}</a>
            @endif
          @endforeach

          @if ($utilities->hasMorePages())
            <a href="{{ $utilities->nextPageUrl() }}" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded transition-all">
              <span class="material-symbols-outlined">chevron_right</span>
            </a>
          @else
            <button class="p-2 text-slate-400 rounded transition-all disabled:opacity-50 cursor-not-allowed" disabled>
              <span class="material-symbols-outlined">chevron_right</span>
            </button>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Search Filter
  function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('tableBody');
    const rows = table.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
      const cells = rows[i].getElementsByTagName('td');
      let match = false;
      
      for (let j = 0; j < cells.length; j++) {
        if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
          match = true;
          break;
        }
      }
      
      rows[i].style.display = match ? '' : 'none';
    }
  }
</script>

@push('scripts')
  @vite(['resources/js/admin/utilities/index.js'])
@endpush

@endsection
