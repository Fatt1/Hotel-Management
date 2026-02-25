@extends('layouts.admin')
@section('content')
  <div class="bg-card-light  rounded-xl shadow-sm border border-border-light">
    <div class="p-6 border-b border-border-light flex items-start space-x-4">
      <div class="p-2 bg-blue-50  rounded-lg text-primary">
        <span class="material-symbols-outlined">shield</span>
      </div>
      <div>
        <h3 class="text-lg font-semibold text-text-light">Thông tin vai trò</h3>
        <p class="text-sm text-text-muted-light">Thiết lập chi tiết quyền hạn cho vai trò</p>
      </div>
    </div>
    <div class="p-6 space-y-8">
      <div>
        <p class="block text-xs font-bold text-text-muted-light uppercase tracking-wider mb-2" for="roleName">
          Tên vai trò
        </p>
        <h2 class="text-2xl font-bold">{{ $role->getRole()->name }}</h2>
      </div>
      <div>
        <div class="flex justify-between items-center mb-4">
          <h4 class="text-xs font-bold text-text-muted-light uppercase tracking-wider">Danh sách
            phân quyền</h4>
          <button
            class="text-xs font-medium text-primary bg-blue-50 px-3 py-1.5 rounded hover:bg-blue-100 transition">CHỌN
            TẤT CẢ</button>
        </div>
        <div class="overflow-x-auto border border-border-light rounded-lg">
          <table class="min-w-full divide-y divide-border-light">
            <thead class="bg-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-bold text-text-muted-light uppercase tracking-wider w-1/3"
                  scope="col">Chức năng</th>
                <th class="px-6 py-3 text-center text-xs font-bold text-text-muted-light uppercase tracking-wider"
                  scope="col">Xem</th>
                <th class="px-6 py-3 text-center text-xs font-bold text-text-muted-light uppercase tracking-wider"
                  scope="col">Thêm</th>
                <th class="px-6 py-3 text-center text-xs font-bold text-text-muted-light uppercase tracking-wider"
                  scope="col">Sửa</th>
                <th class="px-6 py-3 text-center text-xs font-bold text-text-muted-light uppercase tracking-wider"
                  scope="col">Xóa</th>
              </tr>
            </thead>
            <tbody class="bg-card-light divide-y divide-border-light">
              <tr class="bg-blue-50/50">
                <td class="px-6 py-2 text-xs font-bold text-primary uppercase tracking-wide" colspan="5">
                  Vận hành</td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text-light">Sơ đồ
                  phòng
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input {{ $role->hasClaim('layouts','view') ? 'checked' : '' }}
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>

              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text-light">Chỉnh sửa
                  sơ
                  đồ phòng</td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input {{ $role->hasClaim('edit-layouts','view') ? 'checked' : '' }}
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input {{ $role->hasClaim('edit-layouts','create') ? 'checked' : '' }}
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input {{ $role->hasClaim('edit-layouts','eidt') ? 'checked' : '' }}
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input {{ $role->hasClaim('edit-layouts','delete') ? 'checked' : '' }}
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text-light">Quản lý
                  đặt
                  lịch</td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input {{ $role->hasClaim('bookings','view') ? 'checked' : '' }}
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input {{ $role->hasClaim('layouts','view') ? 'checked' : '' }}
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input {{ $role->hasClaim('layouts','view') ? 'checked' : '' }}
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
              </tr>
              <tr class="bg-blue-50/50">
                <td class="px-6 py-2 text-xs font-bold text-primary uppercase tracking-wide" colspan="5">
                  Quản lý phòng</td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text-light">Quản lý
                  phòng</td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input {{ $role->hasClaim('layouts','view') ? 'checked' : '' }}
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text-light">Quản lý
                  loại
                  phòng</td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input checked=""
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
              </tr>
              <tr class="bg-blue-50/50">
                <td class="px-6 py-2 text-xs font-bold text-primary uppercase tracking-wide" colspan="5">
                  Quản lý tài sản</td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text-light">Trang
                  thiết
                  bị</td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input checked=""
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text-light">Phiếu sửa
                  chữa</td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input checked=""
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input checked=""
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
              </tr>
              <tr class="bg-blue-50/50">
                <td class="px-6 py-2 text-xs font-bold text-primary uppercase tracking-wide" colspan="5">
                  Khách hàng</td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text-light">Quản lý
                  khách hàng</td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input checked=""
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input checked=""
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
              </tr>
              <tr class="bg-blue-50/50">
                <td class="px-6 py-2 text-xs font-bold text-primary uppercase tracking-wide" colspan="5">
                  Dịch vụ &amp; Tiện ích</td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text-light">Quản lý
                  dịch
                  vụ</td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input checked=""
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text-light">Loại dịch
                  vụ
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input checked=""
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
              </tr>
              <tr class="bg-blue-50/50">
                <td class="px-6 py-2 text-xs font-bold text-primary uppercase tracking-wide" colspan="5">
                  Hệ thống</td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text-light">Quản lý
                  nhân
                  viên</td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text-light">Quản lý
                  vai
                  trò</td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text-light">Cấu hình
                  chung</td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text-light">Thống kê
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
                <td class="px-6 py-4 whitespace-nowrap text-center"><input
                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" type="checkbox" /></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="px-6 py-4 bg-gray-50 rounded-b-xl border-t border-border-light flex justify-end space-x-3">
      <button
        class="px-5 py-2 rounded-lg bg-white text-text-muted-light border border-gray-300 hover:bg-gray-50 text-sm font-medium transition shadow-sm">
        Hủy
      </button>
      <button
        class="px-5 py-2 rounded-lg bg-primary text-white hover:bg-blue-900 text-sm font-medium transition shadow-sm flex items-center">
        <span class="material-symbols-outlined text-sm mr-2">save</span>
        Lưu vai trò
      </button>
    </div>
  </div>
@endsection