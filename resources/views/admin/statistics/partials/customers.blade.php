@php
  $filters = $customerStatisticsData['filters'];
  $kpi = $customerStatisticsData['kpi'];
  $loyalCustomers = $customerStatisticsData['loyal_customers'];
  $loyalCustomersTotal = $customerStatisticsData['loyal_customers_total'] ?? 0;
@endphp

<article class="bg-white border border-slate-200 rounded-2xl p-5 mb-6">
  <form action="{{ route('admin.statistics.customers') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
    <div>
      <label class="block text-xs font-bold text-slate-500 mb-1">Thời gian</label>
      <input type="date" name="date" value="{{ $filters['date'] }}"
        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:border-orange-500 outline-none">
    </div>

    <div>
      <label class="block text-xs font-bold text-slate-500 mb-1">Loại khách</label>
      <select name="country" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:border-orange-500 outline-none">
        <option value="all" {{ $filters['country'] === 'all' ? 'selected' : '' }}>Tất cả loại khách</option>
        @foreach ($filters['countries'] as $country)
          <option value="{{ $country }}" {{ $filters['country'] === $country ? 'selected' : '' }}>
            {{ $country }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="md:col-span-2 md:justify-self-end">
      <button type="submit" class="bg-blue-700 text-white font-bold text-sm px-6 py-2 rounded-lg hover:bg-blue-800 transition-colors h-[42px]">
        Lọc kết quả
      </button>
    </div>
  </form>
</article>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
  @foreach ($kpi as $item)
    @php
      $isPercent = $item['isPercent'] ?? false;
      $isRating = $item['isRating'] ?? false;
      $change = $item['change'] ?? 0;
      $changeColor = $change >= 0 ? 'text-emerald-600' : 'text-rose-600';
      $changePrefix = $change >= 0 ? '+' : '';
    @endphp
    <article class="bg-white border border-slate-200 rounded-2xl p-5">
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ $item['label'] }}</p>

      <div class="mt-2 flex items-center justify-between gap-3">
        <div>
          @if ($isPercent)
            <p class="text-3xl font-black text-slate-900">{{ number_format($item['value'], 1, ',', '.') }}%</p>
          @elseif ($isRating)
            <p class="text-3xl font-black text-slate-900">{{ number_format($item['value'], 1, ',', '.') }}/5.0</p>
          @else
            <p class="text-3xl font-black text-slate-900">{{ number_format($item['value'], 0, ',', '.') }}</p>
          @endif
        </div>
        <p class="text-xs font-bold {{ $changeColor }}">{{ $changePrefix }}{{ number_format($change, 1, ',', '.') }}%</p>
      </div>

      <p class="mt-1 text-xs {{ $changeColor }}">{{ $changePrefix }}{{ number_format($change, 1, ',', '.') }}% so với kỳ trước</p>
      <p class="mt-1 text-xs text-slate-400">{{ $item['meta'] ?? '' }}</p>
    </article>
  @endforeach
</div>

<article class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
    <div>
      <h2 class="text-2xl font-black text-slate-900">Khách hàng thân thiết (Loyal Customers)</h2>
      <p class="text-sm text-slate-500">
        Danh sách khách có lượt quay lại và chi tiêu cao trong kỳ
        @if ($loyalCustomersTotal > 0)
          ({{ number_format($loyalCustomersTotal, 0, ',', '.') }} khách)
        @endif
      </p>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wide">
        <tr>
          <th class="text-left px-5 py-3">Khách hàng</th>
          <th class="text-left px-5 py-3">Lượt đến</th>
          <th class="text-left px-5 py-3">Tổng chi tiêu (VND)</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($loyalCustomers as $customer)
          <tr class="border-t border-slate-100">
            <td class="px-5 py-3">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-600 to-cyan-500 text-white text-xs font-black flex items-center justify-center">
                  {{ $customer['avatar_seed'] }}
                </div>
                <div>
                  <p class="font-bold text-slate-800">{{ $customer['name'] }}</p>
                  <p class="text-xs text-slate-400">{{ $customer['email'] }}</p>
                </div>
              </div>
            </td>
            <td class="px-5 py-3 text-slate-700 font-semibold">{{ number_format($customer['visits_count'], 0, ',', '.') }} lượt</td>
            <td class="px-5 py-3 text-slate-700 font-semibold">{{ number_format($customer['total_spent'], 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="px-5 py-8 text-center text-slate-500">Không có dữ liệu khách hàng trong kỳ lọc hiện tại.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if ($loyalCustomers instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="px-5 py-4 border-t border-slate-200">
      {{ $loyalCustomers->links('vendor.pagination.custom') }}
    </div>
  @endif
</article>
