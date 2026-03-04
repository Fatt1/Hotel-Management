<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Hotel</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css2?family=Geologica:wght,CRSV@100..900,0&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Jacquarda+Bastarda+9&family=Luxurious+Roman&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="text-slate-900 font-sans bg-slate-50 min-h-screen flex">
  @include("layouts.admin.sidebar")
  <main class="flex flex-1 flex-col min-w-0">
    @include("layouts.admin.header")
    
    <!-- Flash Messages -->
    <div class="px-8 pt-4 max-w-7xl mx-auto w-full">
      @if($message = session('success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-green-800 mb-4">
          <span class="material-symbols-outlined text-green-600">check_circle</span>
          <div class="flex-1">
            <p class="font-semibold text-sm">Thành công</p>
            <p class="text-sm">{{ $message }}</p>
          </div>
          <button onclick="this.parentElement.style.display='none'" class="text-green-600 hover:text-green-800 text-xl">
            <span class="material-symbols-outlined text-lg">close</span>
          </button>
        </div>
      @endif

      @if($message = session('error'))
        <div class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-red-800 mb-4">
          <span class="material-symbols-outlined text-red-600">error</span>
          <div class="flex-1">
            <p class="font-semibold text-sm">Lỗi</p>
            <p class="text-sm">{{ $message }}</p>
          </div>
          <button onclick="this.parentElement.style.display='none'" class="text-red-600 hover:text-red-800 text-xl">
            <span class="material-symbols-outlined text-lg">close</span>
          </button>
        </div>
      @endif

      @if($message = session('warning'))
        <div class="flex items-center gap-3 px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800 mb-4">
          <span class="material-symbols-outlined text-yellow-600">warning</span>
          <div class="flex-1">
            <p class="font-semibold text-sm">Cảnh báo</p>
            <p class="text-sm">{{ $message }}</p>
          </div>
          <button onclick="this.parentElement.style.display='none'" class="text-yellow-600 hover:text-yellow-800 text-xl">
            <span class="material-symbols-outlined text-lg">close</span>
          </button>
        </div>
      @endif

      @if($message = session('info'))
        <div class="flex items-center gap-3 px-4 py-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 mb-4">
          <span class="material-symbols-outlined text-blue-600">info</span>
          <div class="flex-1">
            <p class="font-semibold text-sm">Thông tin</p>
            <p class="text-sm">{{ $message }}</p>
          </div>
          <button onclick="this.parentElement.style.display='none'" class="text-blue-600 hover:text-blue-800 text-xl">
            <span class="material-symbols-outlined text-lg">close</span>
          </button>
        </div>
      @endif
    </div>
    
    @yield("content")
  </main>
  <div id="global-modal" wire:ignore class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog"
  aria-modal="true">
  <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
    <!-- Backdrop -->
    <div class="fixed inset-0 transition-opacity bg-black opacity-60 backdrop-blur-md" aria-hidden="true" onclick="closeModal()">
    </div>
    <!-- Modal Content -->
    <div
      class="relative inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle">
      <div id="global-modal-content" class="p-5">
      </div>
    </div>
  </div>
</div>

  <!-- Axios CDN -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  @stack('scripts')
</body>

</html>