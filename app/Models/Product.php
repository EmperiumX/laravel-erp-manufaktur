<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['sku', 'name', 'weight', 'weight_unit', 'packaging', 'hpp', 'labor_cost', 'overhead_cost', 'other_cost'];

    // Relasi One-to-Many ke tabel harga
    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }
    // Relasi One-to-Many ke tabel BOM (Resep)
    public function boms()
    {
        return $this->hasMany(BillOfMaterial::class);
    }
}