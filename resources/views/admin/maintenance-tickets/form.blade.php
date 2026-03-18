@extends('layouts.admin')
@section('title', $viewModel->formTitle())
@section('content')

<div class="p-8">
  <a href="{{ route('admin.maintenance-tickets.index') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary/80 font-semibold mb-6">
    <span class="material-symbols-outlined">arrow_back</span>
    Quay lại danh sách
  </a>

  <div class="space-y-6">
    <div>
      <h1 class="text-3xl font-black text-slate-900 tracking-tight">{{ $viewModel->formTitle() }}</h1>
      <p class="text-slate-500 font-medium mt-2">{{ $viewModel->formDescription() }}</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
      <form action="{{ $viewModel->formAction() }}" method="POST" class="space-y-6">
        @csrf
        @if($viewModel->isEditing())
          @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-semibold text-slate-900 mb-2">Phòng</label>
            <select name="room_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all @error('room_id') border-red-500 @enderror">
              <option value="">Chọn phòng</option>
              @foreach($viewModel->rooms() as $room)
                <option value="{{ $room->id }}" @selected((int) old('room_id', $viewModel->ticket()->room_id) === $room->id)>{{ $room->name }}</option>
              @endforeach
            </select>
            @error('room_id') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-900 mb-2">Thiết bị</label>
            <select name="equipment_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all @error('equipment_id') border-red-500 @enderror">
              <option value="">Không chọn</option>
              @foreach($viewModel->equipments() as $equipment)
                <option value="{{ $equipment->id }}" @selected((int) old('equipment_id', $viewModel->ticket()->equipment_id) === $equipment->id)>{{ $equipment->name }}</option>
              @endforeach
            </select>
            @error('equipment_id') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2">Mô tả sự cố</label>
          <textarea name="issue_description" rows="4" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all @error('issue_description') border-red-500 @enderror" placeholder="Mô tả chi tiết sự cố...">{{ old('issue_description', $viewModel->ticket()->issue_description) }}</textarea>
          @error('issue_description') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2">Ghi chú kỹ thuật</label>
          <textarea name="technician_note" rows="3" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all @error('technician_note') border-red-500 @enderror" placeholder="Ghi chú bổ sung của kỹ thuật viên...">{{ old('technician_note', $viewModel->ticket()->technician_note) }}</textarea>
          @error('technician_note') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-semibold text-slate-900 mb-2">Trạng thái</label>
            <select name="status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all @error('status') border-red-500 @enderror">
              @foreach($viewModel->statuses() as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $viewModel->ticket()->status ?? 'pending') === $value)>{{ $label }}</option>
              @endforeach
            </select>
            @error('status') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-900 mb-2">Chi phí sửa chữa (VNĐ)</label>
            <div class="relative">
              <input type="number" min="0" step="1000" name="repair_cost" value="{{ old('repair_cost', (float) ($viewModel->ticket()->repair_cost ?? 0)) }}" class="w-full px-4 py-2.5 pr-14 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all @error('repair_cost') border-red-500 @enderror">
              <span class="absolute inset-y-0 right-4 flex items-center text-xs font-semibold text-slate-500">VND</span>
            </div>
            @error('repair_cost') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2">Kỹ thuật viên</label>
          <select name="technician_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all @error('technician_id') border-red-500 @enderror">
            <option value="">Chọn kỹ thuật viên</option>
            @foreach($viewModel->staffs() as $staff)
              <option value="{{ $staff->id }}" @selected((int) old('technician_id', $viewModel->ticket()->technician_id) === $staff->id)>{{ $staff->first_name }} {{ $staff->last_name }}</option>
            @endforeach
          </select>
          @error('technician_id') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
        </div>

        <div class="flex gap-3 pt-6 border-t border-slate-100">
          <a href="{{ route('admin.maintenance-tickets.index') }}" class="flex-1 px-6 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all text-center">
            Hủy
          </a>
          <button type="submit" class="flex-1 px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary/90 transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined !text-lg">build</span>
            {{ $viewModel->submitButtonText() }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
