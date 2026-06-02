<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'account_number', 'bank_name',
        'balance', 'is_active', 'notes'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relasi ke transaksi
    public function transactions()
    {
        return $this->hasMany(CashBankTransaction::class);
    }

    // Relasi ke pembayaran
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scope hanya akun aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope tipe kas
    public function scopeCash($query)
    {
        return $query->where('type', 'Cash');
    }

    // Scope tipe bank
    public function scopeBank($query)
    {
        return $query->where('type', 'Bank');
    }

    // Label nama lengkap (contoh: "BCA - 1234567890")
    public function getFullNameAttribute()
    {
        if ($this->type === 'Bank' && $this->account_number) {
            return $this->name . ' - ' . $this->account_number;
        }
        return $this->name;
    }
}
