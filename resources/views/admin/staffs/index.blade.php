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
                    <button
                      data-staff-id="{{ $item->id }}"
                      data-staff-name="{{ $item->first_name }} {{ $item->last_name }}"
                      data-is-active="{{ $item->is_active ? 1 : 0 }}"
                      class="btn-toggle-active inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $item->is_active ? 'bg-primary' : 'bg-slate-300' }}"
                      title="{{ $item->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}"
                      aria-label="{{ $item->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}"
                    >
                      <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $item->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                    @endcan
                    @can('staffs.view')
                     <button data-staff-id="{{ $item->id }}"
                      class="view-staff-btn text-blue-500 hover:text-blue-700 transition-colors" title="Xem chi tiết">
                      <span class="material-symbols-outlined text-lg">visibility</span>
                    </button>
                    @endcan
                    @can('staffs.edit')
                     <a href="{{ route('admin.staffs.edit', $item->id) }}"
                      class="edit-staff-btn text-amber-500 hover:text-amber-700 transition-colors" title="Chỉnh sửa">
                      <span class="material-symbols-outlined text-lg">edit</span>
                    </a>
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
      {{ $staff->withQueryString()->links('vendor.pagination.custom')}}
    </div>

    
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
        <div class="bg-slate-50 rounded-lg p-4 mb-6 text-left space-y-2">
          <div class="text-sm text-slate-600">
            <span class="font-semibold">Nhân viên: </span>
            <span id="staffName" class="text-slate-900 font-semibold">-</span>
          </div>
          <div class="text-sm text-slate-600">
            <span class="font-semibold">Email: </span>
            <span id="staffEmail" class="text-slate-900">-</span>
          </div>
          <div class="text-sm text-slate-600">
            <span class="font-semibold">Vai trò: </span>
            <span id="staffRole" class="text-slate-900">-</span>
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

  {{-- View Staff Detail Modal --}}
  <div id="viewStaffModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
      {{-- Header --}}
      <div class="flex items-center justify-between px-8 pt-7 pb-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-primary">badge</span>
          </div>
          <h2 class="text-lg font-bold text-slate-900">Thông tin nhân viên</h2>
        </div>
        <button id="closeViewModal" class="text-slate-400 hover:text-slate-600 transition-colors">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      {{-- Body --}}
      <div class="p-8">
        {{-- Avatar + Name --}}
        <div class="flex items-center gap-4 mb-6">
          <div id="viewStaffAvatar" class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xl font-bold">--</div>
          <div>
            <p id="viewStaffFullName" class="text-xl font-bold text-slate-900">-</p>
            <p id="viewStaffCode" class="text-sm text-slate-500 font-medium">-</p>
          </div>
        </div>

        {{-- Fields Grid --}}
        <div class="grid grid-cols-2 gap-4">
          <div class="bg-slate-50 rounded-xl p-4">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Họ</p>
            <p id="viewFirstName" class="text-sm font-semibold text-slate-900">-</p>
          </div>
          <div class="bg-slate-50 rounded-xl p-4">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Tên</p>
            <p id="viewLastName" class="text-sm font-semibold text-slate-900">-</p>
          </div>
          <div class="bg-slate-50 rounded-xl p-4">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Email</p>
            <p id="viewEmail" class="text-sm font-semibold text-slate-900 break-all">-</p>
          </div>
          <div class="bg-slate-50 rounded-xl p-4">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Số điện thoại</p>
            <p id="viewPhone" class="text-sm font-semibold text-slate-900">-</p>
          </div>
          <div class="bg-slate-50 rounded-xl p-4">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Vai trò</p>
            <p id="viewRole" class="text-sm font-semibold text-slate-900">-</p>
          </div>
          <div class="bg-slate-50 rounded-xl p-4">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Trạng thái</p>
            <p id="viewStatus" class="text-sm font-semibold">-</p>
          </div>
        </div>

        {{-- Footer Actions --}}
        <div class="flex gap-3 mt-6">
          <button id="closeViewModalBtn" class="flex-1 px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
            Đóng
          </button>
          <a id="viewEditLink" href="#" class="flex-1 px-4 py-2.5 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
            <span class="material-symbols-outlined !text-base">edit</span>
            Chỉnh sửa
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Toggle Active Confirmation Modal --}}
  <div id="toggleActiveModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
      <div class="flex justify-center pt-8">
        <div id="toggleActiveIconWrap" class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center">
          <span id="toggleActiveIcon" class="material-symbols-outlined text-4xl text-amber-600">lock</span>
        </div>
      </div>

      <div class="p-8 text-center">
        <h2 id="toggleActiveTitle" class="text-xl font-bold text-slate-900 mb-3">Xác nhận khóa tài khoản</h2>
        <p id="toggleActiveText" class="text-sm text-slate-600 mb-6">
          Bạn có chắc chắn muốn khóa tài khoản nhân viên này không?
        </p>

        <div class="bg-slate-50 rounded-lg p-4 mb-6 text-left">
          <div class="text-sm text-slate-600">
            <span class="font-semibold">Nhân viên: </span>
            <span id="toggleStaffName" class="text-slate-900 font-semibold">-</span>
          </div>
        </div>

        <div class="flex gap-3">
          <button id="cancelToggleBtn" class="flex-1 px-4 py-2.5 border border-slate-300 rounded-lg font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
            Quay lại
          </button>
          <button id="confirmToggleBtn" class="flex-1 px-4 py-2.5 bg-amber-600 text-white rounded-lg font-semibold hover:bg-amber-700 transition-colors">
            Xác nhận
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    let currentDeleteId = null;
    let currentToggleId = null;
    let currentToggleState = null;

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
            document.getElementById('staffRole').textContent = data.role_name || '-';
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

    // Toggle Active Button Handler
    document.addEventListener('click', (e) => {
      if (e.target.closest('.btn-toggle-active')) {
        const button = e.target.closest('.btn-toggle-active');
        const id = button.dataset.staffId;
        const name = button.dataset.staffName;
        const isActive = button.dataset.isActive === '1';

        currentToggleId = id;
        currentToggleState = isActive;

        document.getElementById('toggleStaffName').textContent = name;

        const title = document.getElementById('toggleActiveTitle');
        const text = document.getElementById('toggleActiveText');
        const iconWrap = document.getElementById('toggleActiveIconWrap');
        const icon = document.getElementById('toggleActiveIcon');
        const confirmBtn = document.getElementById('confirmToggleBtn');

        if (isActive) {
          title.textContent = 'Xác nhận khóa tài khoản';
          text.textContent = 'Nhân viên sẽ không thể đăng nhập sau khi tài khoản bị khóa.';
          icon.textContent = 'lock';
          iconWrap.className = 'w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center';
          icon.className = 'material-symbols-outlined text-4xl text-amber-600';
          confirmBtn.className = 'flex-1 px-4 py-2.5 bg-amber-600 text-white rounded-lg font-semibold hover:bg-amber-700 transition-colors';
          confirmBtn.textContent = 'Khóa tài khoản';
        } else {
          title.textContent = 'Xác nhận mở khóa tài khoản';
          text.textContent = 'Nhân viên sẽ có thể đăng nhập lại vào hệ thống sau khi mở khóa.';
          icon.textContent = 'lock_open';
          iconWrap.className = 'w-16 h-16 rounded-full bg-green-100 flex items-center justify-center';
          icon.className = 'material-symbols-outlined text-4xl text-green-600';
          confirmBtn.className = 'flex-1 px-4 py-2.5 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors';
          confirmBtn.textContent = 'Mở khóa tài khoản';
        }

        const modal = document.getElementById('toggleActiveModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      }
    });

    // Cancel Toggle Active
    document.getElementById('cancelToggleBtn').addEventListener('click', () => {
      const modal = document.getElementById('toggleActiveModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
      currentToggleId = null;
      currentToggleState = null;
    });

    // Confirm Toggle Active
    document.getElementById('confirmToggleBtn').addEventListener('click', () => {
      if (!currentToggleId || currentToggleState === null) return;

      const modal = document.getElementById('toggleActiveModal');
      const btn = document.getElementById('confirmToggleBtn');
      const originalText = btn.textContent;

      btn.disabled = true;
      btn.textContent = 'Đang xử lý...';

      fetch(`{{ route('admin.staffs.toggle-active', ':id') }}`.replace(':id', currentToggleId), {
        method: 'PATCH',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
      })
        .then(res => {
          const isSuccess = res.ok && res.status === 200;
          return res.json().then(data => ({ data, isSuccess }));
        })
        .then(({ data, isSuccess }) => {
          modal.classList.add('hidden');
          modal.classList.remove('flex');

          const alertDiv = document.createElement('div');
          alertDiv.className = 'fixed top-4 right-4 text-white px-6 py-3 rounded-lg shadow-lg z-40';

          if (isSuccess) {
            alertDiv.className += ' bg-green-500';
            alertDiv.textContent = data.message || 'Cập nhật trạng thái tài khoản thành công';
            document.body.appendChild(alertDiv);

            setTimeout(() => {
              location.reload();
            }, 1200);
          } else {
            alertDiv.className += ' bg-red-500';
            alertDiv.textContent = data.message || 'Không thể cập nhật trạng thái tài khoản';
            document.body.appendChild(alertDiv);

            setTimeout(() => {
              alertDiv.remove();
            }, 2500);
          }
        })
        .catch(err => {
          console.error('Toggle active error:', err);

          const errorAlert = document.createElement('div');
          errorAlert.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-40';
          errorAlert.textContent = 'Lỗi cập nhật trạng thái tài khoản: ' + (err.message || 'Vui lòng thử lại');
          document.body.appendChild(errorAlert);

          setTimeout(() => {
            errorAlert.remove();
          }, 3000);
        })
        .finally(() => {
          btn.disabled = false;
          btn.textContent = originalText;
          currentToggleId = null;
          currentToggleState = null;
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

    document.getElementById('toggleActiveModal').addEventListener('click', (e) => {
      if (e.target.id === 'toggleActiveModal') {
        document.getElementById('toggleActiveModal').classList.add('hidden');
        document.getElementById('toggleActiveModal').classList.remove('flex');
        currentToggleId = null;
        currentToggleState = null;
      }
    });

    // View Staff Detail Modal
    document.addEventListener('click', (e) => {
      if (e.target.closest('.view-staff-btn')) {
        const id = e.target.closest('.view-staff-btn').dataset.staffId;

        fetch(`{{ route('admin.staffs.show', ':id') }}`.replace(':id', id), {
          headers: { 'Accept': 'application/json' },
        })
          .then(res => {
            if (!res.ok) throw new Error('Không thể tải thông tin nhân viên');
            return res.json();
          })
          .then(data => {
            const initials = (data.first_name?.charAt(0) ?? '') + (data.last_name?.charAt(0) ?? '');
            const code = `NV-${String(data.id).padStart(3, '0')}`;

            document.getElementById('viewStaffAvatar').textContent = initials || '--';
            document.getElementById('viewStaffFullName').textContent = `${data.first_name} ${data.last_name}`;
            document.getElementById('viewStaffCode').textContent = code;
            document.getElementById('viewFirstName').textContent = data.first_name || '-';
            document.getElementById('viewLastName').textContent = data.last_name || '-';
            document.getElementById('viewEmail').textContent = data.email || '-';
            document.getElementById('viewPhone').textContent = data.phone_number || '-';
            document.getElementById('viewRole').textContent = data.role_name || '-';

            const statusEl = document.getElementById('viewStatus');
            if (data.is_active) {
              statusEl.textContent = 'Đang hoạt động';
              statusEl.className = 'text-sm font-semibold text-green-600';
            } else {
              statusEl.textContent = 'Ngừng hoạt động';
              statusEl.className = 'text-sm font-semibold text-red-600';
            }

            const editUrl = `{{ route('admin.staffs.edit', ':id') }}`.replace(':id', data.id);
            document.getElementById('viewEditLink').href = editUrl;

            const modal = document.getElementById('viewStaffModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
          })
          .catch(err => {
            console.error('View staff error:', err);
            alert('Lỗi tải thông tin nhân viên: ' + err.message);
          });
      }
    });

    // Close View Modal
    ['closeViewModal', 'closeViewModalBtn'].forEach(id => {
      document.getElementById(id)?.addEventListener('click', () => {
        document.getElementById('viewStaffModal').classList.add('hidden');
        document.getElementById('viewStaffModal').classList.remove('flex');
      });
    });

    document.getElementById('viewStaffModal').addEventListener('click', (e) => {
      if (e.target.id === 'viewStaffModal') {
        document.getElementById('viewStaffModal').classList.add('hidden');
        document.getElementById('viewStaffModal').classList.remove('flex');
      }
    });
  </script>
@endsection
