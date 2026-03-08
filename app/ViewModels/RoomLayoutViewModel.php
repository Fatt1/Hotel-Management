<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Enums\RoomLayoutStatus;
use App\Models\Booking;
use App\Models\BookingDetail;
use Carbon\Carbon;

class RoomLayoutViewModel
{
    public int $roomId;
    public string $roomName;
    public string $roomTypeCode;
    public RoomLayoutStatus $status;
    public ?string $customerName = null;
    public ?Carbon $checkinDate = null;
    public ?Carbon $checkoutDate = null;
    public ?int $bookingId = null;
    public ?string $bookingStatus = null;
    public ?int $daysLeft = null;
    public ?string $note = null;
    public ?string $floorName = null;
    public ?int $floorId = null;
    
    // Second booking (for same-day checkout/checkin)
    public bool $hasMultipleBookings = false;
    public ?string $secondaryCustomerName = null;
    public ?Carbon $secondaryCheckinDate = null;
    public ?Carbon $secondaryCheckoutDate = null;
    public ?int $secondaryBookingId = null;
    public ?string $secondaryBookingStatus = null;

    public function __construct(
        int $roomId,
        string $roomName,
        string $roomTypeCode,
        RoomLayoutStatus $status
    ) {
        $this->roomId = $roomId;
        $this->roomName = $roomName;
        $this->roomTypeCode = $roomTypeCode;
        $this->status = $status;
    }

    public function withBookingInfo(
        ?BookingDetail $bookingDetail,
        ?Booking $booking = null
    ): self {
        if (!$bookingDetail || !$booking) {
            return $this;
        }

        $this->bookingId = $booking->id;
        $this->bookingStatus = $booking->status;
        $this->checkinDate = $bookingDetail->checkin_date;
        $this->checkoutDate = $bookingDetail->checkout_date;
        
        if ($booking->customer) {
            $this->customerName = trim($booking->customer->first_name . ' ' . $booking->customer->last_name);
        }

        // Tính số ngày còn lại
        if ($this->checkoutDate) {
            $this->daysLeft = (int) Carbon::now()->diffInDays($this->checkoutDate, false);
        }

        return $this;
    }

    public function withNote(?string $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function withSecondaryBooking(
        ?BookingDetail $bookingDetail,
        ?Booking $booking = null
    ): self {
        if (!$bookingDetail || !$booking) {
            return $this;
        }

        $this->hasMultipleBookings = true;
        $this->secondaryBookingId = $booking->id;
        $this->secondaryBookingStatus = $booking->status;
        $this->secondaryCheckinDate = $bookingDetail->checkin_date;
        $this->secondaryCheckoutDate = $bookingDetail->checkout_date;
        
        if ($booking->customer) {
            $this->secondaryCustomerName = trim($booking->customer->first_name . ' ' . $booking->customer->last_name);
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'room_id' => $this->roomId,
            'room_name' => $this->roomName,
            'room_type_code' => $this->roomTypeCode,
            'status' => $this->status->value,
            'status_label' => $this->status->getLabel(),
            'status_color' => $this->status->getColor(),
            'customer_name' => $this->customerName,
            'checkin_date' => $this->checkinDate?->format('Y-m-d H:i'),
            'checkout_date' => $this->checkoutDate?->format('Y-m-d H:i'),
            'booking_id' => $this->bookingId,
            'booking_status' => $this->bookingStatus,
            'days_left' => $this->daysLeft,
            'note' => $this->note,
        ];
    }
}
