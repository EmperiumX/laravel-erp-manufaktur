<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillOfMaterial extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'material_id', 'quantity'];

    // BOM ini milik produk apa?
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // BOM ini menggunakan bahan baku apa?
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}