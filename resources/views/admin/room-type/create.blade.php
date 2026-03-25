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
              <div class="flex gap-2">
                <input type="text" name="code" id="codeInput" value="{{ old('code', 'RT-' . str_pad(rand(10, 99), 2, '0', STR_PAD_LEFT)) }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('code') border-red-500 @enderror" readonly>
                <button type="button" onclick="generateCode()" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 rounded-lg transition-all" title="Tạo mã mới">
                  <span class="material-symbols-outlined text-slate-600">refresh</span>
                </button>
              </div>
              @error('code')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Trạng thái -->
            <div>
              <label class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 block">Trạng thái <span class="text-red-500">*</span></label>
              <select name="is_active" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('is_active') border-red-500 @enderror bg-white">
                <option value="1" {{ (int) old('is_active', 1) === 1 ? 'selected' : '' }}>Đang hoạt động</option>
                <option value="0" {{ (int) old('is_active', 1) === 0 ? 'selected' : '' }}>Không hoạt động</option>
              </select>
              @error('is_active')
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
                <input type="number" name="hourly_price" value="{{ old('hourly_price') }}" placeholder="200000" step="0.01" min="0" max="99999999" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('hourly_price') border-red-500 @enderror">
                @error('hourly_price')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>
              <div>
                <label class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 block">Giá ngày (VNĐ) <span class="text-red-500">*</span></label>
                <input type="number" name="daily_price" value="{{ old('daily_price') }}" placeholder="1500000" step="0.01" min="0" max="99999999" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-900 focus:ring-1 focus:ring-blue-900 @error('daily_price') border-red-500 @enderror">
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
                <input type="number" name="adult_quantity" value="{{ old('adult_quantity', 2) }}" min="1" class="w-12 text-center px-2 py-1 border border-slate-300 rounded focus:outline-none focus:border-blue-900" readonly>
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
                <input type="number" name="child_quantity" value="{{ old('child_quantity', 0) }}" min="0" class="w-12 text-center px-2 py-1 border border-slate-300 rounded focus:outline-none focus:border-blue-900" readonly>
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
                <input type="number" name="single_bed_quantity" value="{{ old('single_bed_quantity', 0) }}" min="0" class="w-12 text-center px-2 py-1 border border-slate-300 rounded focus:outline-none focus:border-blue-900" readonly>
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
                <input type="number" name="double_bed_quantity" value="{{ old('double_bed_quantity', 0) }}" min="0" class="w-12 text-center px-2 py-1 border border-slate-300 rounded focus:outline-none focus:border-blue-900" readonly>
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
          
          <div class="p-4">
            <div id="imageGallery" class="space-y-3">
              <div class="w-full py-16 bg-slate-50 rounded-lg flex items-center justify-center border border-dashed border-slate-300" data-placeholder="true">
                <div class="text-center">
                  <span class="material-symbols-outlined text-slate-400 text-4xl block mb-2">image_not_supported</span>
                  <p class="text-xs text-slate-500">Không có ảnh nào được tải lên</p>
                </div>
              </div>
            </div>
            <p class="text-xs text-slate-500 text-center mt-4">Total <span id="imageCount">0</span> images displayed (Editable metadata)</p>
          </div>
        </div>

        <!-- Amenities -->
        @php
          $oldAmenityIds = collect(old('amenities', []))->map(fn ($id) => (int) $id);
          $selectedAmenities = $viewModel->allAmenities()->whereIn('id', $oldAmenityIds->all());
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
          <div class="flex items-center gap-3 mb-4">
            <span class="material-symbols-outlined text-blue-900 text-2xl">star</span>
            <h3 class="text-lg font-bold text-slate-900">Tiện ích (Amenities)</h3>
          </div>
          
          <div class="flex flex-wrap gap-2" id="amenitiesList">
            @foreach($selectedAmenities as $amenity)
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
        @php
          $oldEquipmentIds = collect(old('equipments', []))->map(fn ($id) => (int) $id);
          $oldEquipmentQuantities = old('equipment_quantities', []);
          $selectedEquipments = $viewModel->allEquipments()->whereIn('id', $oldEquipmentIds->all());
        @endphp
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
                  @forelse($selectedEquipments as $equipment)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
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
                          <input type="number" name="equipment_quantities[{{ $equipment->id }}]" value="{{ (int) ($oldEquipmentQuantities[$equipment->id] ?? 1) }}" class="w-12 h-8 text-center border-y border-slate-300 text-sm" min="1">
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

@vite(['resources/js/admin/room-types/create.js'])

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
