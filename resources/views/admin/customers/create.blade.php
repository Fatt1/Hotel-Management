@extends("layouts.admin")

@section('content')
<div class="p-8 space-y-6">

    {{-- Back link --}}
    <a href="{{ route('admin.customers.index') }}"
        class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-primary transition-colors">
        <span class="material-symbols-outlined !text-base">arrow_back</span>
        Quay lại danh sách
    </a>

    {{-- Page Header --}}
    <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Thêm khách hàng mới</h1>
        <p class="text-slate-500 font-medium">Thông tin chi tiết khách hàng Urban Luxe</p>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
        <form action="{{ route('admin.customers.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Họ --}}
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-slate-700">Họ <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}"
                        placeholder="Nhập họ khách hàng"
                        class="w-full rounded-xl border @error('first_name') border-red-400 @else border-slate-200 @enderror bg-slate-50 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 outline-none" />
                    @error('first_name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-slate-700">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="example@email.com"
                        class="w-full rounded-xl border @error('email') border-red-400 @else border-slate-200 @enderror bg-slate-50 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 outline-none" />
                    @error('email')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Tên --}}
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-slate-700">Tên <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                        placeholder="Nhập tên khách hàng"
                        class="w-full rounded-xl border @error('last_name') border-red-400 @else border-slate-200 @enderror bg-slate-50 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 outline-none" />
                    @error('last_name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Số điện thoại --}}
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-semibold text-slate-700">Số điện thoại <span class="text-red-500">*</span></label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                        placeholder="VD: 0901234567"
                        class="w-full rounded-xl border @error('phone_number') border-red-400 @else border-slate-200 @enderror bg-slate-50 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 outline-none" />
                    @error('phone_number')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Quốc gia --}}
                <div class="flex flex-col gap-1 md:col-span-2">
                    <label class="text-sm font-semibold text-slate-700">Quốc gia <span class="text-red-500">*</span></label>
                    <select name="country"
                        class="w-full rounded-xl border @error('country') border-red-400 @else border-slate-200 @enderror bg-slate-50 px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 outline-none">
                        <option value="">Chọn quốc gia</option>
                        @foreach($viewModel->countries() as $c)
                            <option value="{{ $c }}" {{ old('country') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                    @error('country')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-3 mt-8">
                <a href="{{ route('admin.customers.index') }}"
                    class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-colors">
                    Hủy
                </a>
                <button type="submit"
                    class="px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">
                    Lưu thông tin
                </button>
            </div>

        </form>
    </div>

</div>
@endsection