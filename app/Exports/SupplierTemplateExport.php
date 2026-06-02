<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplierTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return ['No', 'Nama Supplier', 'Contact Person', 'No. HP/Telepon', 'Alamat'];
    }

    public function array(): array
    {
        // Contoh data agar user paham formatnya
        return [
            [1, 'PT. Bahan Baku Sejahtera', 'Budi Santoso', '081234567890', 'Jl. Industri No. 10, Surabaya'],
            [2, 'CV. Maju Jaya', 'Siti Aminah', '087654321098', 'Jl. Raya Malang No. 5, Malang'],
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
