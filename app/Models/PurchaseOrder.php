<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable =['po_number', 'supplier_id', 'order_date', 'status', 'total_amount', 'notes'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // Relasi ke Invoice (tagihan dari supplier)
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // Relasi ke Goods Receipt (penerimaan barang)
    public function goodsReceipts()
    {
        return $this->hasMany(GoodsReceipt::class);
    }
}
