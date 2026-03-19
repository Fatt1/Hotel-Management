<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>OTP Đăng nhập</title>
</head>
<body style="margin:0;padding:24px;background:#f5f7fb;font-family:Arial,sans-serif;color:#1f2937;">
	<div style="max-width:560px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;padding:24px;">
		<h2 style="margin:0 0 12px;font-size:20px;">Mã OTP đăng nhập</h2>
		<p style="margin:0 0 16px;line-height:1.6;">Bạn đang yêu cầu đăng nhập tài khoản Urban Luxe. Sử dụng mã bên dưới để tiếp tục:</p>

		<div style="font-size:32px;letter-spacing:8px;font-weight:700;text-align:center;padding:16px;background:#f3f4f6;border-radius:6px;margin-bottom:16px;">
			{{ $otp }}
		</div>

		<p style="margin:0 0 8px;line-height:1.6;">Mã có hiệu lực trong <strong>5 phút</strong>.</p>
		<p style="margin:0;line-height:1.6;">Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email.</p>
	</div>
</body>
</html>
