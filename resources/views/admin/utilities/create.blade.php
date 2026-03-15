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
            <div id="iconGridLoading" class="col-span-8 flex justify-center items-center py-8 hidden">
              <span class="text-sm text-slate-400">Đang tải...</span>
            </div>
            <div id="iconGridEmpty" class="col-span-8 text-center py-8 hidden">
              <span class="text-sm text-slate-400">Không tìm thấy icon nào.</span>
            </div>
          </div>

          <input 
            type="hidden" 
            id="icon" 
            name="icon" 
            value="{{ old('icon') }}"
          />

          @error('icon')
            <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
          @enderror
          
          <div id="iconPreview">
            @if(old('icon'))
              <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="p-2 bg-blue-100 rounded-lg">
                  <span class="material-symbols-outlined text-3xl text-blue-900">{{ old('icon') }}</span>
                </div>
                <span class="text-sm text-blue-900 font-medium">Đã chọn: <span class="font-bold">{{ old('icon') }}</span></span>
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
            Lưu thông tin
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  const iconInput  = document.getElementById('icon');
  const iconSearch = document.getElementById('iconSearch');
  const iconGrid   = document.getElementById('iconGrid');
  const loadingEl  = document.getElementById('iconGridLoading');
  const emptyEl    = document.getElementById('iconGridEmpty');
  const searchUrl  = '{{ route('admin.utilities.icons.search') }}';

  let debounceTimer = null;

  // ── Highlight a button as selected ──────────────────────────────────────
  function highlightButton(btn) {
    iconGrid.querySelectorAll('.icon-option').forEach(opt => {
      opt.classList.remove('border-blue-900', 'bg-blue-900', 'shadow-lg', 'shadow-blue-900/30');
      opt.classList.add('border-slate-200');
      opt.querySelector('span').classList.remove('text-white');
      opt.querySelector('span').classList.add('text-slate-500');
    });
    btn.classList.remove('border-slate-200');
    btn.classList.add('border-blue-900', 'bg-blue-900', 'shadow-lg', 'shadow-blue-900/30');
    btn.querySelector('span').classList.remove('text-slate-500');
    btn.querySelector('span').classList.add('text-white');
  }

  // ── Render icon buttons into grid ────────────────────────────────────────
  function renderIcons(icons) {
    iconGrid.querySelectorAll('.icon-option').forEach(el => el.remove());
    emptyEl.classList.toggle('hidden', icons.length > 0);

    icons.forEach(iconName => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'icon-option p-3 rounded-lg border-2 border-slate-200 hover:border-blue-900 hover:bg-blue-50 transition-all cursor-pointer flex items-center justify-center group relative';
      btn.dataset.icon = iconName;
      btn.title = iconName;
      btn.innerHTML = `<span class="material-symbols-outlined text-3xl text-slate-500 group-hover:text-blue-900">${iconName}</span>`;
      btn.addEventListener('click', (e) => { e.preventDefault(); selectIcon(iconName); });
      iconGrid.appendChild(btn);
    });

    // Re-highlight if current value is visible
    if (iconInput.value) {
      const existing = iconGrid.querySelector(`[data-icon="${iconInput.value}"]`);
      if (existing) highlightButton(existing);
    }
  }

  // ── Fetch icons from backend (proxies Google Fonts API) ─────────────────
  function fetchIcons(search = '') {
    loadingEl.classList.remove('hidden');
    iconGrid.querySelectorAll('.icon-option').forEach(el => el.remove());
    emptyEl.classList.add('hidden');

    const url = search ? `${searchUrl}?search=${encodeURIComponent(search)}` : searchUrl;

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(icons => {
        loadingEl.classList.add('hidden');
        renderIcons(icons);
      })
      .catch(() => {
        loadingEl.classList.add('hidden');
        emptyEl.classList.remove('hidden');
      });
  }

  // ── Select icon ──────────────────────────────────────────────────────────
  function selectIcon(iconName) {
    iconInput.value = iconName;
    const btn = iconGrid.querySelector(`[data-icon="${iconName}"]`);
    if (btn) highlightButton(btn);
    updateIconPreview(iconName);
  }

  // ── Preview ──────────────────────────────────────────────────────────────
  function updateIconPreview(iconName) {
    const previewContainer = document.getElementById('iconPreview');
    previewContainer.innerHTML = iconName ? `
      <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="p-2 bg-blue-100 rounded-lg">
          <span class="material-symbols-outlined text-3xl text-blue-900">${iconName}</span>
        </div>
        <span class="text-sm text-blue-900 font-medium">Đã chọn: <span class="font-bold">${iconName}</span></span>
      </div>` : '';
  }

  // ── Default hotel icons from ViewModel (rendered by PHP) ─────────────────
  const defaultIcons = @json($viewModel->availableIcons());

  // ── Search with debounce ─────────────────────────────────────────────────
  iconSearch.addEventListener('input', (e) => {
    clearTimeout(debounceTimer);
    const term = e.target.value.trim();
    if (term === '') {
      // Quay về danh sách mặc định khi xóa hết từ khóa
      renderIcons(defaultIcons);
    } else {
      debounceTimer = setTimeout(() => fetchIcons(term), 350);
    }
  });

  // ── Init: hiển thị icon khách sạn mặc định, không cần gọi API ────────────
  renderIcons(defaultIcons);
  @if(old('icon'))
    updateIconPreview('{{ old('icon') }}');
  @endif
</script>

@endsection
