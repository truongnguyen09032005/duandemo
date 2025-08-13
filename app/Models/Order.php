<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'phone', 
        'address',
        'total',
        'total_amount',
        'pay_amount',
        'sale_price',
        'status',
        'payment',
        'payment_status',
        'payment_method',
        'shipping_address',
        'shipping_phone',
        'shipping_name',
        'voucher_code', // Sửa từ vorcher_code thành voucher_code
        'notes'
    ];

    protected $casts = [
        'status' => 'string',
        'total' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'pay_amount' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với OrderDetail
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    // Quan hệ với Voucher (nếu có)
    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_code', 'code');
    }

    // Các status constants
    const STATUS_PENDING = 'pending';        // Chờ xử lý
    const STATUS_CONFIRMED = 'confirmed';    // Đã xác nhận
    const STATUS_PROCESSING = 'processing';  // Đang xử lý
    const STATUS_SHIPPING = 'shipping';      // Đang giao hàng
    const STATUS_DELIVERED = 'delivered';    // Đã giao
    const STATUS_CANCELLED = 'cancelled';    // Đã hủy
    const STATUS_RETURNED = 'returned';      // Đã trả hàng

    // Payment constants
    const PAYMENT_COD = 'cod';
    const PAYMENT_ONLINE = 'online';
    const PAYMENT_BANK_TRANSFER = 'bank_transfer';

    // Payment status constants
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_REFUNDED = 'refunded';
    const PAYMENT_STATUS_FAILED = 'failed';

    // Static arrays for dropdown/select options
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Chờ xử lý',
            self::STATUS_CONFIRMED => 'Đã xác nhận',
            self::STATUS_PROCESSING => 'Đang xử lý',
            self::STATUS_SHIPPING => 'Đang giao hàng',
            self::STATUS_DELIVERED => 'Đã giao',
            self::STATUS_CANCELLED => 'Đã hủy',
            self::STATUS_RETURNED => 'Đã trả hàng',
        ];
    }

    public static function getPaymentOptions()
    {
        return [
            self::PAYMENT_COD => 'Thanh toán khi nhận hàng (COD)',
            self::PAYMENT_ONLINE => 'Thanh toán online',
            self::PAYMENT_BANK_TRANSFER => 'Chuyển khoản ngân hàng',
        ];
    }

    public static function getPaymentStatusOptions()
    {
        return [
            self::PAYMENT_STATUS_PENDING => 'Chờ thanh toán',
            self::PAYMENT_STATUS_PAID => 'Đã thanh toán',
            self::PAYMENT_STATUS_REFUNDED => 'Đã hoàn tiền',
            self::PAYMENT_STATUS_FAILED => 'Thanh toán thất bại',
        ];
    }

    // Accessor để hiển thị status
    public function getStatusTextAttribute()
    {
        $statuses = self::getStatusOptions();
        return $statuses[$this->status] ?? 'Không xác định';
    }

    // Accessor để hiển thị payment
    public function getPaymentTextAttribute()
    {
        $payments = self::getPaymentOptions();
        return $payments[$this->payment] ?? ucfirst($this->payment);
    }

    // Accessor để hiển thị payment status
    public function getPaymentStatusTextAttribute()
    {
        $paymentStatuses = self::getPaymentStatusOptions();
        return $paymentStatuses[$this->payment_status] ?? 'Không xác định';
    }

    // Accessor để hiển thị badge class cho status
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return 'badge-warning';
            case self::STATUS_CONFIRMED:
                return 'badge-info';
            case self::STATUS_PROCESSING:
                return 'badge-primary';
            case self::STATUS_SHIPPING:
                return 'badge-secondary';
            case self::STATUS_DELIVERED:
                return 'badge-success';
            case self::STATUS_CANCELLED:
                return 'badge-danger';
            case self::STATUS_RETURNED:
                return 'badge-dark';
            default:
                return 'badge-secondary';
        }
    }

    // Accessor để hiển thị badge class cho payment
    public function getPaymentBadgeClassAttribute()
    {
        switch ($this->payment) {
            case self::PAYMENT_COD:
                return 'badge-warning';
            case self::PAYMENT_ONLINE:
                return 'badge-info';
            case self::PAYMENT_BANK_TRANSFER:
                return 'badge-primary';
            default:
                return 'badge-secondary';
        }
    }

    // Accessor để hiển thị badge class cho payment status
    public function getPaymentStatusBadgeClassAttribute()
    {
        switch ($this->payment_status) {
            case self::PAYMENT_STATUS_PENDING:
                return 'badge-warning';
            case self::PAYMENT_STATUS_PAID:
                return 'badge-success';
            case self::PAYMENT_STATUS_REFUNDED:
                return 'badge-info';
            case self::PAYMENT_STATUS_FAILED:
                return 'badge-danger';
            default:
                return 'badge-secondary';
        }
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeShipping($query)
    {
        return $query->where('status', self::STATUS_SHIPPING);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeReturned($query)
    {
        return $query->where('status', self::STATUS_RETURNED);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment', $method);
    }

    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isProcessing()
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isShipping()
    {
        return $this->status === self::STATUS_SHIPPING;
    }

    public function isDelivered()
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isReturned()
    {
        return $this->status === self::STATUS_RETURNED;
    }

    public function canCancel()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function canConfirm()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canProcess()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function canShip()
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function canDeliver()
    {
        return $this->status === self::STATUS_SHIPPING;
    }

    public function canReturn()
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function canRefund()
    {
        return in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_RETURNED]) 
            && $this->payment_status === self::PAYMENT_STATUS_PAID;
    }

    // Tính toán tổng số sản phẩm trong đơn hàng
    public function getTotalItemsAttribute()
    {
        return $this->orderDetails->sum('quantity');
    }

    // Tính toán số tiền thực tế khách phải trả
    public function getActualPayAmountAttribute()
    {
        return $this->total - ($this->sale_price ?? 0);
    }

    // Format số tiền theo định dạng Việt Nam
    public function getFormattedTotalAttribute()
    {
        return number_format((float) $this->total, 0, ',', '.') . 'đ';
    }

    public function getFormattedPayAmountAttribute()
    {
        return number_format((float) $this->pay_amount, 0, ',', '.') . 'đ';
    }

    public function getFormattedSalePriceAttribute()
    {
        return number_format($this->sale_price ?? 0, 0, ',', '.') . 'đ';
    }

    // Boot method để tự động set một số giá trị
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            // Set default payment status nếu chưa có
            if (empty($order->payment_status)) {
                $order->payment_status = $order->payment === self::PAYMENT_COD 
                    ? self::PAYMENT_STATUS_PENDING 
                    : self::PAYMENT_STATUS_PENDING;
            }

            // Set default status nếu chưa có
            if (empty($order->status)) {
                $order->status = self::STATUS_PENDING;
            }

            // Tính toán pay_amount nếu chưa có
            if (empty($order->pay_amount)) {
                $order->pay_amount = $order->total - ($order->sale_price ?? 0);
            }
        });
    }
}