@extends('layouts.admin')
@section('title', 'Chi tiết phiếu sửa chữa')
@section('content')

<div class="p-8 print:p-0">
  <div class="mx-auto max-w-5xl bg-white rounded-[28px] border border-slate-200 shadow-xl shadow-slate-200/70 overflow-hidden print:shadow-none print:border-none print:rounded-none">
    <div class="px-8 py-6 border-b border-slate-100 flex items-start justify-between gap-4">
      <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-primary">
          <span class="material-symbols-outlined">description</span>
        </div>
        <div>
          <h1 class="text-3xl font-black tracking-tight text-slate-900">Chi tiết phiếu sửa chữa</h1>
          <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mt-2">Maintenance Ticket #{{ $ticket->ticket_code }}</p>
        </div>
      </div>
      <a href="{{ $returnUrl ?? route('admin.maintenance-tickets.index') }}" class="print:hidden text-slate-400 hover:text-slate-600 transition-colors" aria-label="Đóng">
        <span class="material-symbols-outlined">close</span>
      </a>
    </div>

    @php
      $statusStyle = match($ticket->status) {
        'pending' => 'bg-amber-100 text-amber-700',
        'in_progress' => 'bg-blue-100 text-blue-700',
        'completed' => 'bg-green-100 text-green-700',
        default => 'bg-slate-200 text-slate-600',
      };
    @endphp

    <div class="px-8 py-6 space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
          <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Mã phiếu</p>
          <p class="font-extrabold text-primary">#{{ $ticket->ticket_code }}</p>
        </div>
        <div>
          <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Số phòng</p>
          <p class="font-semibold text-slate-900">{{ $ticket->room?->name ?? '-' }}</p>
        </div>
        <div>
          <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Trạng thái</p>
          <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $statusStyle }}">
            {{ \App\Enums\MaintenanceTicketStatus::labelOf($ticket->status) }}
          </span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-slate-100 pt-6">
        <div>
          <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Tên thiết bị</p>
          <div class="flex items-center gap-2 text-slate-900 font-semibold">
            <span class="material-symbols-outlined text-slate-400 !text-lg">tv</span>
            <span>{{ $ticket->equipment?->name ?? 'Không chọn thiết bị' }}</span>
          </div>
        </div>
        <div>
          <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Người báo cáo (Staff ID)</p>
          <p class="text-slate-900 font-semibold">
            {{ $ticket->reportedByStaff?->full_name ?? '-' }}
            @if($ticket->reportedByStaff)
              <span class="text-slate-400 font-medium">(ID: {{ str_pad((string) $ticket->reportedByStaff->id, 4, '0', STR_PAD_LEFT) }})</span>
            @endif
          </p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-slate-100 pt-6">
        <div>
          <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Ngày báo cáo</p>
          <p class="text-slate-900 font-semibold">
            {{ optional($ticket->reported_date)->format('d \\T\\h\\á\\n\\g m, Y') }}
            @if($ticket->created_at)
              - {{ $ticket->created_at->format('H:i') }}
            @endif
          </p>
        </div>
        <div>
          <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Chi phí thực tế (Repair Cost)</p>
          <p class="text-slate-900 text-3xl font-extrabold tracking-tight">{{ number_format((float) $ticket->repair_cost, 0, ',', '.') }}đ</p>
        </div>
      </div>

      <div class="border-t border-slate-100 pt-6 space-y-4">
        <div class="rounded-2xl bg-slate-50 p-5">
          <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Mô tả lỗi chi tiết</p>
          <p class="text-slate-700 leading-7 whitespace-pre-line">{{ $ticket->issue_description }}</p>
        </div>

        <div class="rounded-2xl bg-blue-50/60 p-5 border border-blue-100">
          <p class="text-xs font-bold text-blue-500 uppercase tracking-wide mb-2">Ghi chú từ kỹ thuật viên</p>
          <p class="text-slate-700 leading-7 whitespace-pre-line">{{ $ticket->technician_note ?: 'Chưa có ghi chú kỹ thuật.' }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-slate-100 pt-6">
        <div>
          <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Khởi tạo lúc (CreatedAt)</p>
          <p class="text-slate-700 text-sm">
            {{ $ticket->created_at?->format('d/m/Y H:i:s') ?? '-' }}
            @if($ticket->reportedByStaff)
              <span class="text-slate-500">by {{ $ticket->reportedByStaff->full_name }}</span>
            @endif
          </p>
        </div>
        <div>
          <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Cập nhật cuối (UpdatedAt)</p>
          <p class="text-slate-700 text-sm">
            {{ $ticket->updated_at?->format('d/m/Y H:i:s') ?? '-' }}
            @if($ticket->technician)
              <span class="text-slate-500">by {{ $ticket->technician->full_name }}</span>
            @endif
          </p>
        </div>
      </div>
    </div>

    <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 print:hidden">
      <a href="{{ $returnUrl ?? route('admin.maintenance-tickets.index') }}" class="px-6 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-white transition-colors">
        Đóng cửa sổ
      </a>
      <button type="button" onclick="window.print()" class="px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary/90 transition-colors inline-flex items-center gap-2">
        <span class="material-symbols-outlined !text-base">print</span>
        In phiếu
      </button>
    </div>
  </div>
</div>

@endsection
