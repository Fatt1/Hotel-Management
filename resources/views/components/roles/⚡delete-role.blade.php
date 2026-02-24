<?php
use Livewire\Component;
use Livewire\Attributes\On;
use App\Actions\GetRoleByIdAction;
use App\Actions\DeleteRoleAction;
use App\Models\Role;

new class extends Component {
    public $isOpen = false;
    public ?Role $role = null;

    public function resetForm()
    {
        $this->reset(['role']);
        $this->resetValidation();
    }
    #[On('open-delete-modal')]
    public function open($id, GetRoleByIdAction $getRoleByIdAction)
    {
        $this->resetForm();
        $role = $getRoleByIdAction->handle($id);
        $this->role = $role;
        $this->isOpen = true;

    }
    public function delete(DeleteRoleAction $deleteRoleAction)
    {
        try {
            $deleteRoleAction->handle($this->role->id);
            $this->isOpen = false;
            return redirect()->route('admin.roles.index')->with('success', 'Xóa vai trò thành công');
        } catch (Exception $e) {
            $this->isOpen = false;
            return redirect()->route('admin.roles.index')->with('error', $e->getMessage());
        }

    }
};
?>

<div x-data="{isOpen: @entangle('isOpen')}">
    <div x-show="isOpen" x-cloak>
        <x-modal>
            <div class="flex flex-col items-center text-center space-y-4">
                <div
                    class="w-16 h-16 bg-rose-50 dark:bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500 mb-2">
                    <span class="material-symbols-outlined !text-4xl">warning</span>
                </div>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Xác nhận xóa</h3>
                <p class="text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                    Bạn có chắc chắn muốn xóa vai trò <span class="font-bold text-slate-900">{{ $role?->name }}</span>
                    không? Hành động này
                    không thể hoàn tác
                </p>
                <div class="flex flex-col sm:flex-row gap-3 w-full pt-6">
                    <button @click="isOpen = false"
                        class="flex-1 px-6 py-3.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-2xl text-sm font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                        Quay lại
                    </button>
                    <button wire:click="delete"
                        class="flex-1 px-6 py-3.5 bg-rose-600 text-white rounded-2xl text-sm font-bold shadow-lg shadow-rose-600/20 hover:bg-rose-700 transition-all">
                        Xác nhận xóa
                    </button>
                </div>
            </div>
        </x-modal>
    </div>
</div>