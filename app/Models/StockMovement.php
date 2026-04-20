<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable =['stock_item_id', 'type', 'quantity', 'reference', 'notes', 'user_id'];

    // Relasi ke item stok (Barang apa yang bergerak)
    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    // Relasi ke user (Siapa admin yang melakukan pergerakan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}