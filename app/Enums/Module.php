<?php

namespace App\Enums;

use App\Enums\ActionType;

use function Symfony\Component\String\s;

enum Module: string
{
    case DASHBOARD = 'dashboard';
    case BOOKINGS = 'bookings';
    case CUSTOMERS = 'customers';

    case ROOM_TYPES = 'room_types';
    
    case LAYOUTS = 'layouts';
    case STAFFS = 'staffs';
    case EDIT_LAYOUTS = 'edit_layouts';
    case EQUIPMENTS = 'equipments';
    case EQUIPMENT_CATEGORIES = 'equipment_categories';
    case MAINTENANCE_TICKETS = 'maintenance_tickets';
    case SERVICES = 'services';
    case SERVICE_CATEGORIES = 'service_categories';
    case AMENITIES = 'amenities';
    case ROLE = "roles";
    case SETTINGS = "settings";
    case STATISTICS = "statistics";

    public function label(): string
    {
        return match ($this) {
            self::DASHBOARD => 'Tổng quan',
            self::LAYOUTS    => 'Sơ đồ phòng',
            self::EDIT_LAYOUTS => 'Chỉnh sửa sơ đồ phòng',
            self::BOOKINGS  => 'Quản lý đặt lịch',
            self::SETTINGS  => 'Cấu hình chung',
            self::CUSTOMERS => "Quản lý khách hàng",
            self::STAFFS => "Quản lý nhân viên",
            self::ROLE => "Quản lý vai trò",
            self::ROOM_TYPES => "Quản lý loại phòng",
            self::EQUIPMENT_CATEGORIES => "Danh mục thiết bị",
            self::EQUIPMENTS => "Quản lý thiết bị",
            self::MAINTENANCE_TICKETS => "Quản lý sửa chữa",
            self::SERVICES => "Quản lý dịch vụ",
            self::SERVICE_CATEGORIES => "Loại dịch vụ",
            self::AMENITIES => "Quản lý tiện nghi",
            self::STATISTICS => "Thống kê",
        };
    }


    public function getAllowActions(): array
    {
        return match ($this) {
            self::DASHBOARD => [ActionType::VIEW],
            self::LAYOUTS    => [ActionType::VIEW, ActionType::EDIT],
            self::BOOKINGS  => [ActionType::VIEW, ActionType::CREATE, ActionType::EDIT, ActionType::DELETE],
            self::EDIT_LAYOUTS => [ActionType::VIEW, ActionType::EDIT, ActionType::CREATE, ActionType::DELETE],
            self::SETTINGS  => [ActionType::VIEW, ActionType::EDIT],
            self::CUSTOMERS => [ActionType::VIEW, ActionType::CREATE, ActionType::EDIT, ActionType::DELETE],
            self::STAFFS => [ActionType::VIEW, ActionType::CREATE, ActionType::EDIT, ActionType::DELETE],
            self::ROLE => [ActionType::VIEW, ActionType::CREATE, ActionType::EDIT, ActionType::DELETE],
            self::EQUIPMENT_CATEGORIES => [ActionType::VIEW, ActionType::CREATE, ActionType::EDIT, ActionType::DELETE],
            self::EQUIPMENTS => [ActionType::VIEW, ActionType::CREATE, ActionType::EDIT, ActionType::DELETE],
            self::MAINTENANCE_TICKETS => [ActionType::VIEW, ActionType::CREATE, ActionType::EDIT, ActionType::DELETE],
            self::SERVICES => [ActionType::VIEW, ActionType::CREATE, ActionType::EDIT, ActionType::DELETE],
            self::SERVICE_CATEGORIES => [ActionType::VIEW, ActionType::CREATE, ActionType::EDIT, ActionType::DELETE],
            self::AMENITIES => [ActionType::VIEW, ActionType::CREATE, ActionType::EDIT, ActionType::DELETE],
            self::ROOM_TYPES => [ActionType::VIEW, ActionType::CREATE, ActionType::EDIT, ActionType::DELETE],
            self::STATISTICS => [ActionType::VIEW],
        };
    }

    // 1. Mảng nhóm Vận hành
    public static function groupOperate(): array
    {
        return [
            self::LAYOUTS,
            self::EDIT_LAYOUTS,
            self::BOOKINGS,
            self::ROOM_TYPES,
        ];
    }

    // 2. Mảng nhóm Dịch vụ & Thiết bị
    public static function groupService(): array
    {
        return [
            self::SERVICES,
            self::SERVICE_CATEGORIES,
            self::AMENITIES,
           
        ];
    }

    public static function groupAsset(): array
    {
        return [
            self::EQUIPMENTS,
            self::EQUIPMENT_CATEGORIES,
            self::MAINTENANCE_TICKETS,
        ];
    }
    public static function groupCustomer()
    {
        return [
            self::CUSTOMERS,
        ];
    }

    // 3. Mảng nhóm Quản trị Hệ thống
    public static function groupSystem(): array
    {
        return [
            self::ROLE,
            self::STAFFS,
            self::SETTINGS,
            self::STATISTICS,
        ];
    }
}
