@extends('client.layouts.app')

@section('title', 'Tiện Ích - Urban Luxe Hotel')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
  /* ========================================================
     AMENITIES PAGE — Dark Luxury Theme
     ======================================================== */

  /* ---------- Hero Section ---------- */
  .amenities-hero {
    position: relative;
    padding-top: 160px;
    padding-bottom: 80px;
    text-align: center;
    overflow: hidden;
    background: radial-gradient(ellipse at 50% 0%, rgba(212,175,55,0.06) 0%, transparent 70%),
                linear-gradient(180deg, #0a0a0a 0%, #0f0d0a 100%);
  }
  .amenities-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url('https://source.unsplash.com/1600x900/?luxury-hotel-lobby') center/cover no-repeat;
    opacity: 0.06;
    filter: grayscale(40%);
  }
  .hero-eyebrow {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
  }
  .hero-eyebrow span {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: #d4af37;
  }
  .hero-eyebrow::before,
  .hero-eyebrow::after {
    content: '';
    display: block;
    width: 40px;
    height: 1px;
    background: #d4af37;
    opacity: 0.6;
  }
  .amenities-hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.8rem, 6vw, 5rem);
    font-weight: 700;
    line-height: 1.1;
    color: #e8e0d0;
    letter-spacing: -0.01em;
    margin-bottom: 1.5rem;
  }
  .amenities-hero p {
    max-width: 520px;
    margin: 0 auto;
    font-size: 0.9rem;
    line-height: 1.8;
    color: #7a6e60;
  }

  /* ---------- Featured Grid ---------- */
  .features-section {
    max-width: 1100px;
    margin: 0 auto;
    padding: 3rem 2rem 5rem;
  }
  .features-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
  }
  .feature-card {
    position: relative;
    border-radius: 2px;
    overflow: hidden;
    cursor: pointer;
    aspect-ratio: 4/3;
    background: #111;
  }
  .feature-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    filter: brightness(0.65);
  }
  .feature-card:hover img {
    transform: scale(1.05);
    filter: brightness(0.75);
  }
  .feature-card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
      to top,
      rgba(5,5,5,0.92) 0%,
      rgba(5,5,5,0.4) 45%,
      transparent 100%
    );
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 1.75rem;
    transition: background 0.4s ease;
  }
  .feature-card:hover .feature-card-overlay {
    background: linear-gradient(
      to top,
      rgba(5,5,5,0.95) 0%,
      rgba(5,5,5,0.55) 50%,
      rgba(5,5,5,0.1) 100%
    );
  }
  .feature-icon {
    width: 38px;
    height: 38px;
    margin-bottom: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(212,175,55,0.5);
    border-radius: 50%;
    color: #d4af37;
    font-size: 0.95rem;
    transition: background 0.3s, border-color 0.3s;
  }
  .feature-card:hover .feature-icon {
    background: rgba(212,175,55,0.15);
    border-color: #d4af37;
  }
  .feature-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.3rem;
    font-weight: 600;
    color: #e8e0d0;
    margin-bottom: 0.5rem;
    line-height: 1.3;
  }
  .feature-divider {
    width: 32px;
    height: 1px;
    background: #d4af37;
    margin-bottom: 0.6rem;
    transition: width 0.3s ease;
  }
  .feature-card:hover .feature-divider {
    width: 52px;
  }
  .feature-desc {
    font-size: 0.78rem;
    line-height: 1.65;
    color: #9a9080;
    max-width: 320px;
    transition: color 0.3s;
  }
  .feature-card:hover .feature-desc {
    color: #b8aa98;
  }

  /* ---------- Full Amenities List Section ---------- */
  .amenities-list-section {
    background: #080808;
    border-top: 1px solid rgba(212,175,55,0.08);
    border-bottom: 1px solid rgba(212,175,55,0.08);
    padding: 5rem 2rem;
  }
  .section-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
  }
  .section-label::before,
  .section-label::after {
    content: '';
    display: block;
    width: 30px;
    height: 1px;
    background: #d4af37;
    opacity: 0.5;
  }
  .section-label span {
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: #d4af37;
  }
  .section-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.6rem, 3vw, 2.2rem);
    font-weight: 600;
    color: #e8e0d0;
    text-align: center;
    margin-bottom: 3.5rem;
  }
  .amenities-list-grid {
    max-width: 900px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 0;
  }
  .amenity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border: 1px solid rgba(212,175,55,0.07);
    transition: background 0.3s, border-color 0.3s;
    cursor: default;
  }
  .amenity-item:hover {
    background: rgba(212,175,55,0.04);
    border-color: rgba(212,175,55,0.18);
  }
  .amenity-icon-circle {
    flex-shrink: 0;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: 1px solid rgba(212,175,55,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #d4af37;
    font-size: 0.9rem;
    transition: background 0.3s, border-color 0.3s;
  }
  .amenity-item:hover .amenity-icon-circle {
    background: rgba(212,175,55,0.1);
    border-color: #d4af37;
  }
  .amenity-name {
    font-size: 0.85rem;
    font-weight: 500;
    color: #c8bfb0;
    letter-spacing: 0.01em;
  }

  /* ---------- Responsive ---------- */
  @media (max-width: 640px) {
    .features-grid {
      grid-template-columns: 1fr;
    }
    .feature-card {
      aspect-ratio: 16/9;
    }
    .amenities-list-grid {
      grid-template-columns: 1fr 1fr;
    }
  }
</style>
@endpush

@section('content')

  {{-- ====================================================
       HERO SECTION
       ==================================================== --}}
  <section class="amenities-hero">
    <div style="position: relative; z-index: 1; max-width: 700px; margin: 0 auto; padding: 0 1.5rem;">
      <div class="hero-eyebrow">
        <span>Dành Riêng Cho Bạn</span>
      </div>
      <h1>Trải Nghiệm<br>Đô Thị Đỉnh Cao</h1>
      <p>
        Vượt qua giới hạn thông thường, mọi tiện ích của chúng tôi được thiết kế để mang đến
        không gian thưởng thức sang trọng. Từng chi tiết được chăm chút tinh tế,
        đáp ứng mọi nhu cầu và nâng tầm kỳ nghỉ của bạn giữa lòng thành phố.
      </p>
    </div>
  </section>

  {{-- ====================================================
       FEATURED AMENITY CARDS (2×2 GRID)
       ==================================================== --}}
  <section class="features-section">
    <div class="features-grid">

      {{-- Card 1: Elite Spa & Wellness --}}
      <div class="feature-card">
        <img
          src="https://picsum.photos/seed/spa-wellness/800/600"
          alt="Elite Spa & Wellness"
          loading="lazy"
        >
        <div class="feature-card-overlay">
          <div class="feature-icon">
            <i class="fas fa-spa"></i>
          </div>
          <h3 class="feature-title">Elite Spa & Wellness</h3>
          <div class="feature-divider"></div>
          <p class="feature-desc">
            Không gian nghỉ dưỡng toàn diện với liệu pháp nhiệt, massage đặt riêng
            và bầu không khí yên tĩnh giúp phục hồi tâm trí và thể chất.
          </p>
        </div>
      </div>

      {{-- Card 2: Skyline Infinity Pool --}}
      <div class="feature-card">
        <img
          src="https://picsum.photos/seed/infinity-pool/800/600"
          alt="Bể bơi vô cực trên sân thượng"
          loading="lazy"
        >
        <div class="feature-card-overlay">
          <div class="feature-icon">
            <i class="fas fa-swimming-pool"></i>
          </div>
          <h3 class="feature-title">Bể Bơi Vô Cực Skyline</h3>
          <div class="feature-divider"></div>
          <p class="feature-desc">
            Lơ lửng trên nền ánh đèn thành phố tại bể bơi vô cực kiểm soát nhiệt độ
            trên tầng thượng. Tầm nhìn toàn cảnh đường chân trời ngoạn mục.
          </p>
        </div>
      </div>

      {{-- Card 3: State-of-the-Art Fitness --}}
      <div class="feature-card">
        <img
          src="https://picsum.photos/seed/luxury-gym/800/600"
          alt="Phòng tập thể thao hiện đại"
          loading="lazy"
        >
        <div class="feature-card-overlay">
          <div class="feature-icon">
            <i class="fas fa-dumbbell"></i>
          </div>
          <h3 class="feature-title">Phòng Gym Đẳng Cấp</h3>
          <div class="feature-divider"></div>
          <p class="feature-desc">
            Duy trì phong độ với thiết bị Technogym tiên tiến, phòng tập cá nhân
            và lớp học wellness theo yêu cầu mở cửa 24/7.
          </p>
        </div>
      </div>

      {{-- Card 4: 24/7 Bespoke Concierge --}}
      <div class="feature-card">
        <img
          src="https://picsum.photos/seed/hotel-lobby/800/600"
          alt="Dịch vụ Concierge 24/7"
          loading="lazy"
        >
        <div class="feature-card-overlay">
          <div class="feature-icon">
            <i class="fas fa-concierge-bell"></i>
          </div>
          <h3 class="feature-title">Concierge Riêng 24/7</h3>
          <div class="feature-divider"></div>
          <p class="feature-desc">
            Từ đặt bàn nhà hàng độc quyền đến vận chuyển hạng sang, đội ngũ
            tận tâm của chúng tôi sẵn sàng 24 giờ để hoàn thiện hành trình của bạn.
          </p>
        </div>
      </div>

    </div>
  </section>

  {{-- ====================================================
       FULL AMENITIES LIST FROM DATABASE
       ==================================================== --}}
  @if($amenities->isNotEmpty())
  <section class="amenities-list-section">
    <div class="section-label">
      <span>Đầy Đủ Tiện Nghi</span>
    </div>
    <h2 class="section-title">Tất Cả Tiện Ích</h2>

    <div class="amenities-list-grid">
      @foreach($amenities as $amenity)
        <div class="amenity-item">
          <div class="amenity-icon-circle">
            <i class="fas {{ $amenity->icon }}"></i>
          </div>
          <span class="amenity-name">{{ $amenity->name }}</span>
        </div>
      @endforeach
    </div>
  </section>
  @endif

@endsection

@push('scripts')
<script>
  // Lazy-load fallback: replace broken Unsplash images with solid dark placeholder
  document.querySelectorAll('.feature-card img').forEach(function(img) {
    img.addEventListener('error', function() {
      this.style.display = 'none';
      this.parentElement.style.background = '#1a1710';
    });
  });
</script>
@endpush
