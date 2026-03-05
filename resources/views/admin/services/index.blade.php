@extends("layouts.admin")
@section('content')

<div class="flex-1 flex flex-col">
  <div class="p-8 max-w-7xl mx-auto w-full">
    <!-- Header -->
    <div class="flex items-end justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-slate-900">Quản lý dịch vụ</h1>
        <p class="text-slate-500 text-sm mt-1">Hệ thống quản trị khách sạn Urban Luxe - Quản lý danh mục dịch vụ dựa trên mô hình dữ liệu.</p>
      </div>
      <button onclick="openCreateServiceModal()" class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 transition-all shadow-lg shadow-blue-900/20 active:scale-95">
        <span class="material-symbols-outlined">add</span>
        Thêm dịch vụ mới
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

    <!-- Search & Filter -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6 flex items-center gap-4">
      <div class="relative flex-1">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
        <input
          type="text"
          id="searchInput"
          placeholder="Tìm tên dịch vụ..."
          class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-900/20 outline-none"
          onkeyup="filterTable()"
        />
      </div>
      <select
        id="groupFilter"
        class="px-4 py-2 border border-slate-200 rounded-lg text-sm bg-white text-slate-600"
        onchange="filterByGroup(this)"
      >
        <option value="">Nhóm dịch vụ (Tất cả)</option>
        @foreach($viewModel->serviceGroups() as $group)
          <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
            {{ $group->service_name }}
          </option>
        @endforeach
      </select>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mã dịch vụ (ID)</th>
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tên dịch vụ (Name)</th>
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nhóm dịch vụ (ServiceName)</th>
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Đơn giá (UnitPrice)</th>
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Đơn vị tính (Unit)</th>
              <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Thao tác</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100" id="tableBody">
            @forelse($services as $service)
            <tr class="hover:bg-slate-50/50 transition-colors group">
              <td class="px-6 py-4">
                <p class="font-bold text-blue-900">SRV-{{ str_pad($service->id, 4, '0', STR_PAD_LEFT) }}</p>
              </td>
              <td class="px-6 py-4">
                <p class="font-bold text-slate-900">{{ $service->name }}</p>
              </td>
              <td class="px-6 py-4">
                <span class="inline-block px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-medium">
                  {{ $service->group->service_name ?? 'N/A' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <p class="font-bold text-slate-900">{{ number_format($service->unit_price, 0, '.', '.') }} VND</p>
              </td>
              <td class="px-6 py-4">
                <p class="text-slate-700">{{ $service->unit }}</p>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-3">
                  <button
                    class="edit-service-btn p-2 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-all"
                    title="Chỉnh sửa"
                    data-service-id="{{ $service->id }}"
                    data-service-name="{{ $service->name }}"
                    data-service-group-id="{{ $service->group_id }}"
                    data-service-unit-price="{{ $service->unit_price }}"
                    data-service-unit="{{ $service->unit }}"
                  >
                    <span class="material-symbols-outlined text-xl">edit</span>
                  </button>
                  <button
                    class="delete-service-btn p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"
                    title="Xóa"
                    data-service-id="{{ $service->id }}"
                    data-service-name="{{ $service->name }}"
                  >
                    <span class="material-symbols-outlined text-xl">delete</span>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="px-6 py-12 text-center">
                <p class="text-slate-500 text-sm">Chưa có dịch vụ nào. <a href="javascript:openCreateServiceModal()" class="text-blue-900 font-bold hover:underline">Thêm dịch vụ mới</a></p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Table Footer with Pagination -->
      <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
        <p class="text-xs text-slate-500 font-medium">
          Hiển thị {{ $services->count() }} trên {{ $services->total() }} dịch vụ
        </p>
        <div class="flex items-center gap-2">
          @if ($services->onFirstPage())
            <button class="p-2 text-slate-400 rounded transition-all disabled:opacity-50 cursor-not-allowed" disabled>
              <span class="material-symbols-outlined">chevron_left</span>
            </button>
          @else
            <a href="{{ $services->previousPageUrl() }}" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded transition-all">
              <span class="material-symbols-outlined">chevron_left</span>
            </a>
          @endif

          @foreach ($services->getUrlRange(1, $services->lastPage()) as $page => $url)
            @if ($page == $services->currentPage())
              <button class="px-3 py-1 bg-blue-900 text-white rounded text-sm font-bold">{{ $page }}</button>
            @else
              <a href="{{ $url }}" class="text-slate-600 text-sm font-bold hover:bg-slate-100 px-3 py-1 rounded">{{ $page }}</a>
            @endif
          @endforeach

          @if ($services->hasMorePages())
            <a href="{{ $services->nextPageUrl() }}" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded transition-all">
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

{{-- Service Groups JSON for JS dropdown --}}
<script>
  window.SERVICE_GROUPS = @json($viewModel->serviceGroups()->map(fn($g) => ['id' => $g->id, 'service_name' => $g->service_name]));

  function filterTable() {
    const filter = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.getElementById('tableBody').getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
      // Chỉ tìm theo cột Tên dịch vụ (index 1)
      const nameCell = rows[i].getElementsByTagName('td')[1];
      rows[i].style.display = (nameCell && nameCell.textContent.toLowerCase().includes(filter)) ? '' : 'none';
    }
  }

  function filterByGroup(select) {
    const groupId = select.value;
    const url = new URL(window.location.href);
    if (groupId) {
      url.searchParams.set('group_id', groupId);
    } else {
      url.searchParams.delete('group_id');
    }
    window.location.href = url.toString();
  }

  function openCreateServiceModal() {
    // Logic in resources/js/admin/services/index.js
  }
</script>

@endsection
@push('scripts')
  @vite(['resources/js/admin/services/index.js'])
@endpush
