@extends('layouts.admin')
@section('title', 'Cấu hình chung')
@section('content')

<div class="flex-1 flex flex-col">
  <div class="p-8 max-w-7xl mx-auto w-full">

    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-slate-900">Cấu hình chung</h1>
      <p class="text-slate-500 text-sm mt-1">Quản lý cấu hình vận hành và quy định phụ phí của khách sạn.</p>
    </div>

    <!-- 2-panel layout -->
    <div class="flex gap-6 items-start">

      <!-- Left panel -->
      <div class="w-72 flex-shrink-0 bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider px-2 mb-3">Danh sách cấu hình</p>
        <p class="text-xs text-slate-400 px-2 mb-4">Chọn mục để chỉnh sửa chi tiết</p>

        <a href="{{ route('admin.general-config.index', ['section' => 'general']) }}"
           class="flex flex-col px-4 py-3 rounded-xl mb-2 transition-all {{ $section === 'general' ? 'bg-blue-50 border border-blue-200' : 'hover:bg-slate-50 border border-transparent' }}">
          <span class="font-semibold text-sm {{ $section === 'general' ? 'text-blue-900' : 'text-slate-700' }}">Cấu hình vận hành chung</span>
          <span class="text-xs text-slate-400 mt-0.5">Cài đặt giờ giấc, làm tròn thời gian và các quy định chung.</span>
        </a>

        <a href="{{ route('admin.general-config.index', ['section' => 'surcharge']) }}"
           class="flex flex-col px-4 py-3 rounded-xl transition-all {{ $section === 'surcharge' ? 'bg-blue-50 border border-blue-200' : 'hover:bg-slate-50 border border-transparent' }}">
          <span class="font-semibold text-sm {{ $section === 'surcharge' ? 'text-blue-900' : 'text-slate-700' }}">Quy định phụ phí</span>
          <span class="text-xs text-slate-400 mt-0.5">Thiết lập phí check-in sớm và check-out muộn cho toàn hệ thống.</span>
        </a>
      </div>

      <!-- Right panel -->
      <div class="flex-1 bg-white rounded-2xl border border-slate-200 shadow-sm">

        {{-- ===================== SECTION: GENERAL ===================== --}}
        @if($section === 'general')
        <form action="{{ route('admin.general-config.update-general') }}" method="POST">
          @csrf
          <div class="flex items-start justify-between px-8 pt-8 pb-6 border-b border-slate-100">
            <div>
              <h2 class="text-lg font-bold text-slate-900">Chi tiết cấu hình</h2>
              <p class="text-sm text-slate-400 mt-0.5">Cấu hình vận hành chung cho hệ thống khách sạn.</p>
            </div>
            <button type="submit"
              class="flex items-center gap-2 bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-blue-900/20 active:scale-95">
              <span class="material-symbols-outlined text-base">save</span>
              Lưu thay đổi
            </button>
          </div>

          <div class="px-8 py-6 space-y-6">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-3">Thời gian nhận / trả phòng trong ngày</label>
              <div class="flex items-center gap-3">
                <div class="flex flex-col">
                  <input type="time" name="checkin_time"
                    value="{{ old('checkin_time', $settings['checkin_time'] ?? '14:00') }}"
                    class="w-40 px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-900/20 outline-none @error('checkin_time') border-red-400 @else border-slate-200 @enderror"
                  />
                  @error('checkin_time')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                  @enderror
                </div>
                <span class="material-symbols-outlined text-slate-400 mb-auto mt-3">arrow_forward</span>
                <div class="flex flex-col">
                  <input type="time" name="checkout_time"
                    value="{{ old('checkout_time', $settings['checkout_time'] ?? '12:00') }}"
                    class="w-40 px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-900/20 outline-none @error('checkout_time') border-red-400 @else border-slate-200 @enderror"
                  />
                  @error('checkout_time')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                  @enderror
                </div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-3">Số phút làm tròn thành 1 giờ</label>
              <input type="number" name="rounding_time"
                value="{{ old('rounding_time', $settings['rounding_time'] ?? '15') }}"
                class="w-40 px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-900/20 outline-none @error('rounding_time') border-red-400 @else border-slate-200 @enderror"
              />
              @error('rounding_time')
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
              @enderror
            </div>
          </div>
        </form>
        @endif

        {{-- ===================== SECTION: SURCHARGE ===================== --}}
        @if($section === 'surcharge')

        @php
          $checkinRows = ($errors->any() && old('checkin_early') !== null)
            ? collect(old('checkin_early', []))
            : $checkinPolicies->map(fn($p) => ['hour_mark' => (int)$p->hour_mark, 'price' => (int)$p->price]);

          $checkoutRows = ($errors->any() && old('checkout_late') !== null)
            ? collect(old('checkout_late', []))
            : $checkoutPolicies->map(fn($p) => ['hour_mark' => (int)$p->hour_mark, 'price' => (int)$p->price]);
        @endphp

        <form action="{{ route('admin.general-config.update-surcharge') }}" method="POST" id="surchargeForm">
          @csrf
          <div class="flex items-start justify-between px-8 pt-8 pb-6 border-b border-slate-100">
            <div>
              <h2 class="text-lg font-bold text-slate-900">Chi tiết cấu hình</h2>
              <p class="text-sm text-slate-400 mt-0.5">Quản lý các loại phụ phí nhận và trả phòng.</p>
            </div>
            <button type="submit"
              class="flex items-center gap-2 bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-blue-900/20 active:scale-95">
              <span class="material-symbols-outlined text-base">save</span>
              Lưu thay đổi
            </button>
          </div>

          <div class="px-8 py-6 space-y-8">

            <!-- Phí check-in sớm -->
            <div>
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-blue-900">schedule</span>
                  <h3 class="font-bold text-slate-900">Phí check-in sớm</h3>
                </div>
                <button type="button" onclick="addRow('checkin_early', 'checkin-early-list')"
                  class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-blue-900 transition-all">
                  <span class="material-symbols-outlined text-lg">add</span>
                </button>
              </div>
              <div class="space-y-2" id="checkin-early-list">
                @foreach($checkinRows as $i => $row)
                <div class="flex items-start gap-3">
                  <div class="flex flex-col">
                    <input type="number" name="checkin_early[{{ $i }}][hour_mark]"
                      value="{{ $row['hour_mark'] ?? '' }}"
                      placeholder="Giờ"
                      class="w-50 px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-900/20 outline-none {{ $errors->has('checkin_early.'.$i.'.hour_mark') ? 'border-red-400' : 'border-slate-200' }}"
                    />
                    @error('checkin_early.'.$i.'.hour_mark')
                      <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                  </div>
                  <span class="text-sm text-slate-400 font-medium mt-3">VND</span>
                  <div class="flex flex-col flex-1">
                    <input type="number" name="checkin_early[{{ $i }}][price]"
                      value="{{ $row['price'] ?? '' }}"
                      placeholder="Giá"
                      class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-900/20 outline-none text-right {{ $errors->has('checkin_early.'.$i.'.price') ? 'border-red-400' : 'border-slate-200' }}"
                    />
                    @error('checkin_early.'.$i.'.price')
                      <span class="text-red-500 text-xs mt-1 block text-right">{{ $message }}</span>
                    @enderror
                  </div>
                  <button type="button" onclick="removeRow(this)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all mt-1">
                    <span class="material-symbols-outlined text-lg">close</span>
                  </button>
                </div>
                @endforeach
              </div>
            </div>

            <!-- Phí checkout muộn -->
            <div>
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-blue-900">schedule</span>
                  <h3 class="font-bold text-slate-900">Phí checkout muộn</h3>
                </div>
                <button type="button" onclick="addRow('checkout_late', 'checkout-late-list')"
                  class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-blue-900 transition-all">
                  <span class="material-symbols-outlined text-lg">add</span>
                </button>
              </div>
              <div class="space-y-2" id="checkout-late-list">
                @foreach($checkoutRows as $i => $row)
                <div class="flex items-start gap-3">
                  <div class="flex flex-col">
                    <input type="number" name="checkout_late[{{ $i }}][hour_mark]"
                      value="{{ $row['hour_mark'] ?? '' }}"
                      placeholder="Giờ"
                      class="w-50 px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-900/20 outline-none {{ $errors->has('checkout_late.'.$i.'.hour_mark') ? 'border-red-400' : 'border-slate-200' }}"
                    />
                    @error('checkout_late.'.$i.'.hour_mark')
                      <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                  </div>
                  <span class="text-sm text-slate-400 font-medium mt-3">VND</span>
                  <div class="flex flex-col flex-1">
                    <input type="number" name="checkout_late[{{ $i }}][price]"
                      value="{{ $row['price'] ?? '' }}"
                      placeholder="Giá"
                      class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-blue-900/20 outline-none text-right {{ $errors->has('checkout_late.'.$i.'.price') ? 'border-red-400' : 'border-slate-200' }}"
                    />
                    @error('checkout_late.'.$i.'.price')
                      <span class="text-red-500 text-xs mt-1 block text-right">{{ $message }}</span>
                    @enderror
                  </div>
                  <button type="button" onclick="removeRow(this)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all mt-1">
                    <span class="material-symbols-outlined text-lg">close</span>
                  </button>
                </div>
                @endforeach
              </div>
            </div>

          </div>
        </form>
        @endif

      </div>
    </div>
  </div>
</div>

<script>
  window.GeneralConfig = {
    checkinEarlyCount: {{ $checkinPolicies->count() }},
    checkoutLateCount: {{ $checkoutPolicies->count() }},
  };
</script>

@push('scripts')
  @vite(['resources/js/admin/general-config/surcharge.js'])
@endpush

@endsection