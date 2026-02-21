<?php
namespace App\Enums;

use App\Enums\ActionType;

enum Module:string
{
  case DASHBOARD = 'dashboard';
  case BOOKINGS= 'bookings';
  case ROOMS = 'rooms';
  case CUSTOMERS = 'customers';
  case LAYOUTS = 'layouts';
  case STAFFS = 'staffs';
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
        return match($this) {
            self::DASHBOARD => 'Tổng quan',
            self::LAYOUTS    => 'Sơ đồ phòng',
            self::BOOKINGS  => 'Quản lý đặt lịch',
            self::ROOMS => 'Quản lý phòng',
            self::SETTINGS  => 'Cấu hình chung',
            self::CUSTOMERS => "Quản lý khách hàng",
            self::STAFFS => "Quản lý nhân viên",
            self::ROLE => "Quản lý vai trò",
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
        return match($this) {
            self::DASHBOARD => [ActionType::VIEW],
            self::LAYOUTS    => [ActionType::VIEW, ActionType::EDIT],
            self::BOOKINGS  => [ActionType::VIEW, ActionType::CREATE, ActionType::EDIT, ActionType::DELETE],
            self::ROOMS => [ActionType::VIEW, ActionType::CREATE, ActionType::EDIT, ActionType::DELETE],
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
            self::STATISTICS => [ActionType::VIEW],
        };
    }
}