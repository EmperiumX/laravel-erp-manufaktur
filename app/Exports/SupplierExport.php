<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupplierExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Supplier::all();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Contact Person', 'Phone Number', 'Address', 'Created At'];
    }

    public function map($supplier): array
    {
        return [
            $supplier->id,
            $supplier->name,
            $supplier->contact_person,
            $supplier->phone_number,
            $supplier->address,
            $supplier->created_at ? $supplier->created_at->format('Y-m-d H:i') : '',
        ];
    }
}
