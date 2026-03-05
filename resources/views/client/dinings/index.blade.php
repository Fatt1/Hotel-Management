@extends('client.layouts.app')

@section('title', 'Ẩm Thực & Nhà Hàng — Urban Luxe Hotel')

@section('content')

{{-- ====================================================
    HERO SECTION — Full-screen cinematic banner
===================================================== --}}
<section class="relative h-screen min-h-[600px] flex items-center justify-center text-center overflow-hidden">
    <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1920&q=80" alt="Fine Dining" class="absolute inset-0 w-full h-full object-cover object-center">
    <div class="absolute inset-0 bg-gradient-to-br from-[#0a0805]/60 via-[#0a0805]/80 to-[#0a0805]/95"></div>

    <div class="relative z-10 max-w-3xl px-8">
        <div class="flex items-center justify-center gap-4 mb-6">
            <div class="h-px w-16 bg-[#d4af37]"></div>
            <span class="text-[10.5px] font-bold tracking-[0.3em] uppercase text-[#d4af37]">Ẩm Thực Đặc Sắc</span>
            <div class="h-px w-16 bg-[#d4af37]"></div>
        </div>

        <h1 class="font-['Playfair_Display'] text-[clamp(3rem,7vw,5.5rem)] italic font-bold text-[#f0ead8] leading-tight mb-6 tracking-tight">
            Nghệ Thuật Ẩm Thực
        </h1>

        <p class="text-[15px] text-[#9a8e7e] leading-relaxed max-w-xl mx-auto mb-10 font-light">
            Một bản giao hưởng của hương vị trong không gian thanh lịch tinh tế.
            Trải nghiệm nghệ thuật ẩm thực thuần túy, định nghĩa lại tiêu chuẩn tiệc ngon.
        </p>

        <div class="flex justify-center gap-4 flex-wrap">
            <a href="#thuc-don" class="text-[11px] font-bold tracking-[0.15em] uppercase bg-[#d4af37] text-[#0a0a0a] py-3.5 px-8 transition-colors hover:bg-[#c9a227]">
                Xem Thực Đơn
            </a>
            <a href="#nha-hang" class="text-[11px] font-bold tracking-[0.15em] uppercase border border-[#d4af37]/50 text-[#d4af37] py-3.5 px-8 transition-all hover:bg-[#d4af37]/10 hover:border-[#d4af37]">
                Đặt Bàn Riêng
            </a>
        </div>
    </div>

    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce">
        <span class="text-[#6b5f4f] text-xl">∨</span>
    </div>
</section>

{{-- ====================================================
    SEASONAL CREATIONS + TASTING MENU
===================================================== --}}
<section id="thuc-don" class="bg-[#0a0a0a] py-28">
    <div class="max-w-7xl mx-auto px-8 grid lg:grid-cols-5 gap-20 items-start">

        {{-- Left: Text + Chef Image --}}
        <div class="lg:col-span-2">
            <h2 class="font-['Playfair_Display'] text-[clamp(2rem,4vw,2.75rem)] italic font-semibold text-[#f0ead8] leading-snug mb-5">
                Sáng Tạo Theo Mùa
            </h2>
            <p class="text-[13.5px] text-[#6b6050] leading-[1.9] mb-10">
                Bếp trưởng của chúng tôi kiến tạo thực đơn biến đổi theo từng mùa,
                lựa chọn nguyên liệu tươi ngon địa phương để tạo ra những món ăn
                vừa hiện đại vừa trường tồn.
            </p>

            <div class="relative">
                <img src="https://images.unsplash.com/photo-1577219491135-ce391730fb2c?w=800&q=80" alt="Bếp Trưởng" class="w-full h-[380px] object-cover object-center block">
                <div class="absolute inset-x-0 bottom-0 p-8 pt-16 bg-gradient-to-t from-black/85 to-transparent">
                    <p class="text-[10px] font-bold tracking-[0.25em] uppercase text-[#d4af37] mb-1">Bếp Trưởng & Bàn Ăn</p>
                    <p class="font-['Playfair_Display'] italic text-lg text-[#f0ead8]">Một hành trình ẩm thực đích thực.</p>
                </div>
            </div>
        </div>

        {{-- Right: Menus --}}
        <div id="nha-hang" class="lg:col-span-3">
            <p class="text-[10.5px] font-bold tracking-[0.3em] uppercase text-[#d4af37] mb-10">Các Nhà Hàng & Quầy Bar</p>

            {{-- Static Dining 1 --}}
            <div class="grid sm:grid-cols-[220px_1fr] gap-8 items-center py-8 border-b border-[#d4af37]/10 group">
                <div class="overflow-hidden aspect-[16/10]">
                    <img src="https://picsum.photos/seed/dining-cung-dinh/600/400" alt="Nhà Hàng Cung Đình" class="w-full h-full object-cover block transition-transform duration-500 group-hover:scale-105">
                </div>
                <div>
                    <h3 class="font-['Playfair_Display'] text-2xl font-semibold text-[#f0ead8] mb-2">Nhà Hàng Cung Đình</h3>
                    <p class="text-[13px] text-[#6b6050] leading-relaxed mb-4">Trải nghiệm tinh hoa ẩm thực Việt trong không gian truyền thống sang trọng.</p>
                    <div class="flex flex-wrap gap-6 items-center">
                        <span class="text-[11.5px] text-[#9a8e7e] flex items-center gap-1.5"><span class="text-[#d4af37]">◷</span> 10:00 - 22:00</span>
                        <span class="text-[11.5px] text-[#9a8e7e] flex items-center gap-1.5"><span class="text-[#d4af37]">◎</span> Tầng 1</span>
                    </div>
                    <a href="#" class="inline-block mt-5 text-[10.5px] font-bold tracking-[0.2em] uppercase text-[#d4af37] border-b border-[#d4af37]/30 pb-0.5 transition-colors hover:border-[#d4af37]">Khám Phá →</a>
                </div>
            </div>

            {{-- Static Dining 2 --}}
            <div class="grid sm:grid-cols-[220px_1fr] gap-8 items-center py-8 border-b border-[#d4af37]/10 group">
                <div class="overflow-hidden aspect-[16/10]">
                    <img src="https://picsum.photos/seed/dining-ocean/600/400" alt="Nhà Hàng Ocean Breeze" class="w-full h-full object-cover block transition-transform duration-500 group-hover:scale-105">
                </div>
                <div>
                    <h3 class="font-['Playfair_Display'] text-2xl font-semibold text-[#f0ead8] mb-2">Nhà Hàng Ocean Breeze</h3>
                    <p class="text-[13px] text-[#6b6050] leading-relaxed mb-4">Chuyên các loại hải sản tươi sống và tiệc buffet cao cấp phong cách Âu Á.</p>
                    <div class="flex flex-wrap gap-6 items-center">
                        <span class="text-[11.5px] text-[#9a8e7e] flex items-center gap-1.5"><span class="text-[#d4af37]">◷</span> 06:00 - 23:00</span>
                        <span class="text-[11.5px] text-[#9a8e7e] flex items-center gap-1.5"><span class="text-[#d4af37]">◎</span> Tầng 2</span>
                    </div>
                    <a href="#" class="inline-block mt-5 text-[10.5px] font-bold tracking-[0.2em] uppercase text-[#d4af37] border-b border-[#d4af37]/30 pb-0.5 transition-colors hover:border-[#d4af37]">Khám Phá →</a>
                </div>
            </div>

            {{-- Static Dining 3 --}}
            <div class="grid sm:grid-cols-[220px_1fr] gap-8 items-center py-8 border-b border-[#d4af37]/10 group">
                <div class="overflow-hidden aspect-[16/10]">
                    <img src="https://picsum.photos/seed/dining-skyline/600/400" alt="Skyline Bar" class="w-full h-full object-cover block transition-transform duration-500 group-hover:scale-105">
                </div>
                <div>
                    <h3 class="font-['Playfair_Display'] text-2xl font-semibold text-[#f0ead8] mb-2">Skyline Bar</h3>
                    <p class="text-[13px] text-[#6b6050] leading-relaxed mb-4">Tận hưởng cocktail tuyệt hảo và ngắm nhìn toàn cảnh thành phố về đêm.</p>
                    <div class="flex flex-wrap gap-6 items-center">
                        <span class="text-[11.5px] text-[#9a8e7e] flex items-center gap-1.5"><span class="text-[#d4af37]">◷</span> 17:00 - 02:00</span>
                        <span class="text-[11.5px] text-[#9a8e7e] flex items-center gap-1.5"><span class="text-[#d4af37]">◎</span> Tầng Thượng (Rooftop)</span>
                    </div>
                    <a href="#" class="inline-block mt-5 text-[10.5px] font-bold tracking-[0.2em] uppercase text-[#d4af37] border-b border-[#d4af37]/30 pb-0.5 transition-colors hover:border-[#d4af37]">Khám Phá →</a>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ====================================================
    ATMOSPHERE BANNER — Full-width immersive image
===================================================== --}}
<section class="relative h-[50vh] overflow-hidden">
    <img src="https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?w=1920&q=80" alt="Không gian nhà hàng" class="w-full h-full object-cover object-top">
    <div class="absolute inset-0 bg-[#0a0805]/50"></div>
    <div class="absolute inset-0 flex items-center justify-center">
        <div class="text-center px-4">
            <div class="flex items-center justify-center gap-4 mb-4">
                <div class="h-px w-10 bg-[#d4af37]"></div>
                <span class="text-[10.5px] font-bold tracking-[0.3em] uppercase text-[#d4af37]">Đặt Chỗ Ngay</span>
                <div class="h-px w-10 bg-[#d4af37]"></div>
            </div>
            <h2 class="font-['Playfair_Display'] text-[clamp(1.75rem,4vw,3rem)] italic text-[#f0ead8] font-semibold mb-6">
                Dành Riêng Cho Những Khoảnh Khắc Đặc Biệt
            </h2>
            <a href="#" class="inline-block text-[11px] font-bold tracking-[0.15em] uppercase bg-[#d4af37] text-[#0a0a0a] py-3.5 px-10 transition-colors hover:bg-[#c9a227]">
                Liên Hệ Chúng Tôi
            </a>
        </div>
    </div>
</section>

@endsection
