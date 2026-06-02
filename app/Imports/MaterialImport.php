<?php

namespace App\Imports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithColumnLimit;

class MaterialImport implements ToModel, WithStartRow, SkipsEmptyRows, WithColumnLimit
{
    private int $startRow;
    private int $startColumn; // Index berbasis 0 (kolom B = index 1)
    private int $importedCount = 0;
    private int $skippedCount = 0;

    public function __construct(int $startRow = 2, int $startColumn = 1)
    {
        $this->startRow = $startRow;
        $this->startColumn = $startColumn;
    }

    public function startRow(): int
    {
        return $this->startRow;
    }

    public function endColumn(): string
    {
        // Kolom B ke E (B=1, E=4)
        $endIndex = $this->startColumn + 3;
        return chr(65 + $endIndex);
    }

    public function model(array $row)
    {
        $col = $this->startColumn;

        $name = isset($row[$col]) ? trim($row[$col]) : '';
        $type = isset($row[$col + 1]) ? trim($row[$col + 1]) : '';
        $unit = isset($row[$col + 2]) ? trim($row[$col + 2]) : '';
        $unitPrice = isset($row[$col + 3]) ? trim($row[$col + 3]) : 0;

        if (empty($name)) {
            $this->skippedCount++;
            return null;
        }

        // Validasi tipe
        if (!in_array($type, ['Bahan Pokok', 'Bahan Penolong', 'Packaging'])) {
            $type = 'Bahan Pokok'; // Fallback
        }

        $this->importedCount++;

        return new Material([
            'name'       => $name,
            'type'       => $type,
            'unit'       => $unit,
            'unit_price' => floatval($unitPrice),
        ]);
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}
