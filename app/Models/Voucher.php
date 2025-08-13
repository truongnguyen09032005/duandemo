<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'sale_price',
        'min_order',
        'max_price',
        'quantity',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'sale_price' => 'decimal:2',
        'min_order' => 'decimal:2',
        'max_price' => 'decimal:2',
    ];

    /**
     * Check if voucher is active
     */
    public function isActive(): bool
    {
        return $this->quantity > 0 
            && $this->start_date <= now() 
            && $this->end_date >= now();
    }

    /**
     * Check if voucher is valid for given order amount
     */
    public function isValidForOrder(float $orderAmount): bool
    {
        return $this->isActive() && $orderAmount >= $this->min_order;
    }

    /**
     * Calculate discount amount for given order
     */
    public function calculateDiscount(float $orderAmount): float
    {
        if (!$this->isValidForOrder($orderAmount)) {
            return 0;
        }

        if ($this->type === 'fixed') {
            return (float) min($this->sale_price, $this->max_price);
        } else {
            // Percentage discount
            $discount = ($orderAmount * $this->sale_price) / 100;
            return min($discount, $this->max_price);
        }
    }
}