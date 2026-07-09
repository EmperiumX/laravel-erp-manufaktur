<?php

namespace App\Exports;

use App\Models\DirectSale;
use Maatwebsite\Excel\Concerns\FromQuery; // Kita pakai FromQuery agar mudah di-filter
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SalesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    // Menangkap lemparan tanggal dari Controller
    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        // Filter data berdasarkan rentang tanggal
        return DirectSale::query()
            ->with('store')
            ->whereBetween('sale_date', [$this->startDate, $this->endDate])
            ->orderBy('sale_date', 'asc');
    }

    public function headings(): array
    {
        return[
            'Tanggal Transaksi',
            'No. Invoice / Nota',
            'Tipe Pembeli',
            'Nama Pembeli / Toko',
            'Catatan',
            'Total Belanja (Rp)'
        ];
    }

    public function map($sale): array
    {
        // Pengecekan nama pembeli
        $buyerType = $sale->store_id ? 'Mitra Terdaftar' : 'Pembeli Umum';
        $buyerName = $sale->store_id ? ($sale->store?->name ?? 'Toko Dihapus') : $sale->customer_name;

        return[
            \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y'),
            $sale->invoice_number,
            $buyerType,
            $buyerName,
            $sale->notes ?? '-',
            $sale->total_amount // Biarkan berupa angka agar bisa di-sum/dijumlah di Excel
        ];
    }
}