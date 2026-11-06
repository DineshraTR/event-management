<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'event_sub_category_id',
        'package_name',
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

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(EventSubCategory::class, 'event_sub_category_id');
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
        if ($price <= 0 && $this->event) {
            $price = (float) ($this->event->package_sell_price ?: ($this->event->subCategory?->sell_price ?? 0));
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
