<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number', 'invoice_id', 'type', 'amount',
        'payment_date', 'payment_method', 'reference',
        'cash_bank_id', 'notes', 'created_by'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relasi ke Invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Relasi ke Akun Kas/Bank
    public function cashBank()
    {
        return $this->belongsTo(CashBank::class);
    }

    // Relasi ke User yang membuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke transaksi kas/bank
    public function cashBankTransaction()
    {
        return $this->hasOne(CashBankTransaction::class);
    }

    // Scope pembayaran masuk (piutang diterima)
    public function scopeInbound($query)
    {
        return $query->where('type', 'inbound');
    }

    // Scope pembayaran keluar (hutang dibayar)
    public function scopeOutbound($query)
    {
        return $query->where('type', 'outbound');
    }
}
