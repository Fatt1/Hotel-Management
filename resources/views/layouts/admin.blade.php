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
  @livewireStyles
</head>

<body class="text-slate-900 font-sans bg-slate-50 min-h-screen flex">
  @include("layouts.admin.sidebar")
  <main class="flex flex-1 flex-col min-w-0">
    @include("layouts.admin.header")
    @yield("content")
  </main>


  <!-- Axios CDN -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


  @yield('scripts')
  @livewireScripts
</body>

</html>