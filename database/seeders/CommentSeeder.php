<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\User;
use App\Models\Product;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy users và products có sẵn
        $users = User::where('role', 'customer')->get();
        $products = Product::all();
        $adminUsers = User::where('role', 'admin')->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Cần có ít nhất 1 user customer và 1 product để tạo comment seeds.');
            return;
        }

        // Tạo bình luận mẫu
        $commentContents = [
            'Sản phẩm rất tốt, chất lượng vượt mong đợi!',
            'Giao hàng nhanh, đóng gói cẩn thận.',
            'Giá cả hợp lý, sản phẩm chất lượng.',
            'Tôi rất hài lòng với sản phẩm này.',
            'Sản phẩm đúng như mô tả, sẽ mua lại.',
            'Chất lượng tốt nhưng giá hơi cao.',
            'Sản phẩm ok, nhưng shipping hơi lâu.',
            'Rất đáng tiền, recommend cho mọi người!',
            'Sản phẩm chất lượng, shop phục vụ tốt.',
            'Mình đã dùng và thấy rất ổn.',
        ];

        $replyContents = [
            'Cảm ơn bạn đã đánh giá!',
            'Rất vui khi bạn hài lòng với sản phẩm.',
            'Chúng tôi sẽ cải thiện về thời gian giao hàng.',
            'Cảm ơn feedback của bạn!',
            'Hy vọng bạn sẽ tiếp tục ủng hộ shop.',
        ];

        foreach ($products as $product) {
            // Tạo 3-8 bình luận cho mỗi sản phẩm
            $numComments = rand(3, 8);
            
            for ($i = 0; $i < $numComments; $i++) {
                $user = $users->random();
                
                $comment = Comment::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'parent_id' => null,
                    'content' => $commentContents[array_rand($commentContents)],
                    'rating' => rand(3, 5), // Đánh giá từ 3-5 sao
                    'status' => 'approved',
                    'is_verified_purchase' => rand(0, 1) == 1,
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(1, 30)),
                ]);

                // 30% cơ hội có reply từ admin
                if (rand(1, 10) <= 3 && !$adminUsers->isEmpty()) {
                    Comment::create([
                        'user_id' => $adminUsers->random()->id,
                        'product_id' => $product->id,
                        'parent_id' => $comment->id,
                        'content' => $replyContents[array_rand($replyContents)],
                        'rating' => null,
                        'status' => 'approved',
                        'is_verified_purchase' => false,
                        'created_at' => $comment->created_at->addHours(rand(1, 24)),
                        'updated_at' => $comment->created_at->addHours(rand(1, 24)),
                    ]);
                }

                // 20% cơ hội có reply từ user khác
                if (rand(1, 10) <= 2) {
                    $replyUser = $users->where('id', '!=', $user->id)->random();
                    Comment::create([
                        'user_id' => $replyUser->id,
                        'product_id' => $product->id,
                        'parent_id' => $comment->id,
                        'content' => 'Mình cũng đang quan tâm sản phẩm này, cảm ơn bạn đã review!',
                        'rating' => null,
                        'status' => 'approved',
                        'is_verified_purchase' => false,
                        'created_at' => $comment->created_at->addHours(rand(2, 48)),
                        'updated_at' => $comment->created_at->addHours(rand(2, 48)),
                    ]);
                }
            }

            // Tạo một số bình luận pending để test chức năng admin
            if (rand(1, 10) <= 3) {
                Comment::create([
                    'user_id' => $users->random()->id,
                    'product_id' => $product->id,
                    'parent_id' => null,
                    'content' => 'Bình luận này đang chờ duyệt.',
                    'rating' => rand(1, 5),
                    'status' => 'pending',
                    'is_verified_purchase' => rand(0, 1) == 1,
                    'created_at' => now()->subHours(rand(1, 12)),
                    'updated_at' => now()->subHours(rand(1, 12)),
                ]);
            }
        }

        // Tạo likes cho một số bình luận
        $approvedComments = Comment::approved()->whereNull('parent_id')->get();
        
        foreach ($approvedComments as $comment) {
            // Mỗi bình luận có 0-5 likes
            $numLikes = rand(0, 5);
            $likedUsers = $users->random(min($numLikes, $users->count()));
            
            foreach ($likedUsers as $user) {
                $comment->likes()->attach($user->id, [
                    'created_at' => now()->subDays(rand(1, 10)),
                    'updated_at' => now()->subDays(rand(1, 10)),
                ]);
            }
        }

        $this->command->info('Đã tạo thành công dữ liệu mẫu cho comments!');
        $this->command->info('Total comments: ' . Comment::count());
        $this->command->info('Approved comments: ' . Comment::approved()->count());
        $this->command->info('Pending comments: ' . Comment::pending()->count());
        $this->command->info('Comments with ratings: ' . Comment::whereNotNull('rating')->count());
    }
}