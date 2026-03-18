<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\MaintenanceTicketStatus;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class MaintenanceTicketData extends Data
{
    public function __construct(
        public int $room_id,
        public ?int $equipment_id,
        public string $issue_description,
        public ?string $technician_note,
        public string $status,
        public float $repair_cost,
        public int $technician_id,
    ) {
    }

    public static function rules(ValidationContext|null $context = null): array
    {
        return [
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'equipment_id' => ['nullable', 'integer', 'exists:equipments,id'],
            'issue_description' => ['required', 'string', 'max:2000'],
            'technician_note' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:' . implode(',', MaintenanceTicketStatus::values())],
            'repair_cost' => ['required', 'numeric', 'min:0'],
            'technician_id' => ['required', 'integer', 'exists:staffs,id'],
        ];
    }

    public static function messages(...$args): array
    {
        return [
            'room_id.required' => 'Vui lòng chọn phòng.',
            'room_id.exists' => 'Phòng không tồn tại.',
            'equipment_id.exists' => 'Thiết bị không tồn tại.',
            'issue_description.required' => 'Vui lòng nhập mô tả sự cố.',
            'issue_description.max' => 'Mô tả sự cố không được vượt quá 2000 ký tự.',
            'technician_note.max' => 'Ghi chú kỹ thuật không được vượt quá 2000 ký tự.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'repair_cost.required' => 'Vui lòng nhập chi phí sửa chữa.',
            'repair_cost.numeric' => 'Chi phí sửa chữa phải là số.',
            'repair_cost.min' => 'Chi phí sửa chữa không được âm.',
            'technician_id.required' => 'Vui lòng chọn kỹ thuật viên.',
            'technician_id.exists' => 'Kỹ thuật viên không tồn tại.',
        ];
    }
}
