<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 'description', 'material_id', 'product_id',
        'quantity', 'unit', 'unit_price', 'subtotal'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relasi ke Invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Relasi ke Material (opsional)
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    // Relasi ke Product (opsional)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
