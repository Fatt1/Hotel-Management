@extends("layouts.admin")
@section('content')

<div class="flex-1 flex flex-col">
  <div class="p-8 max-w-2xl mx-auto w-full">
    <!-- Back Button -->
    <a href="{{ route('admin.utilities.index') }}" class="text-blue-900 text-sm font-bold flex items-center gap-1 mb-6">
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
      <form id="utilityForm" action="{{ route('admin.utilities.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Utility Name -->
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

        <!-- Icon Selection -->
        <div class="flex flex-col gap-2">
          <label for="iconSearch" class="text-sm font-bold text-slate-700">Chọn biểu tượng</label>
          <div class="flex gap-2">
            <input 
              type="text" 
              id="iconSearch" 
              placeholder="Tìm kiếm biểu tượng..."
              class="flex-1 px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-900/20 outline-none text-sm"
            />
            <button type="button" class="px-6 py-2.5 bg-slate-200 hover:bg-slate-300 rounded-lg text-slate-700 font-medium transition-all text-sm" id="clearIconBtn">
              Xóa
            </button>
          </div>

          <!-- Icon Grid -->
          <div id="iconGrid" class="grid grid-cols-8 gap-2 p-4 bg-slate-50 border border-slate-200 rounded-lg max-h-80 overflow-y-auto">
            @php
              $icons = [
                'wifi', 'ac_unit', 'chair', 'bed', 'bathtub', 'restaurant', 'fitness_center', 'spa',
                'pool', 'local_parking', 'tv', 'shower', 'kitchen', 'room_service', 'business_center', 'event_available',
                'directions_run', 'landscape', 'music_note', 'theater_comedy', 'local_bar', 'local_cafe', 'desk', 'groups',
                'meeting_room', 'beach_access', 'wc', 'dry_cleaning', 'local_florist', 'concierge', 'card_giftcard', 'balcony',
                'door_front', 'elevator', 'stairs', 'window', 'blinds', 'luggage', 'safe', 'mirror',
                'lamp', 'phone', 'lock', 'key', 'towel', 'iron', 'sofa', 'pillow'
              ];
            @endphp
            @foreach($icons as $icon)
              <button 
                type="button" 
                class="icon-option p-3 rounded-lg border-2 border-slate-200 hover:border-blue-900 hover:bg-blue-50 transition-all cursor-pointer flex items-center justify-center group relative" 
                data-icon="{{ $icon }}"
                title="{{ $icon }}"
              >
                <span class="material-symbols-outlined text-3xl text-slate-500 group-hover:text-blue-900">{{ $icon }}</span>
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
            <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
              <div class="p-2 bg-blue-100 rounded-lg">
                <span class="material-symbols-outlined text-3xl text-blue-900">{{ old('icon') }}</span>
              </div>
              <span class="text-sm text-blue-900 font-medium">Biểu tượng: <span class="font-bold">{{ old('icon') }}</span></span>
            </div>
          @endif
        </div>

        <!-- Form Actions -->
        <div class="flex gap-3 pt-4 border-t border-slate-100">
          <a href="{{ route('admin.utilities.index') }}" class="flex-1 px-4 py-2.5 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-all text-center font-medium">
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
      
      // Update UI - Remove old selection
      iconOptions.forEach(opt => {
        opt.classList.remove('border-2', 'border-blue-900', 'bg-blue-900', 'shadow-lg');
        opt.classList.add('border-2', 'border-slate-200');
        opt.querySelector('span').classList.remove('text-white');
        opt.querySelector('span').classList.add('text-slate-500');
      });
      
      // Add new selection with prominent color
      option.classList.remove('border-slate-200');
      option.classList.add('border-blue-900', 'bg-blue-900', 'shadow-lg', 'shadow-blue-900/30');
      option.querySelector('span').classList.remove('text-slate-500');
      option.querySelector('span').classList.add('text-white');
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
      selectedOption.classList.remove('border-slate-200');
      selectedOption.classList.add('border-blue-900', 'bg-blue-900', 'shadow-lg', 'shadow-blue-900/30');
      selectedOption.querySelector('span').classList.remove('text-slate-500');
      selectedOption.querySelector('span').classList.add('text-white');
    }
  }
</script>

@endsection
