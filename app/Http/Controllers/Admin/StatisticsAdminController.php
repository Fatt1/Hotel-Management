<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\MaintenanceTicket;
use App\Models\Room;
use App\Models\ServiceUsage;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StatisticsAdminController extends Controller
{
    private const SECTION_LABELS = [
        'overview' => 'Tổng quan',
        'revenue' => 'Doanh thu',
        'room-performance' => 'Hiệu suất phòng',
        'customers' => 'Khách hàng',
    ];

    public function index(?string $section = 'overview')
    {
        $section = $section ?: 'overview';

        if (!array_key_exists($section, self::SECTION_LABELS)) {
            abort(404);
        }

        $overviewData = $section === 'overview'
            ? $this->buildOverviewData()
            : null;

        return view('admin.statistics.index', [
            'section' => $section,
            'sectionLabels' => self::SECTION_LABELS,
            'overviewData' => $overviewData,
        ]);
    }

    private function buildOverviewData(): array
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();
        $prevMonthStart = $monthStart->copy()->subMonth()->startOfMonth();
        $prevMonthEnd = $monthStart->copy()->subMonth()->endOfMonth();

        $activeBookingStatuses = ['Chờ xác nhận', 'Đã đặt', 'Đang ở', 'Hoàn tất'];

        $currentMonthBookings = Booking::query()
            ->whereIn('status', $activeBookingStatuses)
            ->whereBetween('booking_date', [$monthStart, $monthEnd]);

        $prevMonthBookings = Booking::query()
            ->whereIn('status', $activeBookingStatuses)
            ->whereBetween('booking_date', [$prevMonthStart, $prevMonthEnd]);

        $monthlyRevenue = (float) (clone $currentMonthBookings)->sum('final_amount');
        $previousRevenue = (float) (clone $prevMonthBookings)->sum('final_amount');

        $monthlyGuests = (int) (clone $currentMonthBookings)
            ->distinct('customer_id')
            ->count('customer_id');
        $previousGuests = (int) (clone $prevMonthBookings)
            ->distinct('customer_id')
            ->count('customer_id');

        [$occupancyRate, $previousOccupancyRate] = $this->calculateOccupancyRate(
            $monthStart,
            $monthEnd,
            $prevMonthStart,
            $prevMonthEnd,
            $activeBookingStatuses
        );

        // Chưa có bảng đánh giá riêng, dùng tỷ lệ booking hoàn tất để ước lượng điểm hài lòng.
        $monthlyCompletedBookings = Booking::query()
            ->where('status', 'Hoàn tất')
            ->whereBetween('booking_date', [$monthStart, $monthEnd])
            ->count();
        $monthlyBookingCount = (int) (clone $currentMonthBookings)->count();

        $previousCompletedBookings = Booking::query()
            ->where('status', 'Hoàn tất')
            ->whereBetween('booking_date', [$prevMonthStart, $prevMonthEnd])
            ->count();
        $previousBookingCount = (int) (clone $prevMonthBookings)->count();

        $averageRating = $this->estimateRating($monthlyCompletedBookings, $monthlyBookingCount);
        $previousRating = $this->estimateRating($previousCompletedBookings, $previousBookingCount);

        $trendSeries = $this->buildRevenueCostTrend($activeBookingStatuses);
        $revenueComposition = $this->buildRevenueComposition($monthStart, $monthEnd, $activeBookingStatuses);
        $recentActivities = $this->buildRecentActivities(30);

        return [
            'kpi' => [
                [
                    'label' => 'Tổng doanh thu tháng',
                    'value' => $monthlyRevenue,
                    'isCurrency' => true,
                    'change' => $this->percentChange($monthlyRevenue, $previousRevenue),
                ],
                [
                    'label' => 'Công suất phòng TB',
                    'value' => $occupancyRate,
                    'isPercent' => true,
                    'change' => $this->percentChange($occupancyRate, $previousOccupancyRate),
                ],
                [
                    'label' => 'Tổng lượt khách',
                    'value' => $monthlyGuests,
                    'change' => $this->percentChange($monthlyGuests, $previousGuests),
                ],
                [
                    'label' => 'Đánh giá trung bình',
                    'value' => $averageRating,
                    'isRating' => true,
                    'change' => $this->percentChange($averageRating, $previousRating),
                ],
            ],
            'trend' => $trendSeries,
            'composition' => $revenueComposition,
            'recentActivities' => $recentActivities,
            'recentActivitiesPreview' => array_slice($recentActivities, 0, 5),
        ];
    }

    private function calculateOccupancyRate(
        Carbon $monthStart,
        Carbon $monthEnd,
        Carbon $prevMonthStart,
        Carbon $prevMonthEnd,
        array $activeBookingStatuses
    ): array {
        $current = $this->occupancyForRange($monthStart, $monthEnd, $activeBookingStatuses);
        $previous = $this->occupancyForRange($prevMonthStart, $prevMonthEnd, $activeBookingStatuses);

        return [round($current, 1), round($previous, 1)];
    }

    private function occupancyForRange(Carbon $start, Carbon $end, array $activeBookingStatuses): float
    {
        $roomsCount = max(1, Room::query()->count());
        $periodEndExclusive = $end->copy()->addDay()->startOfDay();

        $bookingDetails = BookingDetail::query()
            ->join('bookings', 'bookings.id', '=', 'booking_details.booking_id')
            ->whereIn('bookings.status', $activeBookingStatuses)
            ->where('booking_details.checkout_date', '>', $start)
            ->where('booking_details.checkin_date', '<', $periodEndExclusive)
            ->get(['booking_details.checkin_date', 'booking_details.checkout_date']);

        $occupiedRoomNights = 0.0;

        foreach ($bookingDetails as $detail) {
            $effectiveStart = Carbon::parse($detail->checkin_date);
            if ($effectiveStart->lt($start)) {
                $effectiveStart = $start->copy();
            }

            $effectiveEnd = Carbon::parse($detail->checkout_date);
            if ($effectiveEnd->gt($periodEndExclusive)) {
                $effectiveEnd = $periodEndExclusive->copy();
            }

            if ($effectiveEnd->gt($effectiveStart)) {
                $occupiedRoomNights += $effectiveStart->diffInMinutes($effectiveEnd) / (60 * 24);
            }
        }

        $totalCapacity = $roomsCount * $start->daysInMonth;

        return $totalCapacity > 0 ? ($occupiedRoomNights / $totalCapacity) * 100 : 0;
    }

    private function estimateRating(int $completedBookings, int $totalBookings): float
    {
        if ($totalBookings === 0) {
            return 4.8;
        }

        $completionRatio = $completedBookings / $totalBookings;

        return round(min(5, 4 + $completionRatio), 1);
    }

    private function buildRevenueCostTrend(array $activeBookingStatuses): array
    {
        $labels = [];
        $revenueData = [];
        $costData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->copy()->subMonths($i);
            $pointStart = $monthDate->copy()->startOfMonth();
            $pointEnd = $monthDate->copy()->endOfMonth();

            $labels[] = 'Thg ' . $monthDate->format('n');
            $revenueData[] = (float) Booking::query()
                ->whereIn('status', $activeBookingStatuses)
                ->whereBetween('booking_date', [$pointStart, $pointEnd])
                ->sum('final_amount');

            $costData[] = (float) MaintenanceTicket::query()
                ->whereBetween('reported_date', [$pointStart->toDateString(), $pointEnd->toDateString()])
                ->sum('repair_cost');
        }

        return [
            'labels' => $labels,
            'revenueData' => $revenueData,
            'costData' => $costData,
        ];
    }

    private function buildRevenueComposition(Carbon $monthStart, Carbon $monthEnd, array $activeBookingStatuses): array
    {
        $roomRevenue = (float) Booking::query()
            ->whereIn('status', $activeBookingStatuses)
            ->whereBetween('booking_date', [$monthStart, $monthEnd])
            ->sum('total_room_amount');

        $serviceRows = ServiceUsage::query()
            ->join('booking_details', 'booking_details.id', '=', 'service_usages.booking_detail_id')
            ->join('bookings', 'bookings.id', '=', 'booking_details.booking_id')
            ->leftJoin('services', 'services.id', '=', 'service_usages.service_id')
            ->leftJoin('service_groups', 'service_groups.id', '=', 'services.group_id')
            ->whereIn('bookings.status', $activeBookingStatuses)
            ->whereBetween('service_usages.created_at', [$monthStart, $monthEnd])
            ->groupBy('service_groups.service_name')
            ->selectRaw('COALESCE(service_groups.service_name, "Khác") as group_name')
            ->selectRaw('SUM(service_usages.quantity * service_usages.unit_price) as amount')
            ->get();

        $fnbRevenue = 0.0;
        $spaAndOtherRevenue = 0.0;

        /** @var Collection<int, object> $serviceRows */
        foreach ($serviceRows as $row) {
            $groupName = Str::lower((string) $row->group_name);
            $amount = (float) $row->amount;

            if ($this->isFnbGroup($groupName)) {
                $fnbRevenue += $amount;
                continue;
            }

            $spaAndOtherRevenue += $amount;
        }

        $series = [$roomRevenue, $fnbRevenue, $spaAndOtherRevenue];
        $total = array_sum($series);

        $percentages = array_map(
            static fn (float $value): float => $total > 0 ? round(($value / $total) * 100, 1) : 0,
            $series
        );

        return [
            'labels' => ['Đặt phòng (Room)', 'Dịch vụ F&B', 'Spa & Khác'],
            'series' => $series,
            'percentages' => $percentages,
            'total' => $total,
        ];
    }

    private function buildRecentActivities(int $limit = 8): array
    {
        $activities = [];

        $serviceUsages = ServiceUsage::query()
            ->with([
                'service.group',
                'bookingDetail.room',
                'bookingDetail.booking.customer',
            ])
            ->latest()
            ->limit($limit)
            ->get();

        foreach ($serviceUsages as $usage) {
            $serviceName = $usage->service?->name ?? 'Dịch vụ';
            $groupName = Str::lower((string) ($usage->service?->group?->service_name ?? 'khác'));
            $customerName = $usage->bookingDetail?->booking?->customer?->full_name ?? 'Khách lưu trú';
            $roomName = $usage->bookingDetail?->room?->name ?? 'N/A';

            $title = $this->isSpaGroup($groupName)
                ? "Dịch vụ Spa - Phòng {$roomName}"
                : ($this->isFnbGroup($groupName)
                    ? "Dịch vụ F&B - Phòng {$roomName}"
                    : "{$serviceName} - Phòng {$roomName}");

            $activities[] = [
                'title' => $title,
                'subtitle' => "Khách hàng: {$customerName}",
                'time' => $usage->created_at?->diffForHumans() ?? 'Vừa xong',
                'timestamp' => $usage->created_at?->timestamp ?? 0,
                'icon' => $this->isSpaGroup($groupName) ? 'spa' : ($this->isFnbGroup($groupName) ? 'restaurant' : 'room_service'),
                'color' => $this->isSpaGroup($groupName) ? 'text-violet-600 bg-violet-100' : ($this->isFnbGroup($groupName) ? 'text-emerald-600 bg-emerald-100' : 'text-orange-600 bg-orange-100'),
            ];
        }

        $recentBookings = Booking::query()
            ->with('customer')
            ->latest('booking_date')
            ->limit($limit)
            ->get();

        foreach ($recentBookings as $booking) {
            $statusText = (string) $booking->status;
            $activities[] = [
                'title' => "Đơn đặt phòng mới - UL-{$booking->id}",
                'subtitle' => "Khách hàng: " . ($booking->customer?->full_name ?? 'Khách lưu trú') . " • Trạng thái: {$statusText}",
                'time' => $booking->booking_date?->diffForHumans() ?? 'Vừa xong',
                'timestamp' => $booking->booking_date?->timestamp ?? 0,
                'icon' => 'event_available',
                'color' => 'text-blue-600 bg-blue-100',
            ];
        }

        usort($activities, static fn (array $a, array $b): int => ($b['timestamp'] ?? 0) <=> ($a['timestamp'] ?? 0));
        $activities = array_slice($activities, 0, $limit);

        foreach ($activities as &$activity) {
            unset($activity['timestamp']);
        }
        unset($activity);

        return $activities;
    }

    private function isFnbGroup(string $groupName): bool
    {
        return Str::contains($groupName, [
            'f&b',
            'fb',
            'food',
            'restaurant',
            'nha hang',
            'nhà hàng',
            'am thuc',
            'ẩm thực',
            'ăn uống',
            'buffet',
            'bar',
        ]);
    }

    private function isSpaGroup(string $groupName): bool
    {
        return Str::contains($groupName, [
            'spa',
            'massage',
            'wellness',
            'xông',
            'relax',
        ]);
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
