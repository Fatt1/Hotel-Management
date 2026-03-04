@extends('client.layouts.app')

@section('title', 'Ẩm Thực & Nhà Hàng — Urban Luxe Hotel')

@section('content')

{{-- ====================================================
    HERO SECTION — Full-screen cinematic banner
===================================================== --}}
<section style="
    position: relative;
    height: 100vh;
    min-height: 600px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    overflow: hidden;
">
    {{-- Background image --}}
    <img
        src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1920&q=80"
        alt="Fine Dining"
        style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center;"
    >
    {{-- Dark gradient overlay --}}
    <div style="position: absolute; inset: 0; background: linear-gradient(160deg, rgba(10,8,5,0.55) 0%, rgba(10,8,5,0.80) 60%, rgba(10,8,5,0.97) 100%);"></div>

    {{-- Hero content --}}
    <div style="position: relative; z-index: 2; max-width: 720px; padding: 0 2rem;">
        {{-- Eyebrow --}}
        <div style="display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="height: 1px; width: 60px; background-color: #d4af37;"></div>
            <span style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.3em; text-transform: uppercase; color: #d4af37;">Ẩm Thực Đặc Sắc</span>
            <div style="height: 1px; width: 60px; background-color: #d4af37;"></div>
        </div>

        {{-- Headline — italic serif like Figma --}}
        <h1 style="font-family: 'Playfair Display', serif; font-size: clamp(3rem, 7vw, 5.5rem); font-style: italic; font-weight: 700; color: #f0ead8; line-height: 1.05; margin-bottom: 1.5rem; letter-spacing: -0.01em;">
            Nghệ Thuật Ẩm Thực
        </h1>

        <p style="font-size: 0.95rem; color: #9a8e7e; line-height: 1.8; max-width: 520px; margin: 0 auto 2.5rem; font-weight: 300;">
            Một bản giao hưởng của hương vị trong không gian thanh lịch tinh tế.
            Trải nghiệm nghệ thuật ẩm thực thuần túy, định nghĩa lại tiêu chuẩn tiệc ngon.
        </p>

        {{-- CTA buttons --}}
        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
            <a href="#thuc-don"
               style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase;
                      background-color: #d4af37; color: #0a0a0a; padding: 0.85rem 2rem;
                      text-decoration: none; transition: background 0.2s;"
               onmouseover="this.style.backgroundColor='#c9a227'"
               onmouseout="this.style.backgroundColor='#d4af37'">
                Xem Thực Đơn
            </a>
            <a href="#nha-hang"
               style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase;
                      border: 1px solid rgba(212,175,55,0.5); color: #d4af37; padding: 0.85rem 2rem;
                      text-decoration: none; transition: all 0.2s;"
               onmouseover="this.style.borderColor='#d4af37'; this.style.backgroundColor='rgba(212,175,55,0.08)'"
               onmouseout="this.style.borderColor='rgba(212,175,55,0.5)'; this.style.backgroundColor='transparent'">
                Đặt Bàn Riêng
            </a>
        </div>
    </div>

    {{-- Scroll arrow --}}
    <div style="position: absolute; bottom: 2.5rem; left: 50%; transform: translateX(-50%); animation: bounce 2s infinite;">
        <span style="color: #6b5f4f; font-size: 1.25rem;">∨</span>
    </div>
</section>

{{-- ====================================================
    SEASONAL CREATIONS + TASTING MENU
===================================================== --}}
<section id="thuc-don" style="background-color: #0a0a0a; padding: 7rem 0;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 2rem; display: grid; grid-template-columns: 2fr 3fr; gap: 5rem; align-items: start;">

        {{-- Left: Text + Chef Image --}}
        <div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: clamp(2rem, 4vw, 2.75rem); font-style: italic; font-weight: 600; color: #f0ead8; line-height: 1.2; margin-bottom: 1.25rem;">
                Sáng Tạo Theo Mùa
            </h2>
            <p style="font-size: 0.85rem; color: #6b6050; line-height: 1.9; margin-bottom: 2.5rem;">
                Bếp trưởng của chúng tôi kiến tạo thực đơn biến đổi theo từng mùa,
                lựa chọn nguyên liệu tươi ngon địa phương để tạo ra những món ăn
                vừa hiện đại vừa trường tồn.
            </p>

            {{-- Chef photo --}}
            <div style="position: relative;">
                <img
                    src="https://images.unsplash.com/photo-1577219491135-ce391730fb2c?w=800&q=80"
                    alt="Bếp Trưởng"
                    style="width: 100%; height: 380px; object-fit: cover; object-position: center; display: block;"
                >
                {{-- Caption overlay --}}
                <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 2rem 1.5rem; background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, transparent 100%);">
                    <p style="font-size: 0.6rem; font-weight: 700; letter-spacing: 0.25em; text-transform: uppercase; color: #d4af37; margin-bottom: 0.3rem;">Bếp Trưởng & Bàn Ăn</p>
                    <p style="font-family: 'Playfair Display', serif; font-style: italic; font-size: 1.1rem; color: #f0ead8;">Một hành trình ẩm thực đích thực.</p>
                </div>
            </div>
        </div>

        {{-- Right: menus from DB --}}
        <div id="nha-hang">
            {{-- Section label --}}
            <p style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.3em; text-transform: uppercase; color: #d4af37; margin-bottom: 2.5rem;">Các Nhà Hàng & Quầy Bar</p>

            {{-- Loop through dinings from DB --}}
            @forelse($dinings as $dining)
            <div style="
                display: grid;
                grid-template-columns: 220px 1fr;
                gap: 2rem;
                align-items: center;
                padding: 2rem 0;
                border-bottom: 1px solid rgba(212,175,55,0.1);
            ">
                {{-- Thumbnail --}}
                <div style="overflow: hidden; aspect-ratio: 16/10;">
                    <img
                        src="{{ $dining->image
                            ? asset($dining->image)
                            : 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=600&q=80' }}"
                        alt="{{ $dining->name }}"
                        style="width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.4s;"
                        onmouseover="this.style.transform='scale(1.06)'"
                        onmouseout="this.style.transform='scale(1)'"
                        onerror="this.src='https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&q=80'"
                    >
                </div>

                {{-- Info --}}
                <div>
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.35rem; font-weight: 600; color: #f0ead8; margin-bottom: 0.5rem;">
                        {{ $dining->name }}
                    </h3>
                    <p style="font-size: 0.8rem; color: #6b6050; line-height: 1.7; margin-bottom: 1rem;">
                        {{ $dining->description }}
                    </p>
                    <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: center;">
                        @if($dining->opening_hours)
                        <span style="font-size: 0.72rem; color: #9a8e7e; display: flex; align-items: center; gap: 0.4rem;">
                            <span style="color: #d4af37;">◷</span> {{ $dining->opening_hours }}
                        </span>
                        @endif
                        @if($dining->location)
                        <span style="font-size: 0.72rem; color: #9a8e7e; display: flex; align-items: center; gap: 0.4rem;">
                            <span style="color: #d4af37;">◎</span> {{ $dining->location }}
                        </span>
                        @endif
                    </div>
                    <a href="#"
                       style="display: inline-block; margin-top: 1.25rem; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: #d4af37; text-decoration: none; border-bottom: 1px solid rgba(212,175,55,0.3); padding-bottom: 2px; transition: border-color 0.2s;"
                       onmouseover="this.style.borderColor='#d4af37'"
                       onmouseout="this.style.borderColor='rgba(212,175,55,0.3)'">
                        Khám Phá →
                    </a>
                </div>
            </div>
            @empty
            <p style="color: #6b6050; font-style: italic; font-size: 0.9rem;">Hiện chưa có thông tin nhà hàng.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- ====================================================
    ATMOSPHERE BANNER — Full-width immersive image
===================================================== --}}
<section style="position: relative; height: 50vh; overflow: hidden;">
    <img
        src="https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?w=1920&q=80"
        alt="Không gian nhà hàng"
        style="width: 100%; height: 100%; object-fit: cover; object-position: center top;"
    >
    <div style="position: absolute; inset: 0; background: rgba(10,8,5,0.5);"></div>
    <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;">
        <div style="text-align: center;">
            <div style="display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 1rem;">
                <div style="height: 1px; width: 40px; background-color: #d4af37;"></div>
                <span style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.3em; text-transform: uppercase; color: #d4af37;">Đặt Chỗ Ngay</span>
                <div style="height: 1px; width: 40px; background-color: #d4af37;"></div>
            </div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: clamp(1.75rem, 4vw, 3rem); font-style: italic; color: #f0ead8; font-weight: 600; margin-bottom: 1.5rem;">
                Dành Riêng Cho Những Khoảnh Khắc Đặc Biệt
            </h2>
            <a href="#"
               style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; background-color: #d4af37; color: #0a0a0a; padding: 0.9rem 2.5rem; text-decoration: none;"
               onmouseover="this.style.backgroundColor='#c9a227'"
               onmouseout="this.style.backgroundColor='#d4af37'">
                Liên Hệ Chúng Tôi
            </a>
        </div>
    </div>
</section>

{{-- Bounce animation keyframes --}}
@push('styles')
<style>
@keyframes bounce {
    0%, 100% { transform: translateX(-50%) translateY(0); }
    50%       { transform: translateX(-50%) translateY(8px); }
}
</style>
@endpush

@endsection
