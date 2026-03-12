<?php

namespace Database\Seeders;

use App\Enums\ActionType;
use App\Enums\Module;
use App\Models\Role;
use App\Models\RoleClaim;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $adminRole = Role::create([
            'name' => 'Admin',
        ]);

        

        // Staff được tạo trong StaffSeeder
        // Staff::create([
        //     'role_id' => $adminRole->id,
        //     'first_name' => "Admin",
        //     'last_name' => "User",
        //     'phone_number' => "1234567890",
        //     'email' => "admin@gmail.com",
        //     'is_active' => true,
        //     'password' => 'admin', 
        // ]); 

        // Tạo các permissions và gán chúng cho vai trò Admin
         foreach(Module::cases() as $module) {
            $actionValue = ActionType::sum($module->getAllowActions());
            RoleClaim::create([
                'role_id' => $adminRole->id,
                'claim_name' => $module->value,
                'claim_value' => $actionValue,
            ]);
              
         }

        // Seed dữ liệu phòng và đặt phòng
        $this->call([
            StaffSeeder::class,
            FloorSeeder::class,
            AmenitySeeder::class,
            RoomTypeSeeder::class,
            RoomSeeder::class,
            CustomerSeeder::class,
            BookingSeeder::class,
            EquipmentCategorySeeder::class,
            EquipmentSeeder::class,
            AmenitySeeder::class,
            SurchargePolicySeeder::class,
            SystemSettingSeeder::class,
        ]);
    }
}
