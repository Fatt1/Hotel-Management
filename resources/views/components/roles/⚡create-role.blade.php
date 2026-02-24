<?php

use Livewire\Component;
use App\Actions\AddRoleAction;
use App\Data\RoleData;
new class extends Component {
    // Các biến (props) nhận từ bên ngoài

    public $isOpen = false;
    public $name = '';

    // TẠO HÀM DỌN DẸP FORM
    public function resetForm()
    {
        $this->reset('name');         // Xóa chữ đang gõ dở
        $this->resetValidation();     // Quét sạch các lỗi Validation đỏ
    }
    // 3. TẠO HÀM SAVE() ĐỂ CHẠY KHI BẤM SUBMIT
    // Tiêm (Inject) AddRoleAction thẳng vào đây
    public function save(AddRoleAction $createRoleAction)
    {
        // Bước 1: Validate
        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ], [
            'name.required' => 'Vui lòng nhập tên vai trò.',
            'name.string' => 'Tên vai trò phải là chuỗi ký tự.',
            'name.unique' => 'Tên vai trò đã tồn tại. Vui lòng chọn tên khác.'
        ]);


        // Bước 2: Gọi Action để xử lý (Dùng biến $this->name đã được biding)
        $createRoleAction->handle(RoleData::from([
            'name' => $this->name,
        ]));

        // Bước 3: Đóng Modal và xóa trắng input
        $this->isOpen = false;
        $this->reset('name');

        return redirect()->route('admin.roles.index')->with('success', 'Thêm vai trò thành công');
    }

};
?>

<div x-data="{isOpen: @entangle('isOpen')}">

    <button @click="isOpen = true"
        class="cursor-pointer flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl text-sm font-bold shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all hover:-translate-y-0.5">
        <span class="material-symbols-outlined">add_circle</span>
        Thêm vai trò mới
    </button>

    <div x-show="isOpen" x-cloak>
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

            <form wire:submit="save" class="mt-5 space-y-5">
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
                        Thêm mới
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
</div>