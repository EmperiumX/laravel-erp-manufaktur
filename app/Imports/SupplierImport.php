<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithColumnLimit;

class SupplierImport implements ToModel, WithStartRow, SkipsEmptyRows, WithColumnLimit
{
    private int $startRow;
    private int $startColumn; // Index berbasis 0 (kolom 2 = index 1)
    private int $importedCount = 0;
    private int $skippedCount = 0;
    private array $errors = [];

    /**
     * Constructor fleksibel - bisa diset start row & start column
     * Default: startRow = 2 (baris 2), startColumn = 1 (kolom B / kolom 2)
     */
    public function __construct(int $startRow = 2, int $startColumn = 1)
    {
        $this->startRow = $startRow;
        $this->startColumn = $startColumn;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return $this->startRow;
    }

    /**
     * Batasi kolom yang dibaca agar lebih efisien
     */
    public function endColumn(): string
    {
        // Kolom start + 4 field (name, contact_person, phone, address)
        // Kalau start di B(1), maka end di E(4) -> kolom index 4 = 'E'
        $endIndex = $this->startColumn + 3;
        return chr(65 + $endIndex); // Convert ke huruf kolom (A=65)
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $col = $this->startColumn;

        // Ambil nama dari kolom yang ditentukan (default: kolom 2 / index 1)
        $name = isset($row[$col]) ? trim($row[$col]) : '';

        // Skip baris yang nama-nya kosong
        if (empty($name)) {
            $this->skippedCount++;
            return null;
        }

        $this->importedCount++;

        return new Supplier([
            'name'           => $name,
            'contact_person' => isset($row[$col + 1]) ? trim($row[$col + 1]) : null,
            'phone_number'   => isset($row[$col + 2]) ? trim((string) $row[$col + 2]) : null,
            'address'        => isset($row[$col + 3]) ? trim($row[$col + 3]) : null,
        ]);
    }

    /**
     * Jumlah data yang berhasil di-import
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /**
     * Jumlah data yang di-skip (kosong)
     */
    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}
