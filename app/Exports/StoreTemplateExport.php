<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StoreTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return ['No', 'Nama Toko/Mitra', 'Kategori', 'No. HP/Telepon', 'Alamat'];
    }

    public function array(): array
    {
        // Contoh data agar user paham formatnya
        return [
            [1, 'Toko Istana Buah', 'Mitra', '081234567890', 'Jl. Pasar No. 10, Surabaya'],
            [2, 'CV. Sinar Jaya Distributor', 'Distributor', '087654321098', 'Jl. Raya No. 5, Malang'],
            ['', '', 'Pilihan: Mitra, Agen, Distributor, Reseller, End User, Maklon', '', ''],
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
            // Baris 4 (info kategori) italic abu-abu
            4 => [
                'font' => ['italic' => true, 'color' => ['rgb' => '808080']],
            ],
        ];
    }
}
