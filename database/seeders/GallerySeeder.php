<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        $galleries = [
            // Exterior & Interior
            ['title' => 'Toàn cảnh Khách Sạn', 'image_path' => 'images/gallery/exterior-1.jpg', 'category' => 'exterior'],
            ['title' => 'Sảnh đón khách sang trọng', 'image_path' => 'images/gallery/lobby-1.jpg', 'category' => 'interior'],
            
            // Rooms
            ['title' => 'Phòng Tổng Thống - View Biển', 'image_path' => 'images/gallery/presidential-1.jpg', 'category' => 'room'],
            ['title' => 'Phòng Cao Cấp (Deluxe) - Giường đôi', 'image_path' => 'images/gallery/deluxe-1.jpg', 'category' => 'room'],
            ['title' => 'Phòng Tiêu Chuẩn (Standard)', 'image_path' => 'images/gallery/standard-1.jpg', 'category' => 'room'],
            ['title' => 'Phòng Gia Đình tiện nghi', 'image_path' => 'images/gallery/family-1.jpg', 'category' => 'room'],
            
            // Dining
            ['title' => 'Nhà Hàng Cung Đình', 'image_path' => 'images/gallery/dining-cungdinh.jpg', 'category' => 'dining'],
            ['title' => 'Buffet Hải Sản Cao Cấp', 'image_path' => 'images/gallery/dining-buffet.jpg', 'category' => 'dining'],
            ['title' => 'Skyline Bar lãng mạn', 'image_path' => 'images/gallery/dining-bar.jpg', 'category' => 'dining'],
            
            // Event
            ['title' => 'Phòng Hội Nghị Sức chứa 500 khách', 'image_path' => 'images/gallery/event-1.jpg', 'category' => 'event'],
            ['title' => 'Tiệc cưới ngoài trời', 'image_path' => 'images/gallery/event-wedding.jpg', 'category' => 'event'],
        ];

        foreach ($galleries as $gallery) {
            Gallery::create($gallery);
        }
    }
}
