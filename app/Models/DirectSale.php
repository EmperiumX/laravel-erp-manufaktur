<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectSale extends Model
{
    use HasFactory;

    protected $fillable =['invoice_number', 'store_id', 'customer_name', 'sale_date', 'total_amount', 'notes'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function items()
    {
        return $this->hasMany(DirectSaleItem::class);
    }
}