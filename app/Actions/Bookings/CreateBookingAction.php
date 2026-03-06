<?php

namespace App\Actions\Bookings;

use App\Actions\Customers\AddCustomerAction;
use App\Actions\Customers\GetCustomerByEmailAction;
use App\Data\BookingData;
use App\Data\CustomerData;
use App\Models\Booking;

class CreateBookingAction
{
    public function __construct(
        private GetCustomerByEmailAction $getCustomerByEmailAction,
        private AddCustomerAction $createCustomerAction
    ) {
        throw new \Exception('Not implemented');
    }
    public function handle(BookingData $bookingData)
    {
        // Kiểm tra khách hàng đã tồn tại chưa thông qua email
        $customer = $this->getCustomerByEmailAction->handle($bookingData->email);
        if (!$customer) {
            // Nếu chưa tồn tại, tạo mới khách hàng
            $customer = $this->createCustomerAction->handle(new CustomerData($bookingData->first_name, $bookingData->last_name, $bookingData->phone_number, $bookingData->country, $bookingData->email));
        }
        // Tinhs toán các khoản phí dịch vụ, phòng, phụ thu, tổng tiền cuối cùng ở đây (nếu cần) trước khi tạo booking
        $totalServiceAmount = 0; // Tính tổng tiền dịch vụ
        $totalRoomAmount = 0; // Tính tổng tiền phòng
        $totalSurchargeAmount = 0;
        $finalAmount = $totalServiceAmount + $totalRoomAmount + $totalSurchargeAmount; // Tính tổng tiền cuối cùng
        // Sau khi có thông tin khách hàng, tiếp tục xử lý tạo booking với $customer->id
        $booking = new Booking([
            'customer_id' => $customer->id,
            'booking_date' => $bookingData->booking_date,
            'checkout_date' => $bookingData->checkout_date,
            'checkout_time' => $bookingData->checkout_time,
        ]);
    }
}
