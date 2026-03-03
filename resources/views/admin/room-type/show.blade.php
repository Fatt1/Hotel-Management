@extends("layouts.admin")
@section('content')
<div class="flex-1 flex flex-col">
  <!-- Page Content -->
  <div class="p-8 max-w-7xl mx-auto w-full">
    <!-- Breadcrumb -->
    <a href="{{ route('admin.room-types.index') }}" class="inline-flex items-center gap-1 text-slate-600 hover:text-blue-900 text-sm font-medium mb-6 transition-colors">
      <span class="material-symbols-outlined text-sm">arrow_back</span>
      Trở lại danh sách
    </a>

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">Chi tiết loại phòng: Phòng Deluxe</h2>
        <p class="text-slate-500 text-sm mt-1">Hệ thống quản lý khách sạn Urban Luxe</p>
      </div>
      <div class="flex gap-3">
        <span class="inline-flex items-center justify-center px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full">
          Chế độ xem chi tiết (View Only)
        </span>
        <a href="{{ route('admin.room-types.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-900 px-4 py-2 rounded-lg font-bold text-sm transition-all">
          Quay Lại
        </a>
        <a href="{{ route('admin.room-types.edit', 1) }}" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg font-bold text-sm transition-all">
          Chỉnh sửa
        </a>
      </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-3 gap-6">
      <!-- Left Column -->
      <div class="col-span-2 space-y-6">
        <!-- General Info -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
          <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-blue-900 text-2xl">info</span>
            <h3 class="text-lg font-bold text-slate-900">Thông tin chung</h3>
          </div>
          
          <div class="grid grid-cols-2 gap-6">
            <div>
              <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2">Tên loại phòng</p>
              <p class="text-base font-bold text-slate-900">Phòng Deluxe</p>
            </div>
            <div>
              <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2">Mã loại phòng</p>
              <p class="text-base font-bold text-slate-900">DLX-001</p>
            </div>
            <div>
              <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2">Trạng thái</p>
              <p class="text-base font-bold text-green-600">Đang kinh doanh</p>
            </div>
            <div>
              <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2">Mô tả</p>
              <p class="text-base text-slate-600">Phòng sang trọng với đầy đủ tiện nghi hiện đại</p>
            </div>
          </div>
        </div>

        <!-- Size & Price -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
          <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-blue-900 text-2xl">straighten</span>
            <h3 class="text-lg font-bold text-slate-900">Kích thước & Giá</h3>
          </div>
          
          <div class="grid grid-cols-2 gap-6">
            <!-- Left: Dimensions -->
            <div class="bg-slate-50 rounded-lg p-4">
              <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-4 text-center">Kích thước</p>
              <div class="space-y-3">
                <div>
                  <p class="text-xs text-slate-500 mb-1">Rộng (m)</p>
                  <p class="text-2xl font-bold text-blue-900">5.0</p>
                </div>
                <div>
                  <p class="text-xs text-slate-500 mb-1">Dài (m)</p>
                  <p class="text-2xl font-bold text-blue-900">8.0</p>
                </div>
              </div>
            </div>

            <!-- Right: Prices -->
            <div class="space-y-4">
              <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2">Giá giờ (VNĐ)</p>
                <p class="text-3xl font-bold text-blue-900">200000</p>
              </div>
              <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2">Giá ngày (VNĐ)</p>
                <p class="text-3xl font-bold text-blue-900">1500000</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Capacity -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
          <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-blue-900 text-2xl">group</span>
            <h3 class="text-lg font-bold text-slate-900">Sức chứa</h3>
          </div>
          
          <div class="grid grid-cols-4 gap-4">
            <div class="bg-slate-50 rounded-lg p-4 text-center">
              <span class="material-symbols-outlined text-slate-600 text-3xl block mb-2">person</span>
              <p class="text-xs text-slate-400 uppercase font-bold mb-1">Người lớn</p>
              <p class="text-2xl font-bold text-slate-900">2</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-4 text-center">
              <span class="material-symbols-outlined text-slate-600 text-3xl block mb-2">child_care</span>
              <p class="text-xs text-slate-400 uppercase font-bold mb-1">Trẻ em</p>
              <p class="text-2xl font-bold text-slate-900">1</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-4 text-center">
              <span class="material-symbols-outlined text-slate-600 text-3xl block mb-2">bed</span>
              <p class="text-xs text-slate-400 uppercase font-bold mb-1">Giường đơn</p>
              <p class="text-2xl font-bold text-slate-900">1</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-4 text-center">
              <span class="material-symbols-outlined text-slate-600 text-3xl block mb-2">bed</span>
              <p class="text-xs text-slate-400 uppercase font-bold mb-1">Giường đôi</p>
              <p class="text-2xl font-bold text-slate-900">1</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column -->
      <div class="space-y-6">
        <!-- Media -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="p-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
              <span class="material-symbols-outlined text-blue-900 text-2xl">image</span>
              <h3 class="text-lg font-bold text-slate-900">Hình ảnh</h3>
            </div>
          </div>
          
          <div class="p-4 space-y-3">
            <div class="w-full h-48 bg-slate-100 rounded-lg flex items-center justify-center">
              <span class="material-symbols-outlined text-slate-400 text-4xl">image_not_supported</span>
            </div>
            <p class="text-xs text-slate-500 text-center">Total 1 image available</p>
          </div>
        </div>

        <!-- Amenities -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
          <div class="flex items-center gap-3 mb-4">
            <span class="material-symbols-outlined text-blue-900 text-2xl">star</span>
            <h3 class="text-lg font-bold text-slate-900">Tiện ích</h3>
          </div>
          
          <div class="flex flex-wrap gap-2">
            @php
              $amenities = ['Free Wifi', 'Điều hòa', 'Bãi đỗ', 'Hộp bảo'];
            @endphp
            @foreach($amenities as $amenity)
              <span class="inline-flex items-center gap-2 px-3 py-2 bg-slate-100 rounded-full text-sm text-slate-700 font-medium">
                @if($amenity === 'Free Wifi')
                  <span class="material-symbols-outlined text-sm">wifi</span>
                @elseif($amenity === 'Điều hòa')
                  <span class="material-symbols-outlined text-sm">ac_unit</span>
                @elseif($amenity === 'Bãi đỗ')
                  <span class="material-symbols-outlined text-sm">local_parking</span>
                @else
                  <span class="material-symbols-outlined text-sm">security</span>
                @endif
                {{ $amenity }}
              </span>
            @endforeach
          </div>
        </div>

        <!-- Equipment -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="p-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
              <span class="material-symbols-outlined text-blue-900 text-2xl">kitchen</span>
              <h3 class="text-lg font-bold text-slate-900">Thiết bị (Room Equipment)</h3>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                  <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600 tracking-wider">Tên thiết bị</th>
                  <th class="px-6 py-3 text-right text-xs font-bold uppercase text-slate-600 tracking-wider">SL</th>
                </tr>
              </thead>
              <tbody>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                  <td class="px-6 py-3 text-sm text-slate-900">Điều hòa Daikin 1.5HP</td>
                  <td class="px-6 py-3 text-sm text-slate-900 text-right">1</td>
                </tr>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                  <td class="px-6 py-3 text-sm text-slate-900">Tủ lạnh mini Samsung</td>
                  <td class="px-6 py-3 text-sm text-slate-900 text-right">1</td>
                </tr>
                <tr class="hover:bg-slate-50">
                  <td class="px-6 py-3 text-sm text-slate-900">Smart TV 55 inch</td>
                  <td class="px-6 py-3 text-sm text-slate-900 text-right">1</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Actions -->

      </div>
    </div>
  </div>
</div>
@endsection
