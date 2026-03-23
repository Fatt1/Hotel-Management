<?php

namespace App\Data;

use DateTime;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;


class BookingData extends Data
{
    public function __construct(
        public string $email,
        public string $phone_number,
        public string $first_name,
        public string $last_name,
        public string $country,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public DateTime $booking_date,
        public array $booking_details,
        public string $status,
        public ?array $payment = null,
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
            'booking_date.required' => 'Vui lòng cung cấp ngày đặt phòng',
            'booking_date.date' => 'Ngày đặt phòng không hợp lệ',
            'status.required' => 'Vui lòng cung cấp trạng thái booking',
            'status.in' => 'Trạng thái booking không hợp lệ',
            'booking_details.required' => 'Vui lòng chọn ít nhất một phòng',
            'booking_details.array' => 'Danh sách phòng không hợp lệ',
            'booking_details.min' => 'Vui lòng chọn ít nhất một phòng',
            'booking_details.*.room_id.required' => 'Thiếu thông tin phòng',
            'booking_details.*.room_id.integer' => 'Mã phòng không hợp lệ',
            'booking_details.*.room_id.distinct' => 'Một phòng chỉ được xuất hiện một lần trong booking',
            'booking_details.*.room_id.exists' => 'Phòng không tồn tại',
            'booking_details.*.checkin_date.required' => 'Thiếu ngày nhận phòng',
            'booking_details.*.checkin_date.date' => 'Ngày nhận phòng không hợp lệ',
            'booking_details.*.checkout_date.required' => 'Thiếu ngày trả phòng',
            'booking_details.*.checkout_date.date' => 'Ngày trả phòng không hợp lệ',
            'booking_details.*.services.array' => 'Danh sách dịch vụ của phòng không hợp lệ',
            'booking_details.*.services.*.service_id.required' => 'Thiếu mã dịch vụ',
            'booking_details.*.services.*.service_id.integer' => 'Mã dịch vụ không hợp lệ',
            'booking_details.*.services.*.service_id.exists' => 'Dịch vụ không tồn tại',
            'booking_details.*.services.*.quantity.required' => 'Thiếu số lượng dịch vụ',
            'booking_details.*.services.*.quantity.integer' => 'Số lượng dịch vụ phải là số nguyên',
            'booking_details.*.services.*.quantity.min' => 'Số lượng dịch vụ phải lớn hơn 0',
            'payment.array' => 'Thông tin thanh toán không hợp lệ',
            'payment.amount.required_with' => 'Vui lòng nhập số tiền thanh toán',
            'payment.amount.numeric' => 'Số tiền thanh toán không hợp lệ',
            'payment.amount.min' => 'Số tiền thanh toán phải lớn hơn 0',
            'payment.method.in' => 'Phương thức thanh toán không hợp lệ',
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
            'status' => [
                'required',
                'string',
                Rule::in(['Chờ xác nhận', 'Đã đặt', 'Đang ở', 'Hoàn tất', 'Đã hủy']),
            ],
            'booking_details' => [
                'required',
                'array',
                'min:1',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    foreach ((array) $value as $index => $detail) {
                        $checkin = isset($detail['checkin_date']) ? strtotime((string) $detail['checkin_date']) : false;
                        $checkout = isset($detail['checkout_date']) ? strtotime((string) $detail['checkout_date']) : false;

                        if ($checkin !== false && $checkout !== false && $checkout < $checkin) {
                            $fail("booking_details.$index.checkout_date phải lớn hơn hoặc bằng checkin_date");
                        }
                    }
                },
            ],
            'booking_details.*' => 'required|array',
            'booking_details.*.room_id' => 'required|integer|distinct|exists:rooms,id',
            'booking_details.*.checkin_date' => 'required|date',
            'booking_details.*.checkout_date' => 'required|date',
            'booking_details.*.services' => 'nullable|array',
            'booking_details.*.services.*' => 'required|array',
            'booking_details.*.services.*.service_id' => 'required|integer|exists:services,id',
            'booking_details.*.services.*.quantity' => 'required|integer|min:1',
            'payment' => 'nullable|array',
            'payment.amount' => 'required_with:payment|numeric|min:0.01',
            'payment.method' => [
                'nullable',
                'string',
                Rule::in(['cash', 'bank_transfer', 'card', 'momo']),
            ],
        ];
    }

    
}