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
      <h1 class="text-3xl font-bold text-slate-900">Chỉnh sửa tiện ích</h1>
      <p class="text-slate-500 text-sm mt-1">Cập nhật thông tin tiện ích cho hệ thống khách sạn Urban Luxe.</p>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
      <form id="utilityForm" action="{{ route('admin.utilities.update', $viewModel->utility()->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Utility Name -->
        <div class="flex flex-col gap-2">
          <label for="name" class="text-sm font-bold text-slate-700">Tên tiện ích <span class="text-red-500">*</span></label>
          <input 
            type="text" 
            id="name" 
            name="name" 
            value="{{ old('name', $viewModel->utility()->name) }}"
            placeholder="VD: Wifi tốc độ cao"
            class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-900/20 focus:border-transparent outline-none transition-all"
          />
          @error('name')
            <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
          @enderror
        </div>

        <!-- Icon Selection -->
        <div class="flex flex-col gap-2">
          <label for="iconSearch" class="text-sm font-bold text-slate-700">Chọn biểu tượng <span class="text-red-500">*</span></label>
          <input 
            type="text" 
            id="iconSearch" 
            placeholder="Tìm kiếm biểu tượng (VD: wifi, bed, pool...)"
            class="w-full px-4 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-900/20 outline-none text-sm"
          />
          <span class="text-xs text-slate-500">
            Nhập tên icon tiếng Anh để tìm. Xem thêm icon tại 
            <a href="https://fonts.google.com/icons" target="_blank" class="text-blue-600 hover:underline">Google Material Symbols</a>
          </span>

          <!-- Icon Grid -->
          <div id="iconGrid" class="grid grid-cols-8 gap-2 p-4 bg-slate-50 border border-slate-200 rounded-lg max-h-80 overflow-y-auto">
            @foreach($viewModel->availableIcons() as $icon)
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
            value="{{ old('icon', $viewModel->utility()->icon) }}"
          />

          @error('icon')
            <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
          @enderror
          
          <div id="iconPreview">
            @if(old('icon', $viewModel->utility()->icon))
              <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="p-2 bg-blue-100 rounded-lg">
                  <span class="material-symbols-outlined text-3xl text-blue-900">{{ old('icon', $viewModel->utility()->icon) }}</span>
                </div>
                <span class="text-sm text-blue-900 font-medium">Đã chọn: <span class="font-bold">{{ old('icon', $viewModel->utility()->icon) }}</span></span>
              </div>
            @endif
          </div>
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
            Cập nhật thông tin
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

  // Select icon from grid
  iconOptions.forEach(option => {
    option.addEventListener('click', (e) => {
      e.preventDefault();
      selectIcon(option.getAttribute('data-icon'));
    });
  });

  // Function to select/highlight icon
  function selectIcon(iconName) {
    iconInput.value = iconName;
    
    // Reset all icons
    iconOptions.forEach(opt => {
      opt.classList.remove('border-blue-900', 'bg-blue-900', 'shadow-lg');
      opt.classList.add('border-slate-200');
      opt.querySelector('span').classList.remove('text-white');
      opt.querySelector('span').classList.add('text-slate-500');
    });
    
    // Highlight selected icon if in grid
    const selectedOption = document.querySelector(`[data-icon="${iconName}"]`);
    if (selectedOption) {
      selectedOption.classList.remove('border-slate-200');
      selectedOption.classList.add('border-blue-900', 'bg-blue-900', 'shadow-lg', 'shadow-blue-900/30');
      selectedOption.querySelector('span').classList.remove('text-slate-500');
      selectedOption.querySelector('span').classList.add('text-white');
    }

    // Update preview
    updateIconPreview(iconName);
  }

  // Update icon preview
  function updateIconPreview(iconName) {
    const previewContainer = document.getElementById('iconPreview');
    if (iconName) {
      previewContainer.innerHTML = `
        <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
          <div class="p-2 bg-blue-100 rounded-lg">
            <span class="material-symbols-outlined text-3xl text-blue-900">${iconName}</span>
          </div>
          <span class="text-sm text-blue-900 font-medium">Đã chọn: <span class="font-bold">${iconName}</span></span>
        </div>
      `;
    } else {
      previewContainer.innerHTML = '';
    }
  }

  // Icon search - filter grid AND allow custom input
  iconSearch.addEventListener('input', (e) => {
    const searchTerm = e.target.value.toLowerCase().trim();
    
    // Filter icons in grid
    iconOptions.forEach(option => {
      const iconName = option.getAttribute('data-icon');
      option.style.display = iconName.includes(searchTerm) ? '' : 'none';
    });

    // If user types a custom icon name (press Enter or blur)
    if (searchTerm) {
      selectIcon(searchTerm);
    }
  });

  // Highlight selected icon on page load
  if (iconInput.value) {
    selectIcon(iconInput.value);
  }
</script>

@endsection
