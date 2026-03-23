<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Statistics\GetOverviewStatisticsAction;
use App\Actions\Statistics\GetRevenueStatisticsAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatisticsAdminController extends Controller
{
    private const SECTION_LABELS = [
        'overview' => 'Tổng quan',
        'revenue' => 'Doanh thu',
        'room-performance' => 'Hiệu suất phòng',
        'customers' => 'Khách hàng',
    ];

    public function overview(GetOverviewStatisticsAction $action)
    {
        return view('admin.statistics.index', [
            'section' => 'overview',
            'sectionLabels' => self::SECTION_LABELS,
            'overviewData' => $action->execute(),
        ]);
    }

    public function revenue(Request $request, GetRevenueStatisticsAction $action)
    {
        return view('admin.statistics.index', [
            'section' => 'revenue',
            'sectionLabels' => self::SECTION_LABELS,
            'revenueData' => $action->execute($request),
        ]);
    }

    public function roomPerformance()
    {
        return view('admin.statistics.index', [
            'section' => 'room-performance',
            'sectionLabels' => self::SECTION_LABELS,
        ]);
    }

    public function customers()
    {
        return view('admin.statistics.index', [
            'section' => 'customers',
            'sectionLabels' => self::SECTION_LABELS,
        ]);
    }
}