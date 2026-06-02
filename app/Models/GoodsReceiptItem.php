<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = ['goods_receipt_id', 'material_id', 'quantity_ordered', 'quantity_received', 'notes'];

    // Relasi ke Goods Receipt
    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    // Relasi ke Material
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
