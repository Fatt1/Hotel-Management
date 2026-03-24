@extends('client.layouts.app')

@section('title', 'Thông Tin Cá Nhân — Urban Luxe')

@push('styles')
<style>
  /* Hero gradient */
  .profile-hero {
    background: linear-gradient(180deg, rgba(10,10,10,0) 0%, rgba(10,10,10,0.72) 100%),
                url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1600&q=80') center/cover no-repeat;
  }
  /* Sidebar active state */
  .sidebar-link.active {
    background: linear-gradient(135deg, #d4af37 0%, #c9a227 100%);
    color: #0a0a0a;
    font-weight: 600;
  }
  /* Input dark style */
  .profile-input {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.10);
    color: #e8e0d0;
    border-radius: 10px;
    padding: 0.65rem 1rem;
    width: 100%;
    font-size: 0.92rem;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
  }
  .profile-input:focus {
    border-color: rgba(212,175,55,0.5);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.10);
  }
  .profile-input.is-invalid {
    border-color: rgba(255,107,107,0.7);
  }
  .profile-input[readonly] {
    color: #6b6050;
    cursor: not-allowed;
  }
  /* Country picker override for dark theme */
  #profile-country-wrapper .cp-trigger {
    background: rgba(255,255,255,0.04) !important;
    border: 1px solid rgba(255,255,255,0.10) !important;
    border-radius: 10px !important;
    color: #e8e0d0 !important;
    padding: 0.65rem 1rem !important;
    font-size: 0.92rem !important;
  }
  #profile-country-wrapper .cp-trigger:hover,
  #profile-country-wrapper .cp-trigger:focus {
    border-color: rgba(212,175,55,0.5) !important;
    box-shadow: 0 0 0 3px rgba(212,175,55,0.10) !important;
  }
  #profile-country-wrapper .cp-name {
    color: #e8e0d0 !important;
  }

  /* Dial code badge */
  .phone-wrapper {
    display: flex;
    align-items: stretch;
    border: 1px solid rgba(255,255,255,0.10);
    border-radius: 10px;
    overflow: hidden;
    transition: border-color .2s, box-shadow .2s;
  }
  .phone-wrapper:focus-within {
    border-color: rgba(212,175,55,0.5);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.10);
  }
  .phone-wrapper.is-invalid {
    border-color: rgba(255,107,107,0.7);
  }
  .dial-code-badge {
    display: flex;
    align-items: center;
    padding: 0 0.85rem;
    background: rgba(212,175,55,0.10);
    border-right: 1px solid rgba(255,255,255,0.10);
    color: #d4af37;
    font-size: 0.88rem;
    font-weight: 600;
    white-space: nowrap;
    user-select: none;
    flex-shrink: 0;
    min-width: 3.8rem;
    justify-content: center;
  }
  .phone-wrapper .phone-input {
    background: rgba(255,255,255,0.04);
    border: none;
    color: #e8e0d0;
    padding: 0.65rem 1rem;
    width: 100%;
    font-size: 0.92rem;
    outline: none;
  }

  /* Toast */
  .toast-bar {
    animation: slideDown .35s cubic-bezier(.4,0,.2,1);
  }
  @keyframes slideDown {
    from { opacity:0; transform: translateY(-12px); }
    to   { opacity:1; transform: translateY(0); }
  }
</style>
@endpush

@section('content')
@php
  $viewModel = new \App\ViewModels\ClientAuthViewModel();
@endphp

{{-- Hero --}}
<section class="profile-hero relative h-52 pt-16 flex flex-col items-center justify-center text-center">
  <p class="text-[0.65rem] font-semibold uppercase tracking-[0.22em] text-[#d4af37] mb-2">
    ● THÀNH VIÊN
  </p>
  <h1 class="font-['Playfair_Display'] text-3xl sm:text-4xl font-bold text-[#e8e0d0]">
    Thông Tin Cá Nhân
  </h1>
  <p class="mt-2 text-[0.8rem] text-[#9a9080]">Quản lý thông tin và tuỳ chọn cá nhân của bạn.</p>
</section>

{{-- Flash messages --}}
@if(session('success'))
  <div class="toast-bar mx-auto mt-0 max-w-4xl px-4 sm:px-6 -mb-2">
    <div class="mt-2 flex items-center gap-3 rounded-xl border border-emerald-500/25 bg-emerald-900/25 px-5 py-3 text-sm text-emerald-300">
      <span class="text-lg">✓</span>
      {{ session('success') }}
    </div>
  </div>
@endif

@if(session('error'))
  <div class="toast-bar mx-auto mt-0 max-w-4xl px-4 sm:px-6 -mb-2">
    <div class="mt-2 flex items-center gap-3 rounded-xl border border-red-500/25 bg-red-900/25 px-5 py-3 text-sm text-red-300">
      <span class="text-lg">✕</span>
      {{ session('error') }}
    </div>
  </div>
@endif

{{-- Main content --}}
<section class="mx-auto max-w-4xl px-4 sm:px-6 py-10">
  <div class="flex flex-col sm:flex-row gap-6">

    {{-- Sidebar --}}
    <aside class="w-full sm:w-56 flex-shrink-0">
      <div class="rounded-2xl border border-white/8 bg-white/3 p-3 flex flex-col gap-1">
        <a href="{{ route('client.profile') }}"
           class="sidebar-link active flex items-center gap-3 rounded-xl px-4 py-3 text-sm no-underline transition-all">
          <span class="text-base">👤</span>
          <span>Hồ Sơ</span>
        </a>
        <a href="#"
           class="sidebar-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm text-[#9a9080] no-underline transition-all hover:bg-white/5 hover:text-[#e8e0d0]">
          <span class="text-base">🎟</span>
          <span>Lịch Đặt Phòng</span>
        </a>
      </div>
    </aside>

    {{-- Profile Card --}}
    <div class="flex-1">
      <div class="rounded-2xl border border-white/8 bg-white/3 p-6 sm:p-8">

        <div class="mb-6 flex items-center justify-between">
          <h2 class="font-['Playfair_Display'] text-xl font-semibold text-[#e8e0d0]">Hồ Sơ Của Tôi</h2>
          <span class="text-[0.72rem] text-[#6b6050]">Trường bắt buộc đánh dấu <span class="text-[#d4af37]">*</span></span>
        </div>

        <form method="POST" action="{{ route('client.profile.update') }}" novalidate>
          @csrf
          @method('PUT')

          {{-- First & Last name --}}
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
            <div>
              <label for="first_name" class="block text-[0.7rem] font-semibold uppercase tracking-[0.15em] text-[#9a9080] mb-2">
                Tên <span class="text-[#d4af37]">*</span>
              </label>
              <input
                id="first_name"
                name="first_name"
                type="text"
                value="{{ old('first_name', $customer->first_name) }}"
                class="profile-input @error('first_name') is-invalid @enderror"
                placeholder="Minh"
              >
              @error('first_name')
                <p class="mt-1 text-[0.72rem] text-[#ff6b6b]">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="last_name" class="block text-[0.7rem] font-semibold uppercase tracking-[0.15em] text-[#9a9080] mb-2">
                Họ <span class="text-[#d4af37]">*</span>
              </label>
              <input
                id="last_name"
                name="last_name"
                type="text"
                value="{{ old('last_name', $customer->last_name) }}"
                class="profile-input @error('last_name') is-invalid @enderror"
                placeholder="Nguyễn"
              >
              @error('last_name')
                <p class="mt-1 text-[0.72rem] text-[#ff6b6b]">{{ $message }}</p>
              @enderror
            </div>
          </div>

          {{-- Country --}}
          <div class="mb-5">
            <label class="block text-[0.7rem] font-semibold uppercase tracking-[0.15em] text-[#9a9080] mb-2">
              Quốc Gia / Vùng <span class="text-[#d4af37]">*</span>
            </label>
            <div id="profile-country-wrapper">
              @include('admin.customers._country_picker', [
                'inputName'     => 'country',
                'selectedValue' => old('country', $customer->country ?? ''),
                'viewModel'     => $viewModel,
                'placeholder'   => 'Chọn quốc gia',
                'autoWidth'     => false,
              ])
            </div>
            @error('country')
              <p class="mt-1 text-[0.72rem] text-[#ff6b6b]">{{ $message }}</p>
            @enderror
          </div>

          {{-- Phone --}}
          <div class="mb-5">
            <label for="phone_number" class="block text-[0.7rem] font-semibold uppercase tracking-[0.15em] text-[#9a9080] mb-2">
              Số Điện Thoại <span class="text-[#d4af37]">*</span>
            </label>
            <div id="phone-wrapper-el" class="phone-wrapper @error('phone_number') is-invalid @enderror">
              <span id="dial-code-badge" class="dial-code-badge">...</span>
              <input
                id="phone_number"
                name="phone_number"
                type="text"
                value="{{ old('phone_number', $customer->phone_number) }}"
                class="phone-input"
                placeholder="912345678"
              >
            </div>
            @error('phone_number')
              <p class="mt-1 text-[0.72rem] text-[#ff6b6b]">{{ $message }}</p>
            @else
              <p class="mt-1.5 text-[0.72rem] text-[#6b6050]">
                Để thay đổi mã nước, hãy đổi <strong class="text-[#9a9080]">Quốc Gia</strong> trước.
                Định dạng: 9–15 chữ số, có thể bắt đầu bằng +.
              </p>
            @enderror
          </div>

          {{-- Email --}}
          <div class="mb-8">
            <label for="email" class="block text-[0.7rem] font-semibold uppercase tracking-[0.15em] text-[#9a9080] mb-2">
              Địa Chỉ Email <span class="text-[#d4af37]">*</span>
            </label>
            <input
              id="email"
              name="email"
              type="email"
              value="{{ old('email', $customer->email) }}"
              class="profile-input @error('email') is-invalid @enderror"
              placeholder="example@email.com"
            >
            @error('email')
              <p class="mt-1 text-[0.72rem] text-[#ff6b6b]">{{ $message }}</p>
            @enderror
          </div>

          {{-- Submit --}}
          <button type="submit"
            class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl border-none
                   bg-[linear-gradient(135deg,#d4af37_0%,#b8952a_100%)]
                   px-6 py-3.5 text-[0.88rem] font-semibold uppercase tracking-[0.1em] text-[#0a0a0a]
                   shadow-[0_4px_18px_rgba(212,175,55,0.25)]
                   transition-all duration-200
                   hover:brightness-110 hover:shadow-[0_6px_24px_rgba(212,175,55,0.35)]
                   active:scale-[0.98]">
            <span class="material-symbols-outlined !text-lg">save</span>
            Lưu Thay Đổi
          </button>
        </form>
      </div>
    </div>

  </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
  // Vietnamese country name → international dial code mapping
  var DIAL_CODES = {
    'Afghanistan': '+93', 'Ai Cập': '+20', 'Albania': '+355', 'Algeria': '+213',
    'Andorra': '+376', 'Angola': '+244', 'Antigua và Barbuda': '+1268',
    'Áo': '+43', 'Ả Rập Xê út': '+966', 'Argentina': '+54', 'Armenia': '+374',
    'Azerbaijan': '+994', 'Ấn Độ': '+91', 'Ba Lan': '+48', 'Bahamas': '+1242',
    'Bahrain': '+973', 'Bangladesh': '+880', 'Barbados': '+1246',
    'Bắc Macedonia': '+389', 'Belarus': '+375', 'Belize': '+501',
    'Bénin': '+229', 'Bỉ': '+32', 'Bờ Biển Ngà (Côte d\'Ivoire)': '+225',
    'Bồ Đào Nha': '+351', 'Bolivia': '+591', 'Bosnia và Herzegovina': '+387',
    'Botswana': '+267', 'Brazil': '+55', 'Brunei': '+673', 'Bulgaria': '+359',
    'Burkina Faso': '+226', 'Burundi': '+257', 'Bhutan': '+975',
    'Các Tiểu Vương quốc Ả Rập Thống nhất': '+971',
    'Cameroon': '+237', 'Campuchia': '+855', 'Canada': '+1', 'Cape Verde': '+238',
    'Chad': '+235', 'Chile': '+56', 'Colombia': '+57', 'Comoros': '+269',
    'Cộng hòa Dân chủ Congo': '+243', 'Cộng hòa Congo': '+242',
    'Cộng hòa Dominica': '+1809', 'Cộng hòa Séc': '+420',
    'Cộng hòa Trung Phi': '+236', 'Costa Rica': '+506', 'Croatia': '+385',
    'Cuba': '+53', 'Djibouti': '+253', 'Dominica': '+1767',
    'Đài Loan': '+886', 'Đan Mạk': '+45', 'Đông Timor': '+670', 'Đức': '+49',
    'Ecuador': '+593', 'El Salvador': '+503', 'Eritrea': '+291', 'Estonia': '+372',
    'Eswatini': '+268', 'Ethiopia': '+251', 'Fiji': '+679', 'Gabon': '+241',
    'Gambia': '+220', 'Georgia': '+995', 'Ghana': '+233', 'Grenada': '+1473',
    'Guatemala': '+502', 'Guinea': '+224', 'Guinea Bissau': '+245',
    'Guinea Xích Đạo': '+240', 'Guyana': '+592', 'Haiti': '+509',
    'Hà Lan': '+31', 'Hàn Quốc': '+82', 'Honduras': '+504',
    'Hoa Kỳ (Mỹ)': '+1', 'Hungary': '+36', 'Hy Lạp': '+30',
    'Iceland': '+354', 'Indonesia': '+62', 'Iran': '+98', 'Iraq': '+964',
    'Ireland': '+353', 'Israel': '+972', 'Jamaica': '+1876', 'Jordan': '+962',
    'Kazakhstan': '+7', 'Kenya': '+254', 'Kiribati': '+686', 'Kuwait': '+965',
    'Kyrgyzstan': '+996', 'Lào': '+856', 'Latvia': '+371', 'Lebanon': '+961',
    'Lesotho': '+266', 'Liberia': '+231', 'Libya': '+218', 'Liechtenstein': '+423',
    'Lithuania': '+370', 'Luxembourg': '+352', 'Madagascar': '+261',
    'Malawi': '+265', 'Malaysia': '+60', 'Maldives': '+960', 'Mali': '+223',
    'Malta': '+356', 'Mauritania': '+222', 'Mauritius': '+230', 'Mexico': '+52',
    'Micronesia': '+691', 'Moldova': '+373', 'Monaco': '+377', 'Mông Cổ': '+976',
    'Montenegro': '+382', 'Morocco': '+212', 'Mozambique': '+258', 'Myanmar': '+95',
    'Namibia': '+264', 'Nam Phi': '+27', 'Nam Sudan': '+211', 'Nauru': '+674',
    'Na Uy': '+47', 'Nepal': '+977', 'New Zealand': '+64', 'Nga': '+7',
    'Nhật Bản': '+81', 'Nicaragua': '+505', 'Niger': '+227', 'Nigeria': '+234',
    'Oman': '+968', 'Pakistan': '+92', 'Palau': '+680', 'Palestine': '+970',
    'Panama': '+507', 'Papua New Guinea': '+675', 'Paraguay': '+595', 'Peru': '+51',
    'Pháp': '+33', 'Phần Lan': '+358', 'Philippines': '+63',
    'Quần đảo Marshall': '+692', 'Quần đảo Solomon': '+677', 'Qatar': '+974',
    'Romania': '+40', 'Rwanda': '+250', 'Saint Kitts và Nevis': '+1869',
    'Saint Lucia': '+1758', 'Saint Vincent và Grenadines': '+1784',
    'Samoa': '+685', 'San Marino': '+378', 'São Tomé và Príncipe': '+239',
    'Senegal': '+221', 'Serbia': '+381', 'Seychelles': '+248',
    'Sierra Leone': '+232', 'Singapore': '+65', 'Síp (Cyprus)': '+357',
    'Slovakia': '+421', 'Slovenia': '+386', 'Somalia': '+252',
    'Sri Lanka': '+94', 'Sudan': '+249', 'Suriname': '+597', 'Syria': '+963',
    'Tajikistan': '+992', 'Tanzania': '+255', 'Tây Ban Nha': '+34',
    'Thái Lan': '+66', 'Thổ Nhĩ Kỳ': '+90', 'Thụy Điển': '+46',
    'Thụy Sĩ': '+41', 'Togo': '+228', 'Tonga': '+676', 'Triều Tiên': '+850',
    'Trinidad và Tobago': '+1868', 'Trung Quốc': '+86', 'Tunisia': '+216',
    'Turkmenistan': '+993', 'Tuvalu': '+688', 'Úc': '+61', 'Uganda': '+256',
    'Ukraine': '+380', 'Uruguay': '+598', 'Uzbekistan': '+998', 'Vanuatu': '+678',
    'Vatican': '+379', 'Venezuela': '+58', 'Việt Nam': '+84',
    'Vương quốc Anh': '+44', 'Ý': '+39', 'Yemen': '+967',
    'Zambia': '+260', 'Zimbabwe': '+263',
  };

  var badge   = document.getElementById('dial-code-badge');
  var wrapper = document.getElementById('phone-wrapper-el');

  function getDialCode(countryName) {
    return DIAL_CODES[countryName] || '+';
  }

  function updateBadge(countryName) {
    if (badge) badge.textContent = getDialCode(countryName);
  }

  // Init badge from current country hidden input
  var countryInput = document.querySelector('input[name="country"]');
  if (countryInput) {
    updateBadge(countryInput.value);

    // Listen for changes (country picker fires 'change' on the hidden input)
    countryInput.addEventListener('change', function () {
      updateBadge(this.value);
    });
  }
}());
</script>
@endpush
