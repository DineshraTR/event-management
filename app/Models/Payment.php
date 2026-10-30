<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected $fillable = [
        'payment_no', 'order_id', 'customer_id', 'amount',
        'payment_type', 'payment_method', 'reference', 'status', 'cheque_no', 'bank_name', 'bank_id',
        'payment_date', 'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->payment_no)) {
                $model->payment_no = 'PAY-' . str_pad(
                    (self::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT
                );
            }
        });
        static::created(function ($model) {
            $model->order?->recalculateTotals();
        });
        static::updated(function ($model) {
            $model->order?->recalculateTotals();
        });
        static::deleted(function ($model) {
            $model->order?->recalculateTotals();
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function cheque(): HasOne
    {
        return $this->hasOne(Cheque::class);
    }
}
