@php
  $filters = $revenueData['filters'];
  $kpi = $revenueData['kpi'];
  $trend = $revenueData['trend'];
  $composition = $revenueData['composition'];
@endphp

<!-- Filters -->
<article class="bg-white border border-slate-200 rounded-2xl p-5 mb-6">
  <form action="{{ route('admin.statistics.revenue') }}" method="GET" class="flex flex-wrap items-end gap-4">
    <input type="hidden" name="section" value="revenue">
    
    <div>
      <label class="block text-xs font-bold text-slate-500 mb-1">Từ ngày</label>
      <input type="date" name="start_date" value="{{ $filters['start_date'] }}"
        class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:border-orange-500 outline-none">
    </div>
    
    <div>
      <label class="block text-xs font-bold text-slate-500 mb-1">Đến ngày</label>
      <input type="date" name="end_date" value="{{ $filters['end_date'] }}"
        class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:border-orange-500 outline-none">
    </div>

    <div>
      <label class="block text-xs font-bold text-slate-500 mb-1">Nguồn doanh thu</label>
      <select name="source" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:border-orange-500 outline-none min-w-[150px]">
        <option value="all" {{ $filters['source'] === 'all' ? 'selected' : '' }}>Tất cả</option>
        <option value="room" {{ $filters['source'] === 'room' ? 'selected' : '' }}>Tiền phòng</option>
        <option value="service" {{ $filters['source'] === 'service' ? 'selected' : '' }}>Tiền dịch vụ</option>
        <option value="surcharge" {{ $filters['source'] === 'surcharge' ? 'selected' : '' }}>Phụ thu</option>
      </select>
    </div>

    <div>
      <label class="block text-xs font-bold text-slate-500 mb-1">Trạng thái Hóa đơn</label>
      <select name="status" class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:border-orange-500 outline-none min-w-[150px]">
        <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>Tất cả (Trừ đã hủy)</option>
        <option value="paid" {{ $filters['status'] === 'paid' ? 'selected' : '' }}>Đã thanh toán (Hoàn tất)</option>
        <option value="unpaid" {{ $filters['status'] === 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
      </select>
    </div>

    <button type="submit" class="bg-orange-500 text-white font-bold text-sm px-6 py-2 rounded-lg hover:bg-orange-600 transition-colors">
      Áp dụng
    </button>
  </form>
</article>

<!-- KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
  @foreach ($kpi as $item)
    <article class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center justify-between">
      <div>
        <p class="text-sm text-slate-500 font-semibold">{{ $item['label'] }}</p>
        <p class="text-2xl font-black text-slate-900 mt-1">
          {{ number_format($item['value'], 0, ',', '.') }} đ
        </p>
      </div>
      <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $item['bg_color'] }} {{ $item['color'] }}">
        <span class="material-symbols-outlined !text-3xl">{{ $item['icon'] }}</span>
      </div>
    </article>
  @endforeach
</div>

<!-- Charts -->
<div class="grid grid-cols-1 {{ $filters['source'] === 'all' ? 'xl:grid-cols-3' : 'xl:grid-cols-1' }} gap-4">
  <!-- Trend Chart -->
  <article class="{{ $filters['source'] === 'all' ? 'xl:col-span-2' : '' }} bg-white border border-slate-200 rounded-2xl p-5">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl font-black text-slate-900">Xu hướng doanh thu</h2>
      <div class="flex items-center gap-4 text-sm font-semibold">
        <span class="flex items-center gap-1.5 text-slate-600">
          <i class="w-3 h-3 rounded-full bg-orange-500"></i>
          Năm nay ({{ $trend['year'] }})
        </span>
        <span class="flex items-center gap-1.5 text-slate-600">
          <i class="w-3 h-3 rounded-full bg-slate-300"></i>
          Năm ngoái ({{ $trend['year'] - 1 }})
        </span>
      </div>
    </div>
    <div class="h-72 w-full">
      <canvas id="revenueTrendChart"></canvas>
    </div>
  </article>

  @if($filters['source'] === 'all')
  <!-- Composition Chart -->
  <article class="bg-white border border-slate-200 rounded-2xl p-5 flex flex-col">
    <h2 class="text-2xl font-black text-slate-900 mb-4">Cơ cấu doanh thu</h2>
    <div class="h-64 mb-4 relative flex-1">
      <canvas id="revenueCompositionChart"></canvas>
    </div>
    <ul class="space-y-2 mt-auto">
      @foreach ($composition['labels'] as $index => $label)
        @php
          $colors = ['bg-blue-500', 'bg-emerald-500', 'bg-violet-500'];
          $colorIndex = $index % count($colors);
          
          $total = array_sum($composition['series']);
          $value = $composition['series'][$index] ?? 0;
          $percent = $total > 0 ? round(($value / $total) * 100, 1) : 0;
        @endphp
        <li class="flex items-center justify-between text-sm">
          <span class="inline-flex items-center gap-2 text-slate-600">
            <i class="w-2.5 h-2.5 rounded-full {{ $colors[$colorIndex] }}"></i>
            {{ $label }}
          </span>
          <div class="text-right">
            <span class="font-bold text-slate-800">{{ number_format($value, 0, ',', '.') }} đ</span>
            <span class="text-slate-400 text-xs ml-1">({{ number_format($percent, 1, ',', '.') }}%)</span>
          </div>
        </li>
      @endforeach
    </ul>
  </article>
  @endif
</div>

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const trendCanvas = document.getElementById('revenueTrendChart');
      const compositionCanvas = document.getElementById('revenueCompositionChart');

      if (trendCanvas) {
        new Chart(trendCanvas, {
          type: 'line',
          data: {
            labels: @json($trend['labels']),
            datasets: [
              {
                label: 'Năm nay',
                data: @json($trend['current_year']),
                borderColor: '#f97316', // orange-500
                backgroundColor: 'rgba(249, 115, 22, 0.16)',
                fill: true,
                borderWidth: 3,
                tension: 0.35,
                pointRadius: 3,
                pointHoverRadius: 5,
              },
              {
                label: 'Năm ngoái',
                data: @json($trend['last_year']),
                borderColor: '#cbd5e1', // slate-300
                backgroundColor: 'transparent',
                fill: false,
                borderWidth: 2,
                borderDash: [5, 5],
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
              },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    let label = context.dataset.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.parsed.y !== null) {
                        label += new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ';
                    }
                    return label;
                  }
                }
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
                  callback: (value) => new Intl.NumberFormat('vi-VN', { notation: "compact", compactDisplay: "short" }).format(value)
                }
              }
            }
          }
        });
      }

      @if($filters['source'] === 'all')
      if (compositionCanvas) {
        new Chart(compositionCanvas, {
          type: 'doughnut',
          data: {
            labels: @json($composition['labels']),
            datasets: [{
              data: @json($composition['series']),
              backgroundColor: ['#3b82f6', '#10b981', '#8b5cf6'], // blue, emerald, violet
              borderWidth: 0,
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
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
      @endif
    });
  </script>
@endpush
