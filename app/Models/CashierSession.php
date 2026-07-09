<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashierSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cash_bank_id',
        'opening_cash',
        'expected_cash',
        'closing_cash',
        'difference',
        'status',
        'notes',
        'opening_date',
        'closing_date'
    ];

    protected $casts = [
        'opening_date' => 'datetime',
        'closing_date' => 'datetime',
        'opening_cash' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'closing_cash' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashBank()
    {
        return $this->belongsTo(CashBank::class);
    }

    public function directSales()
    {
        return $this->hasMany(DirectSale::class, 'cashier_session_id');
    }
}
