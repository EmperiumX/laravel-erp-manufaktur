<?php

namespace App\Imports;

use App\Models\Store;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithColumnLimit;

class StoreImport implements ToModel, WithStartRow, SkipsEmptyRows, WithColumnLimit
{
    private int $startRow;
    private int $startColumn; // Index berbasis 0 (kolom 2 = index 1)
    private int $importedCount = 0;
    private int $skippedCount = 0;

    // Daftar kategori yang valid sesuai enum di database
    private const VALID_CATEGORIES = ['Mitra', 'Agen', 'Distributor', 'Reseller', 'End User', 'Maklon'];

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
     * Batasi kolom yang dibaca
     */
    public function endColumn(): string
    {
        $endIndex = $this->startColumn + 3;
        return chr(65 + $endIndex);
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $col = $this->startColumn;

        // Ambil nama dari kolom yang ditentukan
        $name = isset($row[$col]) ? trim($row[$col]) : '';

        // Skip baris kosong
        if (empty($name)) {
            $this->skippedCount++;
            return null;
        }

        // Validasi kategori - default ke 'Mitra' jika tidak valid
        $category = isset($row[$col + 1]) ? trim($row[$col + 1]) : 'Mitra';
        if (!in_array($category, self::VALID_CATEGORIES)) {
            $category = 'Mitra';
        }

        $this->importedCount++;

        return new Store([
            'name'         => $name,
            'category'     => $category,
            'phone_number' => isset($row[$col + 2]) ? trim((string) $row[$col + 2]) : null,
            'address'      => isset($row[$col + 3]) ? trim($row[$col + 3]) : null,
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
     * Jumlah data yang di-skip
     */
    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}
