@extends('client.layouts.app')

@section('title', 'Tiện Ích - Urban Luxe Hotel')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@section('content')

  {{-- ====================================================
       HERO SECTION
       ==================================================== --}}
  <section class="relative pt-40 pb-20 text-center overflow-hidden bg-[radial-gradient(ellipse_at_50%_0%,rgba(212,175,55,0.06)_0%,transparent_70%),linear-gradient(180deg,#0a0a0a_0%,#0f0d0a_100%)]">
    <div class="absolute inset-0 bg-[url('https://source.unsplash.com/1600x900/?luxury-hotel-lobby')] bg-center bg-cover opacity-[0.06] grayscale-[40%]"></div>
    <div class="relative z-10 max-w-2xl mx-auto px-6">
      <div class="flex items-center justify-center gap-3 mb-5">
        <div class="w-10 h-px bg-[#d4af37]/60"></div>
        <span class="text-[10px] font-bold tracking-[0.3em] uppercase text-[#d4af37]">Dành Riêng Cho Bạn</span>
        <div class="w-10 h-px bg-[#d4af37]/60"></div>
      </div>
      <h1 class="font-['Playfair_Display'] text-[clamp(2.8rem,6vw,5rem)] font-bold text-[#e8e0d0] leading-[1.1] mb-6 tracking-tight">Trải Nghiệm<br>Đô Thị Đỉnh Cao</h1>
      <p class="max-w-[520px] mx-auto text-[14.5px] text-[#7a6e60] leading-relaxed">
        Vượt qua giới hạn thông thường, mọi tiện ích của chúng tôi được thiết kế để mang đến
        không gian thưởng thức sang trọng. Từng chi tiết được chăm chút tinh tế,
        đáp ứng mọi nhu cầu và nâng tầm kỳ nghỉ của bạn giữa lòng thành phố.
      </p>
    </div>
  </section>

  {{-- ====================================================
       FEATURED AMENITY CARDS (2×2 GRID)
       ==================================================== --}}
  <section class="max-w-6xl mx-auto px-8 py-12 pb-20">
    <div class="grid sm:grid-cols-2 gap-6">

      {{-- Card 1: Elite Spa & Wellness --}}
      <div class="group relative rounded-sm overflow-hidden cursor-pointer aspect-[16/9] sm:aspect-[4/3] bg-[#111]">
        <img
          src="https://picsum.photos/seed/spa-wellness/800/600"
          alt="Elite Spa & Wellness"
          loading="lazy"
          class="w-full h-full object-cover block transition-transform duration-[700ms] ease-[cubic-bezier(0.25,0.46,0.45,0.94)] brightness-[65%] group-hover:scale-105 group-hover:brightness-[75%]"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-[#050505]/92 via-[#050505]/40 to-transparent flex flex-col justify-end p-7 transition-colors duration-400 group-hover:from-[#050505]/95 group-hover:via-[#050505]/55 group-hover:to-[#050505]/10">
          <div class="w-9 h-9 mb-3.5 flex items-center justify-center border border-[#d4af37]/50 rounded-full text-[#d4af37] text-[15px] transition-colors duration-300 group-hover:bg-[#d4af37]/15 group-hover:border-[#d4af37]">
            <i class="fas fa-spa"></i>
          </div>
          <h3 class="font-['Playfair_Display'] text-[21px] font-semibold text-[#e8e0d0] mb-2 leading-snug">Elite Spa & Wellness</h3>
          <div class="w-8 h-px bg-[#d4af37] mb-2.5 transition-all duration-300 group-hover:w-[52px]"></div>
          <p class="text-[12.5px] leading-relaxed text-[#9a9080] max-w-[320px] transition-colors duration-300 group-hover:text-[#b8aa98]">
            Không gian nghỉ dưỡng toàn diện với liệu pháp nhiệt, massage đặt riêng
            và bầu không khí yên tĩnh giúp phục hồi tâm trí và thể chất.
          </p>
        </div>
      </div>

      {{-- Card 2: Skyline Infinity Pool --}}
      <div class="group relative rounded-sm overflow-hidden cursor-pointer aspect-[16/9] sm:aspect-[4/3] bg-[#111]">
        <img
          src="https://picsum.photos/seed/infinity-pool/800/600"
          alt="Bể bơi vô cực trên sân thượng"
          loading="lazy"
          class="w-full h-full object-cover block transition-transform duration-[700ms] ease-[cubic-bezier(0.25,0.46,0.45,0.94)] brightness-[65%] group-hover:scale-105 group-hover:brightness-[75%]"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-[#050505]/92 via-[#050505]/40 to-transparent flex flex-col justify-end p-7 transition-colors duration-400 group-hover:from-[#050505]/95 group-hover:via-[#050505]/55 group-hover:to-[#050505]/10">
          <div class="w-9 h-9 mb-3.5 flex items-center justify-center border border-[#d4af37]/50 rounded-full text-[#d4af37] text-[15px] transition-colors duration-300 group-hover:bg-[#d4af37]/15 group-hover:border-[#d4af37]">
            <i class="fas fa-swimming-pool"></i>
          </div>
          <h3 class="font-['Playfair_Display'] text-[21px] font-semibold text-[#e8e0d0] mb-2 leading-snug">Bể Bơi Vô Cực Skyline</h3>
          <div class="w-8 h-px bg-[#d4af37] mb-2.5 transition-all duration-300 group-hover:w-[52px]"></div>
          <p class="text-[12.5px] leading-relaxed text-[#9a9080] max-w-[320px] transition-colors duration-300 group-hover:text-[#b8aa98]">
            Lơ lửng trên nền ánh đèn thành phố tại bể bơi vô cực kiểm soát nhiệt độ
            trên tầng thượng. Tầm nhìn toàn cảnh đường chân trời ngoạn mục.
          </p>
        </div>
      </div>

      {{-- Card 3: State-of-the-Art Fitness --}}
      <div class="group relative rounded-sm overflow-hidden cursor-pointer aspect-[16/9] sm:aspect-[4/3] bg-[#111]">
        <img
          src="https://picsum.photos/seed/luxury-gym/800/600"
          alt="Phòng tập thể thao hiện đại"
          loading="lazy"
          class="w-full h-full object-cover block transition-transform duration-[700ms] ease-[cubic-bezier(0.25,0.46,0.45,0.94)] brightness-[65%] group-hover:scale-105 group-hover:brightness-[75%]"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-[#050505]/92 via-[#050505]/40 to-transparent flex flex-col justify-end p-7 transition-colors duration-400 group-hover:from-[#050505]/95 group-hover:via-[#050505]/55 group-hover:to-[#050505]/10">
          <div class="w-9 h-9 mb-3.5 flex items-center justify-center border border-[#d4af37]/50 rounded-full text-[#d4af37] text-[15px] transition-colors duration-300 group-hover:bg-[#d4af37]/15 group-hover:border-[#d4af37]">
            <i class="fas fa-dumbbell"></i>
          </div>
          <h3 class="font-['Playfair_Display'] text-[21px] font-semibold text-[#e8e0d0] mb-2 leading-snug">Phòng Gym Đẳng Cấp</h3>
          <div class="w-8 h-px bg-[#d4af37] mb-2.5 transition-all duration-300 group-hover:w-[52px]"></div>
          <p class="text-[12.5px] leading-relaxed text-[#9a9080] max-w-[320px] transition-colors duration-300 group-hover:text-[#b8aa98]">
            Duy trì phong độ với thiết bị Technogym tiên tiến, phòng tập cá nhân
            và lớp học wellness theo yêu cầu mở cửa 24/7.
          </p>
        </div>
      </div>

      {{-- Card 4: 24/7 Bespoke Concierge --}}
      <div class="group relative rounded-sm overflow-hidden cursor-pointer aspect-[16/9] sm:aspect-[4/3] bg-[#111]">
        <img
          src="https://picsum.photos/seed/hotel-lobby/800/600"
          alt="Dịch vụ Concierge 24/7"
          loading="lazy"
          class="w-full h-full object-cover block transition-transform duration-[700ms] ease-[cubic-bezier(0.25,0.46,0.45,0.94)] brightness-[65%] group-hover:scale-105 group-hover:brightness-[75%]"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-[#050505]/92 via-[#050505]/40 to-transparent flex flex-col justify-end p-7 transition-colors duration-400 group-hover:from-[#050505]/95 group-hover:via-[#050505]/55 group-hover:to-[#050505]/10">
          <div class="w-9 h-9 mb-3.5 flex items-center justify-center border border-[#d4af37]/50 rounded-full text-[#d4af37] text-[15px] transition-colors duration-300 group-hover:bg-[#d4af37]/15 group-hover:border-[#d4af37]">
            <i class="fas fa-concierge-bell"></i>
          </div>
          <h3 class="font-['Playfair_Display'] text-[21px] font-semibold text-[#e8e0d0] mb-2 leading-snug">Concierge Riêng 24/7</h3>
          <div class="w-8 h-px bg-[#d4af37] mb-2.5 transition-all duration-300 group-hover:w-[52px]"></div>
          <p class="text-[12.5px] leading-relaxed text-[#9a9080] max-w-[320px] transition-colors duration-300 group-hover:text-[#b8aa98]">
            Từ đặt bàn nhà hàng độc quyền đến vận chuyển hạng sang, đội ngũ
            tận tâm của chúng tôi sẵn sàng 24 giờ để hoàn thiện hành trình của bạn.
          </p>
        </div>
      </div>

    </div>
  </section>

@endsection

@push('scripts')
<script>
  // Lazy-load fallback: replace broken Unsplash images with solid dark placeholder
  document.querySelectorAll('.group img').forEach(function(img) {
    img.addEventListener('error', function() {
      this.style.display = 'none';
      this.parentElement.style.background = '#1a1710';
    });
  });
</script>
@endpush
