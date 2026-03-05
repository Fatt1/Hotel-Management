@extends("layouts.admin")
@section('title', "Quản lý vai trò")
@section('content')

  <div class="p-8 space-y-6">

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Quản lý vai trò</h1>
        <p class="text-slate-500 font-medium">Hệ thống quản trị khách sạn Urban Luxe - Quản lý phân
          quyền và vai trò người dùng (Roles).</p>
      </div>
      @can('roles.create')
        <button id="addRoleBtn"
        class="cursor-pointer flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl text-sm font-bold shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all hover:-translate-y-0.5">
        <span class="material-symbols-outlined">add_circle</span>
        Thêm vai trò mới
    </button>
      @endcan
      
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="p-5 border-b border-slate-100 flex flex-col xl:flex-row gap-4 justify-between items-center">
        <form action="{{ route('admin.roles.index') }}" method="GET" class="relative w-full xl:w-96">
          <div class="relative w-full xl:w-96">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
              <span class="material-symbols-outlined !text-lg">search</span>
            </span>
            <input
              name="search"
              value="{{ request('search') }}"
              class="block w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none"
              placeholder="Tìm tên vai trò..." type="text" />
          </div>
        </form>
      
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-slate-50/50">
              <th class="table-header text-center">Tên vai trò</th>
              <th class="table-header text-right">Thao tác</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @foreach ($roles as $role)
              <tr class="hover:bg-slate-50/50  transition-colors group">
                <td class="table-cell text-center">
                  <div class="">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span class="font-bold text-slate-900">{{ $role->name }}</span>
                  </div>
                </td>
                <td class="table-cell text-right">
                  <div class="flex items-center justify-end gap-2">
                    @can('roles.edit')
                     <a href="{{ route('admin.roles.edit-permission', ['id' => $role->id]) }}"
                      class="action-btn text-primary hover:bg-blue-50" title="Phân quyền">
                      <span class="material-symbols-outlined">key</span>
                    </a>
                    @endcan
                   
                    @can('roles.edit')
                     <button data-role-id="{{ $role->id }}"
                      class="edit-role-btn action-btn text-amber-500 hover:bg-amber-50" title="Chỉnh sửa">
                      <span class="material-symbols-outlined">edit</span>
                    </button>
                    @endcan
                    @can('roles.delete')
                     <button data-role-id="{{ $role->id }}" 
                      class="action-btn text-rose-500 hover:bg-rose-50 btn-delete" title="Xóa">
                      <span class="material-symbols-outlined">delete</span>
                    </button>
                    @endcan
                   
                  </div>
                </td>
              </tr>
            @endforeach

          </tbody>
        </table>

      </div>
      <div class="p-5 border-t border-slate-100 flex items-center justify-between">
        <span class="text-xs font-medium text-slate-500">Hiển thị {{ $roles->lastItem() }} trên {{ $roles->total() }} vai
          trò hệ thống</span>
        <div class="mt-5">
          {{ $roles->withQueryString()->links('vendor.pagination.custom')}}
        </div>
      </div>
    </div>
  </div>  
@endsection
@push('scripts')
  @vite(['resources/js/admin/roles/index.js'])
@endpush