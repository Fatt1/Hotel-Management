@extends("layouts.admin")
@section('title', "Quản lý nhân viên")
@section('content')

  <div class="p-8 space-y-6">

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Quản lý nhân viên</h1>
        <p class="text-slate-500 font-medium">Hệ thống quản trị khách sạn Urban Luxe - Quản lý thông tin nhân viên, vai trò và quyền hạn.</p>
      </div>
      @can('staffs.create')
        <a href="{{ route('admin.staffs.create') }}"
        class="cursor-pointer flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl text-sm font-bold shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all hover:-translate-y-0.5">
        <span class="material-symbols-outlined">add_circle</span>
        Thêm nhân viên mới
    </a>
      @endcan
      
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="p-5 border-b border-slate-100 flex flex-col xl:flex-row gap-4">
        <form action="{{ route('admin.staffs.index') }}" method="GET" class="flex flex-col xl:flex-row gap-3 w-full">
          <div class="relative flex-1 xl:w-96">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
              <span class="material-symbols-outlined !text-lg">search</span>
            </span>
            <input
              name="search"
              value="{{ request('search') }}"
              class="block w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none"
              placeholder="Tìm tên, email nhân viên..." type="text" />
          </div>
          
          <div class="relative xl:w-48">
            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
              <span class="material-symbols-outlined !text-lg">filter_list</span>
            </span>
            <select
              name="role_id"
              onchange="this.form.submit()"
              class="block w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none appearance-none cursor-pointer"
            >
              <option value="">Vai trò (Tất cả)</option>
              @foreach (\App\Models\Role::all() as $role)
                <option value="{{ $role->id }}" @if(request('role_id') == $role->id) selected @endif>{{ $role->name }}</option>
              @endforeach
            </select>
          </div>
        </form>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-slate-50/50">
              <th class="table-header">MA NV</th>
              <th class="table-header">HỌ VÀ TÊN</th>
              <th class="table-header">
                <a href="{{ route('admin.staffs.index', array_merge(request()->query(), ['sort' => request('sort') === 'role:asc' ? 'role:desc' : 'role:asc'])) }}" 
                   class="inline-flex items-center gap-1.5 hover:text-primary transition-colors group/sort">
                  VAI TRÒ
                  <span class="material-symbols-outlined text-base opacity-0 group-hover/sort:opacity-100 transition-opacity">
                    @if(request('sort') === 'role:desc')
                      arrow_upward
                    @elseif(request('sort') === 'role:asc')
                      arrow_downward
                    @else
                      unfold_more
                    @endif
                  </span>
                </a>
              </th>
              <th class="table-header">LIÊN HỆ</th>
              <th class="table-header text-center">TRẠNG THÁI</th>
              <th class="table-header text-right">HÀNH ĐỘNG</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse ($staff as $item)
              <tr class="hover:bg-slate-50/50 transition-colors group">
                <td class="table-cell">
                  <span class="font-bold text-slate-900">NV-{{ str_pad($item->id, 3, '0', STR_PAD_LEFT) }}</span>
                </td>
                <td class="table-cell">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xs font-bold">
                      {{ substr($item->first_name, 0, 1) }}{{ substr($item->last_name, 0, 1) }}
                    </div>
                    <div class="flex flex-col">
                      <span class="font-semibold text-slate-900">{{ $item->first_name }} {{ $item->last_name }}</span>
                      <span class="text-xs text-slate-500">{{ $item->email }}</span>
                    </div>
                  </div>
                </td>
                <td class="table-cell">
                  <span class="inline-flex gap-1.5 px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-0.5"></span>
                    {{ $item->role->name }}
                  </span>
                </td>
                <td class="table-cell">{{ $item->phone_number }}</td>
                <td class="table-cell text-center">
                  @if ($item->is_active)
                    <span class="inline-flex items-center gap-2">
                      <span class="w-2 h-2 rounded-full bg-green-500"></span>
                      <span class="text-sm font-semibold text-green-700">Đang hoạt động</span>
                    </span>
                  @else
                    <span class="inline-flex items-center gap-2">
                      <span class="w-2 h-2 rounded-full bg-red-500"></span>
                      <span class="text-sm font-semibold text-red-700">Ngừng hoạt động</span>
                    </span>
                  @endif
                </td>
                <td class="table-cell text-right">
                  <div class="flex items-center justify-end gap-3">
                    @can('staffs.edit')
                     <button data-staff-id="{{ $item->id }}"
                      class="edit-staff-btn text-blue-500 hover:text-blue-700 transition-colors" title="Chỉnh sửa">
                      <span class="material-symbols-outlined text-lg">visibility</span>
                    </button>
                    @endcan
                    @can('staffs.edit')
                     <button data-staff-id="{{ $item->id }}"
                      class="edit-staff-btn text-amber-500 hover:text-amber-700 transition-colors" title="Chỉnh sửa">
                      <span class="material-symbols-outlined text-lg">edit</span>
                    </button>
                    @endcan
                    @can('staffs.delete')
                     <button data-staff-id="{{ $item->id }}" 
                      class="btn-delete text-rose-500 hover:text-rose-700 transition-colors" title="Xóa">
                      <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                    @endcan
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="table-cell text-center text-slate-500">
                  Không có dữ liệu nhân viên
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Pagination --}}
    @if ($staff->hasPages() || $staff->total() > 0)
      <div class="flex items-center justify-between">
        <div class="text-sm text-slate-500">
          Hiển thị {{ $staff->firstItem() ?? 0 }}-{{ $staff->lastItem() ?? 0 }} hàng {{ $staff->total() }} nhân viên
        </div>
        
        @if ($staff->hasPages())
          <div class="flex items-center justify-center gap-1">
            {{-- Previous Page Link --}}
            @if ($staff->onFirstPage())
              <span class="w-8 h-8 rounded-lg border border-slate-200 text-slate-400 flex items-center justify-center text-xs font-bold cursor-not-allowed">
                <span class="material-symbols-outlined !text-sm">chevron_left</span>
              </span>
            @else
              <a href="{{ $staff->previousPageUrl() }}" class="pagination-link">
                <span class="material-symbols-outlined !text-sm">chevron_left</span>
              </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($staff->getUrlRange(1, $staff->lastPage()) as $page => $url)
              @if ($page == $staff->currentPage())
                <span class="w-8 h-8 cursor-pointer rounded-lg text-xs font-bold flex items-center justify-center bg-primary text-white">{{ $page }}</span>
              @else
                <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
              @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($staff->hasMorePages())
              <a href="{{ $staff->nextPageUrl() }}" class="pagination-link">
                <span class="material-symbols-outlined !text-sm">chevron_right</span>
              </a>
            @else
              <span class="w-8 h-8 rounded-lg border border-slate-200 text-slate-400 flex items-center justify-center text-xs font-bold cursor-not-allowed">
                <span class="material-symbols-outlined !text-sm">chevron_right</span>
              </span>
            @endif
          </div>
        @endif
      </div>
    @endif
  </div>

  {{-- Delete Confirmation Modal --}}
  <div id="deleteConfirmModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
      {{-- Warning Icon --}}
      <div class="flex justify-center pt-8">
        <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
          <span class="material-symbols-outlined text-4xl text-red-600">warning</span>
        </div>
      </div>

      {{-- Content --}}
      <div class="p-8 text-center">
        <h2 class="text-xl font-bold text-slate-900 mb-3">Xác nhận xóa nhân viên này</h2>
        
        {{-- Red Warning Text --}}
        <p class="text-red-600 font-semibold mb-2">Bạn có chắc chắn muốn xóa Nhân viên này không?</p>
        
        {{-- Description --}}
        <p class="text-sm text-slate-600 mb-6">
          Mọi dữ liệu của nhân viên này sẽ bị xóa và hành động này không thể hoàn tác.
        </p>

        {{-- Staff Info --}}
        <div class="bg-slate-50 rounded-lg p-4 mb-6 text-left">
          <div class="text-sm text-slate-600 mb-2">
            <span class="font-semibold">Nhân viên: </span>
            <span id="staffName" class="text-slate-900 font-semibold">-</span>
          </div>
          <div class="text-sm text-slate-600">
            <span class="font-semibold">Email: </span>
            <span id="staffEmail" class="text-slate-900">-</span>
          </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-3">
          <button id="cancelDeleteBtn" class="flex-1 px-4 py-2.5 border border-slate-300 rounded-lg font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
            Quay lại
          </button>
          <button id="confirmDeleteBtn" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors">
            Xác nhận xóa
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    let currentDeleteId = null;

    // Delete Button Handler
    document.addEventListener('click', (e) => {
      if (e.target.closest('.btn-delete')) {
        const id = e.target.closest('.btn-delete').dataset.staffId;
        
        // Fetch staff info
        fetch(`{{ route('admin.staffs.show', ':id') }}`.replace(':id', id), {
          headers: {
            'Accept': 'application/json',
          },
        })
          .then(res => {
            if (!res.ok) throw new Error('Lỗi tải thông tin nhân viên');
            return res.json();
          })
          .then(data => {
            // Populate modal with staff info
            document.getElementById('staffName').textContent = `${data.first_name} ${data.last_name}`;
            document.getElementById('staffEmail').textContent = data.email;
            currentDeleteId = id;

            // Show modal
            document.getElementById('deleteConfirmModal').classList.remove('hidden');
            document.getElementById('deleteConfirmModal').classList.add('flex');
          })
          .catch(err => {
            console.error('Error:', err);
            alert(err.message || 'Lỗi tải thông tin nhân viên');
          });
      }
    });

    // Cancel Delete
    document.getElementById('cancelDeleteBtn').addEventListener('click', () => {
      document.getElementById('deleteConfirmModal').classList.add('hidden');
      document.getElementById('deleteConfirmModal').classList.remove('flex');
      currentDeleteId = null;
    });

    // Confirm Delete
    document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
      if (!currentDeleteId) return;

      const modal = document.getElementById('deleteConfirmModal');
      const btn = document.getElementById('confirmDeleteBtn');
      
      // Show loading state
      btn.disabled = true;
      btn.textContent = 'Đang xóa...';

      fetch(`{{ route('admin.staffs.destroy', ':id') }}`.replace(':id', currentDeleteId), {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
      })
        .then(res => {
          // Check HTTP status code
          const isSuccess = res.ok && res.status === 200;
          return res.json().then(data => ({ data, isSuccess, status: res.status }));
        })
        .then(({ data, isSuccess, status }) => {
          // Close modal
          modal.classList.add('hidden');
          modal.classList.remove('flex');
          
          // Create alert
          const alertDiv = document.createElement('div');
          alertDiv.className = `fixed top-4 right-4 text-white px-6 py-3 rounded-lg shadow-lg z-40`;
          
          if (isSuccess) {
            // Success case
            alertDiv.className += ' bg-green-500';
            alertDiv.textContent = data.message || 'Xóa nhân viên thành công';
            document.body.appendChild(alertDiv);
            
            // Reload page
            setTimeout(() => {
              location.reload();
            }, 1500);
          } else {
            // Error case (status 400)
            alertDiv.className += ' bg-red-500';
            alertDiv.textContent = data.message || 'Không thể xóa nhân viên';
            document.body.appendChild(alertDiv);
            
            // Reopen modal so user can cancel
            setTimeout(() => {
              modal.classList.remove('hidden');
              modal.classList.add('flex');
              alertDiv.remove();
            }, 2000);
          }
        })
        .catch(err => {
          console.error('Delete error:', err);
          modal.classList.add('hidden');
          modal.classList.remove('flex');
          
          const errorAlert = document.createElement('div');
          errorAlert.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-40';
          errorAlert.textContent = 'Lỗi khi xóa nhân viên: ' + (err.message || 'Vui lòng thử lại');
          document.body.appendChild(errorAlert);
          
          setTimeout(() => {
            errorAlert.remove();
            modal.classList.remove('hidden');
            modal.classList.add('flex');
          }, 3000);
        })
        .finally(() => {
          btn.disabled = false;
          btn.textContent = 'Xác nhận xóa';
        });
    });

    // Close modal when clicking outside
    document.getElementById('deleteConfirmModal').addEventListener('click', (e) => {
      if (e.target.id === 'deleteConfirmModal') {
        document.getElementById('deleteConfirmModal').classList.add('hidden');
        document.getElementById('deleteConfirmModal').classList.remove('flex');
        currentDeleteId = null;
      }
    });

    // Edit - navigate to edit page
    document.addEventListener('click', (e) => {
      if (e.target.closest('.edit-staff-btn')) {
        const id = e.target.closest('.edit-staff-btn').dataset.staffId;
        window.location.href = `{{ route('admin.staffs.edit', ':id') }}`.replace(':id', id);
      }
    });
  </script>
@endsection
