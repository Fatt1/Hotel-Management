@extends("layouts.admin")
@section('content')

<div class="flex-1 flex flex-col">
  <div class="p-8 max-w-2xl mx-auto w-full">
    <!-- Back Button -->
    <a href="{{ route('admin.services.index') }}" class="text-blue-900 text-sm font-bold flex items-center gap-1 mb-6">
      <span class="material-symbols-outlined">arrow_back</span>
      Quay lại danh sách
    </a>

    <!-- Page Title -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-slate-900">Thêm tiện ích mới</h1>
      <p class="text-slate-500 text-sm mt-1">Cập nhật thông tin tiện ích cho hệ thống khách sạn Urban Luxe.</p>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
      <form id="serviceForm" action="{{ route('admin.services.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Service Name -->
        <div class="flex flex-col gap-2">
          <label for="name" class="text-sm font-bold text-slate-700">Tên tiện ích <span class="text-red-500">*</span></label>
          <input 
            type="text" 
            id="name" 
            name="name" 
            value="{{ old('name') }}"
            placeholder="VD: Wifi tốc độ cao"
            class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-900/20 focus:border-transparent outline-none transition-all"
          />
          @error('name')
            <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
          @enderror
        </div>

        <!-- Service Group -->
        <div class="flex flex-col gap-2">
          <label for="group_id" class="text-sm font-bold text-slate-700">Nhóm tiện ích <span class="text-red-500">*</span></label>
          <select 
            id="group_id" 
            name="group_id"
            class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-900/20 focus:border-transparent outline-none transition-all"
          >
            <option value="">-- Chọn nhóm tiện ích --</option>
            @foreach($groups as $group)
              <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                {{ $group->service_name }}
              </option>
            @endforeach
          </select>
          @error('group_id')
            <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
          @enderror
        </div>

        <!-- Icon Selection -->
        <div class="flex flex-col gap-2">
          <label for="iconSearch" class="text-sm font-bold text-slate-700">Chọn biểu tượng</label>
          <div class="flex gap-3">
            <input 
              type="text" 
              id="iconSearch" 
              placeholder="Tìm kiếm biểu tượng..."
              class="flex-1 px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-900/20 outline-none"
            />
            <button type="button" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 font-medium transition-all" id="clearIconBtn">
              Xóa
            </button>
          </div>

          <!-- Icon Grid -->
          <div id="iconGrid" class="grid grid-cols-8 gap-2 p-4 bg-slate-50 border border-slate-200 rounded-lg overflow-y-auto max-h-64">
            @php
              $icons = ['wifi', 'ac_unit', 'groups', 'restaurant', 'sports_gymnastics', 'spa', 'pool', 'local_parking', 
                       'local_florist', 'local_bar', 'local_cafe', 'fitness_center', 'tv', 'videogame_asset_lock', 'beach_access', 
                       'desk', 'chair', 'bed', 'bathtub', 'wc', 'shower', 'kitchen', 'room_service', 'business_center',
                       'meeting_room', 'conference_room', 'event_available', 'event_busy', 'directions_run', 'landscape',
                       'local_library', 'music_note', 'theater_comedy', 'nightlife', 'card_giftcard'];
            @endphp
            @foreach($icons as $icon)
              <button 
                type="button" 
                class="icon-option p-3 rounded-lg border border-slate-200 hover:border-blue-900 hover:bg-blue-50 transition-all cursor-pointer flex items-center justify-center" 
                data-icon="{{ $icon }}"
                title="{{ $icon }}"
              >
                <span class="material-symbols-outlined text-2xl text-slate-600">{{ $icon }}</span>
              </button>
            @endforeach
          </div>

          <input 
            type="hidden" 
            id="icon" 
            name="icon" 
            value="{{ old('icon') }}"
          />
          
          @if(old('icon'))
            <div class="flex items-center gap-2 p-2 bg-blue-50 border border-blue-200 rounded-lg">
              <span class="material-symbols-outlined text-2xl text-blue-900">{{ old('icon') }}</span>
              <span class="text-sm text-blue-900 font-medium">Đã chọn: {{ old('icon') }}</span>
            </div>
          @endif
        </div>

        <!-- Unit Price -->
        <div class="flex flex-col gap-2">
          <label for="unit_price" class="text-sm font-bold text-slate-700">Giá tiện ích (VNĐ) <span class="text-red-500">*</span></label>
          <input 
            type="number" 
            id="unit_price" 
            name="unit_price" 
            value="{{ old('unit_price') }}"
            placeholder="VD: 50000"
            step="0.01"
            class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-900/20 focus:border-transparent outline-none transition-all"
          />
          @error('unit_price')
            <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
          @enderror
        </div>

        <!-- Unit -->
        <div class="flex flex-col gap-2">
          <label for="unit" class="text-sm font-bold text-slate-700">Đơn vị <span class="text-red-500">*</span></label>
          <input 
            type="text" 
            id="unit" 
            name="unit" 
            value="{{ old('unit') }}"
            placeholder="VD: suất, khu vực, ngày"
            class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-900/20 focus:border-transparent outline-none transition-all"
          />
          @error('unit')
            <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
          @enderror
        </div>

        <!-- Form Actions -->
        <div class="flex gap-3 pt-4 border-t border-slate-100">
          <a href="{{ route('admin.services.index') }}" class="flex-1 px-4 py-2.5 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-all text-center font-medium">
            Hủy bỏ
          </a>
          <button 
            type="submit" 
            class="flex-1 px-4 py-2.5 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition-all font-bold"
          >
            Lưu thông tin
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Icon selection
  const iconOptions = document.querySelectorAll('.icon-option');
  const iconInput = document.getElementById('icon');
  const iconSearch = document.getElementById('iconSearch');
  const clearIconBtn = document.getElementById('clearIconBtn');

  iconOptions.forEach(option => {
    option.addEventListener('click', (e) => {
      e.preventDefault();
      const icon = option.getAttribute('data-icon');
      iconInput.value = icon;
      
      // Update UI
      iconOptions.forEach(opt => opt.classList.remove('border-blue-900', 'bg-blue-50'));
      option.classList.add('border-blue-900', 'bg-blue-50');
    });
  });

  // Icon search
  iconSearch.addEventListener('keyup', (e) => {
    const searchTerm = e.target.value.toLowerCase();
    iconOptions.forEach(option => {
      const iconName = option.getAttribute('data-icon');
      option.style.display = iconName.includes(searchTerm) ? '' : 'none';
    });
  });

  // Clear icon
  clearIconBtn.addEventListener('click', (e) => {
    e.preventDefault();
    iconInput.value = '';
    iconOptions.forEach(opt => opt.classList.remove('border-blue-900', 'bg-blue-50'));
  });

  // Highlight selected icon on load
  if (iconInput.value) {
    const selectedOption = document.querySelector(`[data-icon="${iconInput.value}"]`);
    if (selectedOption) {
      selectedOption.classList.add('border-blue-900', 'bg-blue-50');
    }
  }
</script>

@endsection
