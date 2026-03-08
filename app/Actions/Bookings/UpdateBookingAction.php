<?php

namespace App\Actions\Bookings;

use App\Data\BookingData;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Room;
use App\Models\Service;
use App\Models\ServiceUsage;
use Illuminate\Support\Facades\DB;

class UpdateBookingAction
{
    public function execute(int $bookingId, BookingData $bookingData): Booking
    {
        return DB::transaction(function () use ($bookingId, $bookingData) {
            // 1. Tìm booking
            $booking = Booking::findOrFail($bookingId);

            // 2. Xóa booking_details và service_usages cũ
            foreach ($booking->bookingDetails as $detail) {
                ServiceUsage::where('booking_detail_id', $detail->id)->delete();
            }
            BookingDetail::where('booking_id', $bookingId)->delete();

            // 3. Load tất cả rooms + roomType và services trong 1 query
            $roomIds = collect($bookingData->booking_details)->pluck('room_id')->toArray();
            $rooms = Room::with('roomType')->whereIn('id', $roomIds)->get()->keyBy('id');
            
            $serviceIds = collect($bookingData->booking_details)
                ->pluck('services')
                ->flatten(1)
                ->pluck('service_id')
                ->unique()
                ->filter()
                ->toArray();
            $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

            // 4. Tính lại totalRoomAmount và totalServiceAmount
            $totalRoomAmount = 0;
            $totalServiceAmount = 0;
            
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
            
            // 5. Cập nhật booking
            $booking->update([
                'booking_date'         => $bookingData->booking_date,
                'status'               => $bookingData->status,
                'total_service_amount' => $totalServiceAmount,
                'total_room_amount'    => $totalRoomAmount,
                'final_amount'         => $totalRoomAmount + $totalServiceAmount,
            ]);

            // 6. Tạo lại booking_details và service_usages
            foreach ($bookingData->booking_details as $detail) {
                $detail = (array) $detail;
                $room = $rooms->get($detail['room_id']);
                
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
                
                $bookingDetail = BookingDetail::create([
                    'booking_id'       => $booking->id,
                    'room_id'          => $detail['room_id'],
                    'checkin_date'     => $detail['checkin_date'],
                    'checkout_date'    => $detail['checkout_date'],
                    'checkout_status'  => false,
                    'hourly_price'     => $room?->roomType->hourly_price ?? 0,
                    'daily_price'      => $room?->roomType->daily_price ?? 0,
                    'service_amount'   => $serviceAmount,
                    'surcharge_amount' => 0,
                ]);
                
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
                                'unit_price'        => $serviceModel->unit_price,
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

            return $booking->fresh(['customer', 'bookingDetails.room', 'bookingDetails.serviceUsages']);
        });
    }
}