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

    public function packages(): HasMany
    {
        return $this->hasMany(QuotationPackage::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(QuotationService::class);
    }

    public function otherCharges(): HasMany
    {
        return $this->hasMany(QuotationOtherCharge::class);
    }

    public function recalculateTotals(): void
    {
        if ($this->packages()->exists()) {
            foreach ($this->packages as $pkg) {
                $unit = (float) $pkg->sell_price;
                if ($unit <= 0 && $pkg->subCategory) {
                    $unit = (float) ($pkg->subCategory->sell_price ?? 0);
                }
                if ($unit <= 0 && $pkg->package_name) {
                    $foundSub = EventSubCategory::where('name', $pkg->package_name)->first();
                    if ($foundSub && (float)$foundSub->sell_price > 0) {
                        $unit = (float) $foundSub->sell_price;
                    }
                }
                if ($unit <= 0) {
                    $unit = (float) ($this->package_sell_price ?: ($this->subCategory?->sell_price ?? 0));
                }
                $qty = $pkg->qty ?: 1;
                $tot = (float) $pkg->total_price;
                if ($tot <= 0) {
                    $tot = $unit * $qty;
                }
                $cost = (float) $pkg->cost_price;
                if ($cost <= 0 && $pkg->subCategory) {
                    $cost = (float) ($pkg->subCategory->cost_price ?? 0);
                }
                if ($cost <= 0) {
                    $cost = (float) ($this->package_cost_price ?: ($this->subCategory?->cost_price ?? 0));
                }

                if ($unit > 0 && ((float)$pkg->sell_price <= 0 || (float)$pkg->total_price <= 0)) {
                    $pkg->update([
                        'sell_price' => $unit,
                        'total_price' => $tot,
                        'cost_price' => $cost,
                    ]);
                }
            }
        } elseif ($this->event_sub_category_id || (float)$this->package_sell_price > 0) {
            $baseSell = (float)($this->package_sell_price ?: ($this->subCategory?->sell_price ?? 0));
            $baseCost = (float)($this->package_cost_price ?: ($this->subCategory?->cost_price ?? 0));
            QuotationPackage::create([
                'quotation_id' => $this->id,
                'event_sub_category_id' => $this->event_sub_category_id,
                'package_name' => $this->package_name ?? $this->subCategory?->name ?? 'Package',
                'cost_price' => $baseCost,
                'sell_price' => $baseSell,
                'qty' => 1,
                'total_price' => $baseSell,
            ]);
        }

        $packageSellTotal = 0;
        if ($this->packages()->exists()) {
            $packageSellTotal = (float) $this->packages()->sum('total_price');
            if ($packageSellTotal <= 0) {
                $packageSellTotal = (float) $this->packages()->sum(\Illuminate\Support\Facades\DB::raw('sell_price * qty'));
            }
        }
        if ($packageSellTotal <= 0) {
            $packageSellTotal = (float) ($this->package_sell_price ?: ($this->subCategory?->sell_price ?? 0));
        }

        $packageCostTotal = 0;
        if ($this->packages()->exists()) {
            $packageCostTotal = (float) $this->packages()->sum(\Illuminate\Support\Facades\DB::raw('cost_price * qty'));
        }
        if ($packageCostTotal <= 0) {
            $packageCostTotal = (float) ($this->package_cost_price ?: ($this->subCategory?->cost_price ?? 0));
        }

        $additionalTotal = (float) $this->services()->sum('total_price');
        $otherChargesTotal = (float) $this->otherCharges()->sum('amount');
        $subTotal = $packageSellTotal + $additionalTotal + $otherChargesTotal;
        $totalAmount = max(0, $subTotal - (float) $this->discount);

        $this->update([
            'package_sell_price' => $packageSellTotal,
            'package_cost_price' => $packageCostTotal,
            'additional_services_total' => $additionalTotal,
            'other_charges_total' => $otherChargesTotal,
            'sub_total' => $subTotal,
            'total_amount' => $totalAmount,
        ]);
    }
}
