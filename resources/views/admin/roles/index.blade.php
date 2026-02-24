@extends("layouts.admin")
@section('content')

  </div>

  <div class="p-8 space-y-6">

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Quản lý vai trò</h1>
        <p class="text-slate-500 font-medium">Hệ thống quản trị khách sạn Urban Luxe - Quản lý phân
          quyền và vai trò người dùng (Roles).</p>
      </div>

      <livewire:roles.create-role />

    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="p-5 border-b border-slate-100 flex flex-col xl:flex-row gap-4 justify-between items-center">
        <div class="relative w-full xl:w-96">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
            <span class="material-symbols-outlined !text-lg">search</span>
          </span>
          <input
            class="block w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none"
            placeholder="Tìm tên vai trò..." type="text" />
        </div>
        <div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
          <button
            class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-50 rounded-xl border border-slate-200 hover:bg-slate-100 transition-colors">
            <span class="material-symbols-outlined !text-lg">filter_list</span>
            <span>Lọc dữ liệu</span>
          </button>
        </div>
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
                    <button data-role-id="{{ $role->id }}" class="action-btn text-primary hover:bg-blue-50"
                      title="Phân quyền">
                      <span class="material-symbols-outlined">key</span>
                    </button>
                    <button onclick="Livewire.dispatch('open-edit-modal', {id: {{ $role->id }}})"
                      class="edit-role-btn action-btn text-amber-500 hover:bg-amber-50" title="Chỉnh sửa">
                      <span class="material-symbols-outlined">edit</span>
                    </button>
                    <button data-role-id="{{ $role->id }}" class="action-btn text-rose-500 hover:bg-rose-50" title="Xóa">
                      <span class="material-symbols-outlined">delete</span>
                    </button>
                  </div>
                </td>
              </tr>
            @endforeach

          </tbody>
        </table>
        <livewire:roles.edit-role />
      </div>
      <div class="p-5 border-t border-slate-100 flex items-center justify-between">
        <span class="text-xs font-medium text-slate-500">Hiển thị {{ $roles->firstItem() }} trên {{ $roles->total() }} vai
          trò hệ thống</span>
        <div class="flex items-center gap-1">
          @if ($roles->currentPage() > 1)
            <a href="{{ route('admin.roles.index', ['page_number' => $roles->currentPage() - 1]) }}"
              class="p-2 rounded-lg border border-slate-200 hover:bg-slate-50">
              <span class="material-symbols-outlined !text-lg">chevron_left</span>
            </a>
          @else
            <span class="p-2 rounded-lg border border-slate-200 opacity-50 cursor-not-allowed">
              <span class="material-symbols-outlined !text-lg">chevron_left</span>
            </span>
          @endif

          @for ($i = 1; $i <= $roles->lastPage(); $i++)
            @if ($i == $roles->currentPage())
              <a href="{{ route('admin.roles.index', ['page_number' => $i]) }}" class="pagination-link text-white bg-primary">
                {{ $i }}
              </a>
            @else
              <a href="{{ route('admin.roles.index', ['page_number' => $i]) }}" class="pagination-link">
                {{ $i }}
              </a>
            @endif
          @endfor

          @if ($roles->currentPage() < $roles->lastPage())
            <a href="{{ route('admin.roles.index', ['page_number' => $roles->currentPage() + 1]) }}"
              class="p-2 rounded-lg border border-slate-200 hover:bg-slate-50">
              <span class="material-symbols-outlined !text-lg">chevron_right</span>
            </a>
          @else
            <span class="p-2 rounded-lg border border-slate-200 opacity-50 cursor-not-allowed">
              <span class="material-symbols-outlined !text-lg">chevron_right</span>
            </span>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection