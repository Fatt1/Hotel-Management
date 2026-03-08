<aside
  class="w-72 flex-shrink-0 bg-white border-r border-slate-200 hidden lg:flex flex-col sticky top-0 h-screen z-20 overflow-hidden">
  <div class="p-8 pb-6 flex items-center gap-3">
    <div
      class="w-10 h-10 bg-[#1e3a8a] rounded-xl flex items-center justify-center text-white shadow-xl shadow-primary/20">
      <span class="material-symbols-outlined !text-2xl">apartment</span>
    </div>
    <div class="flex flex-col">
      <span class="text-lg font-black tracking-tight text-slate-900 uppercase leading-none">Urban
        Luxe</span>
      <span class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mt-1">Management System</span>
    </div>
  </div>
  <nav class="flex-1 overflow-y-auto px-4 pb-8 space-y-0.5 custom-scrollbar">
    <a class="{{ request()->routeIs("admin.dashboard") ? 'sidebar-item-active' : 'sidebar-item' }}"
      href="{{ route("admin.dashboard") }}">
      <span class="material-symbols-outlined">dashboard</span>
      <span>Tổng quan</span>
    </a>
    <div class="sidebar-group-label">VẬN HÀNH</div>
    <a class="{{ request()->routeIs("admin.layout-rooms.*") ? 'sidebar-item-active' : 'sidebar-item' }}" href="{{ route('admin.layout-rooms.index') }}">
      <span class="material-symbols-outlined">grid_view</span>
      <span>Sơ đồ phòng</span>
    </a>
    <a class="{{ request()->routeIs("admin.room-diagrams.edit") ? 'sidebar-item-active' : 'sidebar-item' }}" href="{{ route('admin.room-diagrams.edit') }}">
      <span class="material-symbols-outlined">edit</span>
      <span>Chỉnh sửa sơ đồ phòng</span>
    </a>
    <a class="{{ request()->routeIs("admin.bookings.*") ? 'sidebar-item-active' : 'sidebar-item' }}" href="{{ route("admin.bookings.index") }}">
      <span class="material-symbols-outlined">calendar_month</span>
      <span>Quản lý đặt lịch</span>
    </a>
    <div class="sidebar-group-label">QUẢN LÝ PHÒNG</div>
    <a class="{{ request()->routeIs("admin.room-types.*") ? 'sidebar-item-active' : 'sidebar-item' }}" href="{{ route('admin.room-types.index') }}">
      <span class="material-symbols-outlined">category</span>
      <span>Quản lý loại phòng</span>
    </a>
    <div class="sidebar-group-label">QUẢN LÝ TÀI SẢN</div>
    <a class="{{ request()->routeIs("admin.equipments.*") ? 'sidebar-item-active' : 'sidebar-item' }}" href="{{ route('admin.equipments.index') }}">
      <span class="material-symbols-outlined">inventory_2</span>
      <span>Trang thiết bị</span>
    </a>
    <a class="{{ request()->routeIs("admin.equipment-categories.*") ? 'sidebar-item-active' : 'sidebar-item' }}" href="{{ route('admin.equipment-categories.index') }}">
      <span class="material-symbols-outlined">category</span>
      <span>Quản lý loại thiết bị</span>
    </a>
    <a class="sidebar-item" href="#">
      <span class="material-symbols-outlined">build</span>
      <span>Phiếu sửa chữa</span>
    </a>
    <div class="sidebar-group-label">KHÁCH HÀNG</div>
    <a class="{{ request()->routeIs("admin.customers.*") ? 'sidebar-item-active' : 'sidebar-item' }}" href="{{ route('admin.customers.index') }}">
      <span class="material-symbols-outlined">groups</span>
      <span>Quản lý khách hàng</span>
    </a>
    <div class="sidebar-group-label">DỊCH VỤ &amp; TIỆN ÍCH</div>
    <a class="{{ request()->routeIs('admin.services.*') ? 'sidebar-item-active' : 'sidebar-item' }}" href="{{ route('admin.services.index') }}">
      <span class="material-symbols-outlined">room_service</span>
      <span>Quản lý dịch vụ</span>
    </a>
    <a class="{{ request()->routeIs('admin.service-groups.*') ? 'sidebar-item-active' : 'sidebar-item' }}" href="{{ route('admin.service-groups.index') }}">
      <span class="material-symbols-outlined">list_alt</span>
      <span>Loại dịch vụ</span>
    </a>
    <a class="{{ request()->routeIs('admin.utilities.*') ? 'sidebar-item-active' : 'sidebar-item' }}" href="{{ route('admin.utilities.index') }}">
      <span class="material-symbols-outlined">spa</span>
      <span>Quản lý tiện ích</span>
    </a>
    <div class="sidebar-group-label">HỆ THỐNG</div>
    <a class="{{ request()->routeIs('admin.staffs.*') ? 'sidebar-item-active' : 'sidebar-item' }}"
      href="{{ route('admin.staffs.index') }}">
      <span class="material-symbols-outlined">groups</span>
      <span>Quản lý nhân viên</span>
    </a>
    <a class="{{ request()->routeIs('admin.roles.*') ? 'sidebar-item-active' : 'sidebar-item' }}"
      href="{{ route('admin.roles.index') }}">
      <span class="material-symbols-outlined">admin_panel_settings</span>
      <span>Quản lý vai trò</span>
    </a>
    <a class="sidebar-item" href="#">
      <span class="material-symbols-outlined">settings</span>
      <span>Cấu hình chung</span>
    </a>
    <a class="sidebar-item" href="#">
      <span class="material-symbols-outlined">bar_chart</span>
      <span>Thống kê</span>
    </a>
  </nav>
</aside>