@extends("layouts.admin")
@section('content')
<div class="flex-1 flex flex-col">
  <!-- Page Content -->
  <div class="p-8 max-w-7xl mx-auto w-full">
    <!-- Breadcrumb -->
    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 text-slate-600 hover:text-blue-900 text-sm font-medium mb-6 transition-colors">
      <span class="material-symbols-outlined text-sm">arrow_back</span>
      Trở lại tổng quan
    </a>

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h2 class="text-2xl font-bold text-slate-900">Sơ đồ phòng</h2>
        <p class="text-slate-500 text-sm mt-1">Hệ thống quản lý khách sạn Urban Luxe</p>
      </div>
      <div class="flex gap-3">
        <a href="{{ route('admin.dashboard') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-900 px-4 py-2 rounded-lg font-bold text-sm transition-all">
          Quay Lại
        </a>
        <button onclick="alert('Coming soon')" class="bg-blue-900 hover:bg-blue-800 text-white px-4 py-2 rounded-lg font-bold text-sm transition-all">
          + Thêm sơ đồ
        </button>
      </div>
    </div>

    <!-- Placeholder -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
      <span class="material-symbols-outlined text-slate-300 text-6xl block mb-4">grid_view</span>
      <h3 class="text-lg font-bold text-slate-600 mb-2">Chưa có sơ đồ phòng</h3>
      <p class="text-slate-500 text-sm mb-6">Bắt đầu bằng cách tạo sơ đồ phòng cho khách sạn của bạn</p>
      <button onclick="alert('Coming soon')" class="inline-flex items-center gap-2 bg-blue-900 hover:bg-blue-800 text-white px-6 py-2 rounded-lg font-bold text-sm transition-all">
        <span class="material-symbols-outlined">add</span>
        Tạo sơ đồ phòng
      </button>
    </div>
  </div>
</div>
@endsection
