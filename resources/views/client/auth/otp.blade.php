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
				<h2 class="text-xl font-semibold mb-1">Nhập OTP</h2>
				<p class="text-[0.8rem] text-gray-400">Chúng tôi đã gửi mã gồm 6 ký tự đến</p>
				<p class="text-[0.8rem] text-[#d4af37] mt-1">{{ old('email', $email ?? '') }}</p>
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

				<div class="flex justify-between gap-2 mb-2" id="otp-inputs">
					@for($i = 0; $i < 6; $i++)
						<input
							type="text"
							inputmode="numeric"
							maxlength="1"
							@class([
								'otp-digit w-12 h-14 bg-[#1b222f] text-center text-lg font-medium rounded-lg border focus:outline-none focus:ring-1 transition-colors',
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

				<div class="flex justify-between items-center text-[0.75rem] mb-6">
					<a href="{{ route('client.login') }}" class="text-gray-400 hover:text-white transition-colors">Đổi email</a>
					<button type="submit" form="resend-otp-form" class="text-[#d4af37] hover:text-[#f8d462] transition-colors">Gửi lại</button>
				</div>

				<button type="submit" class="w-full bg-[#1a2c42] hover:bg-[#20344d] text-[#e2e8f0] font-medium text-sm py-3 rounded-lg transition-colors border border-blue-900/30 flex items-center justify-center gap-2">
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

	<script>
		(function () {
			const form = document.getElementById('otp-form');
			if (!form) return;

			const hiddenOtp = document.getElementById('otp-hidden');
			const inputs = Array.from(form.querySelectorAll('.otp-digit'));

			const syncOtp = () => {
				hiddenOtp.value = inputs.map((input) => input.value).join('');
			};

			const focusNext = (index) => {
				if (index < inputs.length - 1) {
					inputs[index + 1].focus();
				}
			};

			const focusPrev = (index) => {
				if (index > 0) {
					inputs[index - 1].focus();
				}
			};

			inputs.forEach((input, index) => {
				input.addEventListener('input', (event) => {
					const value = event.target.value.replace(/\D/g, '');
					event.target.value = value.slice(0, 1);

					if (event.target.value !== '') {
						focusNext(index);
					}

					syncOtp();
				});

				input.addEventListener('keydown', (event) => {
					if (event.key === 'Backspace' && input.value === '') {
						focusPrev(index);
					}
				});

				input.addEventListener('paste', (event) => {
					event.preventDefault();
					const pasted = (event.clipboardData || window.clipboardData)
						.getData('text')
						.replace(/\D/g, '')
						.slice(0, 6)
						.split('');

					pasted.forEach((digit, idx) => {
						if (inputs[idx]) {
							inputs[idx].value = digit;
						}
					});

					syncOtp();

					const focusIndex = Math.min(pasted.length, inputs.length - 1);
					inputs[focusIndex].focus();
				});
			});

			form.addEventListener('submit', () => {
				syncOtp();
			});

			syncOtp();
			const firstEmpty = inputs.find((input) => input.value === '');
			(firstEmpty || inputs[0]).focus();
		})();
	</script>
</body>
</html>
