<?php

namespace App\Actions\Statistics;

use App\Models\Booking;
use App\Models\MaintenanceTicket;
use App\Models\ServiceUsage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GetRevenueStatisticsAction
{
    public function execute(Request $request): array
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : now()->endOfMonth();
        
        $source = $request->input('source', 'all'); // 'all', 'room', 'service', 'surcharge'
        $status = $request->input('status', 'all'); // 'all', 'paid' ('Hoàn tất'), 'unpaid' (!= 'Hoàn tất')

        $query = Booking::query()->whereBetween('booking_date', [$startDate, $endDate]);

        if ($status === 'paid') {
            $query->where('status', 'Hoàn tất');
        } elseif ($status === 'unpaid') {
            $query->where('status', '!=', 'Hoàn tất')->where('status', '!=', 'Đã hủy');
        } else {
            $query->where('status', '!=', 'Đã hủy'); // always exclude cancelled for revenue
        }

        $totalRoom = (float) (clone $query)->sum('total_room_amount');
        $totalService = (float) (clone $query)->sum('total_service_amount');
        $totalSurcharge = (float) (clone $query)->sum('surcharge_amount');

        $filteredTotal = 0;
        if ($source === 'room') {
            $filteredTotal = $totalRoom;
            $totalService = 0;
            $totalSurcharge = 0;
        } elseif ($source === 'service') {
            $filteredTotal = $totalService;
            $totalRoom = 0;
            $totalSurcharge = 0;
        } elseif ($source === 'surcharge') {
            $filteredTotal = $totalSurcharge;
            $totalRoom = 0;
            $totalService = 0;
        } else {
            $filteredTotal = $totalRoom + $totalService + $totalSurcharge;
        }

        // Chart data
        $trendSeries = $this->buildRevenueTrendChart($source, $status);
        
        $revenueComposition = [
            'labels' => ['Tiền phòng', 'Tiền dịch vụ', 'Phụ thu'],
            'series' => [$totalRoom, $totalService, $totalSurcharge],
        ];

        return [
            'filters' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'source' => $source,
                'status' => $status,
            ],
            'kpi' => [
                [
                    'label' => 'Tổng doanh thu',
                    'value' => $filteredTotal,
                    'isCurrency' => true,
                    'icon' => 'account_balance_wallet',
                    'color' => 'text-orange-500',
                    'bg_color' => 'bg-orange-100'
                ],
                [
                    'label' => 'Tiền phòng',
                    'value' => $totalRoom,
                    'isCurrency' => true,
                    'icon' => 'bed',
                    'color' => 'text-blue-500',
                    'bg_color' => 'bg-blue-100'
                ],
                [
                    'label' => 'Tiền dịch vụ',
                    'value' => $totalService,
                    'isCurrency' => true,
                    'icon' => 'room_service',
                    'color' => 'text-emerald-500',
                    'bg_color' => 'bg-emerald-100'
                ],
                [
                    'label' => 'Phụ thu',
                    'value' => $totalSurcharge,
                    'isCurrency' => true,
                    'icon' => 'request_quote',
                    'color' => 'text-violet-500',
                    'bg_color' => 'bg-violet-100'
                ],
            ],
            'trend' => $trendSeries,
            'composition' => $revenueComposition,
        ];
    }

    private function buildRevenueTrendChart(string $source, string $status): array
    {
        $currentYear = now()->year;
        $lastYear = $currentYear - 1;

        $labels = [];
        $currentYearData = [];
        $lastYearData = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = "Tháng $month";

            $currentData = Booking::query()
                ->whereYear('booking_date', $currentYear)
                ->whereMonth('booking_date', $month);
            
            $lastData = Booking::query()
                ->whereYear('booking_date', $lastYear)
                ->whereMonth('booking_date', $month);

            if ($status === 'paid') {
                $currentData->where('status', 'Hoàn tất');
                $lastData->where('status', 'Hoàn tất');
            } elseif ($status === 'unpaid') {
                $currentData->where('status', '!=', 'Hoàn tất')->where('status', '!=', 'Đã hủy');
                $lastData->where('status', '!=', 'Hoàn tất')->where('status', '!=', 'Đã hủy');
            } else {
                $currentData->where('status', '!=', 'Đã hủy');
                $lastData->where('status', '!=', 'Đã hủy');
            }

            $sumCurrent = 0;
            $sumLast = 0;

            if ($source === 'room' || $source === 'all') {
                $sumCurrent += (float) (clone $currentData)->sum('total_room_amount');
                $sumLast += (float) (clone $lastData)->sum('total_room_amount');
            }
            if ($source === 'service' || $source === 'all') {
                $sumCurrent += (float) (clone $currentData)->sum('total_service_amount');
                $sumLast += (float) (clone $lastData)->sum('total_service_amount');
            }
            if ($source === 'surcharge' || $source === 'all') {
                $sumCurrent += (float) (clone $currentData)->sum('surcharge_amount');
                $sumLast += (float) (clone $lastData)->sum('surcharge_amount');
            }

            $currentYearData[] = $sumCurrent;
            $lastYearData[] = $sumLast;
        }

        return [
            'labels' => $labels,
            'current_year' => $currentYearData,
            'last_year' => $lastYearData,
            'year' => $currentYear,
        ];
    }
}
