<?php

namespace App\ViewModels;

use App\Models\Utility;

class UtilityViewModel
{
    private ?Utility $utility;

    public function __construct(Utility $utility = null)
    {
        $this->utility = $utility;
    }

    /**
     * Trả về utility (mới hoặc existing)
     */
    public function utility(): Utility
    {
        return $this->utility ?? new Utility();
    }

    /**
     * Danh sách icon Material Symbols có sẵn cho tiện ích
     */
    public function availableIcons(): array
    {
        return [
            // Kết nối & Công nghệ
            'wifi', 'tv', 'phone', 'router',
            
            // Phòng ngủ & Nội thất
            'bed', 'king_bed', 'chair', 'weekend', 'desk',
            
            // Phòng tắm
            'bathtub', 'shower', 'wc', 'dry',
            
            // Điều hòa & Tiện nghi
            'ac_unit', 'iron', 'light', 'window', 'blinds',
            
            // Ẩm thực
            'restaurant', 'kitchen', 'local_bar', 'local_cafe', 'room_service',
            
            // Giải trí & Thể thao
            'fitness_center', 'spa', 'pool', 'directions_run', 'landscape',
            'music_note', 'theater_comedy',
            
            // Dịch vụ
            'local_parking', 'business_center', 'event_available', 'groups',
            'meeting_room', 'beach_access', 'dry_cleaning', 'concierge',
            'card_giftcard', 'local_florist',
            
            // Cấu trúc & An ninh
            'balcony', 'door_front', 'elevator', 'stairs', 'luggage',
            'security', 'lock', 'key', 'crop_free',
        ];
    }
}
