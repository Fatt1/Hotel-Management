@extends("layouts.admin")
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
  @keyframes slideInRight {
    from {
      transform: translateX(400px);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  @keyframes slideOutRight {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(400px);
      opacity: 0;
    }
  }
</style>
<div class="flex-1 flex flex-col bg-slate-50">
  <!-- Main Container -->
  <div class="flex gap-0 flex-1 min-h-0">
    <!-- Left Sidebar - Room Types -->
    <div class="w-80 bg-white border-r border-slate-200 overflow-hidden flex flex-col">
      <!-- Header -->
      <div class="p-6 pb-4 border-b border-slate-100">
        <h3 class="text-sm font-bold text-slate-600 uppercase tracking-wider mb-4">Loại phòng</h3>
        <div class="flex items-center justify-between">
          <span class="text-xs text-slate-500 font-medium">Tổng số phòng</span>
          <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded text-xs font-bold">{{ $viewModel->totalRooms() }}</span>
        </div>
      </div>

      <!-- Room Types List -->
      <div class="flex-1 overflow-y-auto space-y-1.5 p-4">
        <!-- Tất cả -->
        <a href="{{ route('admin.room-diagrams.edit') }}" class="block group cursor-pointer">
          <div class="p-3 rounded-lg {{ !$viewModel->selectedRoomTypeId() ? 'bg-blue-900 text-white hover:bg-blue-800' : 'bg-slate-50 hover:bg-slate-100 border border-slate-200' }} transition-all">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <p class="font-bold text-sm {{ !$viewModel->selectedRoomTypeId() ? 'text-white' : 'text-slate-900' }}">TẤT CẢ</p>
                <p class="text-xs {{ !$viewModel->selectedRoomTypeId() ? 'text-blue-100' : 'text-slate-500' }} mt-1">Hiển thị tất cả phòng</p>
              </div>
              <span class="{{ !$viewModel->selectedRoomTypeId() ? 'bg-white text-blue-900' : 'bg-slate-200 text-slate-700' }} px-2 py-1 rounded-full text-xs font-bold">{{ $viewModel->totalRooms() }}</span>
            </div>
          </div>
        </a>

        @foreach($viewModel->roomTypes() as $roomType)
        <a href="{{ route('admin.room-diagrams.edit', ['room_type_id' => $roomType->id]) }}" class="block group cursor-pointer">
          <div class="p-3 rounded-lg {{ $viewModel->selectedRoomTypeId() == $roomType->id ? 'bg-blue-900 text-white hover:bg-blue-800' : 'bg-slate-50 hover:bg-slate-100 border border-slate-200' }} transition-all">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <p class="font-bold text-sm {{ $viewModel->selectedRoomTypeId() == $roomType->id ? 'text-white' : 'text-slate-900' }}">{{ $roomType->code }}</p>
                <p class="text-xs {{ $viewModel->selectedRoomTypeId() == $roomType->id ? 'text-blue-100' : 'text-slate-500' }} mt-1">{{ $roomType->name }}</p>
              </div>
              <span class="{{ $viewModel->selectedRoomTypeId() == $roomType->id ? 'bg-white text-blue-900' : 'bg-slate-200 text-slate-700' }} px-2 py-1 rounded-full text-xs font-bold">{{ $roomType->rooms_count }}</span>
            </div>
          </div>
        </a>
        @endforeach
      </div>
    </div>

    <!-- Right Content - Floor Plan -->
    <div class="flex-1 bg-white overflow-hidden flex flex-col">
      <!-- Header -->
      <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-white">
        <div class="flex items-center gap-4">
          @if($viewModel->selectedRoomType())
            <h2 class="text-3xl font-bold text-slate-900">{{ $viewModel->selectedRoomType()->code }}</h2>
            <div class="flex items-center gap-2 text-slate-600">
              <span class="text-sm">{{ $viewModel->selectedRoomType()->name }}</span>
            </div>
          @else
            <h2 class="text-3xl font-bold text-slate-900">TẤT CẢ PHÒNG</h2>
            <div class="flex items-center gap-2 text-slate-600">
              <span class="text-sm">Hiển thị {{ $viewModel->totalRooms() }} phòng trên {{ $viewModel->totalFloors() }} tầng</span>
            </div>
          @endif
        </div>
      </div>

      <!-- Floor Plan -->
      <div class="flex-1 overflow-y-auto p-8 space-y-4 bg-slate-100" id="floorPlanContainer">
        @forelse($viewModel->floors() as $floor)
        <div class="flex items-center gap-6 bg-white p-6 rounded-lg group whitespace-nowrap overflow-x-auto" data-floor-id="{{ $floor->id }}">
          <div class="flex items-center gap-2 flex-shrink-0">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider flex-shrink-0 cursor-pointer hover:bg-slate-200 px-2 py-1 rounded" onclick="editFloor(this, {{ $floor->id }})">{{ $floor->name }}</span>
            <button type="button" onclick="deleteFloor(this, {{ $floor->id }})" class="w-5 h-5 flex items-center justify-center rounded opacity-0 group-hover:opacity-100 hover:bg-red-100 hover:text-red-600 text-slate-400 transition-all flex-shrink-0">
              <span class="material-symbols-outlined text-sm">close</span>
            </button>
          </div>
          <div class="flex items-center gap-2">
            @forelse($floor->rooms as $room)
            <div class="px-4 py-2.5 bg-emerald-100 text-emerald-700 rounded-lg font-bold text-sm min-w-14 text-center group/room cursor-pointer relative flex-shrink-0" 
                 onclick="editRoom(this, {{ $room->id }})" 
                 data-room-id="{{ $room->id }}"
                 data-room-type-id="{{ $room->room_type_id }}"
                 data-status="{{ $room->status }}">
              <span>{{ $room->name }}</span>
              <span class="material-symbols-outlined text-xs absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover/room:opacity-100 transition-all" onclick="event.stopPropagation(); deleteRoom(this.parentElement, {{ $room->id }})">close</span>
            </div>
            @empty
            @endforelse
            <button type="button" onclick="openAddRoomModal(this, {{ $floor->id }}, '{{ $floor->name }}')" class="w-10 h-10 flex items-center justify-center rounded-lg border-2 border-dashed {{ $floor->rooms->count() > 0 ? 'border-slate-300 hover:border-blue-900 hover:text-blue-900 text-slate-300' : 'border-amber-300 text-amber-500 hover:border-amber-400 hover:text-amber-600' }} hover:bg-blue-50 transition-all flex-shrink-0">
              <span class="material-symbols-outlined text-lg">add</span>
            </button>
          </div>
        </div>
        @empty
        <div class="text-center py-16 text-slate-500">
          <span class="material-symbols-outlined text-6xl text-slate-300 mb-4 block">layers</span>
          <p class="text-lg font-medium">Chưa có tầng nào</p>
          <p class="text-sm">Bấm nút bên dưới để thêm tầng mới</p>
        </div>
        @endforelse

        <!-- Add Floor Button -->
        <div class="flex justify-center pt-8" id="addFloorButtonContainer">
          <button type="button" onclick="openAddFloorModal()" class="px-6 py-3 border-2 border-dashed border-slate-300 rounded-lg text-slate-500 hover:text-slate-700 hover:border-slate-400 font-medium text-sm transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined">add</span>
            Thêm tầng mới
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Room Modal -->
<div id="addRoomModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background-color: rgba(0, 0, 0, 0.3);">
  <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b border-slate-200">
      <h3 class="text-lg font-bold text-slate-900" id="addRoomModalTitle">Thêm phòng mới</h3>
      <button type="button" onclick="closeAddRoomModal()" class="text-slate-400 hover:text-slate-600 transition-all">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <!-- Body -->
    <div class="p-6 space-y-4">
      <input type="hidden" id="addRoomFloorId" value="">
      <!-- Số phòng -->
      <div>
        <label class="text-sm font-bold text-slate-600 block mb-2">Số phòng <span class="text-red-500">*</span></label>
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-slate-400">apartment</span>
          <input type="text" id="roomNumber" placeholder="304" class="flex-1 px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900">
        </div>
        <p class="text-xs text-slate-500 mt-1">Mã số phòng là duy nhất trong hệ thống.</p>
      </div>

      <!-- Loại phòng -->
      <div>
        <label class="text-sm font-bold text-slate-600 block mb-2">Loại phòng <span class="text-red-500">*</span></label>
        <select id="roomTypeSelect" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 bg-white">
          <option value="">-- Chọn loại phòng --</option>
          @foreach($viewModel->roomTypes() as $roomType)
          <option value="{{ $roomType->id }}">{{ $roomType->name }} ({{ $roomType->code }})</option>
          @endforeach
        </select>
      </div>
    </div>

    <!-- Footer -->
    <div class="flex gap-3 p-6 border-t border-slate-200">
      <button type="button" onclick="closeAddRoomModal()" class="flex-1 px-4 py-2 border border-slate-300 rounded-lg font-bold text-slate-600 hover:bg-slate-50 transition-all">
        Hủy
      </button>
      <button type="button" onclick="confirmAddRoom()" class="flex-1 px-4 py-2 bg-blue-900 hover:bg-blue-800 text-white rounded-lg font-bold transition-all">
        Thêm phòng
      </button>
    </div>
  </div>
</div>

<!-- Edit Floor Modal -->
<div id="editFloorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background-color: rgba(0, 0, 0, 0.3);">
  <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b border-slate-200">
      <h3 class="text-lg font-bold text-slate-900">Chỉnh sửa tên tầng</h3>
      <button type="button" onclick="closeEditFloorModal()" class="text-slate-400 hover:text-slate-600 transition-all">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <!-- Body -->
    <div class="p-6 space-y-4">
      <input type="hidden" id="editFloorId" value="">
      <!-- Tên tầng -->
      <div>
        <label class="text-sm font-bold text-slate-600 block mb-2">Tên tầng <span class="text-red-500">*</span></label>
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-slate-400">layers</span>
          <input type="text" id="editFloorNameInput" placeholder="Tầng 1" class="flex-1 px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900" onkeypress="if(event.key==='Enter') confirmEditFloor()">
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="flex gap-3 p-6 border-t border-slate-200">
      <button type="button" onclick="closeEditFloorModal()" class="flex-1 px-4 py-2 border border-slate-300 rounded-lg font-bold text-slate-600 hover:bg-slate-50 transition-all">
        Hủy
      </button>
      <button type="button" onclick="confirmEditFloor()" class="flex-1 px-4 py-2 bg-blue-900 hover:bg-blue-800 text-white rounded-lg font-bold transition-all flex items-center justify-center gap-2">
        <span class="material-symbols-outlined text-sm">check</span>
        Lưu
      </button>
    </div>
  </div>
</div>

<!-- Edit Room Modal -->
<div id="editRoomModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background-color: rgba(0, 0, 0, 0.3);">
  <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b border-slate-200">
      <h3 class="text-lg font-bold text-slate-900">Chỉnh sửa phòng</h3>
      <button type="button" onclick="closeEditRoomModal()" class="text-slate-400 hover:text-slate-600 transition-all">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <!-- Body -->
    <div class="p-6 space-y-4">
      <input type="hidden" id="editRoomId" value="">
      <!-- Số phòng -->
      <div>
        <label class="text-sm font-bold text-slate-600 block mb-2">Số phòng <span class="text-red-500">*</span></label>
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-slate-400">apartment</span>
          <input type="text" id="editRoomNumber" placeholder="304" class="flex-1 px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900">
        </div>
      </div>

      <!-- Loại phòng -->
      <div>
        <label class="text-sm font-bold text-slate-600 block mb-2">Loại phòng <span class="text-red-500">*</span></label>
        <select id="editRoomTypeSelect" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 bg-white">
          <option value="">-- Chọn loại phòng --</option>
          @foreach($viewModel->roomTypes() as $roomType)
          <option value="{{ $roomType->id }}">{{ $roomType->name }} ({{ $roomType->code }})</option>
          @endforeach
        </select>
      </div>
    </div>

    <!-- Footer -->
    <div class="flex gap-3 p-6 border-t border-slate-200">
      <button type="button" onclick="closeEditRoomModal()" class="flex-1 px-4 py-2 border border-slate-300 rounded-lg font-bold text-slate-600 hover:bg-slate-50 transition-all">
        Hủy
      </button>
      <button type="button" onclick="confirmEditRoom()" class="flex-1 px-4 py-2 bg-blue-900 hover:bg-blue-800 text-white rounded-lg font-bold transition-all flex items-center justify-center gap-2">
        <span class="material-symbols-outlined text-sm">check</span>
        Lưu thay đổi
      </button>
    </div>
  </div>
</div>

<!-- Add Floor Modal -->
<div id="addFloorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background-color: rgba(0, 0, 0, 0.3);">
  <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b border-slate-200">
      <h3 class="text-lg font-bold text-slate-900">Thêm tầng mới</h3>
      <button type="button" onclick="closeAddFloorModal()" class="text-slate-400 hover:text-slate-600 transition-all">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <!-- Body -->
    <div class="p-6 space-y-4">
      <!-- Tên tầng -->
      <div>
        <label class="text-sm font-bold text-slate-600 block mb-2">Tên tầng <span class="text-red-500">*</span></label>
        <input type="text" id="floorName" placeholder="Ví dụ: Tầng 6, Tầng Thượng,..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900" onkeypress="if(event.key==='Enter') confirmAddFloor()">
      </div>
    </div>

    <!-- Footer -->
    <div class="flex gap-3 p-6 border-t border-slate-200">
      <button type="button" onclick="closeAddFloorModal()" class="flex-1 px-4 py-2 border border-slate-300 rounded-lg font-bold text-slate-600 hover:bg-slate-50 transition-all">
        Hủy
      </button>
      <button type="button" onclick="confirmAddFloor()" class="flex-1 px-4 py-2 bg-blue-900 hover:bg-blue-800 text-white rounded-lg font-bold transition-all flex items-center justify-center gap-2">
        <span class="material-symbols-outlined text-sm">check</span>
        Xác nhận thêm
      </button>
    </div>
  </div>
</div>

<script>
  // CSRF Token for AJAX requests
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // API URLs
  const API = {
    floors: {
      store: '{{ route("admin.floors.store") }}',
      update: (id) => `/admin/floors/${id}`,
      destroy: (id) => `/admin/floors/${id}`,
    },
    rooms: {
      store: '{{ route("admin.rooms.store") }}',
      update: (id) => `/admin/rooms/${id}`,
      destroy: (id) => `/admin/rooms/${id}`,
    }
  };

  // Variables để lưu trữ state
  let floorCount = {{ $viewModel->totalFloors() }};
  let currentFloorId = null;
  let currentFloorName = null;
  let currentEditingFloorId = null;
  let currentEditingFloorSpan = null;

  // ============ ADD ROOM ============
  function openAddRoomModal(buttonElement, floorId, floorName) {
    currentFloorId = floorId;
    currentFloorName = floorName;
    
    document.getElementById('addRoomModalTitle').textContent = `Thêm phòng mới – ${floorName}`;
    document.getElementById('addRoomFloorId').value = floorId;
    document.getElementById('roomNumber').value = '';
    document.getElementById('roomTypeSelect').value = '';
    
    document.getElementById('addRoomModal').classList.remove('hidden');
    document.getElementById('roomNumber').focus();
  }

  function closeAddRoomModal() {
    document.getElementById('addRoomModal').classList.add('hidden');
    document.getElementById('roomNumber').value = '';
    document.getElementById('roomTypeSelect').value = '';
    currentFloorId = null;
    currentFloorName = null;
  }

  async function confirmAddRoom() {
    const roomName = document.getElementById('roomNumber').value.trim();
    const roomTypeId = document.getElementById('roomTypeSelect').value;
    const floorId = document.getElementById('addRoomFloorId').value;
    
    if (!roomName || !roomTypeId) {
      alert('Vui lòng nhập đầy đủ thông tin!');
      return;
    }
    
    try {
      const response = await fetch(API.rooms.store, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          name: roomName,
          floor_id: parseInt(floorId),
          room_type_id: parseInt(roomTypeId),
        }),
      });
      
      const result = await response.json();
      
      if (result.success) {
        // Thêm phòng vào DOM
        const floorRow = document.querySelector(`[data-floor-id="${floorId}"]`);
        if (floorRow) {
          const roomsContainer = floorRow.querySelector('.flex.items-center.gap-2:last-child');
          const addButton = roomsContainer.querySelector('button[onclick*="openAddRoomModal"]');
          
          const newRoom = document.createElement('div');
          newRoom.className = 'px-4 py-2.5 bg-emerald-100 text-emerald-700 rounded-lg font-bold text-sm min-w-14 text-center group/room cursor-pointer relative flex-shrink-0';
          newRoom.setAttribute('data-room-id', result.data.id);
          newRoom.setAttribute('data-room-type-id', result.data.room_type_id);
          newRoom.setAttribute('data-status', result.data.status);
          newRoom.setAttribute('onclick', `editRoom(this, ${result.data.id})`);
          newRoom.innerHTML = `
            <span>${result.data.name}</span>
            <span class="material-symbols-outlined text-xs absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover/room:opacity-100 transition-all" onclick="event.stopPropagation(); deleteRoom(this.parentElement, ${result.data.id})">close</span>
          `;
          
          roomsContainer.insertBefore(newRoom, addButton);
        }
        
        showNotification('success', result.message);
        closeAddRoomModal();
      } else {
        alert(result.message || 'Có lỗi xảy ra!');
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Có lỗi xảy ra khi thêm phòng!');
    }
  }

  // ============ DELETE ROOM ============
  async function deleteRoom(roomElement, roomId) {
    if (!confirm('Bạn có chắc chắn muốn xóa phòng này?')) {
      return;
    }
    
    try {
      const response = await fetch(API.rooms.destroy(roomId), {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
      });
      
      const result = await response.json();
      
      if (result.success) {
        roomElement.remove();
        showNotification('success', result.message);
      } else {
        alert(result.message || 'Có lỗi xảy ra!');
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Có lỗi xảy ra khi xóa phòng!');
    }
  }

  // ============ EDIT ROOM ============
  let currentEditingRoomElement = null;

  function editRoom(roomElement, roomId) {
    currentEditingRoomElement = roomElement;
    const roomName = roomElement.querySelector('span:first-child').textContent.trim();
    const roomTypeId = roomElement.getAttribute('data-room-type-id');
    
    document.getElementById('editRoomId').value = roomId;
    document.getElementById('editRoomNumber').value = roomName;
    document.getElementById('editRoomTypeSelect').value = roomTypeId;
    
    document.getElementById('editRoomModal').classList.remove('hidden');
    document.getElementById('editRoomNumber').focus();
  }

  function closeEditRoomModal() {
    document.getElementById('editRoomModal').classList.add('hidden');
    currentEditingRoomElement = null;
    document.getElementById('editRoomId').value = '';
    document.getElementById('editRoomNumber').value = '';
    document.getElementById('editRoomTypeSelect').value = '';
  }

  async function confirmEditRoom() {
    const roomId = document.getElementById('editRoomId').value;
    const roomName = document.getElementById('editRoomNumber').value.trim();
    const roomTypeId = document.getElementById('editRoomTypeSelect').value;
    
    if (!roomName || !roomTypeId) {
      alert('Vui lòng nhập đầy đủ thông tin!');
      return;
    }
    
    try {
      const response = await fetch(API.rooms.update(roomId), {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          name: roomName,
          room_type_id: parseInt(roomTypeId),
        }),
      });
      
      const result = await response.json();
      
      if (result.success) {
        // Cập nhật DOM
        if (currentEditingRoomElement) {
          currentEditingRoomElement.querySelector('span:first-child').textContent = roomName;
          currentEditingRoomElement.setAttribute('data-room-type-id', roomTypeId);
        }
        
        showNotification('success', result.message);
        closeEditRoomModal();
      } else {
        alert(result.message || 'Có lỗi xảy ra!');
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Có lỗi xảy ra khi cập nhật phòng!');
    }
  }

  // ============ ADD FLOOR ============
  function openAddFloorModal() {
    const nextFloorNumber = floorCount + 1;
    const suggestedName = `Tầng ${nextFloorNumber}`;
    document.getElementById('floorName').value = suggestedName;
    document.getElementById('floorName').focus();
    document.getElementById('addFloorModal').classList.remove('hidden');
  }

  function closeAddFloorModal() {
    document.getElementById('addFloorModal').classList.add('hidden');
    document.getElementById('floorName').value = '';
  }

  async function confirmAddFloor() {
    const floorName = document.getElementById('floorName').value.trim();
    
    if (!floorName) {
      alert('Vui lòng nhập tên tầng!');
      return;
    }
    
    try {
      const response = await fetch(API.floors.store, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ name: floorName }),
      });
      
      const result = await response.json();
      
      if (result.success) {
        floorCount++;
        
        // Tạo element tầng mới
        const newFloor = document.createElement('div');
        newFloor.className = 'flex items-center gap-6 bg-white p-6 rounded-lg group whitespace-nowrap overflow-x-auto';
        newFloor.setAttribute('data-floor-id', result.data.id);
        newFloor.innerHTML = `
          <div class="flex items-center gap-2 flex-shrink-0">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider flex-shrink-0 cursor-pointer hover:bg-slate-200 px-2 py-1 rounded" onclick="editFloor(this, ${result.data.id})">${result.data.name}</span>
            <button type="button" onclick="deleteFloor(this, ${result.data.id})" class="w-5 h-5 flex items-center justify-center rounded opacity-0 group-hover:opacity-100 hover:bg-red-100 hover:text-red-600 text-slate-400 transition-all flex-shrink-0">
              <span class="material-symbols-outlined text-sm">close</span>
            </button>
          </div>
          <div class="flex items-center gap-2">
            <button type="button" onclick="openAddRoomModal(this, ${result.data.id}, '${result.data.name}')" class="w-10 h-10 flex items-center justify-center rounded-lg border-2 border-dashed border-amber-300 text-amber-500 hover:border-amber-400 hover:text-amber-600 hover:bg-amber-50 transition-all flex-shrink-0">
              <span class="material-symbols-outlined text-lg">add</span>
            </button>
          </div>
        `;
        
        const addFloorButtonContainer = document.getElementById('addFloorButtonContainer');
        addFloorButtonContainer.parentElement.insertBefore(newFloor, addFloorButtonContainer);
        
        showNotification('success', result.message);
        closeAddFloorModal();
      } else {
        alert(result.message || 'Có lỗi xảy ra!');
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Có lỗi xảy ra khi thêm tầng!');
    }
  }

  // ============ EDIT FLOOR ============
  function editFloor(floorNameSpan, floorId) {
    currentEditingFloorId = floorId;
    currentEditingFloorSpan = floorNameSpan;
    
    document.getElementById('editFloorId').value = floorId;
    document.getElementById('editFloorNameInput').value = floorNameSpan.textContent.trim();
    document.getElementById('editFloorModal').classList.remove('hidden');
    document.getElementById('editFloorNameInput').focus();
  }

  function closeEditFloorModal() {
    document.getElementById('editFloorModal').classList.add('hidden');
    currentEditingFloorId = null;
    currentEditingFloorSpan = null;
    document.getElementById('editFloorNameInput').value = '';
  }

  async function confirmEditFloor() {
    const newFloorName = document.getElementById('editFloorNameInput').value.trim();
    const floorId = document.getElementById('editFloorId').value;
    
    if (!newFloorName) {
      alert('Vui lòng nhập tên tầng!');
      return;
    }
    
    try {
      const response = await fetch(API.floors.update(floorId), {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ name: newFloorName }),
      });
      
      const result = await response.json();
      
      if (result.success) {
        if (currentEditingFloorSpan) {
          currentEditingFloorSpan.textContent = newFloorName;
        }
        showNotification('success', result.message);
        closeEditFloorModal();
      } else {
        alert(result.message || 'Có lỗi xảy ra!');
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Có lỗi xảy ra khi cập nhật tầng!');
    }
  }

  // ============ DELETE FLOOR ============
  async function deleteFloor(deleteButton, floorId) {
    const floorRow = deleteButton.closest('[data-floor-id]');
    if (!floorRow) return;
    
    // Check if floor has any rooms
    const rooms = floorRow.querySelectorAll('[data-room-id]');
    
    if (rooms.length > 0) {
      alert('Vui lòng xóa tất cả các phòng trước khi xóa tầng!');
      return;
    }
    
    if (!confirm('Bạn có chắc chắn muốn xóa tầng này?')) {
      return;
    }
    
    try {
      const response = await fetch(API.floors.destroy(floorId), {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
      });
      
      const result = await response.json();
      
      if (result.success) {
        floorCount--;
        floorRow.remove();
        showNotification('success', result.message);
      } else {
        alert(result.message || 'Có lỗi xảy ra!');
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Có lỗi xảy ra khi xóa tầng!');
    }
  }

  // ============ NOTIFICATION ============
  function showNotification(type, message) {
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const icon = type === 'success' ? 'check_circle' : 'error';
    
    const toast = document.createElement('div');
    toast.className = `fixed bottom-8 right-8 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 z-50`;
    toast.innerHTML = `
      <span class="material-symbols-outlined">${icon}</span>
      <span>${message}</span>
    `;
    
    document.body.appendChild(toast);
    toast.style.animation = 'slideInRight 0.3s ease-out';
    
    setTimeout(() => {
      toast.style.animation = 'slideOutRight 0.3s ease-in';
      setTimeout(() => {
        toast.remove();
      }, 300);
    }, 3000);
  }
</script>
@endsection
