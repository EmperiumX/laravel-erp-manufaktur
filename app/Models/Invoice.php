<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number', 'type', 'store_id', 'supplier_id',
        'purchase_order_id', 'consignment_shipment_id', 'direct_sale_id',
        'invoice_date', 'due_date', 'subtotal', 'tax_amount',
        'discount_amount', 'total_amount', 'paid_amount', 'status',
        'notes', 'created_by'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    // Relasi ke Toko/Mitra (untuk invoice sales/piutang)
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    // Relasi ke Supplier (untuk invoice purchase/hutang)
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relasi ke Purchase Order
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    // Relasi ke Consignment Shipment (DO)
    public function consignmentShipment()
    {
        return $this->belongsTo(ConsignmentShipment::class);
    }

    // Relasi ke Direct Sale
    public function directSale()
    {
        return $this->belongsTo(DirectSale::class);
    }

    // Relasi ke Item Detail
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Relasi ke Pembayaran
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Relasi ke User yang membuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Hitung sisa yang harus dibayar
    public function getBalanceDueAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    // Cek apakah sudah lunas
    public function getIsPaidAttribute()
    {
        return $this->paid_amount >= $this->total_amount;
    }

    // Cek apakah sudah jatuh tempo
    public function getIsOverdueAttribute()
    {
        return $this->due_date->isPast() && !$this->is_paid;
    }

    // Scope untuk invoice piutang (sales)
    public function scopeSales($query)
    {
        return $query->where('type', 'sales');
    }

    // Scope untuk invoice hutang (purchase)
    public function scopePurchase($query)
    {
        return $query->where('type', 'purchase');
    }

    // Scope untuk yang belum lunas
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['Sent', 'Partial', 'Overdue']);
    }
}
