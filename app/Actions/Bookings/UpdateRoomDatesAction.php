<?php

namespace App\Actions\Bookings;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Room;
use DateTime;
use Illuminate\Support\Facades\DB;

class UpdateRoomDatesAction
{
    public function __construct(private RecalculateBookingAmountsAction $recalculateBookingAmountsAction) {}

    public function execute(int $bookingId, int $roomId, array $data): BookingDetail
    {
        return DB::transaction(function () use ($bookingId, $roomId, $data) {
            $booking = Booking::findOrFail($bookingId);

            // Check if booking is completed
            if ($booking->status === 'Hoàn tất') {
                throw new \Exception('Không thể cập nhật ngày cho booking đã hoàn tất');
            }

            $bookingDetail = BookingDetail::where('booking_id', $bookingId)
                ->where('room_id', $roomId)
                ->firstOrFail();


            // Check if already checked out
            if ($bookingDetail->checkout_status) {
                throw new \Exception('Không thể cập nhật phòng đã checkout');
            }

            $room = Room::with('roomType')->findOrFail($roomId);
            $updatePayload = [];

            // If booking is occupied (Đang ở), only allow updating checkout_date
            if ($booking->status === 'Đang ở') {
                $checkinDate = new DateTime($bookingDetail->checkin_date);
                $checkoutDate = new DateTime($data['checkout_date']);

                if ($checkoutDate <= $checkinDate) {
                    throw new \Exception('Ngày checkout phải lớn hơn ngày checkin');
                }

                if ($this->hasOverlappingBookingDetail($roomId, $bookingDetail->id, $checkinDate, $checkoutDate)) {
                    throw new \Exception('Phòng đã có booking trùng lịch trong khoảng thời gian này');
                }

                $chargedDays = $this->calculateChargedDays($checkinDate, $checkoutDate);

                $updatePayload = [
                    'checkout_date' => $data['checkout_date'],
                    'room_amount' => $room->roomType->daily_price * $chargedDays,
                ];
            } else if( $booking->status === 'Đã đặt') {
                $checkinDate = new DateTime($data['checkin_date']);
                $checkoutDate = new DateTime($data['checkout_date']);

                if ($checkoutDate <= $checkinDate) {
                    throw new \Exception('Ngày checkout phải lớn hơn ngày checkin');
                }

                if ($this->hasOverlappingBookingDetail($roomId, $bookingDetail->id, $checkinDate, $checkoutDate)) {
                    throw new \Exception('Phòng đã có booking trùng lịch trong khoảng thời gian này');
                }

                $chargedDays = $this->calculateChargedDays($checkinDate, $checkoutDate);

                // Otherwise, allow updating both dates
                $updatePayload = [
                    'checkin_date'  => $data['checkin_date'],
                    'checkout_date' => $data['checkout_date'],
                    'room_amount'   => $room->roomType->daily_price * $chargedDays,
                ];
            }

            $bookingDetail->update($updatePayload);

            // Recalculate booking amounts after updating dates (affects room amount)
            $this->recalculateBookingAmountsAction->execute($bookingId);

            return $bookingDetail->fresh();
        });
    }

    private function calculateChargedDays(DateTime $checkinDate, DateTime $checkoutDate): int
    {
        $seconds = max($checkoutDate->getTimestamp() - $checkinDate->getTimestamp(), 0);

        return max((int) ceil($seconds / 86400), 1);
    }

    private function hasOverlappingBookingDetail(
        int $roomId,
        int $excludeBookingDetailId,
        DateTime $newCheckin,
        DateTime $newCheckout,
    ): bool {
        return BookingDetail::query()
            ->join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
            ->where('booking_details.room_id', $roomId)
            ->where('booking_details.id', '!=', $excludeBookingDetailId)
            ->whereIn('bookings.status', ['Đã đặt', 'Đang ở'])
            ->where('booking_details.checkin_date', '<', $newCheckout->format('Y-m-d H:i:s'))
            ->where('booking_details.checkout_date', '>', $newCheckin->format('Y-m-d H:i:s'))
            ->exists();
    }
}
