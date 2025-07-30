<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // admin, customer
        // Add other fillable fields as needed
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Existing relationships
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // New comment relationships
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->approved();
    }

    public function likedComments(): BelongsToMany
    {
        return $this->belongsToMany(Comment::class, 'comment_likes')->withTimestamps();
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function totalCommentsCount(): int
    {
        return $this->comments()->count();
    }

    public function approvedCommentsCount(): int
    {
        return $this->approvedComments()->count();
    }

    public function hasLikedComment(Comment $comment): bool
    {
        return $this->likedComments()->where('comment_id', $comment->id)->exists();
    }

    public function hasPurchasedProduct($productId): bool
    {
        return $this->orders()
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.product_id', $productId)
            ->where('orders.status', 'completed')
            ->exists();
    }
}