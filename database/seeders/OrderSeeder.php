<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo một số user để test
        $users = User::factory(10)->create();
        
        // Tạo một số product để test nếu chưa có
        if (Product::count() == 0) {
            $products = Product::factory(20)->create();
            
            // Tạo variants cho mỗi product
            $products->each(function ($product) {
                Variant::factory(3)->create(['product_id' => $product->id]);
            });
        }

        // Tạo 50 đơn hàng với các trạng thái khác nhau
        Order::factory(10)->pending()->create(['user_id' => $users->random()->id]);
        Order::factory(15)->processing()->create(['user_id' => $users->random()->id]);
        Order::factory(12)->shipped()->create(['user_id' => $users->random()->id]);
        Order::factory(10)->delivered()->create(['user_id' => $users->random()->id]);
        Order::factory(3)->create([
            'user_id' => $users->random()->id,
            'status' => 'cancelled',
            'payment_status' => 'refunded'
        ]);

        // Tạo OrderItems cho mỗi đơn hàng
        Order::all()->each(function ($order) {
            $itemCount = rand(1, 4); // Mỗi đơn hàng có 1-4 sản phẩm
            $totalAmount = 0;

            for ($i = 0; $i < $itemCount; $i++) {
                $product = Product::inRandomOrder()->first();
                $variant = $product->variants()->inRandomOrder()->first();
                $quantity = rand(1, 3);
                $price = $variant ? $variant->price : $product->base_price;
                $total = $quantity * $price;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variant_id' => $variant ? $variant->id : null,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $total,
                ]);

                $totalAmount += $total;
            }

            // Cập nhật tổng tiền của đơn hàng
            $order->update(['total_amount' => $totalAmount]);
        });
    }
}