<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    protected $table = 'cash_transactions';

    protected $fillable = [
        'transaction_no',
        'type',
        'amount',
        'description',
        'reference_no',
        'customer_id',
        'order_id',
        'payment_id',
        'expense_id',
        'transaction_date',
        'running_balance',
        'notes',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    protected $casts = [
        'amount' => 'decimal:2',
        'running_balance' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function getTransactionMethodAttribute(): string
    {
        return 'cash';
    }

    public function getBankNameAttribute(): ?string
    {
        return null;
    }

    public function getChequeNoAttribute(): ?string
    {
        return null;
    }

    public function getAccountTypeAttribute(): string
    {
        return 'cash';
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->transaction_no)) {
                $model->transaction_no = 'CSH-' . str_pad(
                    (self::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT
                );
            }
        });
    }
}
