<?php

namespace App\Actions\Bookings;

use App\Actions\Customers\AddCustomerAction;
use App\Actions\Customers\GetCustomerByEmailAction;
use App\Data\BookingData;
use App\Data\CustomerData;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class CreateBookingAction
{
    public function __construct(
        private GetCustomerByEmailAction $getCustomerByEmailAction,
        private AddCustomerAction $createCustomerAction,
    ) {}

    public function execute(BookingData $bookingData): Booking
    {
        return DB::transaction(function () use ($bookingData) {
            // 1. Lấy hoặc tạo mới khách hàng
            $customer = $this->getCustomerByEmailAction->handle($bookingData->email)
                ?? $this->createCustomerAction->handle(new CustomerData(
                    $bookingData->first_name,
                    $bookingData->last_name,
                    $bookingData->phone_number,
                    $bookingData->country,
                    $bookingData->email,
                ));

            // 2. Load tất cả rooms + roomType trong 1 query duy nhất (tránh N+1)
            $roomIds = collect($bookingData->booking_details)->pluck('room_id')->toArray();
            $rooms = Room::with('roomType')->whereIn('id', $roomIds)->get()->keyBy('id');

            // 3. Tính totalRoomAmount theo từng detail
            $totalRoomAmount = 0;
            foreach ($bookingData->booking_details as $detail) {
                $detail = (array) $detail;
                $room = $rooms->get($detail['room_id']);
                if ($room?->roomType) {
                    $days = max(
                        (new \DateTime($detail['checkin_date']))->diff(new \DateTime($detail['checkout_date']))->days,
                        1
                    );
                    $totalRoomAmount += $room->roomType->daily_price * $days;
                }
            }
            
            // 4. Tạo booking — kiểm tra checkin_date có phải hôm nay không
            $firstDetail = (array) $bookingData->booking_details[0];
            $checkinDate = (new \DateTime($firstDetail['checkin_date']))->format('Y-m-d');
            $today       = (new \DateTime('today'))->format('Y-m-d');
            $status      = $checkinDate === $today ? 'Đang ở' : 'Chờ xác nhận';

            $booking = Booking::create([
                'customer_id'          => $customer->id,
                'booking_date'         => $bookingData->booking_date,
                'status'               => $status,
                'total_service_amount' => 0,
                'total_room_amount'    => $totalRoomAmount,
                'surcharge_amount'     => 0,
                'final_amount'         => $totalRoomAmount,
            ]);

            // 5. Batch insert booking_details (1 query thay vì N query)
            $detailsToInsert = [];
            foreach ($bookingData->booking_details as $detail) {
                $detail = (array) $detail;
                $room = $rooms->get($detail['room_id']);
                $detailsToInsert[] = [
                    'booking_id'       => $booking->id,
                    'room_id'          => $detail['room_id'],
                    'checkin_date'     => $detail['checkin_date'],
                    'checkout_date'    => $detail['checkout_date'],
                    'checkout_status'  => false,
                    'hourly_price'     => $room?->roomType->hourly_price ?? 0,
                    'daily_price'      => $room?->roomType->daily_price ?? 0,
                    'service_amount'   => 0,
                    'surcharge_amount' => 0,
                ];
            }
            BookingDetail::insert($detailsToInsert);

            return $booking;
        });
    }
}
