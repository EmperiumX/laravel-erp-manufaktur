<?php

namespace App\Exports;

use App\Models\StockItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StockExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * Mengambil data dari database
    */
    public function collection()
    {
        // Kita ambil semua stok beserta relasi material & produknya
        return StockItem::with(['material', 'product'])->get();
    }

    /**
    * Menentukan Judul Kolom (Baris Pertama di Excel)
    */
    public function headings(): array
    {
        return[
            'No',
            'Tipe Barang',
            'Nama Barang',
            'Kategori / Satuan',
            'Kuantitas Tersedia'
        ];
    }

    /**
    * Memetakan data ke dalam baris Excel (Satu array ini = 1 baris di Excel)
    */
    public function map($stock): array
    {
        // Bikin nomor urut statis
        static $rowNumber = 0;
        $rowNumber++;

        // Cek ini material atau produk?
        $type = $stock->material ? 'Bahan Baku / Kemasan' : ($stock->product ? 'Produk Jadi' : '-');
        $name = $stock->material ? $stock->material->name : ($stock->product ? $stock->product->name : '-');
        $unit = $stock->material ? $stock->material->unit : ($stock->product ? $stock->product->packaging : '-');

        return[
            $rowNumber,
            $type,
            $name,
            $unit,
            // Format angka desimal agar rapi
            rtrim(rtrim(number_format($stock->quantity, 4, ',', ''), '0'), ',')
        ];
    }
}