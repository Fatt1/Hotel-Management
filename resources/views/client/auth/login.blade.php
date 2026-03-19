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
<body class="hero-bg relative grid min-h-svh place-items-center bg-gray-900 text-white">
    <div class="absolute inset-0 z-0 bg-[#0f151c]/80 backdrop-blur-sm"></div>

    <div class="relative z-10 flex w-full max-w-110 flex-col items-center px-4 py-8 sm:py-10">
        <div class="mb-6 flex flex-col items-center sm:mb-8">
            <div class="mb-3 flex h-9 w-9 items-center justify-center rounded border border-slate-700/50 bg-[#1a2233] text-sm text-[#e2e8f0] sm:mb-4 sm:h-10 sm:w-10 sm:text-base">
                <i class="fa-solid fa-building"></i>
            </div>
            <h1 class="mb-2 text-2xl font-bold tracking-tight sm:text-3xl">Urban Luxe</h1>
            <p class="text-center text-[0.58rem] uppercase tracking-[0.16em] text-gray-400 sm:text-[0.65rem] sm:tracking-[0.2em]">CHỐN BÌNH YÊN GIỮA LÒNG THÀNH PHỐ</p>
        </div>

        <div class="relative w-full overflow-hidden rounded-xl border border-slate-800 bg-[#141a23]/90 p-5 shadow-2xl sm:p-8">
            <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-white/2 to-transparent"></div>

            <div class="relative mb-5 text-center sm:mb-6">
                <h2 class="mb-1 text-lg font-semibold sm:text-xl">Đăng nhập</h2>
                <p class="text-[0.75rem] text-gray-400 sm:text-[0.8rem]">Nhập email để nhận OTP</p>
            </div>

            <form method="POST" action="{{ route('client.login.send-otp') }}" class="relative">
                @csrf
                <div class="mb-5">
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-regular fa-envelope"></i>
                        </div>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            @class([
                                'w-full rounded-lg border bg-[#1b222f] py-2.5 pl-10 pr-4 text-sm text-gray-200 transition-colors focus:outline-none focus:ring-1 sm:py-3',
                                'border-red-500/50 focus:border-red-500 focus:ring-red-500' => $errors->has('email'),
                                'border-slate-700/50 focus:border-blue-500/50 focus:ring-blue-500/50' => ! $errors->has('email'),
                            ])
                            placeholder="Email của bạn"
                            required
                        >
                    </div>

                    @error('email')
                        <div class="mt-2 flex items-start gap-1.5 text-[0.7rem] leading-tight text-red-500">
                            <i class="fa-solid fa-circle-exclamation mt-0.5 text-[0.65rem]"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <button type="submit" class="w-full rounded-lg border border-blue-900/30 bg-[#1a2c42] py-2.5 text-sm font-medium text-[#e2e8f0] transition-colors hover:bg-[#20344d] sm:py-3">
                    Gửi OTP
                </button>
            </form>

            <div class="mx-auto mt-4 max-w-xs text-center text-[0.63rem] leading-relaxed text-gray-500 sm:mt-5 sm:text-[0.65rem]">
                Bằng việc tiếp tục, bạn đồng ý với Điều khoản dịch vụ và Chính sách bảo mật của chúng tôi.
            </div>

            <p class="mt-5 text-center text-[0.73rem] text-gray-400 sm:mt-6 sm:text-[0.75rem]">
                Chưa có tài khoản?
                <a href="{{ route('client.register') }}" class="font-semibold text-white hover:underline">
                    Đăng ký ngay
                </a>
            </p>
        </div>
    </div>
</body>
</html>
