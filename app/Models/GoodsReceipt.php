<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceipt extends Model
{
    use HasFactory;

    protected $fillable = ['receipt_number', 'purchase_order_id', 'receipt_date', 'received_by', 'notes'];

    protected $casts = [
        'receipt_date' => 'date',
    ];

    // Relasi ke Purchase Order
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    // Relasi ke User yang menerima
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // Relasi ke item detail
    public function items()
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }
}
