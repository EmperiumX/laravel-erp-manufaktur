<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use HasFactory;

    protected $fillable = ['material_id', 'product_id', 'quantity'];

    // Relasi ke Material (Bahan Baku)
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    // Relasi ke Product (Barang Jadi)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}