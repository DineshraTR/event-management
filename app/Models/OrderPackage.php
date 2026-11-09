<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'event_package_id',
        'event_sub_category_id',
        'package_name',
        'description',
        'cost_price',
        'sell_price',
        'qty',
        'total_price',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'qty' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function eventPackage()
    {
        return $this->belongsTo(EventPackage::class, 'event_package_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(EventSubCategory::class, 'event_sub_category_id');
    }

    public function getDescriptionAttribute($value): string
    {
        return !empty($value) ? $value : ($this->eventPackage?->description ?: ($this->subCategory?->description ?? ''));
    }

    public function getUnitPriceAttribute(): float
    {
        $price = (float) ($this->sell_price ?: ($this->qty > 0 ? ((float)($this->attributes['total_price'] ?? 0)) / $this->qty : 0));
        if ($price <= 0 && $this->subCategory) {
            $price = (float) ($this->subCategory->sell_price ?? 0);
        }
        if ($price <= 0 && $this->package_name) {
            $foundSub = EventSubCategory::where('name', $this->package_name)->first();
            if ($foundSub && (float)$foundSub->sell_price > 0) {
                $price = (float) $foundSub->sell_price;
            }
        }
        if ($price <= 0 && $this->order) {
            $price = (float) ($this->order->package_sell_price ?: ($this->order->subCategory?->sell_price ?? 0));
        }
        return $price;
    }

    public function getTotalPriceAttribute($value): float
    {
        $total = (float) $value;
        if ($total <= 0) {
            $unit = $this->unit_price;
            $qty = $this->qty ?: 1;
            $total = $unit * $qty;
        }
        return $total;
    }
}
