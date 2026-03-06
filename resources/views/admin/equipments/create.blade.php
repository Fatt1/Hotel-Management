@extends("layouts.admin")
@section('content')

<div class="flex-1 flex flex-col">
  <div class="p-8 max-w-3xl mx-auto w-full">
    <!-- Page Header -->
    <div class="mb-4">
      <a href="{{ route('admin.equipments.index') }}" class="text-blue-900 text-sm font-bold flex items-center gap-1 mb-4">
        <span class="material-symbols-outlined text-lg">arrow_back</span>
        Quay lại danh sách thiết bị
      </a>
    </div>

    <!-- Title -->
    <h1 class="text-3xl font-bold text-slate-900 mb-2">Thêm thiết bị mới</h1>
    <p class="text-slate-500 text-sm mb-8">Cung cấp thông tin chi tiết để quản lý tài sản Urban Luxe hiệu quả hơn.</p>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
      <form action="{{ route('admin.equipments.store') }}" method="POST" id="equipmentForm">
        @csrf

        <!-- Form Section Title -->
        <div class="mb-8 pb-8 border-b border-slate-200">
          <h2 class="text-lg font-bold text-slate-900 mb-2">Thông tin thiết bị</h2>
          <p class="text-slate-500 text-sm">Điền đầy đủ các trường thông tin bên dưới.</p>

          <!-- Name Field -->
          <div class="grid grid-cols-1 gap-6 mt-6">
            <div class="flex flex-col gap-2">
              <label class="text-xs font-bold text-slate-700 uppercase">Tên thiết bị (NAME) <span class="text-red-500">*</span></label>
              <input 
                type="text" 
                name="name" 
                value="{{ old('name') }}"
                placeholder="VD: Tủ lạnh công nghiệp"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-900/20 focus:border-blue-900 outline-none transition-all"
              />
              <span class="text-xs text-slate-500">Tên thiết bị sẽ được sử dụng để tra cứu và quản lý trong hệ thống.</span>
              @error('name')
                <div class="text-red-500 text-sm">{{ $message }}</div>
              @enderror
            </div>

            <!-- Category Field -->
            <div class="flex flex-col gap-2">
              <label class="text-xs font-bold text-slate-700 uppercase">Loại thiết bị <span class="text-red-500">*</span></label>
              <select 
                name="equipment_category_id"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg text-slate-900 focus:ring-2 focus:ring-blue-900/20 focus:border-blue-900 outline-none transition-all bg-white"
              >
                <option value="">Chọn danh mục...</option>
                @foreach($viewModel->categories() as $category)
                  <option value="{{ $category->id }}" {{ old('equipment_category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                  </option>
                @endforeach
              </select>
              @error('equipment_category_id')
                <div class="text-red-500 text-sm">{{ $message }}</div>
              @enderror
            </div>

            <!-- Import Price Field -->
            <div class="flex flex-col gap-2">
              <label class="text-xs font-bold text-slate-700 uppercase">Giá nhập (IMPORT PRICE) <span class="text-red-500">*</span></label>
              <div class="flex items-center">
                <input 
                  type="number" 
                  name="import_price" 
                  value="{{ old('import_price') }}"
                  min="0"
                  placeholder="0"
                  class="flex-1 px-4 py-3 border border-slate-300 border-r-0 rounded-l-lg text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-blue-900/20 focus:border-blue-900 outline-none transition-all"
                />
                <div class="px-4 py-3 bg-slate-100 border border-slate-300 border-l-0 rounded-r-lg text-slate-700 font-semibold text-sm">VNĐ</div>
              </div>
              <span class="text-xs text-slate-500">Giá tiền nhập khẩu hoặc giá mua của thiết bị.</span>
              @error('import_price')
                <div class="text-red-500 text-sm">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-3 pt-4">
          <a href="{{ route('admin.equipments.index') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition-all">
            Hủy bỏ
          </a>
          <button 
            type="submit"
            class="px-6 py-3 bg-blue-900 hover:bg-blue-800 text-white font-bold rounded-lg transition-all flex items-center gap-2 shadow-lg shadow-blue-900/20 active:scale-95"
          >
            <span class="material-symbols-outlined">save</span>
            Lưu thiết bị
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
