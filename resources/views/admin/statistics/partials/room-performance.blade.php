@php
  $filters = $roomPerformanceData['filters'];
  $kpi = $roomPerformanceData['kpi'];
  $roomStatus = $roomPerformanceData['room_status'];
  $topRoomTypes = $roomPerformanceData['top_room_types'];
  $roomTable = $roomPerformanceData['room_table'];
@endphp

<article class="bg-white border border-slate-200 rounded-2xl p-5 mb-6">
  <form action="{{ route('admin.statistics.room-performance') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
    <div>
      <label class="block text-xs font-bold text-slate-500 mb-1">Khoảng thời gian</label>
      <input type="date" name="date" value="{{ $filters['date'] }}"
        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:border-orange-500 outline-none">
    </div>

    <div>
      <label class="block text-xs font-bold text-slate-500 mb-1">Loại phòng</label>
      <select name="room_type_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:border-orange-500 outline-none">
        <option value="all" {{ $filters['room_type_id'] === 'all' ? 'selected' : '' }}>Tất cả loại phòng</option>
        @foreach ($filters['room_types'] as $roomType)
          <option value="{{ $roomType['id'] }}" {{ $filters['room_type_id'] === $roomType['id'] ? 'selected' : '' }}>
            {{ $roomType['label'] }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-xs font-bold text-slate-500 mb-1">Trạng thái đặt phòng</label>
      <select name="booking_status" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:border-orange-500 outline-none">
        <option value="all" {{ $filters['booking_status'] === 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
        @foreach ($filters['booking_statuses'] as $status)
          <option value="{{ $status }}" {{ $filters['booking_status'] === $status ? 'selected' : '' }}>
            {{ $status }}
          </option>
        @endforeach
      </select>
    </div>

    <button type="submit" class="bg-blue-700 text-white font-bold text-sm px-6 py-2 rounded-lg hover:bg-blue-800 transition-colors h-[42px]">
      Lọc dữ liệu
    </button>
  </form>
</article>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
  @foreach ($kpi as $item)
    @php
      $isCurrency = $item['isCurrency'] ?? false;
      $isPercent = $item['isPercent'] ?? false;
      $change = $item['change'] ?? 0;
      $changeColor = $change >= 0 ? 'text-emerald-600' : 'text-rose-600';
    @endphp
    <article class="bg-white border border-slate-200 rounded-2xl p-5">
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ $item['label'] }}</p>
      <div class="mt-1 flex items-center justify-between gap-3">
        <div>
          @if ($isCurrency)
            <p class="text-3xl font-black text-slate-900">{{ number_format($item['value'] / 1000000, 1, ',', '.') }}M</p>
            <p class="text-xs text-slate-400">VND</p>
          @elseif ($isPercent)
            <p class="text-3xl font-black text-slate-900">{{ number_format($item['value'], 1, ',', '.') }}%</p>
          @else
            <p class="text-3xl font-black text-slate-900">{{ number_format($item['value'], 0, ',', '.') }}</p>
          @endif
        </div>
        <p class="text-xs font-bold {{ $changeColor }}">{{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1, ',', '.') }}%</p>
      </div>
      <p class="mt-2 text-xs text-slate-400">{{ $item['meta'] ?? '' }}</p>
      @if ($isPercent)
        <div class="mt-3 h-1.5 rounded-full bg-slate-100 overflow-hidden">
          <div class="h-full rounded-full bg-blue-700" style="width: {{ max(5, min(100, $item['value'])) }}%"></div>
        </div>
      @endif
    </article>
  @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-4">
  <article class="bg-white border border-slate-200 rounded-2xl p-5">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl font-black text-slate-900">Trạng thái phòng hiện tại</h2>
      <span class="text-sm font-semibold text-blue-700">Xem chi tiết</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
      <div class="h-56 relative">
        <canvas id="roomCurrentStatusChart"></canvas>
        <div class="absolute inset-0 flex items-center justify-center flex-col pointer-events-none">
          <p class="text-4xl font-black text-slate-800">{{ number_format($roomStatus['total_rooms'], 0, ',', '.') }}</p>
          <p class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Tổng phòng</p>
        </div>
      </div>

      <div class="space-y-3">
        <p class="flex items-center justify-between text-sm">
          <span class="inline-flex items-center gap-2 text-slate-600">
            <i class="w-2.5 h-2.5 rounded-full bg-blue-700"></i>
            Đang ở:
          </span>
          <span class="font-bold text-slate-800">{{ number_format($roomStatus['occupied'], 0, ',', '.') }}</span>
        </p>
        <p class="flex items-center justify-between text-sm">
          <span class="inline-flex items-center gap-2 text-slate-600">
            <i class="w-2.5 h-2.5 rounded-full bg-slate-300"></i>
            Phòng trống:
          </span>
          <span class="font-bold text-slate-800">{{ number_format($roomStatus['available'], 0, ',', '.') }}</span>
        </p>
        <p class="flex items-center justify-between text-sm">
          <span class="inline-flex items-center gap-2 text-slate-600">
            <i class="w-2.5 h-2.5 rounded-full bg-rose-400"></i>
            Bảo trì:
          </span>
          <span class="font-bold text-slate-800">{{ number_format($roomStatus['maintenance'], 0, ',', '.') }}</span>
        </p>
      </div>
    </div>
  </article>

  <article class="bg-white border border-slate-200 rounded-2xl p-5">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl font-black text-slate-900">Loại phòng được đặt nhiều nhất</h2>
      <span class="text-xs font-semibold text-slate-500 uppercase">Tháng này</span>
    </div>

    @forelse ($topRoomTypes as $item)
      <div class="mb-5">
        <div class="flex items-end justify-between gap-3 mb-1">
          <p class="text-xs font-bold uppercase text-slate-500 tracking-wide">{{ $item['name'] }}</p>
          <p class="text-sm font-black text-slate-800">{{ number_format($item['count'], 0, ',', '.') }} lượt</p>
        </div>
        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
          <div class="h-full rounded-full bg-blue-700" style="width: {{ $item['progress_percent'] }}%"></div>
        </div>
      </div>
    @empty
      <p class="text-sm text-slate-500">Không có dữ liệu đặt phòng theo bộ lọc hiện tại.</p>
    @endforelse
  </article>
</div>

<article class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
    <h2 class="text-2xl font-black text-slate-900">Chi tiết trạng thái phòng</h2>
    <div class="text-xs text-slate-400 font-semibold">Dữ liệu theo tháng đã chọn</div>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wide">
        <tr>
          <th class="text-left px-5 py-3">Mã phòng</th>
          <th class="text-left px-5 py-3">Loại phòng</th>
          <th class="text-left px-5 py-3">Tầng</th>
          <th class="text-left px-5 py-3">Tỉ lệ lấp đầy (tháng)</th>
          <th class="text-left px-5 py-3">Trạng thái hiện tại</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($roomTable as $room)
          <tr class="border-t border-slate-100">
            <td class="px-5 py-3 font-black text-slate-800">{{ $room['room_name'] }}</td>
            <td class="px-5 py-3 text-slate-600">{{ $room['room_type'] }}</td>
            <td class="px-5 py-3 text-slate-600">{{ $room['floor_name'] }}</td>
            <td class="px-5 py-3">
              <p class="text-xs font-bold text-slate-700 mb-1">{{ number_format($room['fill_rate'], 1, ',', '.') }}%</p>
              <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden">
                <div class="h-full rounded-full bg-emerald-500" style="width: {{ $room['fill_rate_width'] }}%"></div>
              </div>
            </td>
            <td class="px-5 py-3">
              <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-bold uppercase {{ $room['status_badge_class'] }}">
                {{ $room['current_status'] }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-5 py-8 text-center text-slate-500">Không có dữ liệu phòng theo bộ lọc hiện tại.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</article>

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const roomStatusCanvas = document.getElementById('roomCurrentStatusChart');

      if (!roomStatusCanvas) {
        return;
      }

      new Chart(roomStatusCanvas, {
        type: 'doughnut',
        data: {
          labels: ['Đang ở', 'Phòng trống', 'Bảo trì'],
          datasets: [{
            data: [
              @json($roomStatus['occupied']),
              @json($roomStatus['available']),
              @json($roomStatus['maintenance'])
            ],
            backgroundColor: ['#1d4ed8', '#cbd5e1', '#fb7185'],
            borderWidth: 0,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '74%',
          plugins: {
            legend: {
              display: false
            }
          }
        }
      });
    });
  </script>
@endpush
