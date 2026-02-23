<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Geologica:wght,CRSV@100..900,0&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Jacquarda+Bastarda+9&family=Luxurious+Roman&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/login.js'])
    @endif
    <title>Đăng nhập quản trị</title>

</head>

<body class="bg-gray-100 text-text-light h-screen w-full overflow-hidden">
    <div class="flex h-full w-full ">
        <div class="hidden lg:block lg:w-1/2 xl:w-2/3 relative">
            <img alt="Luxury Hotel Lobby"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuA5x5uga0ltzTsJC4Bf20QSZkMN0zyTIJBOZ2TVQD56hJ08jmBmJsKrkLLCeIh3XFxm8z0dbh0f54PRWguAL9E7526B34td4jD7KqqWTaRk5CHnYT5qJ0WDjI-AjifRtyTHqhJA6y_Q6rBfLv3x294mD9AZZI6em-wGIRyd0L7VjPsFFHyyrDv2b2AIAxW1Qf7OWQd57VYdkLIa6Dl9xd5rRSMQyZRIspoLxneSZ9HPiGj_McL9wkIxoSPqazkv6vmaGHFdu7UCXC0"
                class="h-full w-full object-cover inset-0 absolute">
            <div class="absolute inset-0 bg-black/40 flex flex-col justify-end p-12 text-white">
                <h2 class="text-4xl font-bold mb-4">Urban Luxe Hotel Management</h2>
                <p class="text-lg opacity-90 max-w-lg">Quản lý khách sạn chuyên nghiệp, nâng tầm trải nghiệm khách hàng
                    với hệ thống quản trị hiện đại.</p>
            </div>
        </div>
        <div
            class="w-full lg:w-1/2 xl:w-1/3 bg-white flex flex-col justify-center px-8 sm:px-12 lg:px-16 xl:px-20 relative">
            <div class="w-full max-w-md mx-auto">
                <div class="mb-10 text-center lg:text-left">
                    <div
                        class="inline-flex justify-center items-center w-12 h-12 rounded-lg bg-primary/10 text-primary relative">
                        <span class="material-symbols-outlined text-2xl">apartment</span>
                    </div>
                </div>
            </div>
            <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-2">Đăng nhập quản trị</h2>
            <p class="text-gray-500 mt-2 text-sm">Chào mừng trở lại! Vui lòng nhập thông tin để tiếp tục</p>
            <form class="space-y-6 mt-12" action="{{ route('admin.login.post') }}" method="POST">
                @csrf
                @error('login_error')
                    <div class="text-red-500 text-md mb-4">{{ $message }}</div>
                @enderror
                <h2></h2>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-gray-400 text-lg">person</span>
                        </div>
                        <input value="{{ old("email") }}" id="email" name="email" type="email"
                            placeholder="admin@gmail.com"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-primary/20 focus:border-primary/20 placeholder-gray-500 transition-colors">
                    </div>
                    @error('email')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu</label>
                    <div class="relative">
                        <div class="absolute pl-2 flex items-center left-0 inset-y-0 pointer-events-none">
                            <span class="material-symbols-outlined text-gray-400 text-lg">lock</span>
                        </div>
                        <input value="{{ old("password") }}" required="" id="password" name="password" type="password"
                            placeholder="*******" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg">

                    </div>
                    @error('password')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <input type="submit" value="ĐĂNG NHẬP"
                        class="text-white w-full bg-primary font-medium py-3 rounded-lg focus:outline-none hover:bg-primary-hover transition-colors cursor-pointer shadow-sm border border-transparent">
                </div>

            </form>
            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-400">© 2024 Urban Luxe Hotel System.</p>
            </div>


        </div>
    </div>
</body>

</html>