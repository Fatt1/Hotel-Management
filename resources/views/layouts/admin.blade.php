<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', "Hotel")</title>
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
    
    <!-- Flash Messages (SweetAlert2) -->
    @if(session('success') || session('error') || session('warning') || session('info'))
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        @if($message = session('success'))
          Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: '{{ addslashes($message) }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#f0fdf4',
            color: '#166534',
            iconColor: '#16a34a',
          });
        @elseif($message = session('error'))
          Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: '{{ addslashes($message) }}',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ef4444',
          });
        @elseif($message = session('warning'))
          Swal.fire({
            icon: 'warning',
            title: 'Cảnh báo',
            text: '{{ addslashes($message) }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
          });
        @elseif($message = session('info'))
          Swal.fire({
            icon: 'info',
            title: 'Thông tin',
            text: '{{ addslashes($message) }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
          });
        @endif
      });
    </script>
    @endif
    
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

  <!-- SweetAlert2 CDN -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Axios CDN -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  @stack('scripts')
</body>

</html>