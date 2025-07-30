<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Comment;
use App\Models\User;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'parent_id' => null,
            'content' => $this->faker->paragraph(rand(1, 3)),
            'rating' => $this->faker->optional(0.7)->numberBetween(1, 5),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'is_verified_purchase' => $this->faker->boolean(60),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the comment is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate that the comment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the comment is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    /**
     * Indicate that the comment has a rating.
     */
    public function withRating(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->numberBetween(1, 5),
        ]);
    }

    /**
     * Indicate that the comment has no rating.
     */
    public function withoutRating(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => null,
        ]);
    }

    /**
     * Indicate that the user has purchased the product.
     */
    public function verifiedPurchase(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified_purchase' => true,
        ]);
    }

    /**
     * Indicate that this is a reply to another comment.
     */
    public function reply(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => Comment::factory()->approved(),
            'rating' => null, // Replies don't have ratings
        ]);
    }

    /**
     * Create a comment with realistic Vietnamese content.
     */
    public function vietnamese(): static
    {
        $contents = [
            'Sản phẩm rất tốt, chất lượng vượt mong đợi! Tôi rất hài lòng với lựa chọn này.',
            'Giao hàng nhanh, đóng gói cẩn thận. Shop phục vụ rất tốt.',
            'Giá cả hợp lý, sản phẩm chất lượng. Sẽ giới thiệu cho bạn bè.',
            'Sản phẩm đúng như mô tả, sẽ mua lại. Cảm ơn shop!',
            'Chất lượng tốt nhưng giá hơi cao. Tuy nhiên vẫn đáng tiền.',
            'Sản phẩm ok, nhưng shipping hơi lâu. Hy vọng shop cải thiện.',
            'Rất đáng tiền, recommend cho mọi người! 5 sao không tiếc.',
            'Sản phẩm chất lượng, shop phục vụ tốt. Sẽ ủng hộ lâu dài.',
            'Mình đã dùng và thấy rất ổn. Hiệu quả tốt.',
            'Đóng gói cẩn thận, sản phẩm nguyên vẹn khi nhận.',
        ];

        return $this->state(fn (array $attributes) => [
            'content' => $this->faker->randomElement($contents),
        ]);
    }
}