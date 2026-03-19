<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Urban Luxe</title>
    <!-- Fonts from existing layout -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js for UI state toggling without refreshing -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-bg {
            background-image: url('https://picsum.photos/seed/hotelcity/1920/1080');
            background-size: cover;
            background-position: center;
        }
        /* Custom scrollbar for OTP inputs to avoid weird arrows on number inputs */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center hero-bg relative"
      x-data="{ 
          step: 1, 
          email: 'unknown@example.com',
          emailError: false,
          otpError: false,
          
          simulateEmailSubmit() {
              if(this.email === 'unknown@example.com') {
                  this.emailError = true;
              } else {
                  this.emailError = false;
                  this.step = 2;
                  // Auto focus first otp input
                  setTimeout(() => document.getElementById('otp-1').focus(), 100);
              }
          },

          simulateOtpSubmit() {
              this.otpError = true;
          },

          goToEmail() {
              this.step = 1;
              this.otpError = false;
              this.emailError = false;
          }
      }">
    
    <!-- Dark Overlay over the background image -->
    <div class="absolute inset-0 bg-[#0f151c]/80 backdrop-blur-sm z-0"></div>

    <!-- Main Container -->
    <div class="relative z-10 w-full max-w-[440px] px-4 flex flex-col items-center">
        
        <!-- Header / Logo Area -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-10 h-10 rounded bg-[#1a2233] border border-slate-700/50 flex items-center justify-center mb-4 text-[#e2e8f0]">
                <i class="fa-solid fa-building"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight mb-2" style="font-family: 'Inter', sans-serif;">Urban Luxe</h1>
            <p class="text-[0.65rem] tracking-[0.2em] uppercase text-gray-400">Chốn bình yên giữa lòng thành phố</p>
        </div>

        <!-- Login Box -->
        <div class="w-full bg-[#141a23] border border-slate-800 rounded-xl p-8 shadow-2xl relative overflow-hidden">
            <!-- Subtle gradient overlay to match Figma's smooth lighting inside modal -->
            <div class="absolute inset-0 bg-gradient-to-b from-white/[0.02] to-transparent pointer-events-none"></div>

            <!-- ================= STEP 1: EMAIL INPUT ================= -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <div class="text-center mb-6">
                    <h2 class="text-xl font-semibold mb-1">Đăng nhập</h2>
                    <p class="text-[0.8rem] text-gray-400">Nhập email để nhận OTP</p>
                </div>

                <form @submit.prevent="simulateEmailSubmit">
                    <!-- Email Input Group -->
                    <div class="mb-5">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-regular fa-envelope"></i>
                            </div>
                            <input type="email" x-model="email" 
                                class="w-full bg-[#1b222f] text-sm text-gray-200 rounded-lg pl-10 pr-4 py-3 border focus:outline-none focus:ring-1 transition-colors"
                                :class="emailError ? 'border-red-500/50 focus:border-red-500 focus:ring-red-500' : 'border-slate-700/50 focus:border-blue-500/50 focus:ring-blue-500/50'"
                                placeholder="Email của bạn">
                        </div>
                        
                        <!-- Error Message -->
                        <div x-show="emailError" class="flex items-start gap-1.5 mt-2 text-red-500 text-[0.7rem] leading-tight" x-transition style="display: none;">
                            <i class="fa-solid fa-circle-exclamation mt-[2px] text-[0.65rem]"></i>
                            <span>Địa chỉ email này chưa được đăng ký. Vui lòng kiểm tra lại chính tả hoặc tạo tài khoản mới</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#1a2c42] hover:bg-[#20344d] text-[#e2e8f0] font-medium text-sm py-3 rounded-lg transition-colors border border-blue-900/30">
                        Gửi OTP
                    </button>
                    <!-- Hint instruction for testing -->
                    <p class="text-center text-[0.6rem] text-gray-500 mt-2">(Mẹo: Đổi email khác 'unknown@example.com' để đi tiếp 🤪)</p>
                </form>
            </div>


            <!-- ================= STEP 2: OTP INPUT ================= -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <div class="text-center mb-6">
                    <h2 class="text-xl font-semibold mb-1">Nhập OTP</h2>
                    <p class="text-[0.8rem] text-gray-400">chúng tôi đã gửi mã gồm 6 ký tự</p>
                </div>

                <form @submit.prevent="simulateOtpSubmit">
                    <!-- 6 Digit OTP Inputs -->
                    <div class="flex justify-between gap-2 mb-2">
                        <template x-for="i in 6">
                            <input type="text" maxlength="1" :id="'otp-'+i"
                                class="w-12 h-14 bg-[#1b222f] text-center text-lg font-medium text-white rounded-lg border focus:outline-none focus:ring-1 transition-colors"
                                :class="otpError ? 'border-red-500/70 text-red-100 focus:border-red-400 focus:ring-red-400' : 'border-slate-700/50 focus:border-blue-500 focus:ring-blue-500'"
                                @input="(e) => { 
                                    const val = e.target.value;
                                    // Handle paste of multiple characters
                                    if (val.length > 1) {
                                        const pasted = val.slice(0, 6).split('');
                                        pasted.forEach((char, index) => {
                                            const el = document.getElementById('otp-' + (index + 1));
                                            if (el) el.value = char;
                                        });
                                        const nextFocus = Math.min(pasted.length + 1, 6);
                                        document.getElementById('otp-' + nextFocus)?.focus();
                                        return;
                                    }
                                    if(e.target.value && i < 6) {
                                        document.getElementById('otp-'+(i+1)).focus();
                                    }
                                }"
                                @keydown.backspace="(e) => {
                                    if(!e.target.value && i > 1) {
                                        document.getElementById('otp-'+(i-1)).focus();
                                    }
                                }"
                                @paste.prevent="(e) => {
                                    const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
                                    if(pastedData) {
                                        pastedData.split('').forEach((char, index) => {
                                            const el = document.getElementById('otp-' + (index + 1));
                                            if (el) el.value = char;
                                        });
                                        const nextFocus = Math.min(pastedData.length + 1, 6);
                                        if (nextFocus <= 6) document.getElementById('otp-' + nextFocus)?.focus();
                                    }
                                }">
                        </template>
                    </div>

                    <!-- Error and Actions row -->
                    <div class="flex flex-col mb-6">
                        <div x-show="otpError" class="flex items-center gap-1.5 justify-center mb-3 mt-1 text-red-500 text-[0.7rem]" x-transition style="display: none;">
                            <i class="fa-solid fa-circle-exclamation text-[0.65rem]"></i>
                            <span>Mã không hợp lệ yêu cầu nhập lại</span>
                        </div>
                        
                        <div class="flex justify-between items-center text-[0.75rem]">
                            <button type="button" @click="goToEmail" class="text-gray-400 hover:text-white transition-colors">Đổi email</button>
                            <button type="button" class="text-[#d4af37] hover:text-[#f8d462] transition-colors">Gửi lại</button>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#1a2c42] hover:bg-[#20344d] text-[#e2e8f0] font-medium text-sm py-3 rounded-lg transition-colors border border-blue-900/30 flex items-center justify-center gap-2">
                        <span>Xác nhận & đăng nhập</span>
                        <i class="fa-solid fa-arrow-right-to-bracket text-xs opacity-80"></i>
                    </button>
                </form>
            </div>

            <!-- ================= COMMON FOOTER ================= -->
            <div class="mt-8">
                <!-- Divider -->
                <div class="flex items-center mb-6">
                    <div class="flex-grow h-px bg-slate-800"></div>
                    <span class="px-4 text-[0.65rem] tracking-widest uppercase text-gray-500 font-semibold">Hoặc bắt đầu với</span>
                    <div class="flex-grow h-px bg-slate-800"></div>
                </div>

               

                <!-- Terms -->
                <div class="text-center text-[0.65rem] text-gray-500 leading-relaxed px-4 mb-6">
                    Bằng việc tiếp tục, bạn đồng ý với<br>
                    Điều khoản dịch vụ và Chính sách<br>
                    bảo mật của chúng tôi.
                </div>

                <!-- Registration Link -->
                <p class="text-center text-[0.75rem] text-gray-400">
                    Chưa có tài khoản?
                    <a href="{{ route('client.register') }}" class="text-white font-semibold hover:underline">
                        Đăng ký ngay
                    </a>
                </p>
            </div>

        </div>
    </div>
</body>
</html>
