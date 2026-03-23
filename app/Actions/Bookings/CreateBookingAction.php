<?php

namespace App\Actions\Bookings;

use App\Actions\Customers\AddCustomerAction;
use App\Actions\Customers\GetCustomerByEmailAction;
use App\Data\BookingData;
use App\Data\CustomerData;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Service;
use App\Models\ServiceUsage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

            // 2. Load tất cả rooms + roomType và services trong 1 query (tránh N+1)
            $roomIds = collect($bookingData->booking_details)->pluck('room_id')->toArray();
            $rooms = Room::with('roomType')->whereIn('id', $roomIds)->get()->keyBy('id');
            
            // Thu thập tất cả service_ids và load services từ DB
            $serviceIds = collect($bookingData->booking_details)
                ->pluck('services')
                ->flatten(1)
                ->pluck('service_id')
                ->unique()
                ->filter()
                ->toArray();
            $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

            // 3. Tính totalRoomAmount và totalServiceAmount (lấy giá từ DB)
            $totalRoomAmount = 0;
            $totalServiceAmount = 0;
            
            foreach ($bookingData->booking_details as $detail) {
                $detail = (array) $detail;
                $room = $rooms->get($detail['room_id']);
                 
                
                // Tính tiền phòng
                if ($room?->roomType) {
                    $chargedDays = $this->calculateChargedDays(
                        new \DateTime($detail['checkin_date']),
                        new \DateTime($detail['checkout_date']),
                    );
                    $totalRoomAmount += $room->roomType->daily_price * $chargedDays;
                }
                
                // Tính tiền dịch vụ cho phòng này (LẤY GIÁ TỪ DB)
                if (isset($detail['services']) && is_array($detail['services'])) {
                    foreach ($detail['services'] as $service) {
                        $service = (array) $service;
                        $serviceModel = $services->get($service['service_id']);
                        if ($serviceModel) {
                            $totalServiceAmount += $serviceModel->unit_price * $service['quantity'];
                        }
                    }
                }
            }
            
            // 4. Tạo booking với tổng tiền đã tính
            // Lấy checkin/checkout từ booking_detail đầu tiên làm "master" dates cho booking
            $firstDetail = (array) $bookingData->booking_details[0];
            $hasBookingCheckinDate = Schema::hasColumn('bookings', 'checkin_date');
            $hasBookingCheckoutDate = Schema::hasColumn('bookings', 'checkout_date');
            $hasBookingDetailRoomAmount = Schema::hasColumn('booking_details', 'room_amount');

            $bookingPayload = [
                'customer_id'          => $customer->id,
                'booking_date'         => $bookingData->booking_date,
                'status'               => $bookingData->status,
                'total_service_amount' => $totalServiceAmount,
                'total_room_amount'    => $totalRoomAmount,
                'surcharge_amount'     => 0,
                'final_amount'         => $totalRoomAmount + $totalServiceAmount,
            ];

            if ($hasBookingCheckinDate) {
                $bookingPayload['checkin_date'] = $firstDetail['checkin_date'] ?? $bookingData->booking_date;
            }

            if ($hasBookingCheckoutDate) {
                $bookingPayload['checkout_date'] = $firstDetail['checkout_date'] ?? $bookingData->booking_date;
            }

            $booking = Booking::create($bookingPayload);

            // 5. Tạo booking_details và service_usages
            foreach ($bookingData->booking_details as $detail) {
                $detail = (array) $detail;
                $room = $rooms->get($detail['room_id']);
                $roomAmount = 0;
                if($room?->roomType) {
                    $chargedDays = $this->calculateChargedDays(
                        new \DateTime($detail['checkin_date']),
                        new \DateTime($detail['checkout_date']),
                    );
                    $roomAmount = $room->roomType->daily_price * $chargedDays;
                }
                

                // Tính service_amount cho detail này (LẤY GIÁ TỪ DB)
                $serviceAmount = 0;
                if (isset($detail['services']) && is_array($detail['services'])) {
                    foreach ($detail['services'] as $service) {
                        $service = (array) $service;
                        $serviceModel = $services->get($service['service_id']);
                        if ($serviceModel) {
                            $serviceAmount += $serviceModel->unit_price * $service['quantity'];
                        }
                    }
                }
                
                // Tạo booking_detail
                $bookingDetailPayload = [
                    'booking_id'       => $booking->id,
                    'room_id'          => $detail['room_id'],
                    'checkin_date'     => $detail['checkin_date'],
                    'checkout_date'    => $detail['checkout_date'],
                    'checkout_status'  => false,
                    'hourly_price'     => $room?->roomType->hourly_price ?? 0,
                    'daily_price'      => $room?->roomType->daily_price ?? 0,
                    'service_amount'   => $serviceAmount,
                    'surcharge_amount' => 0,
                ];

                if ($hasBookingDetailRoomAmount) {
                    $bookingDetailPayload['room_amount'] = $roomAmount;
                }

                $bookingDetail = BookingDetail::create($bookingDetailPayload);
                
                // Tạo service_usages nếu có (LƯU GIÁ TỪ DB)
                if (isset($detail['services']) && is_array($detail['services']) && count($detail['services']) > 0) {
                    $serviceUsagesToInsert = [];
                    foreach ($detail['services'] as $service) {
                        $service = (array) $service;
                        $serviceModel = $services->get($service['service_id']);
                        if ($serviceModel) {
                            $serviceUsagesToInsert[] = [
                                'booking_detail_id' => $bookingDetail->id,
                                'service_id'        => $serviceModel->id,
                                'quantity'          => $service['quantity'],
                                'unit_price'        => $serviceModel->unit_price, // GIÁ TỪ DB
                                'created_at'        => now(),
                                'updated_at'        => now(),
                            ];
                        }
                    }
                    if (count($serviceUsagesToInsert) > 0) {
                        ServiceUsage::insert($serviceUsagesToInsert);
                    }
                }
            }

            // 6. Create payment record if provided
            if (!empty($bookingData->payment) && ($bookingData->payment['amount'] ?? 0) > 0) {
                Payment::create([
                    'booking_id'     => $booking->id,
                    'amount'         => $bookingData->payment['amount'],
                    'payment_method' => $bookingData->payment['method'] ?? 'cash',
                    'staff_id'       => auth('staff')->id(),
                ]);
            }

            return $booking;
        });
    }

    private function calculateChargedDays(\DateTime $checkinDate, \DateTime $checkoutDate): int
    {
        $seconds = max($checkoutDate->getTimestamp() - $checkinDate->getTimestamp(), 0);

        return max((int) ceil($seconds / 86400), 1);
    }
}
