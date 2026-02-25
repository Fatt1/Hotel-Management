<?php

use Livewire\Component;
use App\Actions\UpdateRoleAction;
use App\Actions\GetRoleByIdAction;
use Livewire\Attributes\On;
use App\Data\RoleData;
use App\Models\Role;
new class extends Component {
    public $isOpen = false;
    public $name = '';
    public $roleId = null;

    protected function rules()
    {
        return [
            // Bây giờ $this->roleId hoạt động hoàn hảo
            'name' => 'required|string|max:255|unique:roles,name,' . $this->roleId,
        ];
    }

    protected $messages = [
        'name.required' => 'Vui lòng nhập tên vai trò.',
        'name.string' => 'Tên vai trò phải là chuỗi ký tự.',
        'name.unique' => 'Tên vai trò đã tồn tại. Vui lòng chọn tên khác.'
    ];

    // THÊM HÀM DỌN DẸP
    public function resetForm()
    {
        $this->reset(['name', 'roleId']);
        $this->resetValidation();
    }

    #[On('open-edit-modal')]
    public function loadRoleData($id, GetRoleByIdAction $getRoleByIdAction)
    {
        $role = $getRoleByIdAction->handle($id);
        // Đổ dữ liệu vào các biến
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->isOpen = true;
    }

    public function update(UpdateRoleAction $updateRoleAction)
    {
        $this->validate();
        try {
            $roleData = new RoleData(id: $this->roleId, name: $this->name);
            $updateRoleAction->handle($this->roleId, $roleData);
        } catch (\Exception $e) {
            return session()->flash('error', $e->getMessage());
        }

        return redirect()->route('admin.roles.index')->with('success', 'Cập nhật vai trò thành công');
    }
};
?>

<div x-data="{isOpen: @entangle('isOpen')}">
    <div x-show="isOpen" x-cloak>
        <x-flash-alert></x-flash-alert>
        <x-modal>
            <div class="flex justify-between items-center pb-4 border-b border-slate-200">
                <div class="flex flex-col">
                    <h2 class="text-xl font-bold text-slate-900">Thêm mới vai trò</h2>
                    <p class="text-sm text-slate-500 uppercase">Role</p>
                </div>

                <button @click="isOpen = false"
                    class="p-2 hover:bg-slate-100 rounded-xl transition-colors text-slate-400 cursor-pointer">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form wire:submit="update" class="mt-5 space-y-5">
                @csrf
                <div class="flex flex-col gap-1">
                    <label for="roleName" class="text-sm font-medium text-slate-700">Tên vai trò <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" id="roleName" wire:model="name" placeholder="Nhập tên vai trò mới"
                        class="w-full rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary focus:outline-none px-3 py-2">
                    @error('name')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror

                </div>
                <div class="flex justify-end gap-2">

                    <button type="button" @click="isOpen = false; $wire.resetForm()"
                        class="cursor-pointer px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                        Hủy
                    </button>

                    <button type="submit" id="submitBtn"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors cursor-pointer">
                        Lưu
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
</div>