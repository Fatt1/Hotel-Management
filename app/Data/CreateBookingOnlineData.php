<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class CreateBookingOnlineData extends Data
{
    public function __construct(
        public string $email,
        public string $phone_number,
        public string $first_name,
        public string $last_name,
        public string $country,
        public string $booking_date,
        public string $checkin_date,
        public string $checkout_date,
        public string $status,
        public array $booking_details,
    ) {}

    public static function messages(...$args): array
    {
        return [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'phone_number.required' => 'Vui lòng nhập số điện thoại',
            'first_name.required' => 'Vui lòng nhập tên',
            'last_name.required' => 'Vui lòng nhập họ',
            'country.required' => 'Vui lòng nhập quốc gia',
            'booking_date.required' => 'Thiếu ngày đặt phòng',
            'checkin_date.required' => 'Thiếu ngày nhận phòng',
            'checkout_date.required' => 'Thiếu ngày trả phòng',
            'status.required' => 'Thiếu trạng thái đặt phòng',
            'booking_details.required' => 'Thiếu thông tin phòng',
            'booking_details.array' => 'Định dạng phòng không hợp lệ',
        ];
    }

    public static function rules(ValidationContext|null $context = null): array
    {
        return [
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'booking_date' => 'required|date',
            'checkin_date' => 'required|date',
            'checkout_date' => 'required|date|after_or_equal:checkin_date',
            'status' => 'required|string',
            'booking_details' => 'required|array|min:1',
            'booking_details.*.room_type_id' => 'required|integer|exists:room_types,id',
            'booking_details.*.quantity' => 'required|integer|min:1',
        ];
    }
}
