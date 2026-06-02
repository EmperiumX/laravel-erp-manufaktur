<?php

namespace App\Exports;

use App\Models\Store;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StoreExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Store::all();
    }

    public function headings(): array
    {
        return ['ID', 'Name', 'Category', 'Phone Number', 'Address', 'Created At'];
    }

    public function map($store): array
    {
        return [
            $store->id,
            $store->name,
            $store->category,
            $store->phone_number,
            $store->address,
            $store->created_at ? $store->created_at->format('Y-m-d H:i') : '',
        ];
    }
}
