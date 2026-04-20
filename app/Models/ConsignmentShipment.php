<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsignmentShipment extends Model
{
    use HasFactory;

    protected $fillable =['shipment_number', 'store_id', 'shipment_date', 'status', 'total_amount', 'notes'];

    // Relasi ke Toko
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    // Relasi ke Item Detail
    public function items()
    {
        return $this->hasMany(ConsignmentItem::class);
    }
}