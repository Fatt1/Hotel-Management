@extends("layouts.admin")
@section('content')
<div class="flex-1 flex flex-col bg-slate-50">
  <!-- Page Content -->
  <div class="p-6 max-w-[1920px] mx-auto w-full">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-4">
        <div class="bg-white rounded-xl p-3 shadow-sm border border-slate-200">
          <span class="material-symbols-outlined text-2xl text-blue-900">hotel</span>
        </div>
        <div>
          <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Sơ đồ phòng</h2>
          <p class="text-slate-500 text-sm font-medium">Quản lý trạng thái phòng theo thời gian thực</p>
        </div>
      </div>
      
      <!-- Date Filter -->
      <div class="flex items-center gap-3">
        <input min="{{ now()->format('Y-m-d') }}" type="date" id="date-filter" value="{{ $viewModel->filterDate }}" 
          class="px-4 py-2.5 border border-slate-300 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button onclick="applyDateFilter()"
          class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-blue-900/20">
          <span class="material-symbols-outlined text-base">search</span>
        </button>
      </div>
    </div>

    <!-- Status Filters -->
    <div class="flex items-center gap-3 mb-6 overflow-x-auto pb-2">
      <a href="{{ $viewModel->getStatusFilterUrl('all') }}" 
        class="status-filter-btn {{ $viewModel->isStatusActive('all') ? 'active ring-2 ring-offset-2 ring-slate-300' : '' }} flex items-center gap-2 px-4 py-2 bg-white border-2 border-slate-300 rounded-full font-bold text-sm transition-all hover:scale-105 whitespace-nowrap">
        <span class="material-symbols-outlined text-base">grid_view</span>
        <span>Tất cả</span>
        <span class="bg-slate-100 px-2 py-0.5 rounded-full text-xs">{{ $viewModel->getTotalRooms() }}</span>
      </a>
      
      <a href="{{ $viewModel->getStatusFilterUrl('available') }}" 
        class="status-filter-btn {{ $viewModel->isStatusActive('available') ? 'active ring-2 ring-offset-2 ring-green-500' : '' }} flex items-center gap-2 px-4 py-2 bg-white border-2 border-green-500 rounded-full text-green-700 font-bold text-sm transition-all hover:scale-105 whitespace-nowrap">
        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
        <span>Trống</span>
        <span class="bg-green-50 px-2 py-0.5 rounded-full text-xs">{{ $viewModel->statusCounts['available'] }}</span>
      </a>
      
      <a href="{{ $viewModel->getStatusFilterUrl('reserved') }}" 
        class="status-filter-btn {{ $viewModel->isStatusActive('reserved') ? 'active ring-2 ring-offset-2 ring-blue-500' : '' }} flex items-center gap-2 px-4 py-2 bg-white border-2 border-blue-500 rounded-full text-blue-700 font-bold text-sm transition-all hover:scale-105 whitespace-nowrap">
        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
        <span>Đã đặt</span>
        <span class="bg-blue-50 px-2 py-0.5 rounded-full text-xs">{{ $viewModel->statusCounts['reserved'] }}</span>
      </a>
      
      <a href="{{ $viewModel->getStatusFilterUrl('arriving') }}" 
        class="status-filter-btn {{ $viewModel->isStatusActive('arriving') ? 'active ring-2 ring-offset-2 ring-purple-500' : '' }} flex items-center gap-2 px-4 py-2 bg-white border-2 border-purple-500 rounded-full text-purple-700 font-bold text-sm transition-all hover:scale-105 whitespace-nowrap">
        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
        <span>Sắp đến</span>
        <span class="bg-purple-50 px-2 py-0.5 rounded-full text-xs">{{ $viewModel->statusCounts['arriving'] }}</span>
      </a>
      
      <a href="{{ $viewModel->getStatusFilterUrl('occupied') }}" 
        class="status-filter-btn {{ $viewModel->isStatusActive('occupied') ? 'active ring-2 ring-offset-2 ring-red-500' : '' }} flex items-center gap-2 px-4 py-2 bg-white border-2 border-red-500 rounded-full text-red-700 font-bold text-sm transition-all hover:scale-105 whitespace-nowrap">
        <div class="w-2 h-2 bg-red-500 rounded-full"></div>
        <span>Có khách</span>
        <span class="bg-red-50 px-2 py-0.5 rounded-full text-xs">{{ $viewModel->statusCounts['occupied'] }}</span>
      </a>
      
      <a href="{{ $viewModel->getStatusFilterUrl('late_checkout') }}" 
        class="status-filter-btn {{ $viewModel->isStatusActive('late_checkout') ? 'active ring-2 ring-offset-2 ring-orange-500' : '' }} flex items-center gap-2 px-4 py-2 bg-white border-2 border-orange-500 rounded-full text-orange-700 font-bold text-sm transition-all hover:scale-105 whitespace-nowrap">
        <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
        <span>Chưa đi</span>
        <span class="bg-orange-50 px-2 py-0.5 rounded-full text-xs">{{ $viewModel->statusCounts['late_checkout'] }}</span>
      </a>
      
      <a href="{{ $viewModel->getStatusFilterUrl('dirty') }}" 
        class="status-filter-btn {{ $viewModel->isStatusActive('dirty') ? 'active ring-2 ring-offset-2 ring-gray-500' : '' }} flex items-center gap-2 px-4 py-2 bg-white border-2 border-gray-500 rounded-full text-gray-700 font-bold text-sm transition-all hover:scale-105 whitespace-nowrap">
        <div class="w-2 h-2 bg-gray-500 rounded-full"></div>
        <span>Bẩn</span>
        <span class="bg-gray-50 px-2 py-0.5 rounded-full text-xs">{{ $viewModel->statusCounts['dirty'] }}</span>
      </a>
    </div>

    <!-- Layout Grouping Selector -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-slate-600">view_module</span>
          <span class="text-sm font-bold text-slate-700">Sắp xếp theo:</span>
        </div>
        <div class="flex items-center gap-2">
          <a href="{{ $viewModel->getGroupByUrl('type') }}" 
            class="px-4 py-2 rounded-lg text-sm font-bold transition-all {{ $viewModel->isGroupByActive('type') ? 'bg-blue-900 text-white shadow-lg' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            Loại
          </a>
          <a href="{{ $viewModel->getGroupByUrl('floor') }}" 
            class="px-4 py-2 rounded-lg text-sm font-bold transition-all {{ $viewModel->isGroupByActive('floor') ? 'bg-blue-900 text-white shadow-lg' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            Tầng
          </a>
          <a href="{{ $viewModel->getGroupByUrl('room') }}" 
            class="px-4 py-2 rounded-lg text-sm font-bold transition-all {{ $viewModel->isGroupByActive('room') ? 'bg-blue-900 text-white shadow-lg' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            Phòng
          </a>
        </div>
      </div>
    </div>

    <!-- Room Grid -->
    @foreach($viewModel->getFilteredRooms() as $groupName => $rooms)
    <div class="mb-8" data-group="{{ $groupName }}">
      <div class="flex items-center gap-3 mb-4">
        <div class="bg-slate-900 text-white text-xs font-black px-3 py-1.5 rounded-lg uppercase tracking-widest">
          {{ $groupName }}
        </div>
        <div class="text-sm font-medium text-slate-500">
          {{ $rooms->count() }} phòng
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4">
        @foreach($rooms as $room)
          @php
            $statusColors = [
              'available' => ['border' => 'border-green-500', 'bg' => 'bg-green-50', 'text' => 'text-green-700', 'dot' => 'bg-green-500'],
              'reserved' => ['border' => 'border-blue-500', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500'],
              'arriving' => ['border' => 'border-purple-500', 'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'dot' => 'bg-purple-500'],
              'occupied' => ['border' => 'border-red-500', 'bg' => 'bg-red-50', 'text' => 'text-red-700', 'dot' => 'bg-red-500'],
              'late_checkout' => ['border' => 'border-orange-500', 'bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'dot' => 'bg-orange-500'],
              'dirty' => ['border' => 'border-gray-500', 'bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'dot' => 'bg-gray-500'],
            ];
            $colors = $statusColors[$room->status->value] ?? $statusColors['available'];
          @endphp

          <div class="room-card {{ $colors['border'] }} border-2 rounded-xl {{ $colors['bg'] }} p-4 transition-all hover:shadow-lg cursor-pointer relative"
               data-room-id="{{ $room->roomId }}"
               data-room-name="{{ $room->roomName }}"
               data-room-status="{{ $room->status->value }}"
               data-booking-id="{{ $room->bookingId ?? '' }}"
               data-customer-name="{{ $room->customerName ?? '' }}"
               onclick="showRoomMenu(event, this)">
            
            <!-- Room Header -->
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center gap-2">
                <span class="text-lg font-black {{ $colors['text'] }} uppercase">{{ $room->roomName }}</span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $room->roomTypeCode }}</span>
              </div>
              @if($room->status->value === 'available')
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                  <span class="material-symbols-outlined text-white text-xl">check</span>
                </div>
              @else
                <div class="w-2 h-2 {{ $colors['dot'] }} rounded-full animate-pulse"></div>
              @endif
            </div>

            <!-- Status Label -->
            @if($room->status->value === 'available')
              <div class="text-center py-4">
                <span class="text-sm font-black {{ $colors['text'] }} uppercase tracking-wide">{{ $room->status->getLabel() }}</span>
              </div>
            @else
              <!-- Booking Info -->
              <div class="space-y-2">
                @if($room->customerName)
                  <div class="font-bold text-sm {{ $colors['text'] }} truncate">
                    {{ $room->customerName }}
                  </div>
                @endif

                @if($room->checkinDate && $room->checkoutDate)
                  <div class="text-xs text-slate-600 space-y-1">
                    @if(in_array($room->status->value, ['occupied', 'late_checkout']))
                      <!-- Show full datetime for occupied rooms -->
                      <div class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">login</span>
                        <span class="font-medium">{{ $room->checkinDate->format('d/m/Y H:i:s') }}</span>
                      </div>
                      <div class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs {{ $room->status->value === 'late_checkout' ? 'text-orange-600' : '' }}">logout</span>
                        <span class="font-medium {{ $room->status->value === 'late_checkout' ? 'text-orange-600 font-bold' : '' }}">
                          {{ $room->checkoutDate->format('d/m/Y H:i:s') }}
                        </span>
                      </div>
                    @else
                      <!-- Show full datetime for reserved/arriving -->
                      <div class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">login</span>
                        <span class="font-medium">{{ $room->checkinDate->format('d/m/Y H:i:s') }}</span>
                      </div>
                      <div class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">logout</span>
                        <span class="font-medium">{{ $room->checkoutDate->format('d/m/Y H:i:s') }}</span>
                      </div>
                    @endif
                  </div>
                @endif

                <!-- Status Badge -->
                <div class="pt-2">
                  <div class="inline-flex items-center gap-1 px-2 py-1 {{ $colors['bg'] }} rounded-lg">
                    <div class="w-1.5 h-1.5 {{ $colors['dot'] }} rounded-full"></div>
                    <span class="text-[10px] font-black {{ $colors['text'] }} uppercase tracking-widest">
                      {{ $room->status->getLabel() }}
                    </span>
                  </div>
                </div>

                <!-- Secondary Booking (if exists) -->
                @if($room->hasMultipleBookings && $room->secondaryCustomerName)
                  <div class="border-t border-slate-300 pt-2 mt-2">
                    <div class="font-bold text-sm text-blue-700 truncate">
                      {{ $room->secondaryCustomerName }}
                    </div>
                    @if($room->secondaryCheckinDate && $room->secondaryCheckoutDate)
                      <div class="text-xs text-slate-600 space-y-1 mt-1">
                        <div class="flex items-center gap-1">
                          <span class="material-symbols-outlined text-xs">login</span>
                          <span class="font-medium">{{ $room->secondaryCheckinDate->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                          <span class="material-symbols-outlined text-xs">logout</span>
                          <span class="font-medium">{{ $room->secondaryCheckoutDate->format('d/m/Y H:i:s') }}</span>
                        </div>
                      </div>
                    @endif
                    <div class="pt-1">
                      <div class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 rounded-lg">
                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                        <span class="text-[10px] font-black text-blue-700 uppercase tracking-widest">
                          ĐÃ ĐẶT
                        </span>
                      </div>
                    </div>
                  </div>
                @endif
              </div>
            @endif
          </div>
        @endforeach
      </div>
    </div>
    @endforeach

    @if($viewModel->getFilteredRooms()->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
      <span class="material-symbols-outlined text-slate-300 text-6xl block mb-4">meeting_room</span>
      <h3 class="text-lg font-bold text-slate-600 mb-2">Không có phòng nào</h3>
      <p class="text-slate-500 text-sm">{{ $viewModel->filterStatus ? 'Không có phòng nào với trạng thái được chọn' : 'Hệ thống chưa có dữ liệu phòng' }}</p>
    </div>
    @endif
  </div>
</div>

<!-- Room Action Menu Popup -->
<div id="room-menu" class="hidden fixed bg-white rounded-xl shadow-2xl border border-slate-200 z-50 min-w-[200px]" style="display: none;">
  <div class="py-2">
    <!-- Header -->
    <div id="menu-header" class="px-4 py-2 border-b border-slate-200">
      <div class="flex items-center gap-2">
        <div id="menu-room-badge" class="px-2 py-1 rounded-lg text-xs font-black"></div>
        <div id="menu-room-name" class="font-bold text-sm text-slate-700"></div>
      </div>
      <div id="menu-customer-name" class="text-xs text-slate-500 mt-1"></div>
    </div>

    <!-- Menu Items -->
    <div id="menu-items" class="py-1">
      <!-- Check-in (for reserved/arriving) -->
      <a href="#" id="menu-checkin" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition-colors text-slate-700" style="display: none;">
        <span class="material-symbols-outlined text-lg text-blue-600">login</span>
        <span class="font-medium text-sm">Check-in</span>
      </a>

      <!-- Checkout (for occupied/late_checkout) -->
      <a href="#" id="menu-checkout" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition-colors text-slate-700" style="display: none;">
        <span class="material-symbols-outlined text-lg text-orange-600">logout</span>
        <span class="font-medium text-sm">Checkout</span>
      </a>

      <!-- Đặt phòng (for available) -->
      <a href="#" id="menu-book" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition-colors text-slate-700" style="display: none;">
        <span class="material-symbols-outlined text-lg text-green-600">event_available</span>
        <span class="font-medium text-sm">Đặt phòng</span>
      </a>

      <!-- Làm sạch (for dirty) -->
      <a href="#" id="menu-clean" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition-colors text-slate-700" style="display: none;">
        <span class="material-symbols-outlined text-lg text-purple-600">cleaning_services</span>
        <span class="font-medium text-sm">Làm sạch</span>
      </a>

      <!-- Chi tiết (for all except available and dirty) -->
      <a href="#" id="menu-details" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition-colors text-slate-700" style="display: none;">
        <span class="material-symbols-outlined text-lg text-slate-600">info</span>
        <span class="font-medium text-sm">Chi tiết</span>
      </a>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  @vite(['resources/js/admin/layout-room/index.js'])
@endpush
