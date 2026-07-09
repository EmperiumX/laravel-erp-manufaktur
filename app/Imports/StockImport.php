<?php

namespace App\Imports;

use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithColumnLimit;

class StockImport implements ToModel, WithStartRow, SkipsEmptyRows, WithColumnLimit
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
        // Kolom B ke D (B=1, D=3)
        $endIndex = $this->startColumn + 2;
        return chr(65 + $endIndex);
    }

    public function model(array $row)
    {
        $col = $this->startColumn;

        $type = isset($row[$col]) ? trim($row[$col]) : ''; // "Bahan Baku" atau "Produk Jadi"
        $name = isset($row[$col + 1]) ? trim($row[$col + 1]) : '';
        $initialStock = isset($row[$col + 2]) ? trim($row[$col + 2]) : 0;

        if (empty($name) || empty($type)) {
            $this->skippedCount++;
            return null;
        }

        $stockItem = null;

        // Cocokkan Tipe
        if (strcasecmp($type, 'Bahan Baku') === 0 || strcasecmp($type, 'Bahan Baku / Kemasan') === 0) {
            $material = Material::where('name', $name)->first();
            if ($material) {
                $stockItem = StockItem::firstOrCreate(
                    ['material_id' => $material->id],
                    ['quantity' => 0]
                );
            }
        } elseif (strcasecmp($type, 'Produk Jadi') === 0) {
            $product = Product::where('name', $name)->first();
            if ($product) {
                $stockItem = StockItem::firstOrCreate(
                    ['product_id' => $product->id],
                    ['quantity' => 0]
                );
            }
        }

        if ($stockItem) {
            $oldQty = $stockItem->quantity;
            $newQty = floatval($initialStock);
            
            $stockItem->update(['quantity' => $newQty]);

            // Catat log pergerakan stok awal
            StockMovement::create([
                'stock_item_id' => $stockItem->id,
                'type' => 'IN',
                'quantity' => $newQty,
                'reference' => 'Import Stok Awal',
                'notes' => 'Import stok awal via Excel (Stok sebelumnya: ' . $oldQty . ')',
                'user_id' => Auth::id()
            ]);

            $this->importedCount++;
        } else {
            $this->skippedCount++;
        }

        return null;
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
