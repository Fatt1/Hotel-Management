<?php

namespace App\Actions\Statistics;

use App\Enums\RoomStatus;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class GetRoomPerformanceStatisticsAction
{
    private const CANCELLED_STATUSES = ['Hủy', 'Đã hủy'];

    public function execute(Request $request): array
    {
        $referenceDate = $request->input('date')
            ? Carbon::parse($request->input('date'))
            : now();

        $periodStart = $referenceDate->copy()->startOfMonth()->startOfDay();
        $periodEnd = $referenceDate->copy()->endOfMonth()->endOfDay();
        $periodEndExclusive = $periodEnd->copy()->addSecond();

        $roomTypeFilter = $request->input('room_type_id', 'all');
        $bookingStatusFilter = $request->input('booking_status', 'all');

        $roomTypeOptions = RoomType::query()
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(fn (RoomType $type) => [
                'id' => (string) $type->id,
                'label' => trim(($type->code ? "{$type->code} - " : '') . $type->name),
            ])
            ->values()
            ->all();

        $bookingStatusOptions = Booking::query()
            ->select('status')
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->values()
            ->all();

        $totalRoomsQuery = Room::query();
        if ($roomTypeFilter !== 'all') {
            $totalRoomsQuery->where('room_type_id', (int) $roomTypeFilter);
        }
        $totalRooms = (int) $totalRoomsQuery->count();

        $performanceDetailsQuery = BookingDetail::query()
            ->where('checkout_date', '>', $periodStart)
            ->where('checkin_date', '<', $periodEndExclusive)
            ->with([
                'booking:id,status,booking_date,final_amount',
                'room:id,name,room_type_id,floor_id,status',
                'room.roomType:id,name,code',
                'room.floor:id,name',
            ]);

        if ($roomTypeFilter !== 'all') {
            $performanceDetailsQuery->whereHas('room', function (Builder $query) use ($roomTypeFilter): void {
                $query->where('room_type_id', (int) $roomTypeFilter);
            });
        }

        $this->applyBookingStatusFilterToDetails($performanceDetailsQuery, $bookingStatusFilter);

        $performanceDetails = $performanceDetailsQuery->get();

        $periodDays = max(1, (int) $periodStart->diffInDays($periodEndExclusive));
        $occupiedRoomNights = $this->sumOccupiedNights($performanceDetails, $periodStart, $periodEndExclusive);
        $occupancyRate = $this->calculateOccupancyRate($occupiedRoomNights, $totalRooms, $periodDays);

        $totalBookings = (int) $performanceDetails
            ->pluck('booking_id')
            ->unique()
            ->count();

        $cancelledBookings = $this->buildCancelledBookingsQuery(
            $periodStart,
            $periodEnd,
            $roomTypeFilter
        )->count();

        $projectedRevenue = $this->buildRevenueQuery(
            $periodStart,
            $periodEnd,
            $roomTypeFilter,
            $bookingStatusFilter
        )->sum('final_amount');

        $comparison = $this->buildPeriodComparison(
            $periodStart,
            $periodEnd,
            $roomTypeFilter,
            $bookingStatusFilter,
            $occupancyRate,
            $totalBookings,
            (int) $cancelledBookings,
            (float) $projectedRevenue
        );

        $currentRoomStatus = $this->buildCurrentRoomStatus($roomTypeFilter);
        $topRoomTypes = $this->buildTopRoomTypes(
            $periodStart,
            $periodEndExclusive,
            $roomTypeFilter,
            $bookingStatusFilter
        );

        $roomDetailsTable = $this->buildRoomDetailTable(
            $roomTypeFilter,
            $periodStart,
            $periodEndExclusive,
            $periodDays,
            $currentRoomStatus['occupied_room_ids']
        );

        return [
            'filters' => [
                'date' => $referenceDate->format('Y-m-d'),
                'room_type_id' => (string) $roomTypeFilter,
                'booking_status' => $bookingStatusFilter,
                'room_types' => $roomTypeOptions,
                'booking_statuses' => $bookingStatusOptions,
            ],
            'kpi' => [
                [
                    'label' => 'Hiệu suất phòng (%)',
                    'value' => round($occupancyRate, 1),
                    'isPercent' => true,
                    'change' => $comparison['occupancy_change'],
                    'meta' => $totalRooms > 0
                        ? 'Công suất theo tổng số phòng khả dụng'
                        : 'Chưa có phòng để tính hiệu suất',
                ],
                [
                    'label' => 'Tổng số đặt phòng',
                    'value' => $totalBookings,
                    'change' => $comparison['booking_change'],
                    'meta' => 'Trong tháng đã chọn',
                ],
                [
                    'label' => 'Số lượng hủy phòng',
                    'value' => (int) $cancelledBookings,
                    'change' => $comparison['cancel_change'],
                    'meta' => 'Booking trạng thái hủy',
                ],
                [
                    'label' => 'Doanh thu dự kiến (tháng)',
                    'value' => (float) $projectedRevenue,
                    'isCurrency' => true,
                    'change' => $comparison['revenue_change'],
                    'meta' => 'Tổng final amount theo bộ lọc',
                ],
            ],
            'room_status' => [
                'total_rooms' => $currentRoomStatus['total_rooms'],
                'occupied' => $currentRoomStatus['occupied'],
                'available' => $currentRoomStatus['available'],
                'maintenance' => $currentRoomStatus['maintenance'],
            ],
            'top_room_types' => $topRoomTypes,
            'room_table' => $roomDetailsTable,
        ];
    }

    private function sumOccupiedNights($details, Carbon $rangeStart, Carbon $rangeEndExclusive): float
    {
        $occupiedRoomNights = 0.0;

        foreach ($details as $detail) {
            $effectiveStart = Carbon::parse($detail->checkin_date);
            if ($effectiveStart->lt($rangeStart)) {
                $effectiveStart = $rangeStart->copy();
            }

            $effectiveEnd = Carbon::parse($detail->checkout_date);
            if ($effectiveEnd->gt($rangeEndExclusive)) {
                $effectiveEnd = $rangeEndExclusive->copy();
            }

            if ($effectiveEnd->gt($effectiveStart)) {
                $occupiedRoomNights += $effectiveStart->diffInMinutes($effectiveEnd) / (60 * 24);
            }
        }

        return $occupiedRoomNights;
    }

    private function calculateOccupancyRate(float $occupiedRoomNights, int $totalRooms, int $periodDays): float
    {
        $totalCapacity = $totalRooms * $periodDays;
        if ($totalCapacity <= 0) {
            return 0;
        }

        return ($occupiedRoomNights / $totalCapacity) * 100;
    }

    private function applyBookingStatusFilterToDetails(Builder $query, string $bookingStatusFilter): void
    {
        if ($bookingStatusFilter === 'all') {
            $query->whereHas('booking', function (Builder $bookingQuery): void {
                $bookingQuery->whereNotIn('status', self::CANCELLED_STATUSES)
                    ->where('status', '!=', 'Không đến');
            });

            return;
        }

        $query->whereHas('booking', function (Builder $bookingQuery) use ($bookingStatusFilter): void {
            $bookingQuery->where('status', $bookingStatusFilter);
        });
    }

    private function buildCancelledBookingsQuery(
        Carbon $periodStart,
        Carbon $periodEnd,
        string $roomTypeFilter
    ): Builder {
        $query = Booking::query()
            ->whereBetween('booking_date', [$periodStart, $periodEnd])
            ->whereIn('status', self::CANCELLED_STATUSES);

        if ($roomTypeFilter !== 'all') {
            $query->whereHas('bookingDetails.room', function (Builder $roomQuery) use ($roomTypeFilter): void {
                $roomQuery->where('room_type_id', (int) $roomTypeFilter);
            });
        }

        return $query;
    }

    private function buildRevenueQuery(
        Carbon $periodStart,
        Carbon $periodEnd,
        string $roomTypeFilter,
        string $bookingStatusFilter
    ): Builder {
        $query = Booking::query()
            ->whereBetween('booking_date', [$periodStart, $periodEnd]);

        if ($bookingStatusFilter === 'all') {
            $query->whereNotIn('status', self::CANCELLED_STATUSES)
                ->where('status', '!=', 'Không đến');
        } else {
            $query->where('status', $bookingStatusFilter);
        }

        if ($roomTypeFilter !== 'all') {
            $query->whereHas('bookingDetails.room', function (Builder $roomQuery) use ($roomTypeFilter): void {
                $roomQuery->where('room_type_id', (int) $roomTypeFilter);
            });
        }

        return $query;
    }

    private function buildPeriodComparison(
        Carbon $periodStart,
        Carbon $periodEnd,
        string $roomTypeFilter,
        string $bookingStatusFilter,
        float $currentOccupancyRate,
        int $currentTotalBookings,
        int $currentCancelledBookings,
        float $currentRevenue
    ): array {
        $periodDays = max(1, (int) $periodStart->diffInDays($periodEnd->copy()->addSecond()));
        $previousStart = $periodStart->copy()->subDays($periodDays);
        $previousEnd = $periodStart->copy()->subSecond();
        $previousEndExclusive = $previousEnd->copy()->addSecond();

        $prevTotalRoomsQuery = Room::query();
        if ($roomTypeFilter !== 'all') {
            $prevTotalRoomsQuery->where('room_type_id', (int) $roomTypeFilter);
        }
        $prevTotalRooms = (int) $prevTotalRoomsQuery->count();

        $prevDetailsQuery = BookingDetail::query()
            ->where('checkout_date', '>', $previousStart)
            ->where('checkin_date', '<', $previousEndExclusive);

        if ($roomTypeFilter !== 'all') {
            $prevDetailsQuery->whereHas('room', function (Builder $query) use ($roomTypeFilter): void {
                $query->where('room_type_id', (int) $roomTypeFilter);
            });
        }

        $this->applyBookingStatusFilterToDetails($prevDetailsQuery, $bookingStatusFilter);

        $prevDetails = $prevDetailsQuery->get();

        $prevOccupiedNights = $this->sumOccupiedNights($prevDetails, $previousStart, $previousEndExclusive);
        $prevOccupancyRate = $this->calculateOccupancyRate($prevOccupiedNights, $prevTotalRooms, $periodDays);
        $prevTotalBookings = (int) $prevDetails->pluck('booking_id')->unique()->count();

        $prevCancelled = $this->buildCancelledBookingsQuery(
            $previousStart,
            $previousEnd,
            $roomTypeFilter
        )->count();

        $prevRevenue = $this->buildRevenueQuery(
            $previousStart,
            $previousEnd,
            $roomTypeFilter,
            $bookingStatusFilter
        )->sum('final_amount');

        return [
            'occupancy_change' => $this->percentChange(
                $currentOccupancyRate,
                $prevOccupancyRate
            ),
            'booking_change' => $this->percentChange(
                $currentTotalBookings,
                $prevTotalBookings
            ),
            'cancel_change' => $this->percentChange(
                $currentCancelledBookings,
                $prevCancelled
            ),
            'revenue_change' => $this->percentChange(
                $currentRevenue,
                $prevRevenue
            ),
        ];
    }

    private function buildCurrentRoomStatus(string $roomTypeFilter): array
    {
        $roomBaseQuery = Room::query();
        if ($roomTypeFilter !== 'all') {
            $roomBaseQuery->where('room_type_id', (int) $roomTypeFilter);
        }

        $totalRooms = (int) (clone $roomBaseQuery)->count();

        $maintenanceCount = (int) (clone $roomBaseQuery)
            ->whereIn('status', [RoomStatus::MAINTENANCE->value, RoomStatus::CLEANING->value])
            ->count();

        $now = now();
        $occupiedRoomIdsQuery = BookingDetail::query()
            ->join('bookings', 'bookings.id', '=', 'booking_details.booking_id')
            ->where('bookings.status', 'Đang ở')
            ->where('booking_details.checkout_status', false)
            ->where('booking_details.checkin_date', '<=', $now)
            ->where('booking_details.checkout_date', '>', $now)
            ->select('booking_details.room_id')
            ->distinct();

        if ($roomTypeFilter !== 'all') {
            $occupiedRoomIdsQuery->join('rooms', 'rooms.id', '=', 'booking_details.room_id')
                ->where('rooms.room_type_id', (int) $roomTypeFilter);
        }

        $occupiedRoomIds = $occupiedRoomIdsQuery
            ->pluck('booking_details.room_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $occupiedCount = count($occupiedRoomIds);
        $availableCount = max(0, $totalRooms - $maintenanceCount - $occupiedCount);

        return [
            'total_rooms' => $totalRooms,
            'occupied' => $occupiedCount,
            'available' => $availableCount,
            'maintenance' => $maintenanceCount,
            'occupied_room_ids' => $occupiedRoomIds,
        ];
    }

    private function buildTopRoomTypes(
        Carbon $periodStart,
        Carbon $periodEndExclusive,
        string $roomTypeFilter,
        string $bookingStatusFilter
    ): array {
        $query = BookingDetail::query()
            ->join('bookings', 'bookings.id', '=', 'booking_details.booking_id')
            ->join('rooms', 'rooms.id', '=', 'booking_details.room_id')
            ->join('room_types', 'room_types.id', '=', 'rooms.room_type_id')
            ->where('booking_details.checkout_date', '>', $periodStart)
            ->where('booking_details.checkin_date', '<', $periodEndExclusive);

        if ($bookingStatusFilter === 'all') {
            $query->whereNotIn('bookings.status', self::CANCELLED_STATUSES)
                ->where('bookings.status', '!=', 'Không đến');
        } else {
            $query->where('bookings.status', $bookingStatusFilter);
        }

        if ($roomTypeFilter !== 'all') {
            $query->where('rooms.room_type_id', (int) $roomTypeFilter);
        }

        $rows = $query
            ->groupBy('room_types.id', 'room_types.name', 'room_types.code')
            ->selectRaw('room_types.id as room_type_id')
            ->selectRaw('room_types.name as room_type_name')
            ->selectRaw('room_types.code as room_type_code')
            ->selectRaw('COUNT(booking_details.id) as bookings_count')
            ->orderByDesc('bookings_count')
            ->limit(6)
            ->get();

        $maxCount = max(1, (int) ($rows->first()->bookings_count ?? 0));

        return $rows
            ->map(static fn ($row) => [
                'name' => trim(($row->room_type_code ? "{$row->room_type_code} - " : '') . $row->room_type_name),
                'count' => (int) $row->bookings_count,
                'progress_percent' => round(((int) $row->bookings_count / $maxCount) * 100, 1),
            ])
            ->values()
            ->all();
    }

    private function buildRoomDetailTable(
        string $roomTypeFilter,
        Carbon $periodStart,
        Carbon $periodEndExclusive,
        int $periodDays,
        array $occupiedRoomIdsNow
    ): array {
        $roomsQuery = Room::query()
            ->with([
                'roomType:id,name,code',
                'floor:id,name',
            ])
            ->orderBy('name');

        if ($roomTypeFilter !== 'all') {
            $roomsQuery->where('room_type_id', (int) $roomTypeFilter);
        }

        $rooms = $roomsQuery->get();

        $bookingDetails = BookingDetail::query()
            ->whereIn('room_id', $rooms->pluck('id')->all())
            ->where('checkout_date', '>', $periodStart)
            ->where('checkin_date', '<', $periodEndExclusive)
            ->whereHas('booking', function (Builder $query): void {
                $query->whereNotIn('status', self::CANCELLED_STATUSES)
                    ->where('status', '!=', 'Không đến');
            })
            ->get(['room_id', 'checkin_date', 'checkout_date']);

        $occupiedDaysByRoom = [];

        foreach ($bookingDetails as $detail) {
            $effectiveStart = Carbon::parse($detail->checkin_date);
            if ($effectiveStart->lt($periodStart)) {
                $effectiveStart = $periodStart->copy();
            }

            $effectiveEnd = Carbon::parse($detail->checkout_date);
            if ($effectiveEnd->gt($periodEndExclusive)) {
                $effectiveEnd = $periodEndExclusive->copy();
            }

            if ($effectiveEnd->gt($effectiveStart)) {
                $occupiedDaysByRoom[$detail->room_id] = ($occupiedDaysByRoom[$detail->room_id] ?? 0)
                    + ($effectiveStart->diffInMinutes($effectiveEnd) / (60 * 24));
            }
        }

        $occupiedRoomIdMap = array_fill_keys($occupiedRoomIdsNow, true);

        return $rooms->map(function (Room $room) use ($occupiedDaysByRoom, $periodDays, $occupiedRoomIdMap): array {
            $occupiedDays = (float) ($occupiedDaysByRoom[$room->id] ?? 0);
            $fillRate = $periodDays > 0 ? round(($occupiedDays / $periodDays) * 100, 1) : 0;

            $currentStatus = 'Trống';
            $statusBadgeClass = 'bg-slate-100 text-slate-600';

            if (in_array($room->status, [RoomStatus::MAINTENANCE->value, RoomStatus::CLEANING->value], true)) {
                $currentStatus = 'Bảo trì';
                $statusBadgeClass = 'bg-rose-100 text-rose-600';
            } elseif (isset($occupiedRoomIdMap[$room->id])) {
                $currentStatus = 'Đang ở';
                $statusBadgeClass = 'bg-emerald-100 text-emerald-700';
            }

            return [
                'room_name' => $room->name,
                'room_type' => $room->roomType?->name ?? 'N/A',
                'floor_name' => $room->floor?->name ?? 'N/A',
                'fill_rate' => $fillRate,
                'fill_rate_width' => max(4, min(100, $fillRate)),
                'current_status' => $currentStatus,
                'status_badge_class' => $statusBadgeClass,
            ];
        })->values()->all();
    }

    private function percentChange(float|int $currentValue, float|int $previousValue): float
    {
        $current = (float) $currentValue;
        $previous = (float) $previousValue;

        if ($previous == 0.0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / abs($previous)) * 100, 1);
    }
}
