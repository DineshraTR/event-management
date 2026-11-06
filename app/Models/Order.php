<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_no', 'event_id', 'customer_id', 'event_sub_category_id', 'package_name',
        'package_cost_price', 'package_sell_price', 'additional_services_total', 'other_charges_total',
        'order_date', 'sub_total', 'discount', 'total_amount', 'paid_amount', 'balance',
        'payment_status', 'notes', 'is_auto_created'
    ];

    protected $casts = [
        'order_date' => 'date',
        'package_cost_price' => 'decimal:2',
        'package_sell_price' => 'decimal:2',
        'additional_services_total' => 'decimal:2',
        'other_charges_total' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'is_auto_created' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->order_no)) {
                $model->order_no = 'ORD-' . str_pad(
                    (self::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT
                );
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(EventSubCategory::class, 'event_sub_category_id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(OrderPackage::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(OrderService::class);
    }

    public function otherCharges(): HasMany
    {
        return $this->hasMany(OrderOtherCharge::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function cheques(): HasMany
    {
        return $this->hasMany(Cheque::class);
    }

    public function hasPayments(): bool
    {
        if ((float)$this->paid_amount > 0) {
            return true;
        }
        if ($this->relationLoaded('payments')) {
            return $this->payments->isNotEmpty();
        }
        return $this->payments()->exists();
    }

    public function recalculateTotals(): void
    {
        $packageItem = $this->services()->where('is_package_item', true)->first();
        $packageSellPrice = $packageItem ? (float) $packageItem->total_price : (float) ($this->package_sell_price ?? 0);
        $additionalServicesTotal = (float) $this->services()->where('is_package_item', false)->sum('total_price');
        $chargesTotal = (float) $this->otherCharges()->sum('amount');
        
        $servicesTotal = (float) $this->services()->sum('total_price');
        $subTotal = $servicesTotal + $chargesTotal;
        if ($subTotal <= 0 && (float) $this->sub_total > 0) {
            $subTotal = (float) $this->sub_total;
        }

        $totalAmount = $subTotal - (float) $this->discount;
        if ($totalAmount <= 0 && (float) $this->total_amount > 0) {
            $totalAmount = (float) $this->total_amount;
        }

        $paidAmount = (float) $this->payments()
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'Returned');
            })
            ->sum('amount');
        $balance = $totalAmount - $paidAmount;

        $status = 'pending';
        if ($paidAmount >= $totalAmount) {
            $status = 'paid';
        } elseif ($paidAmount > 0) {
            $status = 'partial';
        }

        $this->update([
            'package_sell_price' => $packageSellPrice,
            'additional_services_total' => $additionalServicesTotal,
            'other_charges_total' => $chargesTotal,
            'sub_total' => $subTotal,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'balance' => $balance,
            'payment_status' => $status,
        ]);
    }
}
