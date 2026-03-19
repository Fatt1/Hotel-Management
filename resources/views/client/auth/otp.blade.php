<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Nhập OTP - Urban Luxe</title>
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
		input[type=number]::-webkit-inner-spin-button,
		input[type=number]::-webkit-outer-spin-button {
			-webkit-appearance: none;
			margin: 0;
		}
	</style>
</head>
<body class="hero-bg relative grid min-h-svh place-items-center bg-gray-900 text-white">
	<div class="absolute inset-0 bg-[#0f151c]/80 backdrop-blur-sm z-0"></div>

	<div class="relative z-10 flex w-full max-w-110 flex-col items-center px-4 py-8 sm:py-10">
		<div class="mb-6 flex flex-col items-center sm:mb-8">
			<div class="mb-3 flex h-9 w-9 items-center justify-center rounded border border-slate-700/50 bg-[#1a2233] text-sm text-[#e2e8f0] sm:mb-4 sm:h-10 sm:w-10 sm:text-base">
				<i class="fa-solid fa-building"></i>
			</div>
			<h1 class="mb-2 text-2xl font-bold tracking-tight sm:text-3xl">Urban Luxe</h1>
			<p class="text-center text-[0.58rem] uppercase tracking-[0.16em] text-gray-400 sm:text-[0.65rem] sm:tracking-[0.2em]">CHỐN BÌNH YÊN GIỮA LÒNG THÀNH PHỐ</p>
		</div>

		<div class="relative w-full overflow-hidden rounded-xl border border-slate-800 bg-[#141a23]/90 p-5 shadow-2xl sm:p-8">
			<div class="absolute inset-0 bg-linear-to-b from-white/2 to-transparent pointer-events-none"></div>

			<div class="relative mb-5 text-center sm:mb-6">
				<h2 class="mb-1 text-lg font-semibold sm:text-xl">Nhập OTP</h2>
				<p class="text-[0.75rem] text-gray-400 sm:text-[0.8rem]">Chúng tôi đã gửi mã gồm 6 ký tự đến</p>
				<p class="mt-1 break-all px-3 text-[0.75rem] text-[#d4af37] sm:text-[0.8rem]">{{ old('email', $email ?? '') }}</p>
			</div>

			@if(session('otp_sent'))
				<div class="relative mb-4 rounded-lg border border-emerald-700/50 bg-emerald-900/20 px-3 py-2 text-[0.75rem] text-emerald-300">
					{{ session('otp_sent') }}
				</div>
			@endif

			<form method="POST" action="{{ route('client.login.post') }}" id="otp-form" class="relative">
				@csrf
				<input type="hidden" name="email" value="{{ old('email', $email ?? '') }}">
				<input type="hidden" name="otp" id="otp-hidden" value="{{ old('otp', '') }}">

				<div class="mb-2 flex justify-between gap-1.5 sm:gap-2" id="otp-inputs">
					@for($i = 0; $i < 6; $i++)
						<input
							type="text"
							inputmode="numeric"
							maxlength="1"
							@class([
								'otp-digit h-12 w-10 rounded-lg border bg-[#1b222f] text-center text-base font-medium transition-colors focus:outline-none focus:ring-1 sm:h-14 sm:w-12 sm:text-lg',
								'border-red-500/70 text-red-100 focus:border-red-400 focus:ring-red-400' => $errors->has('otp'),
								'text-white border-slate-700/50 focus:border-blue-500 focus:ring-blue-500' => ! $errors->has('otp'),
							])
							value="{{ old('otp') ? substr(old('otp'), $i, 1) : '' }}"
						>
					@endfor
				</div>

				@error('otp')
					<div class="flex items-center gap-1.5 justify-center mb-3 mt-1 text-red-500 text-[0.7rem]">
						<i class="fa-solid fa-circle-exclamation text-[0.65rem]"></i>
						<span>{{ $message }}</span>
					</div>
				@enderror

				<div class="mb-5 flex items-center justify-between text-[0.73rem] sm:mb-6 sm:text-[0.75rem]">
					<a href="{{ route('client.login') }}" class="text-gray-400 hover:text-white transition-colors">Đổi email</a>
					<button type="submit" form="resend-otp-form" class="text-[#d4af37] hover:text-[#f8d462] transition-colors">Gửi lại</button>
				</div>

				<button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg border border-blue-900/30 bg-[#1a2c42] py-2.5 text-sm font-medium text-[#e2e8f0] transition-colors hover:bg-[#20344d] sm:py-3">
					<span>Xác nhận & đăng nhập</span>
					<i class="fa-solid fa-arrow-right-to-bracket text-xs opacity-80"></i>
				</button>
			</form>

			<form id="resend-otp-form" method="POST" action="{{ route('client.login.send-otp') }}" class="hidden">
				@csrf
				<input type="hidden" name="email" value="{{ old('email', $email ?? '') }}">
			</form>
		</div>
	</div>
    @vite(['resources/js/client/login.js'])
</body>
</html>
