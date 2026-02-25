@if (session()->has('success') || session()->has('error'))
  @php
    // Xác định loại thông báo và nội dung từ Session
    $type = session()->has('success') ? 'success' : 'error';
    $message = session('success') ?? session('error');

    // Cấu hình giao diện dựa trên $type
    $bgColor = $type === 'success' ? 'bg-emerald-500' : 'bg-rose-500';
    $textColor = $type === 'success' ? 'text-emerald-500' : 'text-rose-500';
    $icon = $type === 'success' ? 'check_circle' : 'error';
    $title = $type === 'success' ? 'Thành công' : 'Lỗi';
  @endphp

  <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10"
    x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-10"
    class="fixed top-6 right-6 z-[9999] flex w-full max-w-sm overflow-hidden rounded-2xl bg-white dark:bg-slate-800 shadow-2xl ring-1 ring-slate-200 dark:ring-slate-700"
    role="alert">

    <div class="w-2 {{ $bgColor }}"></div>

    <div class="flex flex-1 items-center p-4">
      <div class="flex-shrink-0">
        <span class="material-symbols-outlined !text-3xl {{ $textColor }}">{{ $icon }}</span>
      </div>

      <div class="ml-4 flex-1">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $title }}</h3>
        <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">{{ $message }}</p>
      </div>

      <div class="ml-4 flex flex-shrink-0">
        <button type="button" @click="show = false"
          class="inline-flex rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
          <span class="material-symbols-outlined !text-xl">close</span>
        </button>
      </div>
    </div>
  </div>
@endif