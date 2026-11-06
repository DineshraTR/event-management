<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    protected $fillable = [
        'event_no', 'customer_id', 'event_sub_category_id', 'event_name',
        'event_date', 'today_date', 'venue', 'remarks',
        'package_cost_price', 'package_sell_price',
        'additional_services_total', 'other_charges_total',
        'sub_total', 'discount', 'total_amount', 'advance_payment', 'balance',
        'status', 'notification_7_sent', 'notification_3_sent'
    ];

    protected $casts = [
        'event_date' => 'date',
        'today_date' => 'date',
        'package_cost_price' => 'decimal:2',
        'package_sell_price' => 'decimal:2',
        'additional_services_total' => 'decimal:2',
        'other_charges_total' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'balance' => 'decimal:2',
        'notification_7_sent' => 'boolean',
        'notification_3_sent' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->event_no)) {
                $model->event_no = 'EV-' . str_pad(
                    (self::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT
                );
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(EventSubCategory::class, 'event_sub_category_id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(EventPackage::class);
    }

    public function additionalServices(): HasMany
    {
        return $this->hasMany(EventService::class);
    }

    public function otherCharges(): HasMany
    {
        return $this->hasMany(EventOtherCharge::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class)->latestOfMany();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function hasPayments(): bool
    {
        if ($this->relationLoaded('orders')) {
            foreach ($this->orders as $o) {
                if ((float)$o->paid_amount > 0) return true;
                if ($o->relationLoaded('payments') && $o->payments->isNotEmpty()) return true;
            }
        }
        if ($this->relationLoaded('order') && $this->order) {
            if ((float)$this->order->paid_amount > 0) return true;
            if ($this->order->relationLoaded('payments') && $this->order->payments->isNotEmpty()) return true;
        }
        return $this->orders()->where('paid_amount', '>', 0)->exists() 
            || $this->orders()->whereHas('payments')->exists();
    }

    public function recalculateTotals(): void
    {
        $packageSellTotal = $this->packages()->exists() 
            ? (float) $this->packages()->sum('total_price') 
            : (float) $this->package_sell_price;

        $packageCostTotal = $this->packages()->exists()
            ? (float) $this->packages()->sum(DB::raw('cost_price * qty'))
            : (float) $this->package_cost_price;

        $additionalTotal = (float) $this->additionalServices()->sum('total_price');
        $otherChargesTotal = (float) $this->otherCharges()->sum('amount');
        $subTotal = $packageSellTotal + $additionalTotal + $otherChargesTotal;
        $totalAmount = max(0, $subTotal - (float) $this->discount);
        $balance = max(0, $totalAmount - (float) $this->advance_payment);

        $this->update([
            'package_sell_price' => $packageSellTotal,
            'package_cost_price' => $packageCostTotal,
            'additional_services_total' => $additionalTotal,
            'other_charges_total' => $otherChargesTotal,
            'sub_total' => $subTotal,
            'total_amount' => $totalAmount,
            'balance' => $balance,
        ]);
    }
}
