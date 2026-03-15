<?php

namespace App\Actions\Bookings;

use App\Actions\Bookings\RecalculateBookingAmountsAction;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Service;
use App\Models\ServiceUsage;
use Illuminate\Support\Facades\DB;


class AddOrUpdateServiceAction
{
    public function __construct(private RecalculateBookingAmountsAction $recalculateBookingAmountsAction)
    {
        
    }
    public function execute(int $bookingId, int $roomId, array $data): ServiceUsage
    {
        return DB::transaction(function () use ($bookingId, $roomId, $data) {
            $booking = Booking::findOrFail($bookingId);
            
            // Check if booking is completed
            if ($booking->status === 'Hoàn tất') {
                throw new \Exception('Không thể thêm dịch vụ vào booking đã hoàn tất');
            }
            
            $bookingDetail = BookingDetail::where('booking_id', $bookingId)
                ->where('room_id', $roomId)
                ->firstOrFail();
            
            // Check if already checked out
            if ($bookingDetail->checkout_status) {
                throw new \Exception('Không thể thêm dịch vụ cho phòng đã checkout');
            }
            
            $service = Service::findOrFail($data['service_id']);

            // Check if service usage already exists (update or create)
            $serviceUsage = ServiceUsage::updateOrCreate(
                [
                    'booking_detail_id' => $bookingDetail->id,
                    'service_id'        => $service->id,
                ],
                [
                    'quantity'   => $data['quantity'],
                    'unit_price' => $service->unit_price,
                ]
            );
            $totalServiceAmount = 0;
            foreach($bookingDetail->serviceUsages as $usage) {
                    $totalServiceAmount += $usage->quantity * $usage->unit_price;
            }
            $bookingDetail->update([
                'service_amount' => $totalServiceAmount,
            ]);


            $this->recalculateBookingAmountsAction->execute($booking->id);
            return $serviceUsage->load('service');
        });
    }
}
