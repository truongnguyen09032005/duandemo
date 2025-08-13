<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'image', 'description', 
        'metarial', 'instrut', 'status', 'category_id'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // New comment relationships
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->approved()->parentComments();
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Comment::class)->approved()->whereNotNull('rating');
    }

    // Helper methods for comments and ratings
    public function averageRating(): float
    {
        return round($this->ratings()->avg('rating') ?: 0, 1);
    }

    public function totalRatings(): int
    {
        return $this->ratings()->count();
    }

    public function totalComments(): int
    {
        return $this->approvedComments()->count();
    }

    public function getRatingDistribution(): array
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->ratings()->where('rating', $i)->count();
        }
        return $distribution;
    }

    public function hasUserCommented($userId): bool
    {
        return $this->comments()
            ->where('user_id', $userId)
            ->whereNull('parent_id') // Only parent comments
            ->exists();
    }

    public function hasUserRated($userId): bool
    {
        return $this->comments()
            ->where('user_id', $userId)
            ->whereNotNull('rating')
            ->exists();
    }
}