@extends('layouts.admin')
@section('title', 'Quản lý phiếu sửa chữa')
@section('content')

<div class="p-8 space-y-6">
  <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
      <h1 class="text-3xl font-black text-slate-900 tracking-tight">Quản lý phiếu sửa chữa</h1>
      <p class="text-slate-500 font-medium">Hệ thống quản trị khách sạn Urban Luxe - Quản lý phiếu sửa chữa thiết bị.</p>
    </div>
    @can('maintenance_tickets.create')
    <a href="{{ route('admin.maintenance-tickets.create') }}"
      class="cursor-pointer flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl text-sm font-bold shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all hover:-translate-y-0.5">
      <span class="material-symbols-outlined">add_circle</span>
      Tạo phiếu sửa chữa mới
    </a>
    @endcan
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100">
      <form action="{{ route('admin.maintenance-tickets.index') }}" method="GET" class="flex flex-col xl:flex-row gap-3 w-full">
        <div class="relative flex-1 xl:w-96">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
            <span class="material-symbols-outlined !text-lg">search</span>
          </span>
          <input
            name="search"
            value="{{ request('search') }}"
            class="block w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none"
            placeholder="Tìm theo phòng, thiết bị, mô tả sự cố..." type="text" />
        </div>

        <div class="relative xl:w-56">
          <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
            <span class="material-symbols-outlined !text-lg">tune</span>
          </span>
          <select
            name="status"
            onchange="this.form.submit()"
            class="block w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none appearance-none cursor-pointer"
          >
            <option value="">Trạng thái (Tất cả)</option>
            @foreach (\App\Enums\MaintenanceTicketStatus::options() as $value => $label)
              <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="bg-slate-50/50">
            <th class="table-header">MÃ PHIẾU</th>
            <th class="table-header">PHÒNG</th>
            <th class="table-header">THIẾT BỊ</th>
            <th class="table-header">NGÀY BÁO CÁO</th>
            <th class="table-header">CHI PHÍ SỬA (VND)</th>
            <th class="table-header text-center">TRẠNG THÁI</th>
            <th class="table-header text-right">THAO TÁC</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse ($tickets as $ticket)
            <tr class="hover:bg-slate-50/50 transition-colors group">
              <td class="table-cell">
                <span class="font-bold text-primary">{{ $ticket->ticket_code }}</span>
              </td>
              <td class="table-cell font-semibold text-slate-900">{{ $ticket->room?->name ?? '-' }}</td>
              <td class="table-cell text-slate-700">{{ $ticket->equipment?->name ?? '-' }}</td>
              <td class="table-cell text-slate-700">{{ optional($ticket->reported_date)->format('d/m/Y') }}</td>
              <td class="table-cell font-semibold text-slate-900">{{ number_format((float) $ticket->repair_cost, 0, ',', '.') }}đ</td>
              <td class="table-cell text-center">
                @php
                  $statusStyle = match($ticket->status) {
                    'pending' => 'bg-amber-100 text-amber-700',
                    'in_progress' => 'bg-blue-100 text-blue-700',
                    'completed' => 'bg-green-100 text-green-700',
                    default => 'bg-slate-200 text-slate-600',
                  };
                @endphp
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $statusStyle }}">
                  {{ \App\Enums\MaintenanceTicketStatus::labelOf($ticket->status) }}
                </span>
              </td>
              <td class="table-cell text-right">
                <div class="flex items-center justify-end gap-2">
                  @can('maintenance_tickets.view')
                    <a href="{{ route('admin.maintenance-tickets.show', $ticket->id) }}" class="text-blue-500 hover:text-blue-700 transition-colors" title="Xem">
                      <span class="material-symbols-outlined text-lg">visibility</span>
                    </a>
                  @endcan
                  @can('maintenance_tickets.edit')
                    <a href="{{ route('admin.maintenance-tickets.edit', $ticket->id) }}" class="text-amber-500 hover:text-amber-700 transition-colors" title="Sửa">
                      <span class="material-symbols-outlined text-lg">edit</span>
                    </a>
                  @endcan
                  @can('maintenance_tickets.delete')
                    <button data-ticket-id="{{ $ticket->id }}" data-ticket-code="{{ $ticket->ticket_code }}" class="delete-maintenance-ticket-btn text-rose-500 hover:text-rose-700 transition-colors" title="Xóa">
                      <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                  @endcan
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="table-cell text-center text-slate-500 py-10">
                Không có dữ liệu phiếu sửa chữa
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
      <span class="text-xs font-medium text-slate-500">Hiển thị {{ $tickets->lastItem() ?? 0 }} trên {{ $tickets->total() }} phiếu sửa chữa</span>
      <div class="mt-5">
        {{ $tickets->withQueryString()->links('vendor.pagination.custom') }}
      </div>
    </div>
  </div>
</div>

<div id="deleteMaintenanceTicketModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
    <div class="flex justify-center pt-8">
      <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
        <span class="material-symbols-outlined text-4xl text-red-600">warning</span>
      </div>
    </div>

    <div class="p-8 text-center">
      <h2 class="text-xl font-bold text-slate-900 mb-3">Xác nhận xóa phiếu sửa chữa</h2>
      <p class="text-red-600 font-semibold mb-2">Bạn có chắc chắn muốn xóa phiếu sửa chữa này không?</p>
      <p class="text-sm text-slate-600 mb-6">
        Dữ liệu phiếu sửa chữa sẽ bị xóa và hành động này không thể hoàn tác.
      </p>

      <div class="bg-slate-50 rounded-lg p-4 mb-6 text-left space-y-2">
        <div class="text-sm text-slate-600">
          <span class="font-semibold">Mã phiếu: </span>
          <span id="deleteTicketCode" class="text-slate-900 font-semibold">-</span>
        </div>
      </div>

      <div class="flex gap-3">
        <button id="cancelDeleteMaintenanceTicketBtn" class="flex-1 px-4 py-2.5 border border-slate-300 rounded-lg font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
          Quay lại
        </button>
        <button id="confirmDeleteMaintenanceTicketBtn" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors">
          Xác nhận xóa
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
  @vite(['resources/js/admin/maintenance-tickets/index.js'])
@endpush

@endsection
