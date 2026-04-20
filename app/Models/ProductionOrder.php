<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $fillable =['production_number', 'product_id', 'quantity', 'production_date', 'status', 'notes'];

    // Relasi ke Produk (Barang Jadi)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}