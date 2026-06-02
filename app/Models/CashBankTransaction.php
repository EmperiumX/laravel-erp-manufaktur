<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBankTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_bank_id', 'type', 'amount', 'balance_after',
        'transaction_date', 'reference', 'description', 'category',
        'is_reconciled', 'payment_id', 'created_by'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'is_reconciled' => 'boolean',
    ];

    // Relasi ke Akun Kas/Bank
    public function cashBank()
    {
        return $this->belongsTo(CashBank::class);
    }

    // Relasi ke Payment (opsional)
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    // Relasi ke User yang membuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scope belum direkonsiliasi
    public function scopeUnreconciled($query)
    {
        return $query->where('is_reconciled', false);
    }

    // Scope sudah direkonsiliasi
    public function scopeReconciled($query)
    {
        return $query->where('is_reconciled', true);
    }

    // Scope debit (masuk)
    public function scopeDebit($query)
    {
        return $query->where('type', 'Debit');
    }

    // Scope credit (keluar)
    public function scopeCredit($query)
    {
        return $query->where('type', 'Credit');
    }
}
