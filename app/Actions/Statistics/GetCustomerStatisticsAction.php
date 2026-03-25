<?php

namespace App\Actions\Statistics;

use App\Models\Booking;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class GetCustomerStatisticsAction
{
    private const CANCELLED_STATUSES = ['Hủy', 'Đã hủy', 'Không đến'];
    private const LOYAL_CUSTOMERS_PER_PAGE = 5;
    private const PAGE_SIZE_OPTIONS = [5, 10, 25, 50, 100];

    public function execute(Request $request): array
    {
        $referenceDate = $request->input('date')
            ? Carbon::parse($request->input('date'))
            : now();

        $periodStart = $referenceDate->copy()->startOfMonth()->startOfDay();
        $periodEnd = $referenceDate->copy()->endOfMonth()->endOfDay();

        $countryFilter = (string) $request->input('country', 'all');

        $requestedPageSize = (int) $request->input('page_size', self::LOYAL_CUSTOMERS_PER_PAGE);
        $pageSize = in_array($requestedPageSize, self::PAGE_SIZE_OPTIONS, true)
            ? $requestedPageSize
            : self::LOYAL_CUSTOMERS_PER_PAGE;

        $countryOptions = Customer::query()
            ->whereNotNull('country')
            ->select('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->values()
            ->all();

        $validBookingsQuery = $this->baseValidBookingsQuery($periodStart, $periodEnd, $countryFilter);

        $totalVisitors = (int) (clone $validBookingsQuery)
            ->distinct('customer_id')
            ->count('customer_id');

        $newCustomers = $this->calculateNewCustomers($periodStart, $periodEnd, $countryFilter);
        $returnRate = $this->calculateReturnRate($periodStart, $periodEnd, $countryFilter);
        $averageRating = $this->estimateRating($periodStart, $periodEnd, $countryFilter);

        $comparison = $this->buildPeriodComparison(
            $periodStart,
            $periodEnd,
            $countryFilter,
            $totalVisitors,
            $newCustomers,
            $returnRate,
            $averageRating
        );

        $loyalCustomersResult = $this->buildLoyalCustomers(
            $periodStart,
            $periodEnd,
            $countryFilter,
            $pageSize
        );

        return [
            'filters' => [
                'date' => $referenceDate->format('Y-m-d'),
                'country' => $countryFilter,
                'countries' => $countryOptions,
                'page_size' => $pageSize,
            ],
            'kpi' => [
                [
                    'label' => 'Tổng lượt khách',
                    'value' => $totalVisitors,
                    'change' => $comparison['total_visitors_change'],
                    'meta' => 'Tổng khách có booking trong kỳ',
                ],
                [
                    'label' => 'Khách hàng mới',
                    'value' => $newCustomers,
                    'change' => $comparison['new_customers_change'],
                    'meta' => 'Lần đầu phát sinh booking',
                ],
                [
                    'label' => 'Tỷ lệ quay lại',
                    'value' => $returnRate,
                    'isPercent' => true,
                    'change' => $comparison['return_rate_change'],
                    'meta' => 'Khách có từ 2 booking trở lên',
                ],
                [
                    'label' => 'Đánh giá TB',
                    'value' => $averageRating,
                    'isRating' => true,
                    'change' => $comparison['rating_change'],
                    'meta' => 'Ước lượng theo tỷ lệ hoàn tất',
                ],
            ],
            'loyal_customers' => $loyalCustomersResult['items'],
            'loyal_customers_total' => $loyalCustomersResult['total'],
        ];
    }

    private function baseValidBookingsQuery(Carbon $start, Carbon $end, string $countryFilter): Builder
    {
        $query = Booking::query()
            ->whereBetween('booking_date', [$start, $end])
            ->whereNotIn('status', self::CANCELLED_STATUSES);

        if ($countryFilter !== 'all') {
            $query->whereHas('customer', function (Builder $customerQuery) use ($countryFilter): void {
                $customerQuery->where('country', $countryFilter);
            });
        }

        return $query;
    }

    private function calculateNewCustomers(Carbon $start, Carbon $end, string $countryFilter): int
    {
        $firstBookingSubQuery = Booking::query()
            ->selectRaw('customer_id, MIN(booking_date) as first_booking_date')
            ->whereNotIn('status', self::CANCELLED_STATUSES)
            ->groupBy('customer_id');

        $query = Customer::query()
            ->joinSub($firstBookingSubQuery, 'first_bookings', function ($join): void {
                $join->on('customers.id', '=', 'first_bookings.customer_id');
            })
            ->whereBetween('first_bookings.first_booking_date', [$start, $end]);

        if ($countryFilter !== 'all') {
            $query->where('customers.country', $countryFilter);
        }

        return (int) $query->count();
    }

    private function calculateReturnRate(Carbon $start, Carbon $end, string $countryFilter): float
    {
        $periodBookingCounts = Booking::query()
            ->selectRaw('customer_id, COUNT(*) as booking_count')
            ->whereBetween('booking_date', [$start, $end])
            ->whereNotIn('status', self::CANCELLED_STATUSES)
            ->when($countryFilter !== 'all', function (Builder $query) use ($countryFilter): void {
                $query->whereHas('customer', function (Builder $customerQuery) use ($countryFilter): void {
                    $customerQuery->where('country', $countryFilter);
                });
            })
            ->groupBy('customer_id')
            ->get();

        $activeCustomers = (int) $periodBookingCounts->count();
        if ($activeCustomers === 0) {
            return 0;
        }

        $returningCustomers = (int) $periodBookingCounts
            ->filter(static fn ($row) => (int) $row->booking_count >= 2)
            ->count();

        return round(($returningCustomers / $activeCustomers) * 100, 1);
    }

    private function estimateRating(Carbon $start, Carbon $end, string $countryFilter): float
    {
        $query = $this->baseValidBookingsQuery($start, $end, $countryFilter);

        $totalBookings = (int) (clone $query)->count();
        if ($totalBookings === 0) {
            return 4.8;
        }

        $completedBookings = (int) (clone $query)
            ->where('status', 'Hoàn tất')
            ->count();

        $completionRatio = $completedBookings / $totalBookings;

        return round(min(5, 4 + $completionRatio), 1);
    }

    private function buildPeriodComparison(
        Carbon $periodStart,
        Carbon $periodEnd,
        string $countryFilter,
        int $currentTotalVisitors,
        int $currentNewCustomers,
        float $currentReturnRate,
        float $currentAverageRating
    ): array {
        $periodDays = max(1, (int) $periodStart->diffInDays($periodEnd->copy()->addSecond()));
        $previousStart = $periodStart->copy()->subDays($periodDays);
        $previousEnd = $periodStart->copy()->subSecond();

        $previousVisitors = (int) $this->baseValidBookingsQuery($previousStart, $previousEnd, $countryFilter)
            ->distinct('customer_id')
            ->count('customer_id');

        $previousNewCustomers = $this->calculateNewCustomers($previousStart, $previousEnd, $countryFilter);
        $previousReturnRate = $this->calculateReturnRate($previousStart, $previousEnd, $countryFilter);
        $previousRating = $this->estimateRating($previousStart, $previousEnd, $countryFilter);

        return [
            'total_visitors_change' => $this->percentChange($currentTotalVisitors, $previousVisitors),
            'new_customers_change' => $this->percentChange($currentNewCustomers, $previousNewCustomers),
            'return_rate_change' => $this->percentChange($currentReturnRate, $previousReturnRate),
            'rating_change' => $this->percentChange($currentAverageRating, $previousRating),
        ];
    }

    private function buildLoyalCustomers(
        Carbon $start,
        Carbon $end,
        string $countryFilter,
        int $pageSize
    ): array
    {
        $baseQuery = Booking::query()
            ->join('customers', 'customers.id', '=', 'bookings.customer_id')
            ->whereBetween('bookings.booking_date', [$start, $end])
            ->whereNotIn('bookings.status', self::CANCELLED_STATUSES);

        if ($countryFilter !== 'all') {
            $baseQuery->where('customers.country', $countryFilter);
        }

        $query = (clone $baseQuery)
            ->groupBy('customers.id', 'customers.first_name', 'customers.last_name', 'customers.email')
            ->selectRaw('customers.id as customer_id')
            ->selectRaw('customers.first_name as first_name')
            ->selectRaw('customers.last_name as last_name')
            ->selectRaw('customers.email as email')
            ->selectRaw('COUNT(bookings.id) as visits_count')
            ->selectRaw('SUM(bookings.final_amount) as total_spent')
            ->orderByDesc('visits_count')
            ->orderByDesc('total_spent');

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($pageSize)->withQueryString();
        $paginator->setCollection(
            $paginator->getCollection()->map(fn ($row) => $this->mapLoyalCustomerRow($row))
        );

        return [
            'items' => $paginator,
            'total' => $paginator->total(),
        ];
    }

    private function mapLoyalCustomerRow(object $row): array
    {
        $fullName = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));
        $avatarSeed = strtoupper(substr($row->first_name ?? 'K', 0, 1) . substr($row->last_name ?? 'H', 0, 1));

        return [
            'name' => $fullName !== '' ? $fullName : 'Khách hàng',
            'email' => (string) ($row->email ?? ''),
            'avatar_seed' => $avatarSeed,
            'visits_count' => (int) $row->visits_count,
            'total_spent' => (float) $row->total_spent,
        ];
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
