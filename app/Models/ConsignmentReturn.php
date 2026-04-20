<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsignmentReturn extends Model
{
    use HasFactory;

    protected $fillable =['return_number', 'store_id', 'return_date', 'notes'];

    // Relasi ke Toko
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    // Relasi ke Item Detail
    public function items()
    {
        return $this->hasMany(ConsignmentReturnItem::class);
    }
}