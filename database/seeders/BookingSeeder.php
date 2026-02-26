<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Customer;
use App\Models\Room;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::where('email', 'nguyenvanan@example.com')->first();
        $staff    = Staff::first();

        $room101 = Room::where('name', '101')->first();
        $room201 = Room::where('name', '201')->first();

        // --- Booking 1: Đang lưu trú (staying) ---
        $booking1 = Booking::create([
            'customer_id'          => $customer->id,
            'booking_date'         => now()->subDays(2),
            'staff_id'             => $staff?->id,
            'total_service_amount' => 0,
            'total_room_amount'    => 600000.00,
            'surcharge_amount'     => 0,
            'final_amount'         => 600000.00,
        ]);

        // Thêm status qua DB vì model fillable không khai báo status
        DB::table('bookings')->where('id', $booking1->id)->update(['status' => 'Đang ở']);

        BookingDetail::create([
            'room_id'       => $room101->id,
            'booking_id'    => $booking1->id,
            'checkin_date'  => now()->subDays(2)->setTime(14, 0),
            'checkout_date' => now()->addDays(1)->setTime(12, 0),
            'hourly_price'  => $room101->roomType->hourly_price,
            'daily_price'   => $room101->roomType->daily_price,
            'service_amount'  => 0,
            'surcharge_amount' => 0,
        ]);
        DB::table('booking_details')->where('booking_id', $booking1->id)->update(['checkout_status' => false]);

        // --- Booking 2: Đã hoàn thành (completed), 2 phòng ---
        $customer2 = Customer::where('email', 'tranthibinh@example.com')->first();

        $booking2 = Booking::create([
            'customer_id'          => $customer2->id,
            'booking_date'         => now()->subDays(10),
            'staff_id'             => $staff?->id,
            'total_service_amount' => 200000.00,
            'total_room_amount'    => 1300000.00,
            'surcharge_amount'     => 0,
            'final_amount'         => 1500000.00,
        ]);

        DB::table('bookings')->where('id', $booking2->id)->update(['status' => 'Hoàn tất']);

        // Chi tiết phòng 101
        BookingDetail::create([
            'room_id'          => $room101->id,
            'booking_id'       => $booking2->id,
            'checkin_date'     => now()->subDays(10)->setTime(14, 0),
            'checkout_date'    => now()->subDays(8)->setTime(12, 0),
            'hourly_price'     => $room101->roomType->hourly_price,
            'daily_price'      => $room101->roomType->daily_price,
            'service_amount'   => 100000.00,
            'surcharge_amount' => 0,
        ]);
        DB::table('booking_details')->where('booking_id', $booking2->id)->where('room_id', $room101->id)
            ->update(['checkout_status' => true]);

        // Chi tiết phòng 201 (Deluxe)
        BookingDetail::create([
            'room_id'          => $room201->id,
            'booking_id'       => $booking2->id,
            'checkin_date'     => now()->subDays(10)->setTime(14, 0),
            'checkout_date'    => now()->subDays(8)->setTime(12, 0),
            'hourly_price'     => $room201->roomType->hourly_price,
            'daily_price'      => $room201->roomType->daily_price,
            'service_amount'   => 100000.00,
            'surcharge_amount' => 0,
        ]);
        DB::table('booking_details')->where('booking_id', $booking2->id)->where('room_id', $room201->id)
            ->update(['checkout_status' => true]);

        // --- Booking 3: Đặt phòng (pending) – chưa check-in ---
        $booking3 = Booking::create([
            'customer_id'          => $customer->id,
            'booking_date'         => now(),
            'staff_id'             => $staff?->id,
            'total_service_amount' => 0,
            'total_room_amount'    => 500000.00,
            'surcharge_amount'     => 0,
            'final_amount'         => 500000.00,
        ]);

        DB::table('bookings')->where('id', $booking3->id)->update(['status' => 'Chờ xác nhận']);

        BookingDetail::create([
            'room_id'          => $room201->id,
            'booking_id'       => $booking3->id,
            'checkin_date'     => now()->addDay()->setTime(14, 0),
            'checkout_date'    => now()->addDays(2)->setTime(12, 0),
            'hourly_price'     => $room201->roomType->hourly_price,
            'daily_price'      => $room201->roomType->daily_price,
            'service_amount'   => 0,
            'surcharge_amount' => 0,
        ]);
        DB::table('booking_details')->where('booking_id', $booking3->id)->update(['checkout_status' => false]);
    }
}
