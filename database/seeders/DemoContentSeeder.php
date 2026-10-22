<?php

namespace Database\Seeders;

use App\Enums\CarStatus;
use App\Enums\ServiceType;
use App\Models\Car;
use App\Models\CarCategory;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Dữ liệu demo TV5: dịch vụ bảo dưỡng/bảo hành và xe Toyota hiển thị công khai.
 * Dùng firstOrCreate để chạy lại không bị trùng (idempotent).
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        // Dịch vụ bookable cho /dat-lich và /dich-vu.
        Service::firstOrCreate(
            ['slug' => 'bao-duong-10k'],
            [
                'name' => 'Bảo dưỡng định kỳ 10.000km',
                'type' => ServiceType::Maintenance,
                'description' => 'Kiểm tra và bảo dưỡng định kỳ theo mốc km: thay dầu, lọc gió, kiểm động cơ và phanh.',
                'allow_booking' => true,
                'is_visible' => true,
            ]
        );
        Service::firstOrCreate(
            ['slug' => 'bao-hanh-hang'],
            [
                'name' => 'Bảo hành hãng',
                'type' => ServiceType::Warranty,
                'description' => 'Bảo hành chính hãng Toyota theo chính sách nhà sản xuất.',
                'allow_booking' => true,
                'is_visible' => true,
            ]
        );

        // Nhóm xe + xe hiển thị cho lịch xem xe.
        $vios = CarCategory::firstOrCreate(
            ['slug' => 'vios'],
            ['brand' => 'Toyota', 'name' => 'Vios', 'is_visible' => true]
        );
        Car::firstOrCreate(
            ['code' => 'TOY-DEMO-001'],
            [
                'slug' => 'toyota-vios-1-5g', 'name' => 'Toyota Vios 1.5G',
                'category_id' => $vios->id, 'status' => CarStatus::Available,
                'is_visible' => true, 'year' => (int) date('Y'),
                'color' => 'Trắng', 'price' => 539000000,
            ]
        );
        Car::firstOrCreate(
            ['code' => 'TOY-DEMO-002'],
            [
                'slug' => 'toyota-vios-1-5e', 'name' => 'Toyota Vios 1.5E',
                'category_id' => $vios->id, 'status' => CarStatus::Available,
                'is_visible' => true, 'year' => (int) date('Y'),
                'color' => 'Đen', 'price' => 574000000,
            ]
        );
    }
}
