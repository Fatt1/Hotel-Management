<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Urban Luxe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        .register-bg {
            background-image: linear-gradient(rgba(4, 16, 28, 0.70), rgba(4, 16, 28, 0.70)),
                url('https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="register-bg min-h-screen text-white">
    <div class="min-h-screen w-full flex items-center justify-center px-4 py-10 backdrop-blur-[2px]">
        <div class="w-full max-w-2xl">
            <div class="text-center mb-6">
                <div class="mx-auto w-10 h-10 rounded-lg border border-white/20 bg-white/10 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-building text-sm"></i>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight">Urban Luxe</h1>
                <p class="text-[0.7rem] tracking-[0.18em] uppercase text-slate-300 mt-1">Chốn bình yên giữa lòng thành phố</p>
            </div>

            <div class="bg-[#0e1724]/75 border border-white/10 rounded-2xl shadow-2xl p-8 md:p-10">
                <div class="text-center mb-8">
                    <h2 class="text-4xl md:text-5xl font-semibold tracking-tight">Đăng ký</h2>
                </div>

                @if(session('error'))
                    <div class="mb-6 rounded-xl border border-red-400/40 bg-red-900/30 px-4 py-3 text-sm text-red-100">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('client.register.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-[0.7rem] uppercase tracking-[0.18em] text-slate-300 mb-2">Tên</label>
                            <input
                                id="first_name"
                                name="first_name"
                                type="text"
                                value="{{ old('first_name') }}"
                                class="w-full h-11 rounded-lg border border-white/10 bg-white/5 px-3 text-sm text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/40 @error('first_name') border-red-400 @enderror"
                                placeholder="Minh"
                            >
                            @error('first_name') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-[0.7rem] uppercase tracking-[0.18em] text-slate-300 mb-2">Họ</label>
                            <input
                                id="last_name"
                                name="last_name"
                                type="text"
                                value="{{ old('last_name') }}"
                                class="w-full h-11 rounded-lg border border-white/10 bg-white/5 px-3 text-sm text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/40 @error('last_name') border-red-400 @enderror"
                                placeholder="Nguyễn"
                            >
                            @error('last_name') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="phone_number" class="block text-[0.7rem] uppercase tracking-[0.18em] text-slate-300 mb-2">Số điện thoại</label>
                            <input
                                id="phone_number"
                                name="phone_number"
                                type="text"
                                value="{{ old('phone_number') }}"
                                class="w-full h-11 rounded-lg border border-white/10 bg-white/5 px-3 text-sm text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/40 @error('phone_number') border-red-400 @enderror"
                                placeholder="+84 9xx xxx xxx"
                            >
                            @error('phone_number') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-[0.7rem] uppercase tracking-[0.18em] text-slate-300 mb-2">Địa chỉ email</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                class="w-full h-11 rounded-lg border border-white/10 bg-white/5 px-3 text-sm text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/40 @error('email') border-red-400 @enderror"
                                placeholder="example@urbanluxe.com"
                            >
                            @error('email') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[0.7rem] uppercase tracking-[0.18em] text-slate-300 mb-2">Quốc gia</label>
                        @include('admin.customers._country_picker', [
                            'inputName' => 'country',
                            'selectedValue' => old('country', ''),
                            'viewModel' => $viewModel,
                            'placeholder' => 'Chọn quốc gia',
                            'autoWidth' => false,
                        ])
                        @error('country') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="w-full h-12 rounded-lg bg-[#0b2a57] hover:bg-[#123568] text-white text-sm font-semibold transition-colors">
                        Tạo tài khoản
                    </button>
                </form>

                <p class="mt-6 text-center text-xs text-slate-300">
                    Đã có tài khoản?
                    <a href="{{ route('client.login') }}" class="text-white font-semibold hover:underline">Đăng nhập</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
