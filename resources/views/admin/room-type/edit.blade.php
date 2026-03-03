@extends("layouts.admin")
@section('content')
<div class="flex-1 flex flex-col">
  <!-- Page Content -->
  <div class="p-8 max-w-7xl mx-auto w-full">
    <!-- Breadcrumb -->
    <a href="{{ route('admin.room-types.index') }}" class="inline-flex items-center gap-1 text-slate-600 hover:text-blue-900 text-sm font-medium mb-6 transition-colors">
      <span class="material-symbols-outlined text-sm">arrow_back</span>
      Trở lại danh sách
    </a>

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">Tạo loại phòng mới</h2>
        <p class="text-slate-500 text-sm mt-1">Hệ thống quản lý khách sạn Urban Luxe</p>
      </div>
      <div class="flex gap-3">
        <a href="{{ route('admin.room-types.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-900 px-4 py-2 rounded-lg font-bold text-sm transition-all">
          Quay Lại
        </a>
        <button type="submit" form="roomTypeForm" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg font-bold text-sm transition-all">
          Lưu thay đổi
        </button>
      </div>
    </div>

    <!-- Form -->
    <form id="roomTypeForm" action="{{ route('admin.room-types.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-3 gap-6">
      @csrf

      <!-- Left Column -->
      <div class="col-span-2 space-y-6">
        <!-- General Info -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
          <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-blue-900 text-2xl">info</span>
            <h3 class="text-lg font-bold text-slate-900">Thông tin chung</h3>
          </div>
          
          <div class="space-y-4">
            <!-- Tên loại phòng -->
            <div>
              <label class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 block">Tên loại phòng <span class="text-red-500">*</span></label>
              <input type="text" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Phòng Deluxe" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('name') border-red-500 @enderror">
              @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Mã loại phòng -->
            <div>
              <label class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 block">Mã loại phòng <span class="text-red-500">*</span></label>
              <input type="text" name="code" value="{{ old('code') }}" placeholder="Ví dụ: DLX-001" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('code') border-red-500 @enderror">
              @error('code')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Trạng thái -->
            <div>
              <label class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 block">Trạng thái <span class="text-red-500">*</span></label>
              <select name="status" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('status') border-red-500 @enderror bg-white">
                <option value="">-- Chọn trạng thái --</option>
                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Đang kinh doanh</option>
                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Không kinh doanh</option>
              </select>
              @error('status')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Mô tả -->
            <div>
              <label class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 block">Mô tả</label>
              <textarea name="description" rows="4" placeholder="Nhập mô tả chi tiết về loại phòng..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
              @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>

        <!-- Size & Price -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
          <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-blue-900 text-2xl">straighten</span>
            <h3 class="text-lg font-bold text-slate-900">Kích thước & Giá</h3>
          </div>
          
          <div class="grid grid-cols-2 gap-6">
            <!-- Left: Dimensions -->
            <div class="bg-slate-50 rounded-lg p-4">
              <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-4 text-center">Kích thước</p>
              <div class="space-y-3">
                <div>
                  <label class="text-xs text-slate-500 font-medium mb-1 block">Rộng (m) <span class="text-red-500">*</span></label>
                  <input type="number" name="width" value="{{ old('width') }}" placeholder="5.0" step="0.01" min="1" max="999.99" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('width') border-red-500 @enderror">
                  @error('width')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                  @enderror
                </div>
                <div>
                  <label class="text-xs text-slate-500 font-medium mb-1 block">Dài (m) <span class="text-red-500">*</span></label>
                  <input type="number" name="height" value="{{ old('height') }}" placeholder="8.0" step="0.01" min="1" max="999.99" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('height') border-red-500 @enderror">
                  @error('height')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Right: Prices -->
            <div class="space-y-4">
              <div>
                <label class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 block">Giá giờ (VNĐ) <span class="text-red-500">*</span></label>
                <input type="number" name="hourly_price" value="{{ old('hourly_price') }}" placeholder="200000" step="0.01" min="0" max="999999.99" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('hourly_price') border-red-500 @enderror">
                @error('hourly_price')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>
              <div>
                <label class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 block">Giá ngày (VNĐ) <span class="text-red-500">*</span></label>
                <input type="number" name="daily_price" value="{{ old('daily_price') }}" placeholder="1500000" step="0.01" min="0" max="999999.99" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('daily_price') border-red-500 @enderror">
                @error('daily_price')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>
            </div>
          </div>
        </div>

        <!-- Capacity -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
          <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-blue-900 text-2xl">group</span>
            <h3 class="text-lg font-bold text-slate-900">Sức chứa</h3>
          </div>
          
          <div class="grid grid-cols-4 gap-4">
            <!-- Người lớn -->
            <div class="bg-slate-50 rounded-lg p-4 text-center">
              <span class="material-symbols-outlined text-slate-600 text-3xl block mb-2">person</span>
              <p class="text-xs text-slate-400 uppercase font-bold mb-3">Người lớn</p>
              <div class="flex items-center justify-center gap-3">
                <button type="button" class="w-6 h-6 rounded border border-slate-300 hover:border-blue-900 flex items-center justify-center" onclick="decrementValue(this)">
                  <span class="text-lg">−</span>
                </button>
                <input type="number" name="max_adults" value="{{ old('max_adults', 2) }}" min="0" class="w-12 text-center px-2 py-1 border border-slate-300 rounded focus:outline-none focus:border-blue-900" readonly>
                <button type="button" class="w-6 h-6 rounded border border-slate-300 hover:border-blue-900 flex items-center justify-center" onclick="incrementValue(this)">
                  <span class="text-lg">+</span>
                </button>
              </div>
            </div>

            <!-- Trẻ em -->
            <div class="bg-slate-50 rounded-lg p-4 text-center">
              <span class="material-symbols-outlined text-slate-600 text-3xl block mb-2">child_care</span>
              <p class="text-xs text-slate-400 uppercase font-bold mb-3">Trẻ em</p>
              <div class="flex items-center justify-center gap-3">
                <button type="button" class="w-6 h-6 rounded border border-slate-300 hover:border-blue-900 flex items-center justify-center" onclick="decrementValue(this)">
                  <span class="text-lg">−</span>
                </button>
                <input type="number" name="max_children" value="{{ old('max_children', 1) }}" min="0" class="w-12 text-center px-2 py-1 border border-slate-300 rounded focus:outline-none focus:border-blue-900" readonly>
                <button type="button" class="w-6 h-6 rounded border border-slate-300 hover:border-blue-900 flex items-center justify-center" onclick="incrementValue(this)">
                  <span class="text-lg">+</span>
                </button>
              </div>
            </div>

            <!-- Giường đơn -->
            <div class="bg-slate-50 rounded-lg p-4 text-center">
              <span class="material-symbols-outlined text-slate-600 text-3xl block mb-2">bed</span>
              <p class="text-xs text-slate-400 uppercase font-bold mb-3">Giường đơn</p>
              <div class="flex items-center justify-center gap-3">
                <button type="button" class="w-6 h-6 rounded border border-slate-300 hover:border-blue-900 flex items-center justify-center" onclick="decrementValue(this)">
                  <span class="text-lg">−</span>
                </button>
                <input type="number" name="single_beds" value="{{ old('single_beds', 1) }}" min="0" class="w-12 text-center px-2 py-1 border border-slate-300 rounded focus:outline-none focus:border-blue-900" readonly>
                <button type="button" class="w-6 h-6 rounded border border-slate-300 hover:border-blue-900 flex items-center justify-center" onclick="incrementValue(this)">
                  <span class="text-lg">+</span>
                </button>
              </div>
            </div>

            <!-- Giường đôi -->
            <div class="bg-slate-50 rounded-lg p-4 text-center">
              <span class="material-symbols-outlined text-slate-600 text-3xl block mb-2">bed</span>
              <p class="text-xs text-slate-400 uppercase font-bold mb-3">Giường đôi</p>
              <div class="flex items-center justify-center gap-3">
                <button type="button" class="w-6 h-6 rounded border border-slate-300 hover:border-blue-900 flex items-center justify-center" onclick="decrementValue(this)">
                  <span class="text-lg">−</span>
                </button>
                <input type="number" name="double_beds" value="{{ old('double_beds', 1) }}" min="0" class="w-12 text-center px-2 py-1 border border-slate-300 rounded focus:outline-none focus:border-blue-900" readonly>
                <button type="button" class="w-6 h-6 rounded border border-slate-300 hover:border-blue-900 flex items-center justify-center" onclick="incrementValue(this)">
                  <span class="text-lg">+</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column -->
      <div class="space-y-6">
        <!-- Media -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="p-6 border-b border-slate-100">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-blue-900 text-2xl">image</span>
                <h3 class="text-lg font-bold text-slate-900">Media (Hình ảnh)</h3>
              </div>
              <button type="button" onclick="document.getElementById('imageUpload').click()" class="inline-flex items-center gap-2 bg-blue-900 hover:bg-blue-800 text-white px-3 py-1 rounded text-xs font-bold transition-all">
                <span class="material-symbols-outlined text-sm">upload</span>
                Upload
              </button>
              <input type="file" id="imageUpload" name="images[]" multiple accept="image/*" class="hidden" onchange="handleImageUpload(event)">
            </div>
          </div>
          
          <div class="p-4 space-y-3">
            <div id="imageGallery" class="space-y-3">
              <div class="w-full h-48 bg-slate-100 rounded-lg flex items-center justify-center">
                <div class="text-center">
                  <span class="material-symbols-outlined text-slate-400 text-4xl block mb-2">image_not_supported</span>
                  <p class="text-xs text-slate-500">Không có ảnh nào được tải lên</p>
                </div>
              </div>
            </div>
            <p class="text-xs text-slate-500 text-center"><span id="imageCount">0</span> image(s) displayed</p>
          </div>
        </div>

        <!-- Amenities -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
          <div class="flex items-center gap-3 mb-4">
            <span class="material-symbols-outlined text-blue-900 text-2xl">star</span>
            <h3 class="text-lg font-bold text-slate-900">Tiện ích (Amenities)</h3>
          </div>
          
          <div class="flex flex-wrap gap-2" id="amenitiesList">
            @php
              $defaultAmenities = ['Free Wifi', 'Điều hòa', 'Bãi đỗ', 'Hộp bảo'];
            @endphp
            @foreach($defaultAmenities as $amenity)
              <span class="inline-flex items-center gap-2 px-3 py-2 bg-blue-100 text-blue-900 rounded-full text-sm font-medium cursor-pointer hover:bg-blue-200 transition-all" onclick="removeAmenity(this)">
                @if($amenity === 'Free Wifi')
                  <span class="material-symbols-outlined text-sm">wifi</span>
                @elseif($amenity === 'Điều hòa')
                  <span class="material-symbols-outlined text-sm">ac_unit</span>
                @elseif($amenity === 'Bãi đỗ')
                  <span class="material-symbols-outlined text-sm">local_parking</span>
                @else
                  <span class="material-symbols-outlined text-sm">security</span>
                @endif
                {{ $amenity }}
                <span class="material-symbols-outlined text-sm cursor-pointer">close</span>
              </span>
            @endforeach
            <button type="button" onclick="addAmenityModal()" class="inline-flex items-center gap-1 px-3 py-2 bg-slate-100 text-slate-600 rounded-full text-sm font-medium hover:bg-slate-200 transition-all">
              <span class="material-symbols-outlined text-sm">add</span>
              Thêm tiện ích
            </button>
          </div>
        </div>

        <!-- Equipment -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="p-6 border-b border-slate-100">
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-blue-900 text-2xl">kitchen</span>
                <h3 class="text-lg font-bold text-slate-900">Thiết bị (Room Equipment)</h3>
              </div>
            </div>
            
            <div class="overflow-x-auto">
              <table class="w-full" id="equipmentTable">
                <thead>
                  <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600 tracking-wider">Tên thiết bị</th>
                    <th class="px-6 py-3 text-right text-xs font-bold uppercase text-slate-600 tracking-wider">SL</th>
                    <th class="px-6 py-3 text-center text-xs font-bold uppercase text-slate-600 tracking-wider">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="hover:bg-slate-50">
                    <td colspan="3" class="px-6 py-8 text-center text-slate-500 text-sm">Chưa có thiết bị nào</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
          <div class="p-6 border-t border-slate-100">
            <button type="button" onclick="openEquipmentModal()" class="w-full px-4 py-3 border-2 border-dashed border-slate-300 hover:border-blue-900 text-slate-600 hover:text-blue-900 rounded-lg font-bold text-sm transition-all flex items-center justify-center gap-2">
              <span class="material-symbols-outlined">add</span>
              Thêm thiết bị
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  function incrementValue(btn) {
    const input = btn.parentElement.querySelector('input');
    input.value = parseInt(input.value) + 1;
  }

  function decrementValue(btn) {
    const input = btn.parentElement.querySelector('input');
    if (parseInt(input.value) > 0) {
      input.value = parseInt(input.value) - 1;
    }
  }

  function handleImageUpload(event) {
    const files = event.target.files;
    const gallery = document.getElementById('imageGallery');
    const count = document.getElementById('imageCount');
    
    gallery.innerHTML = '';
    
    if (files.length === 0) {
      gallery.innerHTML = `
        <div class="w-full h-48 bg-slate-100 rounded-lg flex items-center justify-center">
          <div class="text-center">
            <span class="material-symbols-outlined text-slate-400 text-4xl block mb-2">image_not_supported</span>
            <p class="text-xs text-slate-500">Không có ảnh nào được tải lên</p>
          </div>
        </div>
      `;
    } else {
      Array.from(files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
          const img = document.createElement('div');
          img.className = 'relative';
          img.innerHTML = `
            <img src="${e.target.result}" alt="Preview ${index}" class="w-full h-32 object-cover rounded-lg">
            <button type="button" onclick="this.parentElement.remove(); updateImageCount();" class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full hover:bg-red-600 transition-all">
              <span class="material-symbols-outlined text-sm">delete</span>
            </button>
          `;
          gallery.appendChild(img);
        };
        reader.readAsDataURL(file);
      });
    }
    
    count.textContent = files.length;
  }

  function updateImageCount() {
    const input = document.getElementById('imageUpload');
    document.getElementById('imageCount').textContent = input.files.length;
  }

  function removeAmenity(element) {
    element.remove();
  }

  function addAmenityModal() {
    document.getElementById('amenityModal').classList.remove('hidden');
    document.getElementById('amenitySearch').value = '';
    filterAmenities('');
  }

  function closeAmenityModal() {
    document.getElementById('amenityModal').classList.add('hidden');
    uncheckAllAmenities();
  }

  function filterAmenities(query) {
    const items = document.querySelectorAll('#amenityGrid .amenity-item');
    items.forEach(item => {
      const text = item.getAttribute('data-text').toLowerCase();
      if (text.includes(query.toLowerCase())) {
        item.classList.remove('hidden');
      } else {
        item.classList.add('hidden');
      }
    });
  }

  function toggleAmenity(element) {
    element.classList.toggle('ring-2');
    element.classList.toggle('ring-blue-900');
    element.classList.toggle('bg-blue-50');
    element.querySelector('input[type="checkbox"]').checked = !element.querySelector('input[type="checkbox"]').checked;
  }

  function uncheckAllAmenities() {
    document.querySelectorAll('#amenityGrid .amenity-item').forEach(item => {
      item.classList.remove('ring-2', 'ring-blue-900', 'bg-blue-50');
      item.querySelector('input[type="checkbox"]').checked = false;
    });
  }

  function confirmAmenities() {
    const selected = Array.from(document.querySelectorAll('#amenityGrid .amenity-item input[type="checkbox"]:checked'))
      .map(input => ({
        name: input.getAttribute('data-name'),
        icon: input.getAttribute('data-icon')
      }));

    if (selected.length === 0) {
      alert('Vui lòng chọn ít nhất một tiện ích!');
      return;
    }

    const list = document.getElementById('amenitiesList');
    
    selected.forEach(amenity => {
      // Check if already exists
      const exists = Array.from(list.querySelectorAll('span')).some(span => span.textContent.includes(amenity.name));
      if (!exists) {
        const span = document.createElement('span');
        span.className = 'inline-flex items-center gap-2 px-3 py-2 bg-blue-100 text-blue-900 rounded-full text-sm font-medium cursor-pointer hover:bg-blue-200 transition-all';
        span.onclick = function() { removeAmenity(this); };
        span.innerHTML = `
          <span class="material-symbols-outlined text-sm">${amenity.icon}</span>
          ${amenity.name}
          <span class="material-symbols-outlined text-sm cursor-pointer">close</span>
        `;
        list.insertBefore(span, list.lastElementChild);
      }
    });

    closeAmenityModal();
  }

  function addEquipmentRow(name, quantity) {
    const table = document.getElementById('equipmentTable');
    const tbody = table.querySelector('tbody');
    
    if (tbody.querySelector('tr td[colspan]')) {
      tbody.innerHTML = '';
    }
    
    const row = document.createElement('tr');
    row.className = 'border-b border-slate-100 hover:bg-slate-50';
    row.innerHTML = `
      <td class="px-6 py-3 text-sm text-slate-900">${name}</td>
      <td class="px-6 py-3 text-sm text-slate-900 text-right">
        <input type="number" value="${quantity}" min="1" class="w-20 px-2 py-1 border border-slate-300 rounded text-center focus:outline-none focus:border-blue-900">
      </td>
      <td class="px-6 py-3 text-center">
        <button type="button" onclick="this.closest('tr').remove();" class="text-red-500 hover:text-red-700 transition-all">
          <span class="material-symbols-outlined text-lg">delete</span>
        </button>
      </td>
    `;
    tbody.appendChild(row);
  }

  function openEquipmentModal() {
    document.getElementById('equipmentModal').classList.remove('hidden');
    document.getElementById('equipmentSearch').value = '';
    filterEquipment('');
  }

  function closeEquipmentModal() {
    document.getElementById('equipmentModal').classList.add('hidden');
    uncheckAllEquipment();
  }

  function filterEquipment(query) {
    const items = document.querySelectorAll('#equipmentGrid .equipment-item');
    items.forEach(item => {
      const text = item.getAttribute('data-text').toLowerCase();
      if (text.includes(query.toLowerCase())) {
        item.classList.remove('hidden');
      } else {
        item.classList.add('hidden');
      }
    });
  }

  function toggleEquipment(element) {
    element.classList.toggle('ring-2');
    element.classList.toggle('ring-blue-900');
    element.classList.toggle('bg-blue-50');
    const checkbox = element.querySelector('input[type="checkbox"]');
    checkbox.checked = !checkbox.checked;
  }

  function uncheckAllEquipment() {
    document.querySelectorAll('#equipmentGrid .equipment-item').forEach(item => {
      item.classList.remove('ring-2', 'ring-blue-900', 'bg-blue-50');
      item.querySelector('input[type="checkbox"]').checked = false;
    });
  }

  function confirmEquipment() {
    const selected = Array.from(document.querySelectorAll('#equipmentGrid .equipment-item input[type="checkbox"]:checked'))
      .map(input => ({
        name: input.getAttribute('data-name'),
        category: input.getAttribute('data-category')
      }));

    if (selected.length === 0) {
      alert('Vui lòng chọn ít nhất một thiết bị!');
      return;
    }

    selected.forEach(equipment => {
      addEquipmentRow(equipment.name, '1');
    });

    closeEquipmentModal();
  }

  function addEquipmentModal() {
    const name = prompt('Tên thiết bị:');
    if (!name) return;
    
    const quantity = prompt('Số lượng:', '1');
    if (!quantity) return;
    
    addEquipmentRow(name, quantity);
  }
</script>

<!-- Amenity Modal -->
<div id="amenityModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950" style="background-color: rgba(15, 23, 42, 0.5);">
  <div class="bg-white rounded-2xl shadow-lg max-w-md w-full mx-4">
    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b border-slate-200">
      <div class="flex items-center gap-3">
        <span class="material-symbols-outlined text-blue-900 text-2xl">star_rate</span>
        <h3 class="text-lg font-bold text-slate-900">Chọn tiện ích</h3>
      </div>
      <button type="button" onclick="closeAmenityModal()" class="text-slate-400 hover:text-slate-600 transition-all">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <!-- Search -->
    <div class="p-4 border-b border-slate-200">
      <div class="relative">
        <span class="material-symbols-outlined absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400">search</span>
        <input type="text" id="amenitySearch" placeholder="Tìm kiếm tiện ích..." onkeyup="filterAmenities(this.value)" class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900">
      </div>
    </div>

    <!-- Grid -->
    <div id="amenityGrid" class="grid grid-cols-4 gap-3 p-6">
      <!-- Mini bar -->
      <div class="amenity-item p-4 border border-slate-300 rounded-lg text-center cursor-pointer hover:bg-slate-50 transition-all" onclick="toggleAmenity(this)" data-text="mini bar" data-name="Mini bar" data-icon="local_bar">
        <input type="checkbox" hidden>
        <span class="material-symbols-outlined text-2xl block mb-2">local_bar</span>
        <p class="text-xs font-bold text-slate-600">Mini bar</p>
      </div>

      <!-- Ban công -->
      <div class="amenity-item p-4 border border-slate-300 rounded-lg text-center cursor-pointer hover:bg-slate-50 transition-all" onclick="toggleAmenity(this)" data-text="ban công" data-name="Ban công" data-icon="balcony">
        <input type="checkbox" hidden>
        <span class="material-symbols-outlined text-2xl block mb-2">balcony</span>
        <p class="text-xs font-bold text-slate-600">Ban công</p>
      </div>

      <!-- Bồn tắm -->
      <div class="amenity-item p-4 border border-slate-300 rounded-lg text-center cursor-pointer hover:bg-slate-50 transition-all ring-2 ring-blue-900 bg-blue-50" onclick="toggleAmenity(this)" data-text="bồn tắm" data-name="Bồn tắm" data-icon="bathtub">
        <input type="checkbox" hidden checked>
        <span class="material-symbols-outlined text-2xl block mb-2">bathtub</span>
        <p class="text-xs font-bold text-slate-600">Bồn tắm</p>
      </div>

      <!-- Máy cà phê -->
      <div class="amenity-item p-4 border border-slate-300 rounded-lg text-center cursor-pointer hover:bg-slate-50 transition-all" onclick="toggleAmenity(this)" data-text="máy cà phê" data-name="Máy cà phê" data-icon="coffee">
        <input type="checkbox" hidden>
        <span class="material-symbols-outlined text-2xl block mb-2">coffee</span>
        <p class="text-xs font-bold text-slate-600">Máy cà phê</p>
      </div>

      <!-- Kết sát -->
      <div class="amenity-item p-4 border border-slate-300 rounded-lg text-center cursor-pointer hover:bg-slate-50 transition-all" onclick="toggleAmenity(this)" data-text="kết sát" data-name="Kết sát" data-icon="lock">
        <input type="checkbox" hidden>
        <span class="material-symbols-outlined text-2xl block mb-2">lock</span>
        <p class="text-xs font-bold text-slate-600">Kết sát</p>
      </div>

      <!-- Dịch vụ phòng -->
      <div class="amenity-item p-4 border border-slate-300 rounded-lg text-center cursor-pointer hover:bg-slate-50 transition-all" onclick="toggleAmenity(this)" data-text="dịch vụ phòng" data-name="Dịch vụ phòng" data-icon="room_service">
        <input type="checkbox" hidden>
        <span class="material-symbols-outlined text-2xl block mb-2">room_service</span>
        <p class="text-xs font-bold text-slate-600">Dịch vụ phòng</p>
      </div>

      <!-- TV -->
      <div class="amenity-item p-4 border border-slate-300 rounded-lg text-center cursor-pointer hover:bg-slate-50 transition-all ring-2 ring-blue-900 bg-blue-50" onclick="toggleAmenity(this)" data-text="tv" data-name="TV" data-icon="smart_display">
        <input type="checkbox" hidden checked>
        <span class="material-symbols-outlined text-2xl block mb-2">smart_display</span>
        <p class="text-xs font-bold text-slate-600">TV</p>
      </div>

      <!-- View phố -->
      <div class="amenity-item p-4 border border-slate-300 rounded-lg text-center cursor-pointer hover:bg-slate-50 transition-all" onclick="toggleAmenity(this)" data-text="view phố" data-name="View phố" data-icon="landscape">
        <input type="checkbox" hidden>
        <span class="material-symbols-outlined text-2xl block mb-2">landscape</span>
        <p class="text-xs font-bold text-slate-600">View phố</p>
      </div>

      <!-- Free Wifi -->
      <div class="amenity-item p-4 border border-slate-300 rounded-lg text-center cursor-pointer hover:bg-slate-50 transition-all ring-2 ring-blue-900 bg-blue-50" onclick="toggleAmenity(this)" data-text="free wifi" data-name="Free Wifi" data-icon="wifi">
        <input type="checkbox" hidden checked>
        <span class="material-symbols-outlined text-2xl block mb-2">wifi</span>
        <p class="text-xs font-bold text-slate-600">Free Wifi</p>
      </div>

      <!-- Điều hòa -->
      <div class="amenity-item p-4 border border-slate-300 rounded-lg text-center cursor-pointer hover:bg-slate-50 transition-all ring-2 ring-blue-900 bg-blue-50" onclick="toggleAmenity(this)" data-text="điều hòa" data-name="Điều hòa" data-icon="ac_unit">
        <input type="checkbox" hidden checked>
        <span class="material-symbols-outlined text-2xl block mb-2">ac_unit</span>
        <p class="text-xs font-bold text-slate-600">Điều hòa</p>
      </div>

      <!-- Bãi đỗ -->
      <div class="amenity-item p-4 border border-slate-300 rounded-lg text-center cursor-pointer hover:bg-slate-50 transition-all" onclick="toggleAmenity(this)" data-text="bãi đỗ" data-name="Bãi đỗ" data-icon="local_parking">
        <input type="checkbox" hidden>
        <span class="material-symbols-outlined text-2xl block mb-2">local_parking</span>
        <p class="text-xs font-bold text-slate-600">Bãi đỗ</p>
      </div>

      <!-- Hộp bảo -->
      <div class="amenity-item p-4 border border-slate-300 rounded-lg text-center cursor-pointer hover:bg-slate-50 transition-all ring-2 ring-blue-900 bg-blue-50" onclick="toggleAmenity(this)" data-text="hộp bảo" data-name="Hộp bảo" data-icon="security">
        <input type="checkbox" hidden checked>
        <span class="material-symbols-outlined text-2xl block mb-2">security</span>
        <p class="text-xs font-bold text-slate-600">Hộp bảo</p>
      </div>
    </div>

    <!-- Footer -->
    <div class="flex gap-3 p-6 border-t border-slate-200">
      <button type="button" onclick="closeAmenityModal()" class="flex-1 px-4 py-2 border border-slate-300 rounded-lg font-bold text-slate-600 hover:bg-slate-50 transition-all">
        Hủy
      </button>
      <button type="button" onclick="confirmAmenities()" class="flex-1 px-4 py-2 bg-blue-900 hover:bg-blue-800 text-white rounded-lg font-bold transition-all">
        Xác nhận
      </button>
    </div>
  </div>
</div>

<!-- Equipment Modal -->
<div id="equipmentModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950" style="background-color: rgba(15, 23, 42, 0.1);">
  <div class="bg-white rounded-2xl shadow-lg max-w-xl w-full mx-4 max-h-screen overflow-y-auto">
    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b border-slate-200 sticky top-0 bg-white">
      <div class="flex items-center gap-3">
        <span class="material-symbols-outlined text-blue-900 text-2xl">kitchen</span>
        <h3 class="text-lg font-bold text-slate-900">Chọn thiết bị</h3>
      </div>
      <button type="button" onclick="closeEquipmentModal()" class="text-slate-400 hover:text-slate-600 transition-all">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <!-- Search -->
    <div class="p-4 border-b border-slate-200 sticky top-16 bg-white">
      <div class="relative">
        <span class="material-symbols-outlined absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400">search</span>
        <input type="text" id="equipmentSearch" placeholder="Tìm kiếm thiết bị ..." onkeyup="filterEquipment(this.value)" class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900">
      </div>
    </div>

    <!-- Equipment List -->
    <div id="equipmentGrid" class="p-4 space-y-3">
      <!-- Smart TV 55 inch -->
      <div class="equipment-item p-4 border border-slate-300 rounded-lg cursor-pointer hover:bg-slate-50 transition-all ring-2 ring-blue-900 bg-blue-50 flex items-start justify-between" onclick="toggleEquipment(this)" data-text="smart tv 55 inch electronics">
        <div>
          <p class="font-bold text-slate-900">Smart TV 55 inch</p>
          <p class="text-xs text-slate-500">Electronics</p>
        </div>
        <div class="text-blue-900">
          <span class="material-symbols-outlined">check_circle</span>
        </div>
        <input type="checkbox" hidden checked data-name="Smart TV 55 inch" data-category="Electronics">
      </div>

      <!-- Tủ lạnh mini Samsung -->
      <div class="equipment-item p-4 border border-slate-300 rounded-lg cursor-pointer hover:bg-slate-50 transition-all ring-2 ring-blue-900 bg-blue-50 flex items-start justify-between" onclick="toggleEquipment(this)" data-text="tủ lạnh mini samsung kitchen">
        <div>
          <p class="font-bold text-slate-900">Tủ lạnh mini Samsung</p>
          <p class="text-xs text-slate-500">Kitchen</p>
        </div>
        <div class="text-blue-900">
          <span class="material-symbols-outlined">check_circle</span>
        </div>
        <input type="checkbox" hidden checked data-name="Tủ lạnh mini Samsung" data-category="Kitchen">
      </div>

      <!-- Điều hòa Daikin 1.5HP -->
      <div class="equipment-item p-4 border border-slate-300 rounded-lg cursor-pointer hover:bg-slate-50 transition-all ring-2 ring-blue-900 bg-blue-50 flex items-start justify-between" onclick="toggleEquipment(this)" data-text="điều hòa daikin 1.5hp cooling">
        <div>
          <p class="font-bold text-slate-900">Điều hòa Daikin 1.5HP</p>
          <p class="text-xs text-slate-500">Cooling</p>
        </div>
        <div class="text-blue-900">
          <span class="material-symbols-outlined">check_circle</span>
        </div>
        <input type="checkbox" hidden checked data-name="Điều hòa Daikin 1.5HP" data-category="Cooling">
      </div>

      <!-- Máy sấy tóc Panasonic -->
      <div class="equipment-item p-4 border border-slate-300 rounded-lg cursor-pointer hover:bg-slate-50 transition-all flex items-start justify-between" onclick="toggleEquipment(this)" data-text="máy sấy tóc panasonic bathroom">
        <div>
          <p class="font-bold text-slate-900">Máy sấy tóc Panasonic</p>
          <p class="text-xs text-slate-500">Bathroom</p>
        </div>
        <div class="text-slate-300">
          <span class="material-symbols-outlined">radio_button_unchecked</span>
        </div>
        <input type="checkbox" hidden data-name="Máy sấy tóc Panasonic" data-category="Bathroom">
      </div>

      <!-- Ấm đun nước siêu tốc -->
      <div class="equipment-item p-4 border border-slate-300 rounded-lg cursor-pointer hover:bg-slate-50 transition-all flex items-start justify-between" onclick="toggleEquipment(this)" data-text="ấm đun nước siêu tốc kitchen">
        <div>
          <p class="font-bold text-slate-900">Ấm đun nước siêu tốc</p>
          <p class="text-xs text-slate-500">Kitchen</p>
        </div>
        <div class="text-slate-300">
          <span class="material-symbols-outlined">radio_button_unchecked</span>
        </div>
        <input type="checkbox" hidden data-name="Ấm đun nước siêu tốc" data-category="Kitchen">
      </div>

      <!-- Bàn là hơi nước -->
      <div class="equipment-item p-4 border border-slate-300 rounded-lg cursor-pointer hover:bg-slate-50 transition-all flex items-start justify-between" onclick="toggleEquipment(this)" data-text="bàn là hơi nước laundry">
        <div>
          <p class="font-bold text-slate-900">Bàn là hơi nước</p>
          <p class="text-xs text-slate-500">Laundry</p>
        </div>
        <div class="text-slate-300">
          <span class="material-symbols-outlined">radio_button_unchecked</span>
        </div>
        <input type="checkbox" hidden data-name="Bàn là hơi nước" data-category="Laundry">
      </div>

      <!-- Loa Bluetooth JBL -->
      <div class="equipment-item p-4 border border-slate-300 rounded-lg cursor-pointer hover:bg-slate-50 transition-all flex items-start justify-between" onclick="toggleEquipment(this)" data-text="loa bluetooth jbl electronics">
        <div>
          <p class="font-bold text-slate-900">Loa Bluetooth JBL</p>
          <p class="text-xs text-slate-500">Electronics</p>
        </div>
        <div class="text-slate-300">
          <span class="material-symbols-outlined">radio_button_unchecked</span>
        </div>
        <input type="checkbox" hidden data-name="Loa Bluetooth JBL" data-category="Electronics">
      </div>
    </div>

    <!-- Footer -->
    <div class="flex gap-3 p-6 border-t border-slate-200 sticky bottom-0 bg-white">
      <button type="button" onclick="closeEquipmentModal()" class="flex-1 px-4 py-2 border border-slate-300 rounded-lg font-bold text-slate-600 hover:bg-slate-50 transition-all">
        Hủy
      </button>
      <button type="button" onclick="confirmEquipment()" class="flex-1 px-4 py-2 bg-blue-900 hover:bg-blue-800 text-white rounded-lg font-bold transition-all">
        Xác nhận
      </button>
    </div>
  </div>
</div>

@endsection
