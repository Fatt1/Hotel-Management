<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Urban Luxe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-bg {
            background-image: url('https://picsum.photos/seed/hotelcity/1920/1080');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center hero-bg relative">
    <div class="absolute inset-0 bg-[#0f151c]/80 backdrop-blur-sm z-0"></div>

    <div class="relative z-10 w-full max-w-110 px-4 flex flex-col items-center">
        <div class="flex flex-col items-center mb-8">
            <div class="w-10 h-10 rounded bg-[#1a2233] border border-slate-700/50 flex items-center justify-center mb-4 text-[#e2e8f0]">
                <i class="fa-solid fa-building"></i>
            </div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">Urban Luxe</h1>
            <p class="text-[0.65rem] tracking-[0.2em] uppercase text-gray-400">CHỐN BÌNH YÊN GIỮA LÒNG THÀNH PHỐ</p>
        </div>

        <div class="w-full bg-[#141a23]/90 border border-slate-800 rounded-xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 bg-linear-to-b from-white/2 to-transparent pointer-events-none"></div>

            <div class="relative text-center mb-6">
                <h2 class="text-xl font-semibold mb-1">Đăng nhập</h2>
                <p class="text-[0.8rem] text-gray-400">Nhập email để nhận OTP</p>
            </div>

            <form method="POST" action="{{ route('client.login.send-otp') }}" class="relative">
                @csrf
                <div class="mb-5">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-regular fa-envelope"></i>
                        </div>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            @class([
                                'w-full bg-[#1b222f] text-sm text-gray-200 rounded-lg pl-10 pr-4 py-3 border focus:outline-none focus:ring-1 transition-colors',
                                'border-red-500/50 focus:border-red-500 focus:ring-red-500' => $errors->has('email'),
                                'border-slate-700/50 focus:border-blue-500/50 focus:ring-blue-500/50' => ! $errors->has('email'),
                            ])
                            placeholder="Email của bạn"
                            required
                        >
                    </div>

                    @error('email')
                        <div class="flex items-start gap-1.5 mt-2 text-red-500 text-[0.7rem] leading-tight">
                            <i class="fa-solid fa-circle-exclamation mt-0.5 text-[0.65rem]"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>


                <button type="submit" class="w-full bg-[#1a2c42] hover:bg-[#20344d] text-[#e2e8f0] font-medium text-sm py-3 rounded-lg transition-colors border border-blue-900/30">
                    Gửi OTP
                </button>
            </form>

               

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
