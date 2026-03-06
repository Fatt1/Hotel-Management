@extends("layouts.admin")
@section('title', $viewModel->formTitle())
@section('content')

<div class="p-8">
  <!-- Breadcrumb / Back Link -->
  <a href="{{ route('admin.staffs.index') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary/80 font-semibold mb-6">
    <span class="material-symbols-outlined">arrow_back</span>
    Quay lại danh sách
  </a>

  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-black text-slate-900 tracking-tight">{{ $viewModel->formTitle() }}</h1>
      <p class="text-slate-500 font-medium mt-2">{{ $viewModel->formDescription() }}</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
      <form action="{{ $viewModel->formAction() }}" method="POST" id="staffForm" class="space-y-6">
        @csrf
        @if($viewModel->isEditing())
          @method('PUT')
          <input type="hidden" name="id" value="{{ $viewModel->staff()->id }}">
        @endif

        <!-- 2-Column Layout: Họ & Vai trò -->
        <div class="grid grid-cols-2 gap-6">
          <!-- HỌ -->
          <div>
            <label class="block text-sm font-semibold text-slate-900 mb-2">HỌ</label>
            <input
              type="text"
              name="first_name"
              value="{{ old('first_name', $viewModel->staff()->first_name) }}"
              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all @error('first_name') border-red-500 @enderror"
              placeholder="Nguyễn"
            />
            @error('first_name')
              <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span>
            @enderror
          </div>

          <!-- VAI TRÒ -->
          <div>
            <label class="block text-sm font-semibold text-slate-900 mb-2">VAI TRÒ</label>
            <select
              name="role_id"
              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all appearance-none cursor-pointer bg-white @error('role_id') border-red-500 @enderror"
            >
              <option value="">Chọn vai trò</option>
              @foreach ($viewModel->roles() as $role)
                <option value="{{ $role->id }}" @if(old('role_id', $viewModel->staff()->role_id) == $role->id) selected @endif>
                  {{ $role->name }}
                </option>
              @endforeach
            </select>
            @error('role_id')
              <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <!-- 2-Column Layout: Tên & Mật khẩu -->
        <div class="grid grid-cols-2 gap-6">
          <!-- TÊN -->
          <div>
            <label class="block text-sm font-semibold text-slate-900 mb-2">TÊN</label>
            <input
              type="text"
              name="last_name"
              value="{{ old('last_name', $viewModel->staff()->last_name) }}"
              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all @error('last_name') border-red-500 @enderror"
              placeholder="Văn A"
            />
            @error('last_name')
              <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span>
            @enderror
          </div>

          <!-- MẬT KHẨU -->
          <div>
            <label class="block text-sm font-semibold text-slate-900 mb-2">MẬT KHẨU</label>
            <input
              type="password"
              name="password"
              autocomplete="new-password"
              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all @error('password') border-red-500 @enderror"
              placeholder="••••••••"
            />
            @error('password')
              <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span>
            @enderror
            @if($viewModel->isEditing())
              <p class="text-xs text-slate-500 mt-1.5">Để trống nếu không thay đổi mật khẩu</p>
            @endif
          </div>
        </div>

        <!-- 2-Column Layout: Số điện thoại & Xác nhận mật khẩu -->
        <div class="grid grid-cols-2 gap-6">
          <!-- SỐ ĐIỆN THOẠI -->
          <div>
            <label class="block text-sm font-semibold text-slate-900 mb-2">SỐ ĐIỆN THOẠI</label>
            <input
              type="tel"
              name="phone_number"
              value="{{ old('phone_number', $viewModel->staff()->phone_number) }}"
              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all @error('phone_number') border-red-500 @enderror"
              placeholder="090 123 4567"
            />
            @error('phone_number')
              <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span>
            @enderror
          </div>

          <!-- XÁC NHẬN MẬT KHẨU -->
          <div>
            <label class="block text-sm font-semibold text-slate-900 mb-2">XÁC NHẬN MẬT KHẨU</label>
            <input
              type="password"
              name="password_confirmation"
              autocomplete="new-password"
              class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all @error('password_confirmation') border-red-500 @enderror"
              placeholder="••••••••"
            />
            @error('password_confirmation')
              <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <!-- EMAIL (Full Width) -->
        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2">EMAIL</label>
          <input
            type="email"
            name="email"
            autocomplete="off"
            value="{{ old('email', $viewModel->staff()->email) }}"
            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all @error('email') border-red-500 @enderror"
            placeholder="example@urbanluxe.com"
          />
          @error('email')
            <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span>
          @enderror
        </div>

        <!-- Form Actions -->
        <div class="flex gap-3 pt-6 border-t border-slate-100">
          <a href="{{ route('admin.staffs.index') }}" class="flex-1 px-6 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all text-center">
            Hủy
          </a>
          <button
            type="submit"
            class="flex-1 px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary/90 transition-all flex items-center justify-center gap-2"
          >
            <span class="material-symbols-outlined !text-lg">{{ $viewModel->submitButtonIcon() }}</span>
            {{ $viewModel->submitButtonText() }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
