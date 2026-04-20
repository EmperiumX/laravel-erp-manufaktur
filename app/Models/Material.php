<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable =[
        'name', 
        'type', 
        'unit', 
        'unit_price'
    ];
    // Relasi One-to-Many ke tabel BOM (Melihat bahan baku ini dipakai di resep mana saja)
    public function boms()
    {
        return $this->hasMany(BillOfMaterial::class);
    }
}