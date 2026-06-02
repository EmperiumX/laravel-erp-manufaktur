<?php

namespace App\Exports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MaterialExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Material::all();
    }

    public function headings(): array
    {
        return ['ID', 'Nama Bahan', 'Tipe', 'Satuan', 'Harga Satuan (Rp)', 'Created At'];
    }

    public function map($material): array
    {
        return [
            $material->id,
            $material->name,
            $material->type,
            $material->unit,
            $material->unit_price,
            $material->created_at ? $material->created_at->format('Y-m-d H:i') : '',
        ];
    }
}
