<?php

namespace App\Actions\Bookings;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class GetClientBookingsAction
{
    /**
     * @return array{upcomingBookings: Collection<int, Booking>, pastBookings: LengthAwarePaginator}
     */
    public function execute(int $customerId, int $historyPageSize = 5): array
    {
        $now = Carbon::now();

        $hasBookingCheckoutDate = Schema::hasColumn('bookings', 'checkout_date');
        $detailCheckoutSql = "(SELECT MAX(bd.checkout_date) FROM booking_details bd WHERE bd.booking_id = bookings.id)";

        $upcomingBookingsQuery = Booking::query()
            ->where('customer_id', $customerId)
            ->whereNotIn('status', ['Hủy', 'Không đến', 'Hoàn tất'])
            ->with(['bookingDetails.room.roomType'])
            ->orderByDesc('booking_date');

        if ($hasBookingCheckoutDate) {
            $upcomingBookingsQuery->where(function ($query) use ($now): void {
                $query
                    ->whereNull('bookings.checkout_date')
                    ->orWhere('bookings.checkout_date', '>=', $now);
            });
        } else {
            $upcomingBookingsQuery->whereRaw("({$detailCheckoutSql} IS NULL OR {$detailCheckoutSql} >= ?)", [$now]);
        }

        $pastBookingsQuery = Booking::query()
            ->where('customer_id', $customerId)
            ->with(['bookingDetails.room.roomType'])
            ->orderByDesc('booking_date');

        if ($hasBookingCheckoutDate) {
            $pastBookingsQuery->where(function ($query) use ($now): void {
                $query
                    ->whereIn('status', ['Hủy', 'Không đến', 'Hoàn tất'])
                    ->orWhere(function ($subQuery) use ($now): void {
                        $subQuery
                            ->whereNotNull('bookings.checkout_date')
                            ->where('bookings.checkout_date', '<', $now);
                    });
            });
        } else {
            $pastBookingsQuery->where(function ($query) use ($now, $detailCheckoutSql): void {
                $query
                    ->whereIn('status', ['Hủy', 'Không đến', 'Hoàn tất'])
                    ->orWhereRaw("({$detailCheckoutSql} IS NOT NULL AND {$detailCheckoutSql} < ?)", [$now]);
            });
        }

        return [
            'upcomingBookings' => $upcomingBookingsQuery->get(),
            'pastBookings' => $pastBookingsQuery
                ->paginate($historyPageSize, ['*'], 'history_page')
                ->withQueryString(),
        ];
    }
}
