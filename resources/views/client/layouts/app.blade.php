<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Urban Luxe Hotel')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
</head>
<body class="bg-[#0a0a0a] font-['Inter'] text-[#e8e0d0]">

  {{-- Navbar --}}
  <nav class="fixed inset-x-0 top-0 z-50 border-b border-[#d4af37]/15 bg-[#0a0a0a]/95 backdrop-blur-sm">
    <div class="mx-auto flex h-16 max-w-300 items-center justify-between px-8">
      {{-- Logo --}}
      <a href="/" class="flex items-center gap-2 no-underline">
        <span class="font-['Playfair_Display'] text-xl font-bold uppercase tracking-[0.15em] text-[#e8e0d0]">Urban Luxe</span>
      </a>

      {{-- Nav Links --}}
      @php
        $navRooms     = request()->routeIs('client.rooms.*');
        $navAmenities = request()->routeIs('client.amenities.*');
        $navDinings   = request()->routeIs('client.dinings.*');
        $navGallery   = request()->routeIs('client.gallery.*');
      @endphp
      <div class="flex items-center gap-10">
        <a href="{{ route('client.rooms.index') }}"
           @class([
             'border-b pb-0.5 text-[0.7rem] font-semibold uppercase tracking-[0.15em] no-underline transition-colors',
             'border-[#d4af37] text-[#d4af37]' => $navRooms,
             'border-transparent text-[#9a9080] hover:text-[#d4af37]' => ! $navRooms,
           ])>Phòng</a>

        <a href="{{ route('client.amenities.index') }}"
           @class([
             'border-b pb-0.5 text-[0.7rem] font-semibold uppercase tracking-[0.15em] no-underline transition-colors',
             'border-[#d4af37] text-[#d4af37]' => $navAmenities,
             'border-transparent text-[#9a9080] hover:text-[#d4af37]' => ! $navAmenities,
           ])>Tiện Ích</a>

        <a href="{{ route('client.dinings.index') }}"
           @class([
             'border-b pb-0.5 text-[0.7rem] font-semibold uppercase tracking-[0.15em] no-underline transition-colors',
             'border-[#d4af37] text-[#d4af37]' => $navDinings,
             'border-transparent text-[#9a9080] hover:text-[#d4af37]' => ! $navDinings,
           ])>Ẩm Thực</a>

        <a href="{{ route('client.gallery.index') }}"
           @class([
             'border-b pb-0.5 text-[0.7rem] font-semibold uppercase tracking-[0.15em] no-underline transition-colors',
             'border-[#d4af37] text-[#d4af37]' => $navGallery,
             'border-transparent text-[#9a9080] hover:text-[#d4af37]' => ! $navGallery,
           ])>Thư Viện</a>
      </div>

      {{-- CTA Buttons --}}
      <div class="flex items-center gap-4">
        @auth("customer")
          @php
            $customer = auth('customer')->user();
            $fullName = trim($customer->full_name ?: 'Guest');
            $nameParts = preg_split('/\s+/', $fullName);
            $initials = strtoupper(substr($nameParts[0] ?? 'G', 0, 1) . substr(end($nameParts) ?: '', 0, 1));
            if (strlen($initials) === 1) {
                $initials .= 'U';
            }
          @endphp
          <div class="group relative">
            <div class="relative inline-flex min-w-36.5 cursor-pointer items-center gap-[0.55rem] rounded-full border border-white/15 bg-white/10 py-[0.3rem] pl-[0.35rem] pr-[0.9rem] text-gray-100" aria-label="Tài khoản khách hàng">
              <span class="inline-flex size-[1.9rem] items-center justify-center rounded-full bg-[linear-gradient(145deg,#f2d4b2_0%,#e6ba8f_100%)] text-[0.72rem] font-bold tracking-[0.03em] text-[#2f3641]">{{ $initials }}</span>
              <span class="text-[0.98rem] font-medium leading-none text-gray-50">{{ $fullName }}</span>
              <span class="ml-auto text-[0.6rem] text-white/60 transition-transform duration-200 group-hover:rotate-180 group-focus-within:rotate-180">▼</span>
            </div>

            <div class="invisible absolute left-[-0.8rem] top-[calc(100%+0.75rem)] z-80 w-50 -translate-y-1.5 rounded-[14px] border border-white/10 bg-[linear-gradient(180deg,rgba(28,36,56,0.97)_0%,rgba(23,30,47,0.98)_100%)] px-[0.6rem] py-3 opacity-0 shadow-[0_18px_36px_rgba(0,0,0,0.42)] transition-all duration-200 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100">
              <p class="mb-[0.55rem] px-2 text-[0.72rem] uppercase tracking-[0.06em] text-[#8f96a8]">Tài khoản</p>

              <a href="#" class="flex items-center gap-[0.65rem] rounded-[10px] px-2 py-[0.58rem] text-[0.98rem] text-gray-200 no-underline transition-colors hover:bg-white/5" aria-disabled="true">
                <span class="w-4 text-center text-[0.86rem] text-[#5ea1ff]">👤</span>
                <span>Thông tin cá nhân</span>
              </a>

              <a href="#" class="flex items-center gap-[0.65rem] rounded-[10px] px-2 py-[0.58rem] text-[0.98rem] text-gray-200 no-underline transition-colors hover:bg-white/5" aria-disabled="true">
                <span class="w-4 text-center text-[0.86rem] text-[#5ea1ff]">🎟</span>
                <span>Lịch đặt phòng</span>
              </a>

              <div class="my-2 h-px bg-white/10"></div>

              <form method="POST" action="{{ route('client.logout') }}">
                @csrf
                <button type="submit" class="flex w-full cursor-pointer items-center gap-[0.65rem] rounded-[10px] border-none bg-transparent px-2 py-[0.58rem] text-left text-base text-[#ff6b6b] transition-colors hover:bg-[#ff6b6b]/10">
                  <span class="w-4 text-center text-[0.86rem] text-[#ff6b6b]">↪</span>
                  <span>Đăng xuất</span>
                </button>
              </form>
            </div>
          </div>
        @endauth
        @guest('customer')
           <a href="{{ route('client.login') }}" class="text-[0.7rem] font-semibold uppercase tracking-[0.12em] text-[#9a9080] no-underline transition-colors hover:text-[#e8e0d0]">Đăng Nhập</a>
        @endguest
       
        <a href="{{ route('client.rooms.index') }}" class="bg-[#d4af37] px-5 py-2 text-[0.7rem] font-semibold uppercase tracking-[0.12em] text-[#0a0a0a] no-underline transition-colors hover:bg-[#c9a227]">Đặt Phòng</a>
      </div>
    </div>
  </nav>

  {{-- Main Content --}}
  <main>
    @yield('content')
  </main>

  {{-- Footer --}}
  <footer class="border-t border-[#d4af37]/10 bg-[#050505] pb-8 pt-16">
    <div class="mx-auto max-w-300 px-8">
      <div class="mb-12 grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1.5fr]">
        {{-- Brand --}}
        <div>
          <div class="mb-4 flex items-center gap-2">
            <span class="font-['Playfair_Display'] text-base font-bold uppercase tracking-[0.15em] text-[#e8e0d0]">Urban Luxe</span>
          </div>
          <p class="mb-2 text-[0.8rem] leading-7 text-[#6b6050]">1700 Đường Market<br>San Francisco, CA 94102<br>Hoa Kỳ</p>
        </div>
        {{-- Company --}}
        <div>
          <p class="mb-5 text-[0.65rem] font-bold uppercase tracking-[0.2em] text-[#9a9080]">Công Ty</p>
          <div class="flex flex-col gap-3">
            @foreach(['Về Chúng Tôi', 'Sự Nghiệp', 'Báo Chí', 'Blog'] as $link)
              <a href="#" class="text-[0.8rem] text-[#6b6050] no-underline transition-colors hover:text-[#d4af37]">{{ $link }}</a>
            @endforeach
          </div>
        </div>
        {{-- Support --}}
        <div>
          <p class="mb-5 text-[0.65rem] font-bold uppercase tracking-[0.2em] text-[#9a9080]">Hỗ Trợ</p>
          <div class="flex flex-col gap-3">
            @foreach(['Trung Tâm Hỗ Trợ', 'Câu Hỏi Thường Gặp', 'Điều Khoản Dịch Vụ', 'Chính Sách Riêng Tư'] as $link)
              <a href="#" class="text-[0.8rem] text-[#6b6050] no-underline transition-colors hover:text-[#d4af37]">{{ $link }}</a>
            @endforeach
          </div>
        </div>
        {{-- Newsletter --}}
        <div>
          <p class="mb-3 text-[0.65rem] font-bold uppercase tracking-[0.2em] text-[#9a9080]">Nhận Thông Tin</p>
          <p class="mb-4 text-[0.75rem] text-[#6b6050]">Đăng ký nhận ưu đãi độc quyền.</p>
          <div class="flex">
            <input type="email" placeholder="Địa chỉ email" class="flex-1 border border-[#d4af37]/20 bg-[#111111] px-4 py-[0.6rem] text-[0.75rem] text-[#e8e0d0] outline-none placeholder:text-[#6b6050]">
            <button class="cursor-pointer border-none bg-[#d4af37] px-4 text-base text-[#0a0a0a] transition-colors hover:bg-[#c9a227]">→</button>
          </div>
        </div>
      </div>
      <div class="flex flex-col items-start justify-between gap-3 border-t border-[#d4af37]/10 pt-6 md:flex-row md:items-center">
        <p class="text-[0.7rem] text-[#3d3530]">© 2026 Urban Luxe Hotel. Đã đăng ký bản quyền.</p>
        <div class="flex gap-4">
          <a href="#" class="text-[0.7rem] text-[#3d3530] no-underline transition-colors hover:text-[#d4af37]">IG</a>
          <a href="#" class="text-[0.7rem] text-[#3d3530] no-underline transition-colors hover:text-[#d4af37]">FB</a>
        </div>
      </div>
    </div>
  </footer>

  @stack('scripts')
</body>
</html>
