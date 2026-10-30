<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    protected $fillable = [
        'quotation_no', 'customer_id', 'event_sub_category_id', 'package_name',
        'package_cost_price', 'package_sell_price', 'additional_services_total', 'other_charges_total',
        'quotation_date', 'valid_until', 'event_name', 'event_date', 'venue',
        'sub_total', 'discount', 'total_amount', 'status', 'remarks'
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'valid_until' => 'date',
        'event_date' => 'date',
        'package_cost_price' => 'decimal:2',
        'package_sell_price' => 'decimal:2',
        'additional_services_total' => 'decimal:2',
        'other_charges_total' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->quotation_no)) {
                $model->quotation_no = 'QUO-' . str_pad(
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

    public function services(): HasMany
    {
        return $this->hasMany(QuotationService::class);
    }

    public function otherCharges(): HasMany
    {
        return $this->hasMany(QuotationOtherCharge::class);
    }
}
