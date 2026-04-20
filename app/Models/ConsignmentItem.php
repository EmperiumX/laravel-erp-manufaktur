<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsignmentItem extends Model
{
    use HasFactory;

    protected $fillable =['consignment_shipment_id', 'product_id', 'quantity', 'unit_price', 'subtotal'];

    // Relasi ke Produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}