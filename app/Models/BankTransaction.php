<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    protected $table = 'bank_transactions';

    protected $fillable = [
        'transaction_no', 'type', 'transaction_method', 'account_type', 'bank_id',
        'amount', 'description', 'reference_no', 'customer_id', 'order_id', 'payment_id',
        'expense_id', 'cheque_id', 'cheque_no', 'bank_name',
        'transaction_date', 'running_balance', 'notes'
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

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

    public function cheque()
    {
        return $this->belongsTo(Cheque::class);
    }

    protected $casts = [
        'amount' => 'decimal:2',
        'running_balance' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->transaction_no)) {
                $model->transaction_no = 'BNK-' . str_pad(
                    (self::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT
                );
            }
        });
    }
}
