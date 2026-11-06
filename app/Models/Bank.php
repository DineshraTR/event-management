<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'bank_name',
        'account_name',
        'account_number',
        'branch',
        'swift_code',
        'contact_number',
        'opening_balance',
        'current_balance',
        'is_active',
        'is_primary',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
    ];

    public static function getPrimaryBank(): ?self
    {
        return self::where('is_primary', true)->where('is_active', true)->first()
            ?? self::where('is_primary', true)->first()
            ?? self::where('is_active', true)->first()
            ?? self::first();
    }

    public function getDisplayNameAttribute(): string
    {
        $branch = $this->branch ? " - {$this->branch}" : "";
        return "{$this->bank_name} ({$this->account_number}){$branch}";
    }

    public function cheques(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Cheque::class);
    }
}
