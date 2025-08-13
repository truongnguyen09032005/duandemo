<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = [
            [
                'khach_hang' => 'Nguyễn Văn Nam',
                'email' => 'nguyenvannam@gmail.com',
                'so_dien_thoai' => '0123456789',
                'dia_chi' => '123 Đường ABC, Quận 1, TP.HCM',
                'tong_tien' => 450000,
                'thanh_toan' => 'paid',
                'trang_thai' => 'completed',
                'ngay_tao' => Carbon::now()->subDays(5),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'khach_hang' => 'Trần Thị Hoa',
                'email' => 'tranthihoa@gmail.com',
                'so_dien_thoai' => '0987654321',
                'dia_chi' => '456 Đường XYZ, Quận 3, Hà Nội',
                'tong_tien' => 680000,
                'thanh_toan' => 'pending',
                'trang_thai' => 'processing',
                'ngay_tao' => Carbon::now()->subDays(2),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'khach_hang' => 'Lê Văn Đức',
                'email' => 'levanduc@gmail.com',
                'so_dien_thoai' => '0369852147',
                'dia_chi' => '789 Đường DEF, Quận 7, TP.HCM',
                'tong_tien' => 320000,
                'thanh_toan' => 'paid',
                'trang_thai' => 'shipping',
                'ngay_tao' => Carbon::now()->subDays(1),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'khach_hang' => 'Phạm Thị Lan',
                'email' => 'phamthilan@gmail.com',
                'so_dien_thoai' => '0741852963',
                'dia_chi' => '321 Đường GHI, Quận 5, Đà Nẵng',
                'tong_tien' => 890000,
                'thanh_toan' => 'paid',
                'trang_thai' => 'completed',
                'ngay_tao' => Carbon::now()->subHours(12),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'khach_hang' => 'Võ Văn Minh',
                'email' => 'vovanminh@gmail.com',
                'so_dien_thoai' => '0258147963',
                'dia_chi' => '654 Đường JKL, Quận 2, TP.HCM',
                'tong_tien' => 150000,
                'thanh_toan' => 'pending',
                'trang_thai' => 'pending',
                'ngay_tao' => Carbon::now()->subHours(3),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($orders as $orderData) {
            Order::create($orderData);
        }
    }
}