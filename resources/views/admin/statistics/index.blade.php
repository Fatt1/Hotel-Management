@extends('layouts.admin')

@section('title', 'Thống kê')

@section('content')
  <section class="p-6 xl:p-8 space-y-6">
    <div class="space-y-1">
      <h1 class="text-3xl font-black tracking-tight text-slate-900">Thống kê {{ $sectionLabels[$section] }}</h1>
      <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">Ngày làm việc: {{ now()->isoFormat('DD [tháng] MM, YYYY') }}</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-2 inline-flex gap-2 shadow-sm">
      @foreach ($sectionLabels as $key => $label)
          @php
              $routeName = $key === 'overview' ? 'admin.statistics.index' : "admin.statistics.{$key}";
          @endphp
          <a href="{{ route($routeName) }}"
            class="px-6 py-2.5 rounded-xl text-sm font-bold transition-colors {{ $section === $key ? 'bg-orange-500 text-white shadow' : 'text-slate-600 hover:bg-slate-100' }}">
            {{ $label }}
          </a>
        @endforeach
      </div>

      @if ($section === 'overview' && $overviewData)
        @php
          $kpi = $overviewData['kpi'];
          $trend = $overviewData['trend'];
          $composition = $overviewData['composition'];
        @endphp
        
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach ($kpi as $item)
          @php
            $isCurrency = $item['isCurrency'] ?? false;
            $isPercent = $item['isPercent'] ?? false;
            $isRating = $item['isRating'] ?? false;
            $change = $item['change'];
            $changeColor = $change >= 0 ? 'text-emerald-600' : 'text-rose-600';
          @endphp
          <article class="bg-white border border-slate-200 rounded-2xl p-5">
            <p class="text-sm text-slate-500 font-semibold">{{ $item['label'] }}</p>
            <div class="mt-1 flex items-end justify-between gap-3">
              <div>
                @if ($isCurrency)
                  <p class="text-2xl font-black text-slate-900">{{ number_format($item['value'], 0, ',', '.') }} đ</p>
                @elseif ($isPercent)
                  <p class="text-2xl font-black text-slate-900">{{ number_format($item['value'], 1, ',', '.') }}%</p>
                @elseif ($isRating)
                  <p class="text-2xl font-black text-slate-900">{{ number_format($item['value'], 1, ',', '.') }}/5</p>
                @else
                  <p class="text-2xl font-black text-slate-900">{{ number_format($item['value'], 0, ',', '.') }}</p>
                @endif
              </div>
              <p class="text-xs font-bold {{ $changeColor }}">{{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1, ',', '.') }}%</p>
            </div>
            @if ($isRating)
              <p class="mt-2 text-amber-500 text-sm">★★★★★</p>
            @else
              <div class="mt-3 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full bg-orange-500 rounded-full" style="width: {{ max(8, min(100, abs($change))) }}%"></div>
              </div>
            @endif
          </article>
        @endforeach
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <article class="xl:col-span-2 bg-white border border-slate-200 rounded-2xl p-5">
          <div class="flex items-start justify-between gap-4 mb-4">
            <div>
              <h2 class="text-2xl font-black text-slate-900">Doanh thu & Chi phí</h2>
              <p class="text-sm text-slate-500">Thống kê theo 6 tháng gần nhất</p>
            </div>
            <div class="text-xs font-semibold text-slate-500 flex items-center gap-4">
              <span class="inline-flex items-center gap-1.5"><i class="w-2 h-2 rounded-full bg-orange-500"></i>Doanh thu</span>
              <span class="inline-flex items-center gap-1.5"><i class="w-2 h-2 rounded-full bg-slate-300"></i>Chi phí</span>
            </div>
          </div>
          <div class="h-80">
            <canvas id="overviewTrendChart"></canvas>
          </div>
        </article>

        <article class="bg-white border border-slate-200 rounded-2xl p-5">
          <h2 class="text-2xl font-black text-slate-900">Cơ cấu doanh thu</h2>
          <div class="h-64 mt-4">
            <canvas id="overviewCompositionChart"></canvas>
          </div>

          <ul class="mt-3 space-y-2">
            @foreach ($composition['labels'] as $index => $label)
              @php
                $colors = ['bg-orange-500', 'bg-emerald-500', 'bg-blue-500', 'bg-violet-500', 'bg-pink-500', 'bg-rose-500', 'bg-yellow-500', 'bg-teal-500', 'bg-cyan-500', 'bg-sky-500'];
                $colorIndex = $index % count($colors);
                $percent = $composition['percentages'][$index] ?? 0;
              @endphp
              <li class="flex items-center justify-between text-sm">
                <span class="inline-flex items-center gap-2 text-slate-600">
                  <i class="w-2.5 h-2.5 rounded-full {{ $colors[$colorIndex] }}"></i>
                  {{ $label }}
                </span>
                <span class="font-bold text-slate-800">{{ number_format($percent, 1, ',', '.') }}%</span>
              </li>
            @endforeach
          </ul>
        </article>
      </div>

      @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            const trendCanvas = document.getElementById('overviewTrendChart');
            const compositionCanvas = document.getElementById('overviewCompositionChart');

            const trendLabels = @json($trend['labels']);
            const trendRevenue = @json($trend['revenueData']);
            const trendCost = @json($trend['costData']);

            if (trendCanvas) {
              new Chart(trendCanvas, {
                type: 'line',
                data: {
                  labels: trendLabels,
                  datasets: [{
                      label: 'Doanh thu',
                      data: trendRevenue,
                      borderColor: '#f97316',
                      backgroundColor: 'rgba(249, 115, 22, 0.16)',
                      fill: true,
                      borderWidth: 3,
                      tension: 0.35,
                      pointRadius: 3,
                      pointHoverRadius: 5,
                    },
                    {
                      label: 'Chi phí',
                      data: trendCost,
                      borderColor: '#94a3b8',
                      backgroundColor: 'transparent',
                      borderDash: [6, 6],
                      fill: false,
                      borderWidth: 2,
                      tension: 0.35,
                      pointRadius: 2,
                      pointHoverRadius: 4,
                    }
                  ]
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                    legend: {
                      display: false
                    }
                  },
                  scales: {
                    x: {
                      grid: {
                        display: false
                      }
                    },
                    y: {
                      beginAtZero: true,
                      ticks: {
                        callback: (value) => new Intl.NumberFormat('vi-VN').format(value) + ' đ'
                      }
                    }
                  }
                }
              });
            }

            const compositionSeries = @json($composition['series']);

            if (compositionCanvas) {
              new Chart(compositionCanvas, {
                type: 'doughnut',
                data: {
                  labels: @json($composition['labels']),
                  datasets: [{
                    data: compositionSeries,
                    backgroundColor: ['#f97316', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899', '#f43f5e', '#eab308', '#14b8a6', '#06b6d4', '#0ea5e9'],
                    borderWidth: 0,
                  }]
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  cutout: '68%',
                  plugins: {
                    legend: {
                      display: false
                    },
                    tooltip: {
                      callbacks: {
                        label: (context) => {
                          const value = context.raw || 0;
                          return context.label + ': ' + new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                        }
                      }
                    }
                  }
                }
              });
            }
          });
        </script>
      @endpush
    @elseif ($section === 'revenue' && isset($revenueData))
      @include('admin.statistics.partials.revenue')
    @elseif ($section === 'room-performance' && isset($roomPerformanceData))
      @include('admin.statistics.partials.room-performance')
    @elseif ($section === 'customers' && isset($customerStatisticsData))
      @include('admin.statistics.partials.customers')
    @else
      <article class="bg-white border border-slate-200 rounded-2xl p-10 text-center">
        <h2 class="text-2xl font-black text-slate-900">{{ $sectionLabels[$section] }}</h2>
        <p class="mt-2 text-slate-500">Phần này đang được hoàn thiện. Mình sẽ triển khai chi tiết ở bước tiếp theo.</p>
      </article>
    @endif
  </section>
@endsection
