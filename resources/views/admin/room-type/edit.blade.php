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
        <h2 class="text-2xl font-bold text-slate-900">Chỉnh sửa loại phòng: {{ $viewModel->roomType()->name }}</h2>
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

    <!-- Validation Errors -->
    @if ($errors->any())
      <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-center gap-2 text-red-700 font-bold mb-2">
          <span class="material-symbols-outlined">error</span>
          Vui lòng kiểm tra lại thông tin:
        </div>
        <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Form -->
    <form id="roomTypeForm" action="{{ route('admin.room-types.update', $roomType->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-3 gap-6">
      @csrf
      @method('PUT')

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
              <input type="text" name="name" value="{{ old('name') ?? $viewModel->roomType()->name }}" placeholder="Ví dụ: Phòng Deluxe" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('name') border-red-500 @enderror">
              @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Mã loại phòng -->
            <div>
              <label class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 block">Mã loại phòng</label>
              <input type="text" name="code" value="{{ $viewModel->roomType()->code }}" class="w-full px-4 py-2 border border-slate-200 rounded-lg bg-slate-100 text-slate-500 cursor-not-allowed" readonly>
              <p class="text-slate-400 text-xs mt-1">Mã loại phòng không thể thay đổi</p>
            </div>

            <!-- Trạng thái -->
            <div>
              <label class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 block">Trạng thái <span class="text-red-500">*</span></label>
              @php $currentStatus = old('is_active', $viewModel->roomType()->is_active->value ?? 1); @endphp
              <select name="is_active" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('is_active') border-red-500 @enderror bg-white" required>
                @foreach(\App\Enums\RoomTypeStatus::cases() as $status)
                  <option value="{{ $status->value }}" {{ $currentStatus == $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                @endforeach
              </select>
              @error('is_active')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Mô tả -->
            <div>
              <label class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 block">Mô tả</label>
              <textarea name="description" rows="4" placeholder="Nhập mô tả chi tiết về loại phòng..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('description') border-red-500 @enderror">{{ old('description', $viewModel->roomType()->description) }}</textarea>
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
                  <input type="number" name="width" value="{{ old('width', $viewModel->dimensions()['width']) }}" placeholder="5.0" step="0.01" min="1" max="999.99" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('width') border-red-500 @enderror">
                  @error('width')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                  @enderror
                </div>
                <div>
                  <label class="text-xs text-slate-500 font-medium mb-1 block">Dài (m) <span class="text-red-500">*</span></label>
                  <input type="number" name="height" value="{{ old('height', $viewModel->dimensions()['height']) }}" placeholder="8.0" step="0.01" min="1" max="999.99" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('height') border-red-500 @enderror">
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
                <input type="number" name="hourly_price" value="{{ old('hourly_price', $viewModel->pricing()['hourly_price']) }}" placeholder="200000" step="0.01" min="0" max="999999.99" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('hourly_price') border-red-500 @enderror">
                @error('hourly_price')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>
              <div>
                <label class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 block">Giá ngày (VNĐ) <span class="text-red-500">*</span></label>
                <input type="number" name="daily_price" value="{{ old('daily_price', $viewModel->pricing()['daily_price']) }}" placeholder="1500000" step="0.01" min="0" max="999999.99" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('daily_price') border-red-500 @enderror">
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
                <input type="number" name="adult_quantity" value="{{ old('adult_quantity') ?? $viewModel->capacity()['adult_quantity'] }}" min="1" max="10" class="w-12 text-center px-2 py-1 border border-slate-300 rounded focus:outline-none focus:border-blue-900" readonly>
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
                <input type="number" name="child_quantity" value="{{ old('child_quantity') ?? $viewModel->capacity()['child_quantity'] }}" min="0" max="10" class="w-12 text-center px-2 py-1 border border-slate-300 rounded focus:outline-none focus:border-blue-900" readonly>
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
                <input type="number" name="single_bed_quantity" value="{{ old('single_bed_quantity') ?? $viewModel->capacity()['single_bed_quantity'] }}" min="0" max="10" class="w-12 text-center px-2 py-1 border border-slate-300 rounded focus:outline-none focus:border-blue-900" readonly>
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
                <input type="number" name="double_bed_quantity" value="{{ old('double_bed_quantity') ?? $viewModel->capacity()['double_bed_quantity'] }}" min="0" max="10" class="w-12 text-center px-2 py-1 border border-slate-300 rounded focus:outline-none focus:border-blue-900" readonly>
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
            <!-- Existing Images -->
            @if($viewModel->images()->count() > 0)
              <div class="space-y-2 mb-4">
                <p class="text-xs text-slate-500 font-bold uppercase">Hình ảnh hiện có:</p>
                @foreach($viewModel->images() as $image)
                  <div class="flex items-center gap-3 p-2 border border-slate-200 rounded-lg bg-white hover:bg-slate-50 transition-all group relative" id="image-item-{{ $image->id }}">
                    <img src="{{ asset('storage/' . $image->image_url) }}" alt="Image" class="w-12 h-12 object-cover rounded">
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-slate-900 truncate" title="{{ basename($image->image_url) }}">
                        {{ Str::limit(basename($image->image_url), 20) }}
                      </p>
                      <p class="text-xs text-slate-500">Đã lưu</p>
                    </div>
                    <button type="button" onclick="toggleDeleteImage({{ $image->id }})" class="delete-btn p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded transition-all opacity-0 group-hover:opacity-100">
                      <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                    <!-- Hidden input for deletion (added via JS when marked) -->
                  </div>
                @endforeach
              </div>
            @endif
            
            <!-- New Upload Area -->
            <div id="imageGallery" class="space-y-3"></div>
            <p class="text-xs text-slate-500 text-center">Hiện có {{ $viewModel->images()->count() }} ảnh | Thêm mới: <span id="imageCount">0</span> ảnh</p>
          </div>
        </div>

        <!-- Amenities -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
          <div class="flex items-center gap-3 mb-4">
            <span class="material-symbols-outlined text-blue-900 text-2xl">star</span>
            <h3 class="text-lg font-bold text-slate-900">Tiện ích (Amenities)</h3>
          </div>
          
          <div class="flex flex-wrap gap-2" id="amenitiesList">
            @foreach($viewModel->amenities() as $amenity)
              <span class="inline-flex items-center gap-2 px-3 py-2 bg-blue-100 text-blue-900 rounded-full text-sm font-medium cursor-pointer hover:bg-blue-200 transition-all" onclick="removeAmenity(this)" data-id="{{ $amenity->id }}">
                <span class="material-symbols-outlined text-sm">{{ $amenity->icon ?? 'star' }}</span>
                {{ $amenity->name }}
                <input type="hidden" name="amenities[]" value="{{ $amenity->id }}">
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
                  @forelse($viewModel->equipment() as $equipment)
                    <tr class="hover:bg-slate-50 border-b border-slate-100">
                      <td class="px-6 py-4">
                        <div>
                          <p class="font-medium text-slate-900">{{ $equipment->name }}</p>
                          <p class="text-xs text-slate-500">{{ $equipment->category->name ?? 'Chưa phân loại' }}</p>
                        </div>
                        <input type="hidden" name="equipments[]" value="{{ $equipment->id }}">
                      </td>
                      <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end">
                          <button type="button" onclick="decrementValue(this)" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 border border-slate-300 rounded-l-lg">
                            <span class="material-symbols-outlined text-sm">remove</span>
                          </button>
                          <input type="number" name="equipment_quantities[{{ $equipment->id }}]" value="{{ $equipment->pivot->quantity ?? 1 }}" class="w-12 h-8 text-center border-y border-slate-300 text-sm" min="1">
                          <button type="button" onclick="incrementValue(this)" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 border border-slate-300 rounded-r-lg">
                            <span class="material-symbols-outlined text-sm">add</span>
                          </button>
                        </div>
                      </td>
                      <td class="px-6 py-4 text-center">
                        <button type="button" onclick="removeEquipmentRow(this)" class="text-red-500 hover:text-red-700 transition-all">
                          <span class="material-symbols-outlined">delete</span>
                        </button>
                      </td>
                    </tr>
                  @empty
                    <tr class="hover:bg-slate-50 empty-row">
                      <td colspan="3" class="px-6 py-8 text-center text-slate-500 text-sm">Chưa có thiết bị nào</td>
                    </tr>
                  @endforelse
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
  // Helper function to truncate filename
  function truncateFilename(filename, maxLength = 25) {
    if (filename.length <= maxLength) return filename;
    const ext = filename.split('.').pop();
    const nameWithoutExt = filename.substring(0, filename.lastIndexOf('.'));
    const truncatedName = nameWithoutExt.substring(0, maxLength - ext.length - 4) + '...';
    return truncatedName + '.' + ext;
  }

  // Toggle image for deletion
  function toggleDeleteImage(imageId) {
    const item = document.getElementById('image-item-' + imageId);
    const isMarked = item.classList.contains('marked-delete');
    
    if (isMarked) {
      // Undo delete mark
      item.classList.remove('marked-delete', 'bg-red-50', 'border-red-300');
      item.classList.add('bg-white', 'border-slate-200');
      item.querySelector('img').classList.remove('opacity-40');
      item.querySelector('.flex-1 p:first-child').classList.remove('line-through', 'text-red-400');
      item.querySelector('.flex-1 p:last-child').textContent = 'Đã lưu';
      item.querySelector('.flex-1 p:last-child').classList.remove('text-red-500');
      item.querySelector('.flex-1 p:last-child').classList.add('text-slate-500');
      item.querySelector('.delete-btn span').textContent = 'close';
      // Remove hidden input
      const hiddenInput = item.querySelector('input[name="delete_images[]"]');
      if (hiddenInput) hiddenInput.remove();
    } else {
      // Mark for deletion
      item.classList.add('marked-delete', 'bg-red-50', 'border-red-300');
      item.classList.remove('bg-white', 'border-slate-200');
      item.querySelector('img').classList.add('opacity-40');
      item.querySelector('.flex-1 p:first-child').classList.add('line-through', 'text-red-400');
      item.querySelector('.flex-1 p:last-child').textContent = 'Sẽ xóa';
      item.querySelector('.flex-1 p:last-child').classList.add('text-red-500');
      item.querySelector('.flex-1 p:last-child').classList.remove('text-slate-500');
      item.querySelector('.delete-btn span').textContent = 'undo';
      // Add hidden input
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'delete_images[]';
      input.value = imageId;
      item.appendChild(input);
    }
  }

  function incrementValue(btn) {
    const input = btn.parentElement.querySelector('input');
    input.value = parseInt(input.value) + 1;
  }

  function decrementValue(btn) {
    const input = btn.parentElement.querySelector('input');
    if (parseInt(input.value) > 1) {
      input.value = parseInt(input.value) - 1;
    }
  }

  function removeEquipmentRow(btn) {
    const row = btn.closest('tr');
    row.remove();
    
    // Check if table is empty and show empty row
    const tbody = document.querySelector('#equipmentTable tbody');
    if (tbody.querySelectorAll('tr:not(.empty-row)').length === 0) {
      tbody.innerHTML = '<tr class="hover:bg-slate-50 empty-row"><td colspan="3" class="px-6 py-8 text-center text-slate-500 text-sm">Chưa có thiết bị nào</td></tr>';
    }
  }

  function handleImageUpload(event) {
    const files = event.target.files;
    const gallery = document.getElementById('imageGallery');
    const count = document.getElementById('imageCount');
    
    Array.from(files).forEach((file, index) => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const row = document.createElement('div');
        row.className = 'flex items-center gap-4 p-3 border border-green-200 rounded-lg bg-green-50 hover:bg-green-100 transition-all group';
        row.setAttribute('data-image-item', 'true');
        row.innerHTML = `
          <img src="${e.target.result}" alt="${file.name}" class="w-16 h-16 object-cover rounded">
          <div class="flex-1">
            <p class="text-sm font-medium text-slate-900 truncate" title="${file.name}">${truncateFilename(file.name)}</p>
            <p class="text-xs text-green-600">${(file.size / 1024).toFixed(2)} KB - Ảnh mới</p>
          </div>
          <button type="button" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded transition-all opacity-0 group-hover:opacity-100" onclick="this.closest('div').remove(); updateImageCount();">
            <span class="material-symbols-outlined text-lg">delete</span>
          </button>
        `;
        gallery.appendChild(row);
        updateImageCount();
      };
      reader.readAsDataURL(file);
    });
    
    count.textContent = files.length;
  }

  function updateImageCount() {
    const gallery = document.getElementById('imageGallery');
    const count = document.getElementById('imageCount');
    const items = gallery.querySelectorAll('[data-image-item]');
    count.textContent = items.length;
  }

  function removeAmenity(element) {
    element.remove();
  }

  function addAmenityModal() {
    document.getElementById('amenityModal').classList.remove('hidden');
    document.getElementById('amenitySearch').value = '';
    filterAmenities('');
    
    // Mark already selected amenities
    const selectedIds = Array.from(document.querySelectorAll('#amenitiesList input[name="amenities[]"]')).map(input => input.value);
    document.querySelectorAll('#amenityGrid .amenity-item').forEach(item => {
      const id = item.getAttribute('data-id');
      if (selectedIds.includes(id)) {
        item.classList.add('ring-2', 'ring-green-500', 'bg-green-50', 'opacity-50', 'pointer-events-none');
        item.querySelector('input[type="checkbox"]').checked = false;
        // Add "Đã chọn" label
        if (!item.querySelector('.selected-label')) {
          const label = document.createElement('span');
          label.className = 'selected-label text-xs text-green-600 font-bold';
          label.textContent = 'Đã chọn';
          item.appendChild(label);
        }
      } else {
        item.classList.remove('ring-2', 'ring-green-500', 'bg-green-50', 'ring-blue-900', 'opacity-50', 'pointer-events-none');
        const label = item.querySelector('.selected-label');
        if (label) label.remove();
      }
    });
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
        id: input.closest('.amenity-item').getAttribute('data-id'),
        name: input.getAttribute('data-name'),
        icon: input.getAttribute('data-icon')
      }));

    if (selected.length === 0) {
      alert('Vui lòng chọn ít nhất một tiện ích!');
      return;
    }

    const list = document.getElementById('amenitiesList');
    
    selected.forEach(amenity => {
      // Check if already exists by ID
      const exists = list.querySelector(`input[name="amenities[]"][value="${amenity.id}"]`);
      if (!exists) {
        const span = document.createElement('span');
        span.className = 'inline-flex items-center gap-2 px-3 py-2 bg-blue-100 text-blue-900 rounded-full text-sm font-medium cursor-pointer hover:bg-blue-200 transition-all';
        span.setAttribute('data-id', amenity.id);
        span.onclick = function() { removeAmenity(this); };
        span.innerHTML = `
          <span class="material-symbols-outlined text-sm">${amenity.icon}</span>
          ${amenity.name}
          <input type="hidden" name="amenities[]" value="${amenity.id}">
          <span class="material-symbols-outlined text-sm cursor-pointer">close</span>
        `;
        list.insertBefore(span, list.lastElementChild);
      }
    });

    closeAmenityModal();
  }

  function addEquipmentRow(id, name, category, quantity) {
    const table = document.getElementById('equipmentTable');
    const tbody = table.querySelector('tbody');
    
    // Check if already exists
    if (tbody.querySelector(`input[name="equipments[]"][value="${id}"]`)) {
      return; // Already exists
    }
    
    // Remove empty row if exists
    const emptyRow = tbody.querySelector('tr.empty-row');
    if (emptyRow) {
      emptyRow.remove();
    }
    
    const row = document.createElement('tr');
    row.className = 'border-b border-slate-100 hover:bg-slate-50';
    row.innerHTML = `
      <td class="px-6 py-4">
        <div>
          <p class="font-medium text-slate-900">${name}</p>
          <p class="text-xs text-slate-500">${category || 'Chưa phân loại'}</p>
        </div>
        <input type="hidden" name="equipments[]" value="${id}">
      </td>
      <td class="px-6 py-4 text-right">
        <div class="flex items-center justify-end">
          <button type="button" onclick="decrementValue(this)" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 border border-slate-300 rounded-l-lg">
            <span class="material-symbols-outlined text-sm">remove</span>
          </button>
          <input type="number" name="equipment_quantities[${id}]" value="${quantity}" class="w-12 h-8 text-center border-y border-slate-300 text-sm" min="1">
          <button type="button" onclick="incrementValue(this)" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 border border-slate-300 rounded-r-lg">
            <span class="material-symbols-outlined text-sm">add</span>
          </button>
        </div>
      </td>
      <td class="px-6 py-4 text-center">
        <button type="button" onclick="removeEquipmentRow(this)" class="text-red-500 hover:text-red-700 transition-all">
          <span class="material-symbols-outlined">delete</span>
        </button>
      </td>
    `;
    tbody.appendChild(row);
  }

  function openEquipmentModal() {
    document.getElementById('equipmentModal').classList.remove('hidden');
    document.getElementById('equipmentSearch').value = '';
    filterEquipment('');
    
    // Mark already selected equipment
    const selectedIds = Array.from(document.querySelectorAll('#equipmentTable input[name="equipments[]"]')).map(input => input.value);
    document.querySelectorAll('#equipmentGrid .equipment-item').forEach(item => {
      const id = item.getAttribute('data-id');
      const iconDiv = item.querySelector('div:last-of-type');
      if (selectedIds.includes(id)) {
        item.classList.add('ring-2', 'ring-green-500', 'bg-green-50', 'opacity-50', 'pointer-events-none');
        item.classList.remove('ring-blue-900');
        item.querySelector('input[type="checkbox"]').checked = false;
        iconDiv.innerHTML = '<span class="material-symbols-outlined text-green-500">check_circle</span><span class="text-xs text-green-600 font-bold ml-1">Đã chọn</span>';
      } else {
        item.classList.remove('ring-2', 'ring-green-500', 'bg-green-50', 'ring-blue-900', 'opacity-50', 'pointer-events-none');
        iconDiv.innerHTML = '<span class="material-symbols-outlined text-slate-300">radio_button_unchecked</span>';
      }
    });
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
    // Don't toggle if already selected (disabled state)
    if (element.classList.contains('pointer-events-none')) {
      return;
    }
    
    const isSelected = element.classList.toggle('ring-2');
    element.classList.toggle('ring-blue-900');
    element.classList.toggle('bg-blue-50');
    const checkbox = element.querySelector('input[type="checkbox"]');
    checkbox.checked = !checkbox.checked;
    
    // Update icon
    const iconDiv = element.querySelector('div:last-of-type');
    if (checkbox.checked) {
      iconDiv.innerHTML = '<span class="material-symbols-outlined text-blue-900">check_circle</span>';
    } else {
      iconDiv.innerHTML = '<span class="material-symbols-outlined text-slate-300">radio_button_unchecked</span>';
    }
  }

  function uncheckAllEquipment() {
    document.querySelectorAll('#equipmentGrid .equipment-item').forEach(item => {
      // Don't uncheck already selected (disabled) items
      if (!item.classList.contains('pointer-events-none')) {
        item.classList.remove('ring-2', 'ring-blue-900', 'bg-blue-50');
        item.querySelector('input[type="checkbox"]').checked = false;
        const iconDiv = item.querySelector('div:last-of-type');
        iconDiv.innerHTML = '<span class="material-symbols-outlined text-slate-300">radio_button_unchecked</span>';
      }
    });
  }

  function confirmEquipment() {
    const selected = Array.from(document.querySelectorAll('#equipmentGrid .equipment-item input[type="checkbox"]:checked'))
      .map(input => ({
        id: input.closest('.equipment-item').getAttribute('data-id'),
        name: input.getAttribute('data-name'),
        category: input.getAttribute('data-category')
      }));

    if (selected.length === 0) {
      alert('Vui lòng chọn ít nhất một thiết bị!');
      return;
    }

    selected.forEach(equipment => {
      addEquipmentRow(equipment.id, equipment.name, equipment.category, '1');
    });

    closeEquipmentModal();
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
      @forelse($viewModel->allAmenities() as $amenity)
        <div class="amenity-item p-4 border border-slate-300 rounded-lg text-center cursor-pointer hover:bg-slate-50 transition-all" onclick="toggleAmenity(this)" data-text="{{ strtolower($amenity->name) }}" data-id="{{ $amenity->id }}">
          <input type="checkbox" hidden data-name="{{ $amenity->name }}" data-icon="{{ $amenity->icon ?? 'star' }}">
          <span class="material-symbols-outlined text-2xl block mb-2">{{ $amenity->icon ?? 'star' }}</span>
          <p class="text-xs font-bold text-slate-600">{{ $amenity->name }}</p>
        </div>
      @empty
        <div class="col-span-4 text-center text-slate-500 py-8">
          <span class="material-symbols-outlined text-4xl block mb-2">info</span>
          <p>Chưa có tiện ích nào trong hệ thống</p>
        </div>
      @endforelse
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
      @forelse($viewModel->allEquipments() as $equipment)
        <div class="equipment-item p-4 border border-slate-300 rounded-lg cursor-pointer hover:bg-slate-50 transition-all flex items-start justify-between" onclick="toggleEquipment(this)" data-text="{{ strtolower($equipment->name . ' ' . ($equipment->category->name ?? '')) }}" data-id="{{ $equipment->id }}">
          <div>
            <p class="font-bold text-slate-900">{{ $equipment->name }}</p>
            <p class="text-xs text-slate-500">{{ $equipment->category->name ?? 'Chưa phân loại' }}</p>
          </div>
          <div class="text-slate-300">
            <span class="material-symbols-outlined">radio_button_unchecked</span>
          </div>
          <input type="checkbox" hidden data-name="{{ $equipment->name }}" data-category="{{ $equipment->category->name ?? '' }}">
        </div>
      @empty
        <div class="text-center text-slate-500 py-8">
          <span class="material-symbols-outlined text-4xl block mb-2">info</span>
          <p>Chưa có thiết bị nào trong hệ thống</p>
        </div>
      @endforelse
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
