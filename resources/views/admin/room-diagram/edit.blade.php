@extends("layouts.admin")
@section('content')
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
          <span class="text-xs text-slate-500 font-medium">Tất cả</span>
          <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded text-xs font-bold">25</span>
        </div>
      </div>

      <!-- Room Types List -->
      <div class="flex-1 overflow-y-auto space-y-1.5 p-4">
        <!-- SUITE -->
        <div class="group cursor-pointer">
          <div class="p-3 rounded-lg bg-blue-900 text-white hover:bg-blue-800 transition-all">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <p class="font-bold text-sm">SUITE</p>
                <p class="text-xs text-blue-100 mt-1">Urban Suite King</p>
              </div>
              <span class="bg-white text-blue-900 px-2 py-1 rounded-full text-xs font-bold">6</span>
            </div>
          </div>
        </div>

        <!-- DELUXE -->
        <div class="group cursor-pointer">
          <div class="p-3 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 transition-all">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <p class="font-bold text-sm text-slate-900">DELUXE</p>
                <p class="text-xs text-slate-500 mt-1">Deluxe Twin City View</p>
              </div>
              <span class="bg-slate-200 text-slate-700 px-2 py-1 rounded-full text-xs font-bold">10</span>
            </div>
          </div>
        </div>

        <!-- STANDARD -->
        <div class="group cursor-pointer">
          <div class="p-3 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 transition-all">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <p class="font-bold text-sm text-slate-900">STANDARD</p>
                <p class="text-xs text-slate-500 mt-1">Standard Double</p>
              </div>
              <span class="bg-slate-200 text-slate-700 px-2 py-1 rounded-full text-xs font-bold">9</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Content - Floor Plan -->
    <div class="flex-1 bg-white overflow-hidden flex flex-col">
      <!-- Header -->
      <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-white">
        <div class="flex items-center gap-4">
          <h2 class="text-3xl font-bold text-slate-900">SUITE</h2>
          <div class="flex items-center gap-2 text-slate-600">
            <span class="text-sm">Urban Suite King</span>
            <button type="button" onclick="showSaveNotification()" class="text-slate-400 hover:text-blue-900 transition-colors">
              <span class="material-symbols-outlined text-lg">save</span>
            </button>
          </div>
        </div>
        <button type="button" class="text-red-600 hover:bg-red-50 p-2 rounded transition-all">
          <span class="material-symbols-outlined text-lg">delete</span>
        </button>
      </div>

      <!-- Floor Plan -->
      <div class="flex-1 overflow-y-auto p-8 space-y-4 bg-slate-100">
        <!-- TẦNG 1 -->
        <div class="flex items-center gap-6 bg-white p-6 rounded-lg group whitespace-nowrap overflow-x-auto">
          <div class="flex items-center gap-2 flex-shrink-0">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider flex-shrink-0 cursor-pointer hover:bg-slate-200 px-2 py-1 rounded" onclick="editFloor(this)">Tầng 1</span>
            <button type="button" onclick="deleteFloor(this)" class="w-5 h-5 flex items-center justify-center rounded opacity-0 group-hover:opacity-100 hover:bg-red-100 hover:text-red-600 text-slate-400 transition-all flex-shrink-0">
              <span class="material-symbols-outlined text-sm">close</span>
            </button>
          </div>
          <div class="flex items-center gap-2">
            <div class="px-4 py-2.5 bg-emerald-100 text-emerald-700 rounded-lg font-bold text-sm min-w-14 text-center group/room cursor-pointer relative flex-shrink-0" onclick="editRoom(this)" data-status="">
              <span>101</span>
              <span class="material-symbols-outlined text-xs absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover/room:opacity-100 transition-all" onclick="event.stopPropagation(); deleteRoom(this.parentElement)">close</span>
            </div>
            <button type="button" onclick="openAddRoomModal(this)" class="w-10 h-10 flex items-center justify-center rounded-lg border-2 border-dashed border-slate-300 hover:border-blue-900 hover:text-blue-900 text-slate-300 hover:bg-blue-50 transition-all flex-shrink-0">
              <span class="material-symbols-outlined text-lg">add</span>
            </button>
          </div>
        </div>

        <!-- TẦNG 2 -->
        <div class="flex items-center gap-6 bg-white p-6 rounded-lg group whitespace-nowrap overflow-x-auto">
          <div class="flex items-center gap-2 flex-shrink-0">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider flex-shrink-0 cursor-pointer hover:bg-slate-200 px-2 py-1 rounded" onclick="editFloor(this)">Tầng 2</span>
            <button type="button" onclick="deleteFloor(this)" class="w-5 h-5 flex items-center justify-center rounded opacity-0 group-hover:opacity-100 hover:bg-red-100 hover:text-red-600 text-slate-400 transition-all flex-shrink-0">
              <span class="material-symbols-outlined text-sm">close</span>
            </button>
          </div>
          <div class="flex items-center gap-2">
            <div class="px-4 py-2.5 bg-emerald-100 text-emerald-700 rounded-lg font-bold text-sm min-w-14 text-center group/room cursor-pointer relative flex-shrink-0" onclick="editRoom(this)" data-status="">
              <span>201</span>
              <span class="material-symbols-outlined text-xs absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover/room:opacity-100 transition-all" onclick="event.stopPropagation(); deleteRoom(this.parentElement)">close</span>
            </div>
            <div class="px-4 py-2.5 bg-emerald-100 text-emerald-700 rounded-lg font-bold text-sm min-w-14 text-center group/room cursor-pointer relative flex-shrink-0" onclick="editRoom(this)" data-status="">
              <span>202</span>
              <span class="material-symbols-outlined text-xs absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover/room:opacity-100 transition-all" onclick="event.stopPropagation(); deleteRoom(this.parentElement)">close</span>
            </div>
            <button type="button" onclick="openAddRoomModal(this)" class="w-10 h-10 flex items-center justify-center rounded-lg border-2 border-dashed border-slate-300 hover:border-blue-900 hover:text-blue-900 text-slate-300 hover:bg-blue-50 transition-all flex-shrink-0">
              <span class="material-symbols-outlined text-lg">add</span>
            </button>
          </div>
        </div>

        <!-- TẦNG 3 -->
        <div class="flex items-center gap-6 bg-white p-6 rounded-lg group whitespace-nowrap overflow-x-auto">
          <div class="flex items-center gap-2 flex-shrink-0">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider flex-shrink-0 cursor-pointer hover:bg-slate-200 px-2 py-1 rounded" onclick="editFloor(this)">Tầng 3</span>
            <button type="button" onclick="deleteFloor(this)" class="w-5 h-5 flex items-center justify-center rounded opacity-0 group-hover:opacity-100 hover:bg-red-100 hover:text-red-600 text-slate-400 transition-all flex-shrink-0">
              <span class="material-symbols-outlined text-sm">close</span>
            </button>
          </div>
          <div class="flex items-center gap-2">
            <div class="px-4 py-2.5 bg-emerald-100 text-emerald-700 rounded-lg font-bold text-sm min-w-14 text-center group/room cursor-pointer relative flex-shrink-0" onclick="editRoom(this)" data-status="">
              <span>301</span>
              <span class="material-symbols-outlined text-xs absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover/room:opacity-100 transition-all" onclick="event.stopPropagation(); deleteRoom(this.parentElement)">close</span>
            </div>
            <div class="px-4 py-2.5 bg-emerald-100 text-emerald-700 rounded-lg font-bold text-sm min-w-14 text-center group/room cursor-pointer relative flex-shrink-0" onclick="editRoom(this)" data-status="">
              <span>302</span>
              <span class="material-symbols-outlined text-xs absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover/room:opacity-100 transition-all" onclick="event.stopPropagation(); deleteRoom(this.parentElement)">close</span>
            </div>
            <div class="px-4 py-2.5 bg-emerald-100 text-emerald-700 rounded-lg font-bold text-sm min-w-14 text-center group/room cursor-pointer relative flex-shrink-0" onclick="editRoom(this)" data-status="">
              <span>303</span>
              <span class="material-symbols-outlined text-xs absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover/room:opacity-100 transition-all" onclick="event.stopPropagation(); deleteRoom(this.parentElement)">close</span>
            </div>
            <button type="button" onclick="openAddRoomModal(this)" class="w-10 h-10 flex items-center justify-center rounded-lg border-2 border-dashed border-slate-300 hover:border-blue-900 hover:text-blue-900 text-slate-300 hover:bg-blue-50 transition-all flex-shrink-0">
              <span class="material-symbols-outlined text-lg">add</span>
            </button>
          </div>
        </div>

        <!-- TẦNG 4 (Empty) -->
        <div class="flex items-center gap-6 bg-white p-6 rounded-lg group whitespace-nowrap overflow-x-auto">
          <div class="flex items-center gap-2 flex-shrink-0">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider flex-shrink-0 cursor-pointer hover:bg-slate-200 px-2 py-1 rounded" onclick="editFloor(this)">Tầng 4</span>
            <button type="button" onclick="deleteFloor(this)" class="w-5 h-5 flex items-center justify-center rounded opacity-0 group-hover:opacity-100 hover:bg-red-100 hover:text-red-600 text-slate-400 transition-all flex-shrink-0">
              <span class="material-symbols-outlined text-sm">close</span>
            </button>
          </div>
          <div class="flex items-center gap-2">
            <button type="button" onclick="openAddRoomModal(this)" class="w-10 h-10 flex items-center justify-center rounded-lg border-2 border-dashed border-amber-300 text-amber-500 hover:border-amber-400 hover:text-amber-600 hover:bg-amber-50 transition-all flex-shrink-0">
              <span class="material-symbols-outlined text-lg">add</span>
            </button>
          </div>
        </div>

        <!-- TẦNG 5 -->
        <div class="flex items-center gap-6 bg-white p-6 rounded-lg group whitespace-nowrap overflow-x-auto">
          <div class="flex items-center gap-2 flex-shrink-0">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider flex-shrink-0 cursor-pointer hover:bg-slate-200 px-2 py-1 rounded" onclick="editFloor(this)">Tầng 5</span>
            <button type="button" onclick="deleteFloor(this)" class="w-5 h-5 flex items-center justify-center rounded opacity-0 group-hover:opacity-100 hover:bg-red-100 hover:text-red-600 text-slate-400 transition-all flex-shrink-0">
              <span class="material-symbols-outlined text-sm">close</span>
            </button>
          </div>
          <div class="flex items-center gap-2">
            <div class="px-4 py-2.5 bg-emerald-100 text-emerald-700 rounded-lg font-bold text-sm min-w-14 text-center group/room cursor-pointer relative flex-shrink-0" onclick="editRoom(this)" data-status="">
              <span>501</span>
              <span class="material-symbols-outlined text-xs absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover/room:opacity-100 transition-all" onclick="event.stopPropagation(); deleteRoom(this.parentElement)">close</span>
            </div>
            <div class="px-4 py-2.5 bg-emerald-100 text-emerald-700 rounded-lg font-bold text-sm min-w-14 text-center group/room cursor-pointer relative flex-shrink-0" onclick="editRoom(this)" data-status="">
              <span>502</span>
              <span class="material-symbols-outlined text-xs absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover/room:opacity-100 transition-all" onclick="event.stopPropagation(); deleteRoom(this.parentElement)">close</span>
            </div>
            <button type="button" onclick="openAddRoomModal(this)" class="w-10 h-10 flex items-center justify-center rounded-lg border-2 border-dashed border-slate-300 hover:border-blue-900 hover:text-blue-900 text-slate-300 hover:bg-blue-50 transition-all flex-shrink-0">
              <span class="material-symbols-outlined text-lg">add</span>
            </button>
          </div>
        </div>

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
      <h3 class="text-lg font-bold text-slate-900">Thêm phòng mới – TẦNG 3</h3>
      <button type="button" onclick="closeAddRoomModal()" class="text-slate-400 hover:text-slate-600 transition-all">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <!-- Body -->
    <div class="p-6 space-y-4">
      <!-- Số phòng -->
      <div>
        <label class="text-sm font-bold text-slate-600 block mb-2">Số phòng</label>
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-slate-400">apartment</span>
          <input type="text" id="roomNumber" placeholder="304" class="flex-1 px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900">
        </div>
        <p class="text-xs text-slate-500 mt-1">Mã số phòng là duy nhất trong hệ thống.</p>
      </div>

      <!-- Loại phòng -->
      <div>
        <label class="text-sm font-bold text-slate-600 block mb-2">Loại phòng</label>
        <select id="roomType" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 bg-white">
          <option value="">-- Chọn loại phòng --</option>
          <option value="suite">Urban Suite King</option>
          <option value="deluxe">Deluxe Twin City View</option>
          <option value="standard">Standard Double</option>
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
        <input type="text" id="floorName" placeholder="Vị dự: Tầng 6, Tầng Thương,..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900" onkeypress="if(event.key==='Enter') confirmAddFloor()">
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
  // Variables để lưu trữ tầng
  let floorCount = 5;
  let currentFloor = null;
  let currentFloorIndex = null;

  function openAddRoomModal(buttonElement) {
    // Lấy floor div từ button element
    const floorRow = buttonElement.closest('.flex.items-center.gap-6.bg-white');
    if (!floorRow) return;
    
    // Lấy tên tầng
    const floorLabel = floorRow.querySelector('.text-xs.font-bold.text-slate-500').textContent.trim();
    currentFloor = floorLabel;
    
    // Lấy index của tầng này
    const allFloors = document.querySelectorAll('.flex.items-center.gap-6.bg-white');
    currentFloorIndex = Array.from(allFloors).indexOf(floorRow);
    
    // Tìm số phòng cao nhất trong tầng này
    const roomDivs = floorRow.querySelectorAll('.px-4.py-2\\.5.bg-emerald-100');
    let maxRoomNumber = 0;
    
    roomDivs.forEach(room => {
      const roomNum = parseInt(room.textContent);
      if (!isNaN(roomNum) && roomNum > maxRoomNumber) {
        maxRoomNumber = roomNum;
      }
    });
    
    // Tính số phòng tiếp theo
    let nextRoomNumber;
    if (maxRoomNumber === 0) {
      // Không có phòng nào, dùng (floorIndex + 1) * 100 + 01
      nextRoomNumber = (currentFloorIndex + 1) * 100 + 1;
    } else {
      // Có phòng rồi, thêm 1 vào số lớn nhất
      nextRoomNumber = maxRoomNumber + 1;
    }
    
    // Update modal title và room number input
    document.querySelector('#addRoomModal h3').textContent = `Thêm phòng mới – ${currentFloor}`;
    document.getElementById('roomNumber').value = nextRoomNumber;
    document.getElementById('roomType').value = '';
    
    document.getElementById('addRoomModal').classList.remove('hidden');
  }

  function closeAddRoomModal() {
    document.getElementById('addRoomModal').classList.add('hidden');
    document.getElementById('roomNumber').value = '';
    document.getElementById('roomType').value = '';
    currentFloor = null;
    currentFloorIndex = null;
  }

  function confirmAddRoom() {
    const roomNumber = document.getElementById('roomNumber').value;
    const roomType = document.getElementById('roomType').value;
    
    if (!roomNumber || !roomType) {
      alert('Vui lòng nhập đầy đủ thông tin!');
      return;
    }
    
    // Thêm phòng vào tầng hiện tại
    const floorElements = document.querySelectorAll('.flex.items-center.gap-6.bg-white');
    if (currentFloorIndex >= 0 && currentFloorIndex < floorElements.length) {
      const floorEl = floorElements[currentFloorIndex];
      // Tìm nút add room và lấy parent container (rooms container)
      const addButton = floorEl.querySelector('button[onclick*="openAddRoomModal"]');
      const roomsContainer = addButton.parentElement;
      
      // Tạo phần tử phòng mới với delete functionality
      const newRoom = document.createElement('div');
      newRoom.className = 'px-4 py-2.5 bg-emerald-100 text-emerald-700 rounded-lg font-bold text-sm min-w-14 text-center group/room cursor-pointer relative flex-shrink-0';
      newRoom.setAttribute('data-status', '');
      newRoom.onclick = function() { editRoom(this); };
      newRoom.innerHTML = `
        <span>${roomNumber}</span>
        <span class="material-symbols-outlined text-xs absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover/room:opacity-100 transition-all" onclick="event.stopPropagation(); deleteRoom(this.parentElement)">close</span>
      `;
      
      // Insert trước nút +
      roomsContainer.insertBefore(newRoom, addButton);
    }
    
    closeAddRoomModal();
  }

  function deleteRoom(roomElement) {
    if (confirm('Bạn có chắc chắn muốn xóa phòng này?')) {
      roomElement.remove();
    }
  }

  let currentEditingFloor = null;

  function editFloor(floorNameSpan) {
    currentEditingFloor = floorNameSpan;
    const floorName = floorNameSpan.textContent;
    document.getElementById('editFloorNameInput').value = floorName;
    document.getElementById('editFloorModal').classList.remove('hidden');
    document.getElementById('editFloorNameInput').focus();
  }

  function closeEditFloorModal() {
    document.getElementById('editFloorModal').classList.add('hidden');
    currentEditingFloor = null;
    document.getElementById('editFloorNameInput').value = '';
  }

  function confirmEditFloor() {
    const newFloorName = document.getElementById('editFloorNameInput').value.trim();
    if (!newFloorName) {
      alert('Vui lòng nhập tên tầng!');
      return;
    }
    
    if (currentEditingFloor) {
      currentEditingFloor.textContent = newFloorName;
    }
    
    closeEditFloorModal();
  }

  function deleteFloor(deleteButton) {
    const floorRow = deleteButton.closest('.flex.items-center.gap-6.bg-white');
    if (!floorRow) return;
    
    // Check if floor has any rooms
    const rooms = floorRow.querySelectorAll('.px-4.py-2\\.5.bg-emerald-100');
    
    if (rooms.length > 0) {
      alert('Vui lòng xóa tất cả các phòng trước khi xóa tầng!');
      return;
    }
    
    if (confirm('Bạn có chắc chắn muốn xóa tầng này?')) {
      floorCount--;
      floorRow.remove();
    }
  }

  function openAddFloorModal() {
    // Auto-suggest next floor name
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

  function confirmAddFloor() {
    const floorName = document.getElementById('floorName').value;
    
    if (!floorName.trim()) {
      alert('Vui lòng nhập tên tầng!');
      return;
    }
    
    floorCount++;
    
    // Create new floor element
    const newFloor = document.createElement('div');
    newFloor.className = 'flex items-center gap-6 bg-white p-6 rounded-lg group whitespace-nowrap overflow-x-auto';
    newFloor.innerHTML = `
      <div class="flex items-center gap-2 flex-shrink-0">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider flex-shrink-0 cursor-pointer hover:bg-slate-200 px-2 py-1 rounded" onclick="editFloor(this)">${floorName}</span>
        <button type="button" onclick="deleteFloor(this)" class="w-5 h-5 flex items-center justify-center rounded opacity-0 group-hover:opacity-100 hover:bg-red-100 hover:text-red-600 text-slate-400 transition-all flex-shrink-0">
          <span class="material-symbols-outlined text-sm">close</span>
        </button>
      </div>
      <div class="flex items-center gap-2">
        <button type="button" onclick="openAddRoomModal(this)" class="w-10 h-10 flex items-center justify-center rounded-lg border-2 border-dashed border-slate-300 hover:border-blue-900 hover:text-blue-900 text-slate-300 hover:bg-blue-50 transition-all flex-shrink-0">
          <span class="material-symbols-outlined text-lg">add</span>
        </button>
      </div>
    `;
    
    // Insert new floor before add floor button container
    const addFloorButtonContainer = document.getElementById('addFloorButtonContainer');
    if (addFloorButtonContainer && addFloorButtonContainer.parentElement) {
      addFloorButtonContainer.parentElement.insertBefore(newFloor, addFloorButtonContainer);
    }
    
    document.getElementById('floorName').value = '';
    closeAddFloorModal();
  }

  function showSaveNotification() {
    // Tạo toast notification
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-8 right-8 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 z-50 animate-in';
    toast.innerHTML = `
      <span class="material-symbols-outlined">check_circle</span>
      <span>Đã lưu thành công!</span>
    `;
    
    document.body.appendChild(toast);
    
    // Thêm animation
    toast.style.animation = 'slideInRight 0.3s ease-out';
    
    // Tự động xóa sau 3 giây
    setTimeout(() => {
      toast.style.animation = 'slideOutRight 0.3s ease-in';
      setTimeout(() => {
        toast.remove();
      }, 300);
    }, 3000);
  }

</script>
@endsection
