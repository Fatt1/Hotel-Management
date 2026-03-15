@extends("layouts.admin")
@section('content')
<div class="flex-1 flex flex-col">
  <!-- Page Content -->
  <div class="p-8 max-w-7xl mx-auto w-full">
    <div class="flex items-end justify-between mb-8">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">Quản lý loại phòng</h2>
        <p class="text-slate-500 text-sm mt-1">Hệ thống quản lý khách sạn Urban Luxe - Quản lý danh mục loại phòng và chính sách giá.</p>
      </div>
      <a href="{{ route('admin.room-types.create') }}" class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 transition-all shadow-lg shadow-blue-900/20 active:scale-95">
        <span class="material-symbols-outlined">add</span>
        Thêm loại phòng mới
      </a>
    </div>

    <!-- Filters -->
    <form action="{{ route('admin.room-types.index') }}" method="GET"
          class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6 flex items-center gap-4">
      <div class="relative flex-1">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
        <input
          type="text"
          name="search"
          value="{{ request('search') }}"
          placeholder="Tìm theo tên loại phòng (Room Type)..."
          class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-900/20 outline-none"
        />
      </div>
      <select name="status" onchange="this.form.submit()"
              class="bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 text-sm text-slate-600 outline-none focus:ring-2 focus:ring-blue-900/20">
        <option value="">Tất cả trạng thái</option>
        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang hoạt động</option>
        <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Sắp ra mắt</option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Không hoạt động</option>
      </select>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50/50 border-b border-slate-100">
            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tên loại phòng</th>
            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Diện tích (m2)</th>
            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">SL C CHL A (NL/TE)</th>
            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Giá theo giờ</th>
            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Giá theo ngày</th>
            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Trạng thái</th>
            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Hành động</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($roomTypes as $room)
          <tr class="hover:bg-slate-50/50 transition-colors group">
            <td class="px-6 py-4">
              <div class="flex items-center gap-4">
                {{-- <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-900 transition-colors">
                  <span class="material-symbols-outlined">hotel</span>
                </div> --}}
                <div>
                  <p class="font-bold text-slate-900">{{ $room['name'] }}</p>
                  {{-- <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $room['code'] ?? 'N/A' }}</p> --}}
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center font-medium text-slate-600">{{ number_format($room['width'] * $room['height'], 2, ',', '.') }} m²</td>
            <td class="px-6 py-4 text-center">
              <div class="flex items-center justify-center gap-3 text-slate-600 text-sm font-medium">
                <span class="flex items-center gap-1">
                  <span class="material-symbols-outlined text-slate-400" style="font-size: 14px;">group</span> 
                  {{ $room['adult_quantity'] }}
                </span>
                <span class="flex items-center gap-1">
                  <span class="material-symbols-outlined text-slate-400" style="font-size: 14px;">person</span> 
                  {{ $room['child_quantity'] }}
                </span>
              </div>
            </td>
            <td class="px-6 py-4 text-right font-bold text-blue-900">{{ number_format($room['hourly_price'], 0, ',', '.') }}đ</td>
            <td class="px-6 py-4 text-right font-bold text-blue-900">{{ number_format($room['daily_price'], 0, ',', '.') }}đ</td>
            <td class="px-6 py-4 text-center">
              @php $status = $room['status'] ?? 0; @endphp
              @if($status === 1)
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  Đang hoạt động
                </span>
              @elseif($status === 2)
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                  Sắp ra mắt
                </span>
              @else
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-bold">
                  <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                  Không hoạt động
                </span>
              @endif

              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-2">
                <a href="{{ route('admin.room-types.show', $room['id']) }}" class="p-2 text-slate-400 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-all" title="Xem chi tiết">
                  <span class="material-symbols-outlined">visibility</span>
                </a>
                <a href="{{ route('admin.room-types.edit', $room['id']) }}" class="p-2 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-all" title="Chỉnh sửa">
                  <span class="material-symbols-outlined">edit</span>
                </a>
                <form action="{{ route('admin.room-types.destroy', $room['id']) }}" method="POST" style="display:inline;" onclick="openDeleteModal(event, '{{ route('admin.room-types.destroy', $room['id']) }}', '{{ $room['name'] }}')">
                  @csrf
                  @method('DELETE')
                  <button type="button" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Xóa">
                    <span class="material-symbols-outlined">delete</span>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="px-6 py-8 text-center text-slate-500">
              Không có dữ liệu loại phòng
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
      
      <!-- Pagination Info -->
      <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
        <p class="text-xs text-slate-500 font-medium">
          @if(count($roomTypes) > 0)
            Tổng cộng {{ count($roomTypes) }} loại phòng
          @else
            Không có dữ liệu
          @endif
        </p>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950" style="background-color: rgba(15, 23, 42, 0.1);">
  <div class="bg-white rounded-2xl shadow-lg max-w-md w-full mx-4 overflow-hidden">
    <!-- Content -->
    <div class="p-8 text-center">
      <!-- Icon Warning -->
      <div class="mb-4 flex justify-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
          <span class="material-symbols-outlined text-red-600 text-4xl">warning</span>
        </div>
      </div>

      <!-- Title -->
      <h2 class="text-xl font-bold text-slate-900 mb-3">Xác nhận xóa loại phòng</h2>

      <!-- Description -->
      <p class="text-slate-600 text-sm mb-6 leading-relaxed ">
        Bạn có chắc chắn muốn xóa loại phòng <span id="deleteItemName" class="font-bold text-red-600">này</span> không? Mọi dữ liệu liên quan sẽ bị xóa vĩnh viễn và không thể hoàn tác.
      </p>

      <!-- Action Links -->
      <div class="flex gap-2 mb-8 justify-center text-xs bg-slate-50 rounded-lg p-3">
        <button type="button" onclick="closeDeleteModal()" class="px-3 py-1 text-slate-500 hover:text-slate-700 font-medium transition-colors">
          XÓA/PHÒNG CẤN XÓA
        </button>
        <span class="text-slate-300">•</span>
        <button type="button" onclick="confirmDelete()" class="px-3 py-1 text-blue-600 hover:text-blue-700 font-medium transition-colors">
          PHÒNG ĐỘ PIXI
        </button>
      </div>
    </div>

    <!-- Footer Buttons -->
    <div class="flex gap-3 p-6 bg-slate-50 border-t border-slate-100">
      <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-3 border border-slate-300 rounded-lg font-bold text-slate-600 hover:bg-slate-100 transition-all">
        Hủy
      </button>
      <button type="button" onclick="confirmDelete()" class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold transition-all">
        Xác nhận xóa
      </button>
    </div>
  </div>
</div>

<script>
let deleteFormUrl = '';

function openDeleteModal(event, url, roomName) {
  event.preventDefault();
  deleteFormUrl = url;
  document.getElementById('deleteItemName').textContent = roomName;
  document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
  document.getElementById('deleteModal').classList.add('hidden');
  deleteFormUrl = '';
}

function confirmDelete() {
  if (!deleteFormUrl) return;
  
  // Submit the hidden form
  const hiddenForm = document.getElementById('deleteForm');
  hiddenForm.action = deleteFormUrl;
  hiddenForm.submit();
}

// Close modal when clicking outside
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
  if (e.target === this) {
    closeDeleteModal();
  }
});
</script>

<!-- Hidden Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
  @csrf
  @method('DELETE')
</form>

@endsection
