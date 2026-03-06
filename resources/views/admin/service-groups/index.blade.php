@extends("layouts.admin")
@section('content')

<div class="flex-1 flex flex-col">
  <div class="p-8 max-w-7xl mx-auto w-full">
    <!-- Header -->
    <div class="flex items-end justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-slate-900">Quản lý loại dịch vụ</h1>
        <p class="text-slate-500 text-sm mt-1">Trang quản lý nhóm dịch vụ (ServiceGroup) cho hệ thống khách sạn Urban Luxe.</p>
      </div>
      <button onclick="openCreateModal()" class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 transition-all shadow-lg shadow-blue-900/20 active:scale-95">
        <span class="material-symbols-outlined">add</span>
        Thêm loại dịch vụ
      </button>
    </div>

    @if(session('success'))
      <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm font-medium">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm font-medium">
        {{ session('error') }}
      </div>
    @endif

    <!-- Search Filter -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6 flex items-center gap-4">
      <div class="relative flex-1">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
        <input
          type="text"
          id="searchInput"
          placeholder="Tìm tên nhóm dịch vụ..."
          class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-900/20 outline-none"
          onkeyup="filterTable()"
        />
      </div>
      <button disabled class="flex items-center gap-2 px-4 py-2 border border-slate-200 rounded-lg text-sm bg-white text-slate-400 cursor-not-allowed opacity-60">
        <span class="material-symbols-outlined text-sm">filter_list</span>
        Lọc
      </button>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mã nhóm (ID)</th>
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tên nhóm dịch vụ (ServiceName)</th>
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Thao tác</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100" id="tableBody">
            @forelse($serviceGroups as $group)
            <tr class="hover:bg-slate-50/50 transition-colors group">
              <td class="px-6 py-4">
                <p class="font-bold text-blue-900">GRP-{{ str_pad($group->id, 3, '0', STR_PAD_LEFT) }}</p>
              </td>
              <td class="px-6 py-4">
                <p class="font-bold text-slate-900">{{ $group->service_name }}</p>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-3">
                  <button
                    class="edit-group-btn p-2 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-all"
                    title="Chỉnh sửa"
                    data-group-id="{{ $group->id }}"
                    data-group-name="{{ $group->service_name }}"
                  >
                    <span class="material-symbols-outlined text-xl">edit</span>
                  </button>
                  <button
                    class="delete-group-btn p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"
                    title="Xóa"
                    data-group-id="{{ $group->id }}"
                    data-group-name="{{ $group->service_name }}"
                  >
                    <span class="material-symbols-outlined text-xl">delete</span>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="px-6 py-12 text-center">
                <p class="text-slate-500 text-sm">Chưa có loại dịch vụ nào. <a href="javascript:openCreateModal()" class="text-blue-900 font-bold hover:underline">Thêm loại mới</a></p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Table Footer with Pagination -->
      <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
        <p class="text-xs text-slate-500 font-medium">
          Hiển thị {{ $serviceGroups->count() }} trên {{ $serviceGroups->total() }} nhóm dịch vụ
        </p>
        <div class="flex items-center gap-2">
          @if ($serviceGroups->onFirstPage())
            <button class="p-2 text-slate-400 rounded transition-all disabled:opacity-50 cursor-not-allowed" disabled>
              <span class="material-symbols-outlined">chevron_left</span>
            </button>
          @else
            <a href="{{ $serviceGroups->previousPageUrl() }}" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded transition-all">
              <span class="material-symbols-outlined">chevron_left</span>
            </a>
          @endif

          @foreach ($serviceGroups->getUrlRange(1, $serviceGroups->lastPage()) as $page => $url)
            @if ($page == $serviceGroups->currentPage())
              <button class="px-3 py-1 bg-blue-900 text-white rounded text-sm font-bold">{{ $page }}</button>
            @else
              <a href="{{ $url }}" class="text-slate-600 text-sm font-bold hover:bg-slate-100 px-3 py-1 rounded">{{ $page }}</a>
            @endif
          @endforeach

          @if ($serviceGroups->hasMorePages())
            <a href="{{ $serviceGroups->nextPageUrl() }}" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded transition-all">
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
  function filterTable() {
    const filter = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.getElementById('tableBody').getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
      // Chỉ tìm theo cột Tên nhóm dịch vụ (index 1)
      const nameCell = rows[i].getElementsByTagName('td')[1];
      rows[i].style.display = (nameCell && nameCell.textContent.toLowerCase().includes(filter)) ? '' : 'none';
    }
  }

  function openCreateModal() {
    // Logic in resources/js/admin/service-groups/index.js
  }
</script>

@endsection
@push('scripts')
  @vite(['resources/js/admin/service-groups/index.js'])
@endpush
