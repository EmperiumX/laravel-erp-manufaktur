<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsignmentReturnItem extends Model
{
    use HasFactory;

    protected $fillable =['consignment_return_id', 'product_id', 'quantity', 'condition'];

    // Relasi ke Produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}