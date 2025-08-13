<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'color_id', 'size_id', 
        'price', 'sale', 'stock', 'image'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}