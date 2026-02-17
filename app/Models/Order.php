<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_PROCESSING,
        self::STATUS_SHIPPED,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'client_id',
        'user_id',
        'order_number',
        'order_date',
        'status',
        'total',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Cliente del pedido.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Usuario/vendedor que creó el pedido.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Productos del pedido con cantidad y precio.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_product')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    /**
     * Calcula el total del pedido desde los items.
     */
    public function recalculateTotal(): void
    {
        $total = $this->products->sum(function ($product) {
            return $product->pivot->quantity * $product->pivot->price;
        });
        $this->update(['total' => $total]);
    }

    /**
     * Genera un número de pedido único.
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-' . date('Ymd') . '-';
        $lastOrder = static::withTrashed()
            ->where('order_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastOrder
            ? (int) substr($lastOrder->order_number, strlen($prefix)) + 1
            : 1;

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Genera un número de pedido único para factories (evita colisiones en batch).
     */
    public static function generateOrderNumberForFactory(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
    }
}
