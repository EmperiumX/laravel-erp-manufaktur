<?php

namespace App\Exports;

use App\Models\Material;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return ['No', 'Tipe Kategori (Bahan Baku / Produk Jadi)', 'Nama Barang', 'Stok Awal'];
    }

    public function array(): array
    {
        $data = [];
        $no = 1;

        // Ambil data bahan baku yang ada
        $materials = Material::all();
        foreach ($materials as $material) {
            $data[] = [
                $no++,
                'Bahan Baku',
                $material->name,
                $material->stockItem->quantity ?? 0
            ];
        }

        // Ambil data produk jadi yang ada
        $products = Product::all();
        foreach ($products as $product) {
            $data[] = [
                $no++,
                'Produk Jadi',
                $product->name,
                $product->stockItem->quantity ?? 0
            ];
        }

        // Jika kosong, berikan contoh
        if (empty($data)) {
            $data = [
                [1, 'Bahan Baku', 'Tepung Terigu', 100],
                [2, 'Produk Jadi', 'Bandeng Retort 250g', 50]
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
            ],
        ];
    }
}
