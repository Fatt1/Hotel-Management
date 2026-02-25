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
          <button id="selectAllBtn" type="button"
            class="text-xs font-medium text-primary bg-blue-50 px-3 py-1.5 rounded hover:bg-blue-100 transition">CHỌN
            TẤT CẢ</button>
        </div>
        <div class="overflow-x-auto border border-border-light rounded-lg">
          <table class="min-w-full divide-y divide-border-light">
            <thead class="bg-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-bold text-text-muted-light uppercase tracking-wider w-1/3"
                  scope="col">Chức năng</th>
                @foreach ([
                    App\Enums\ActionType::VIEW,
                    App\Enums\ActionType::CREATE,
                    App\Enums\ActionType::EDIT,
                    App\Enums\ActionType::DELETE,
                ] as $actionType)
                  <th class="px-6 py-3 text-center text-xs font-bold text-text-muted-light uppercase tracking-wider"
                    scope="col">{{ $actionType->label() }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody class="bg-card-light divide-y divide-border-light">
              @php
                $moduleGroups = [
                    'Vận hành' => App\Enums\Module::groupOperate(),
                    'Quản lý tài sản' => App\Enums\Module::groupAsset(),
                    'Khách hàng' => App\Enums\Module::groupCustomer(),
                    'Dịch vụ & Tiện ích' => App\Enums\Module::groupService(),
                    'Hệ thống' => App\Enums\Module::groupSystem(),
                ];
              @endphp

              @foreach ($moduleGroups as $groupLabel => $modules)
                <tr class="bg-blue-50/50">
                  <td class="px-6 py-2 text-xs font-bold text-primary uppercase tracking-wide" colspan="5">
                    {{ $groupLabel }}
                  </td>
                </tr>

                @foreach ($modules as $module)
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-text-light">
                      {{ $module->label() }}
                    </td>
                    @foreach ([
                        App\Enums\ActionType::VIEW,
                        App\Enums\ActionType::CREATE,
                        App\Enums\ActionType::EDIT,
                        App\Enums\ActionType::DELETE,
                    ] as $actionType)
                      <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if (in_array($actionType, $module->getAllowActions()))
                          @php
                            $isChecked = $role->hasClaim($module->value, strtolower($actionType->name));
                          @endphp
                          <input type="checkbox" name="permissions[{{ $module->value }}][]"
                            value="{{ $actionType->value }}" {{ $isChecked ? 'checked' : '' }}
                            class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4" />
                        @else
                          <span class="text-gray-300">—</span>
                        @endif
                      </td>
                    @endforeach
                  </tr>
                @endforeach
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="px-6 py-4 bg-gray-50 rounded-b-xl border-t border-border-light flex justify-end space-x-3">
      <a href="{{ route('admin.roles.index') }}"
        class="px-5 py-2 rounded-lg bg-white text-text-muted-light border border-gray-300 hover:bg-gray-50 text-sm font-medium transition shadow-sm">
        Hủy
      </a>
      <button id="saveBtn" type="button"
        class="px-5 py-2 rounded-lg bg-primary text-white hover:bg-blue-900 text-sm font-medium transition shadow-sm flex items-center">
        <span class="material-symbols-outlined text-sm mr-2">save</span>
        Lưu vai trò
      </button>
    </div>
  </div>

  
@endsection