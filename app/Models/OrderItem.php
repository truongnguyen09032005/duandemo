<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'quantity',
        'price',
        'total'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    // Quan hệ với Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Quan hệ với Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Quan hệ với Variant
    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    // Mutator tự động tính total khi set quantity hoặc price
    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = $value;
        $this->calculateTotal();
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value;
        $this->calculateTotal();
    }

    private function calculateTotal()
    {
        if (isset($this->attributes['quantity']) && isset($this->attributes['price'])) {
            $this->attributes['total'] = $this->attributes['quantity'] * $this->attributes['price'];
        }
    }
}