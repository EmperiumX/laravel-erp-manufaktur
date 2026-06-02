<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaterialTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return ['No', 'Nama Bahan', 'Tipe (Bahan Pokok/Bahan Penolong/Packaging)', 'Satuan', 'Harga Satuan (Rp)'];
    }

    public function array(): array
    {
        return [
            [1, 'Tepung Terigu', 'Bahan Pokok', 'kg', 12000],
            [2, 'Gula Pasir', 'Bahan Pokok', 'kg', 15000],
            [3, 'Pewarna Makanan Hijau', 'Bahan Penolong', 'botol', 8000],
            [4, 'Karton Box Citra', 'Packaging', 'pcs', 2500],
        ];
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
