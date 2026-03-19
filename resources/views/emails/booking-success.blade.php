<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xac nhan dat phong</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f8fafc; margin: 0; padding: 24px; color: #0f172a;">
    <div style="max-width: 640px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
        <div style="padding: 20px 24px; background: #1e3a8a; color: #ffffff;">
            <h1 style="margin: 0; font-size: 20px;">Xac nhan dat phong thanh cong</h1>
            <p style="margin: 6px 0 0; font-size: 13px; opacity: 0.9;">Urban Luxe Hotel</p>
        </div>

        <div style="padding: 20px 24px;">
            <p style="margin-top: 0;">Xin chao {{ $bookingData['guest_name'] ?? 'Quy khach' }},</p>
            <p>Cam on ban da dat phong tai Urban Luxe Hotel. Don dat phong cua ban da duoc ghi nhan.</p>

            <div style="margin: 16px 0; padding: 14px; background: #f1f5f9; border-radius: 8px;">
                <p style="margin: 0 0 8px;"><strong>Ma dat phong:</strong> #{{ $bookingRef }}</p>
                <p style="margin: 0 0 8px;"><strong>Ngay nhan phong:</strong> {{ $bookingData['check_in'] ?? '-' }}</p>
                <p style="margin: 0 0 8px;"><strong>Ngay tra phong:</strong> {{ $bookingData['check_out'] ?? '-' }}</p>
                <p style="margin: 0 0 8px;"><strong>So dem:</strong> {{ $bookingData['nights'] ?? '-' }}</p>
                <p style="margin: 0;"><strong>Tong thanh toan:</strong> {{ number_format((float) ($bookingData['subtotal'] ?? 0), 0, ',', '.') }} VND</p>
            </div>

            @if(!empty($bookingData['rooms']) && is_array($bookingData['rooms']))
                <h2 style="font-size: 16px; margin: 0 0 8px;">Danh sach phong</h2>
                <ul style="margin: 0 0 16px 18px; padding: 0;">
                    @foreach($bookingData['rooms'] as $room)
                        <li style="margin-bottom: 6px;">
                            {{ $room['qty'] ?? 1 }} x {{ $room['name'] ?? 'Phong' }}
                        </li>
                    @endforeach
                </ul>
            @endif

            <p style="margin-bottom: 0;">Neu ban can ho tro, vui long lien he hotline 24/7 cua chung toi.</p>
        </div>
    </div>
</body>
</html>
