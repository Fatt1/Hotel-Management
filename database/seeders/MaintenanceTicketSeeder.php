<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\MaintenanceTicket;
use App\Models\Room;
use App\Models\Staff;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaintenanceTicketSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $rooms = Room::pluck('id')->toArray();
        $equipments = Equipment::pluck('id')->toArray();
        $staffIds = Staff::pluck('id')->toArray();

        $tickets = [
            [
                'room_id' => $rooms[0] ?? 1,
                'equipment_id' => $equipments[0] ?? null,
                'reported_date' => '2026-02-10',
                'issue_description' => 'Màn hình TV bị sọc ngang, hình ảnh không hiển thị bình thường.',
                'technician_note' => 'Đã kiểm tra, cần thay bo mạch màn hình.',
                'status' => 'completed',
                'repair_cost' => 850000,
                'reported_by_staff_id' => $staffIds[0] ?? 1,
                'technician_id' => $staffIds[1] ?? 1,
            ],
            [
                'room_id' => $rooms[1] ?? 1,
                'equipment_id' => $equipments[2] ?? null,
                'reported_date' => '2026-02-18',
                'issue_description' => 'Điều hòa không lạnh, chạy nhưng không ra hơi mát.',
                'technician_note' => 'Bơm thêm gas điều hòa, vệ sinh dàn lạnh.',
                'status' => 'completed',
                'repair_cost' => 450000,
                'reported_by_staff_id' => $staffIds[0] ?? 1,
                'technician_id' => $staffIds[1] ?? 1,
            ],
            [
                'room_id' => $rooms[2] ?? 1,
                'equipment_id' => $equipments[4] ?? null,
                'reported_date' => '2026-03-01',
                'issue_description' => 'Tủ lạnh mini phát ra tiếng ồn lớn, không làm lạnh được.',
                'technician_note' => null,
                'status' => 'in_progress',
                'repair_cost' => 0,
                'reported_by_staff_id' => $staffIds[0] ?? 1,
                'technician_id' => $staffIds[1] ?? 1,
            ],
            [
                'room_id' => $rooms[3] ?? 1,
                'equipment_id' => null,
                'reported_date' => '2026-03-05',
                'issue_description' => 'Vòi nước trong phòng tắm bị rò rỉ, nước nhỏ giọt liên tục.',
                'technician_note' => 'Thay gioăng cao su vòi nước.',
                'status' => 'completed',
                'repair_cost' => 120000,
                'reported_by_staff_id' => $staffIds[0] ?? 1,
                'technician_id' => $staffIds[1] ?? 1,
            ],
            [
                'room_id' => $rooms[0] ?? 1,
                'equipment_id' => $equipments[1] ?? null,
                'reported_date' => '2026-03-08',
                'issue_description' => 'Ghế sofa bị rách ở tay vịn bên phải.',
                'technician_note' => null,
                'status' => 'pending',
                'repair_cost' => 0,
                'reported_by_staff_id' => $staffIds[0] ?? 1,
                'technician_id' => $staffIds[1] ?? 1,
            ],
            [
                'room_id' => $rooms[4] ?? 1,
                'equipment_id' => $equipments[3] ?? null,
                'reported_date' => '2026-03-10',
                'issue_description' => 'Máy pha cà phê không hoạt động, bật nguồn không lên đèn.',
                'technician_note' => 'Đang chờ phụ tùng thay thế.',
                'status' => 'in_progress',
                'repair_cost' => 0,
                'reported_by_staff_id' => $staffIds[0] ?? 1,
                'technician_id' => $staffIds[1] ?? 1,
            ],
            [
                'room_id' => $rooms[5] ?? 1,
                'equipment_id' => null,
                'reported_date' => '2026-03-12',
                'issue_description' => 'Bóng đèn phòng ngủ bị hỏng, phòng thiếu ánh sáng.',
                'technician_note' => 'Đã thay bóng LED mới.',
                'status' => 'completed',
                'repair_cost' => 85000,
                'reported_by_staff_id' => $staffIds[0] ?? 1,
                'technician_id' => $staffIds[1] ?? 1,
            ],
            [
                'room_id' => $rooms[2] ?? 1,
                'equipment_id' => null,
                'reported_date' => '2026-03-14',
                'issue_description' => 'Khóa cửa phòng bị kẹt, khách không thể mở từ bên trong.',
                'technician_note' => null,
                'status' => 'pending',
                'repair_cost' => 0,
                'reported_by_staff_id' => $staffIds[0] ?? 1,
                'technician_id' => $staffIds[1] ?? 1,
            ],
            [
                'room_id' => $rooms[6] ?? 1,
                'equipment_id' => $equipments[0] ?? null,
                'reported_date' => '2026-03-15',
                'issue_description' => 'Remote TV bị mất, khách không thể điều khiển ti vi.',
                'technician_note' => 'Đã hủy yêu cầu, thay bằng remote mới từ kho.',
                'status' => 'cancelled',
                'repair_cost' => 0,
                'reported_by_staff_id' => $staffIds[0] ?? 1,
                'technician_id' => $staffIds[1] ?? 1,
            ],
            [
                'room_id' => $rooms[1] ?? 1,
                'equipment_id' => null,
                'reported_date' => '2026-03-16',
                'issue_description' => 'Hệ thống wifi trong phòng yếu, khách phản ánh không kết nối được.',
                'technician_note' => null,
                'status' => 'pending',
                'repair_cost' => 0,
                'reported_by_staff_id' => $staffIds[0] ?? 1,
                'technician_id' => $staffIds[1] ?? 1,
            ],
        ];

        foreach ($tickets as $ticket) {
            MaintenanceTicket::create($ticket);
        }
    }
}
