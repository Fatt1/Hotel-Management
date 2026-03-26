<?php

namespace App\Actions\Bookings;

use App\Data\CreateBookingOnlineData;
use App\Data\BookingData;
use App\Mail\BookingSuccessMail;
use App\Models\Room;
use App\Models\BookingDetail;
use App\Models\Booking;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CreateBookingOnlineAction
{
    private string $checkinTime;
    private string $checkoutTime;

    public function __construct(
        private CreateBookingAction $createBookingAction,
    ) {
        $this->checkinTime = (string) (SystemSetting::where('setting_key', 'checkin_time')->value('setting_value') ?? '14:00');
        $this->checkoutTime = (string) (SystemSetting::where('setting_key', 'checkout_time')->value('setting_value') ?? '12:00');
    }

    public function execute(CreateBookingOnlineData $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $normalizedCheckinDate = $this->normalizeDateWithConfiguredTime($data->checkin_date, $this->checkinTime);
            $normalizedCheckoutDate = $this->normalizeDateWithConfiguredTime($data->checkout_date, $this->checkoutTime);

            // Tìm danh sách phòng đã được đặt trong khoảng thời gian này
            $bookedRoomIds = BookingDetail::join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
                ->whereIn('bookings.status', ['Đã đặt', 'Đang ở'])
                ->where('booking_details.checkin_date', '<', $normalizedCheckoutDate)
                ->where('booking_details.checkout_date', '>', $normalizedCheckinDate)
                ->pluck('booking_details.room_id')
                ->toArray();

            $bookingDetailsPayload = [];
            $totalAmount = 0;

            foreach ($data->booking_details as $detail) {
                // detail = ['room_type_id' => ..., 'quantity' => ...]
                // Lấy phòng trống theo room_type_id
                $availableRooms = Room::with('roomType')->where('room_type_id', $detail['room_type_id'])
                    ->where('status', 'ready')
                    ->whereNotIn('id', $bookedRoomIds)
                    ->limit($detail['quantity'])
                    ->get();

                if ($availableRooms->count() < $detail['quantity']) {
                    throw new Exception("Không đủ phòng trống cho loại phòng bạn chọn (ID: " . $detail['room_type_id'] . "). Vui lòng chọn ngày khác hoặc giảm số lượng.");
                }

                $checkin = new \DateTime($normalizedCheckinDate);
                $checkout = new \DateTime($normalizedCheckoutDate);
                $seconds = max($checkout->getTimestamp() - $checkin->getTimestamp(), 0);
                $chargedDays = max((int) ceil($seconds / 86400), 1);

                foreach ($availableRooms as $room) {
                    $bookingDetailsPayload[] = [
                        'room_id' => $room->id,
                        'checkin_date' => $normalizedCheckinDate,
                        'checkout_date' => $normalizedCheckoutDate,
                        'services' => [] // Online không đặt kèm service ban đầu ở luồng này
                    ];

                    if ($room->roomType) {
                        $totalAmount += $room->roomType->daily_price * $chargedDays;
                    }
                }
            }

            // Map qua DTO cũ để tái sử dụng logic lưu DB
            $bookingData = new BookingData(
                email: $data->email,
                phone_number: $data->phone_number,
                first_name: $data->first_name,
                last_name: $data->last_name,
                country: $data->country,
                booking_date: new \DateTime($data->booking_date),
                booking_details: $bookingDetailsPayload,
                status: $data->status,
                payment: [
                    'amount' => $totalAmount,
                    'method' => 'cash'
                ]
            );

            $booking = $this->createBookingAction->execute($bookingData);
            
            $booking->load(['bookingDetails.room.roomType', 'customer']);
            // Gửi email xác nhận booking thành công
            Mail::to($booking->customer->email)->send(new BookingSuccessMail($booking));
            return $booking;
        });
    }

    private function normalizeDateWithConfiguredTime(string $date, string $configuredTime): string
    {
        $datePart = Carbon::parse($date)->format('Y-m-d');

        $timeText = trim($configuredTime);
        if (preg_match('/^(\d{1,2}):(\d{1,2})(?::(\d{1,2}))?$/', $timeText, $matches)) {
            $hour = max(0, min(23, (int) $matches[1]));
            $minute = max(0, min(59, (int) $matches[2]));
            $second = isset($matches[3]) ? max(0, min(59, (int) $matches[3])) : 0;

            return sprintf('%s %02d:%02d:%02d', $datePart, $hour, $minute, $second);
        }

        // Fallback an toàn nếu cấu hình time bị lỗi định dạng
        return $datePart . ' 00:00:00';
    }
}
