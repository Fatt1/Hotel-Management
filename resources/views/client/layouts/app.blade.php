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
<body style="background-color: #0a0a0a; color: #e8e0d0; font-family: 'Inter', sans-serif;">

  {{-- Navbar --}}
  <nav style="position: fixed; top: 0; left: 0; right: 0; z-index: 50; background-color: rgba(10,10,10,0.95); border-bottom: 1px solid rgba(212,175,55,0.15);">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem; display: flex; align-items: center; height: 64px; justify-content: space-between;">
      {{-- Logo --}}
      <a href="/" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
        <span style="font-family: 'Playfair Display', serif; font-size: 1.25rem; font-weight: 700; color: #e8e0d0; letter-spacing: 0.15em; text-transform: uppercase;">Urban Luxe</span>
      </a>

      {{-- Nav Links --}}
      @php
        $navRooms     = request()->routeIs('client.rooms.*');
        $navAmenities = request()->routeIs('client.amenities.*');
        $navDinings   = request()->routeIs('client.dinings.*');
        $navGallery   = request()->routeIs('client.gallery.*');
      @endphp
      <div style="display: flex; align-items: center; gap: 2.5rem;">
        <a href="{{ route('client.rooms.index') }}"
           style="font-size: 0.7rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; text-decoration: none;
                  color: {{ $navRooms ? '#d4af37' : '#9a9080' }};
                  {{ $navRooms ? 'border-bottom: 1px solid #d4af37; padding-bottom: 2px;' : '' }}"
           @unless($navRooms) onmouseover="this.style.color='#d4af37'" onmouseout="this.style.color='#9a9080'" @endunless>Phòng</a>

        <a href="{{ route('client.amenities.index') }}"
           style="font-size: 0.7rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; text-decoration: none;
                  color: {{ $navAmenities ? '#d4af37' : '#9a9080' }};
                  {{ $navAmenities ? 'border-bottom: 1px solid #d4af37; padding-bottom: 2px;' : '' }}"
           @unless($navAmenities) onmouseover="this.style.color='#d4af37'" onmouseout="this.style.color='#9a9080'" @endunless>Tiện Ích</a>

        <a href="{{ route('client.dinings.index') }}"
           style="font-size: 0.7rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; text-decoration: none;
                  color: {{ $navDinings ? '#d4af37' : '#9a9080' }};
                  {{ $navDinings ? 'border-bottom: 1px solid #d4af37; padding-bottom: 2px;' : '' }}"
           @unless($navDinings) onmouseover="this.style.color='#d4af37'" onmouseout="this.style.color='#9a9080'" @endunless>Ẩm Thực</a>

        <a href="{{ route('client.gallery.index') }}"
           style="font-size: 0.7rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; text-decoration: none;
                  color: {{ $navGallery ? '#d4af37' : '#9a9080' }};
                  {{ $navGallery ? 'border-bottom: 1px solid #d4af37; padding-bottom: 2px;' : '' }}"
           @unless($navGallery) onmouseover="this.style.color='#d4af37'" onmouseout="this.style.color='#9a9080'" @endunless>Thư Viện</a>
      </div>

      {{-- CTA Buttons --}}
      <div style="display: flex; align-items: center; gap: 1rem;">
        <a href="{{ route('client.login') }}" style="font-size: 0.7rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: #9a9080; text-decoration: none;" onmouseover="this.style.color='#e8e0d0'" onmouseout="this.style.color='#9a9080'">Đăng Nhập</a>
        <a href="#" style="font-size: 0.7rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; background-color: #d4af37; color: #0a0a0a; padding: 0.5rem 1.25rem; text-decoration: none;" onmouseover="this.style.backgroundColor='#c9a227'" onmouseout="this.style.backgroundColor='#d4af37'">Đặt Phòng</a>
      </div>
    </div>
  </nav>

  {{-- Main Content --}}
  <main>
    @yield('content')
  </main>

  {{-- Footer --}}
  <footer style="background-color: #050505; border-top: 1px solid rgba(212,175,55,0.1); padding: 4rem 0 2rem;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
      <div style="display: grid; grid-template-columns: 1.5fr 1fr 1fr 1.5fr; gap: 3rem; margin-bottom: 3rem;">
        {{-- Brand --}}
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            <span style="font-family: 'Playfair Display', serif; font-size: 1rem; font-weight: 700; color: #e8e0d0; letter-spacing: 0.15em; text-transform: uppercase;">Urban Luxe</span>
          </div>
          <p style="font-size: 0.8rem; color: #6b6050; line-height: 1.7; margin-bottom: 0.5rem;">1700 Đường Market<br>San Francisco, CA 94102<br>Hoa Kỳ</p>
        </div>
        {{-- Company --}}
        <div>
          <p style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: #9a9080; margin-bottom: 1.25rem;">Công Ty</p>
          <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            @foreach(['Về Chúng Tôi', 'Sự Nghiệp', 'Báo Chí', 'Blog'] as $link)
              <a href="#" style="font-size: 0.8rem; color: #6b6050; text-decoration: none;" onmouseover="this.style.color='#d4af37'" onmouseout="this.style.color='#6b6050'">{{ $link }}</a>
            @endforeach
          </div>
        </div>
        {{-- Support --}}
        <div>
          <p style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: #9a9080; margin-bottom: 1.25rem;">Hỗ Trợ</p>
          <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            @foreach(['Trung Tâm Hỗ Trợ', 'Câu Hỏi Thường Gặp', 'Điều Khoản Dịch Vụ', 'Chính Sách Riêng Tư'] as $link)
              <a href="#" style="font-size: 0.8rem; color: #6b6050; text-decoration: none;" onmouseover="this.style.color='#d4af37'" onmouseout="this.style.color='#6b6050'">{{ $link }}</a>
            @endforeach
          </div>
        </div>
        {{-- Newsletter --}}
        <div>
          <p style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: #9a9080; margin-bottom: 0.75rem;">Nhận Thông Tin</p>
          <p style="font-size: 0.75rem; color: #6b6050; margin-bottom: 1rem;">Đăng ký nhận ưu đãi độc quyền.</p>
          <div style="display: flex;">
            <input type="email" placeholder="Địa chỉ email" style="flex: 1; background-color: #111; border: 1px solid rgba(212,175,55,0.2); color: #e8e0d0; padding: 0.6rem 1rem; font-size: 0.75rem; outline: none;">
            <button style="background-color: #d4af37; border: none; padding: 0 1rem; cursor: pointer; font-size: 1rem; color: #0a0a0a;" onmouseover="this.style.backgroundColor='#c9a227'" onmouseout="this.style.backgroundColor='#d4af37'">→</button>
          </div>
        </div>
      </div>
      <div style="border-top: 1px solid rgba(212,175,55,0.1); padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <p style="font-size: 0.7rem; color: #3d3530;">© 2026 Urban Luxe Hotel. Đã đăng ký bản quyền.</p>
        <div style="display: flex; gap: 1rem;">
          <a href="#" style="font-size: 0.7rem; color: #3d3530; text-decoration: none;" onmouseover="this.style.color='#d4af37'" onmouseout="this.style.color='#3d3530'">IG</a>
          <a href="#" style="font-size: 0.7rem; color: #3d3530; text-decoration: none;" onmouseover="this.style.color='#d4af37'" onmouseout="this.style.color='#3d3530'">FB</a>
        </div>
      </div>
    </div>
  </footer>

  @stack('scripts')
</body>
</html>
