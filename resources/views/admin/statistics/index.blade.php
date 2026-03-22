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
        <a href="{{ route('admin.statistics.index', ['section' => $key]) }}"
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
        $recentActivities = $overviewData['recentActivities'];
        $recentActivitiesPreview = $overviewData['recentActivitiesPreview'] ?? $recentActivities;
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
                $colors = ['bg-orange-500', 'bg-emerald-500', 'bg-blue-500'];
                $percent = $composition['percentages'][$index] ?? 0;
              @endphp
              <li class="flex items-center justify-between text-sm">
                <span class="inline-flex items-center gap-2 text-slate-600">
                  <i class="w-2.5 h-2.5 rounded-full {{ $colors[$index] }}"></i>
                  {{ $label }}
                </span>
                <span class="font-bold text-slate-800">{{ number_format($percent, 1, ',', '.') }}%</span>
              </li>
            @endforeach
          </ul>
        </article>
      </div>

      <article class="bg-white border border-slate-200 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-2xl font-black text-slate-900">Hoạt động gần đây</h2>
          <button type="button" id="openRecentActivitiesModal"
            class="text-xs font-bold text-orange-500 uppercase hover:text-orange-600 transition-colors">
            Xem tất cả
          </button>
        </div>

        @if (count($recentActivitiesPreview) > 0)
          <div class="space-y-2">
            @foreach ($recentActivitiesPreview as $activity)
              <div class="border border-slate-100 rounded-xl p-3 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                  <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $activity['color'] }}">
                    <span class="material-symbols-outlined !text-xl">{{ $activity['icon'] }}</span>
                  </div>
                  <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-900 truncate">{{ $activity['title'] }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ $activity['subtitle'] }}</p>
                  </div>
                </div>
                <span class="text-xs font-semibold text-slate-400 whitespace-nowrap">{{ $activity['time'] }}</span>
              </div>
            @endforeach
          </div>
        @else
          <div class="py-10 text-center text-slate-500">
            Chưa có hoạt động dịch vụ gần đây.
          </div>
        @endif
      </article>

      <div id="recentActivitiesModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div id="recentActivitiesBackdrop" class="absolute inset-0 bg-slate-900/45 backdrop-blur-sm"></div>
        <div class="relative h-full w-full flex items-center justify-center p-4">
          <div class="w-full max-w-3xl max-h-[85vh] overflow-hidden bg-white rounded-2xl border border-slate-200 shadow-xl">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
              <h3 class="text-lg font-black text-slate-900">Tất cả hoạt động khách hàng chọn gần đây</h3>
              <button type="button" id="closeRecentActivitiesModal"
                class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 transition-colors">
                <span class="material-symbols-outlined !text-xl text-slate-600">close</span>
              </button>
            </div>
            <div class="p-5 overflow-y-auto max-h-[calc(85vh-72px)]">
              @if (count($recentActivities) > 0)
                <div class="space-y-2">
                  @foreach ($recentActivities as $activity)
                    <div class="border border-slate-100 rounded-xl p-3 flex items-center justify-between gap-4">
                      <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $activity['color'] }}">
                          <span class="material-symbols-outlined !text-xl">{{ $activity['icon'] }}</span>
                        </div>
                        <div class="min-w-0">
                          <p class="text-sm font-bold text-slate-900 truncate">{{ $activity['title'] }}</p>
                          <p class="text-xs text-slate-500 truncate">{{ $activity['subtitle'] }}</p>
                        </div>
                      </div>
                      <span class="text-xs font-semibold text-slate-400 whitespace-nowrap">{{ $activity['time'] }}</span>
                    </div>
                  @endforeach
                </div>
              @else
                <div class="py-10 text-center text-slate-500">
                  Chưa có hoạt động dịch vụ gần đây.
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            const trendCanvas = document.getElementById('overviewTrendChart');
            const compositionCanvas = document.getElementById('overviewCompositionChart');
            const recentActivitiesModal = document.getElementById('recentActivitiesModal');
            const openRecentActivitiesModal = document.getElementById('openRecentActivitiesModal');
            const closeRecentActivitiesModal = document.getElementById('closeRecentActivitiesModal');
            const recentActivitiesBackdrop = document.getElementById('recentActivitiesBackdrop');

            const showRecentActivitiesModal = () => {
              if (!recentActivitiesModal) {
                return;
              }

              recentActivitiesModal.classList.remove('hidden');
              document.body.classList.add('overflow-hidden');
            };

            const hideRecentActivitiesModal = () => {
              if (!recentActivitiesModal) {
                return;
              }

              recentActivitiesModal.classList.add('hidden');
              document.body.classList.remove('overflow-hidden');
            };

            openRecentActivitiesModal?.addEventListener('click', showRecentActivitiesModal);
            closeRecentActivitiesModal?.addEventListener('click', hideRecentActivitiesModal);
            recentActivitiesBackdrop?.addEventListener('click', hideRecentActivitiesModal);

            document.addEventListener('keydown', function(event) {
              if (event.key === 'Escape' && recentActivitiesModal && !recentActivitiesModal.classList.contains('hidden')) {
                hideRecentActivitiesModal();
              }
            });

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
                    backgroundColor: ['#f97316', '#10b981', '#3b82f6'],
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
    @else
      <article class="bg-white border border-slate-200 rounded-2xl p-10 text-center">
        <h2 class="text-2xl font-black text-slate-900">{{ $sectionLabels[$section] }}</h2>
        <p class="mt-2 text-slate-500">Phần này đang được hoàn thiện. Mình sẽ triển khai chi tiết ở bước tiếp theo.</p>
      </article>
    @endif
  </section>
@endsection
