<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use App\Exports\StockExport; 
use Maatwebsite\Excel\Facades\Excel;

class InventoryController extends Controller
{
    // Halaman 1: Melihat Stok Saat Ini
    public function index()
    {
        // Ambil semua data stok, sertakan relasi material dan produknya
        $stocks = StockItem::with(['material', 'product'])->get();
        return view('inventory.index', compact('stocks'));
    }

    // Halaman 2: Melihat Riwayat Pergerakan Stok (Kartu Stok)
    public function history()
    {
        // Ambil log pergerakan, urutkan dari yang paling baru (latest), sertakan relasi
        $movements = StockMovement::with(['stockItem.material', 'stockItem.product', 'user'])
                        ->latest()
                        ->get();
                        
        return view('inventory.history', compact('movements'));
    }

    // FUNGSI BARU: Download Excel
    public function exportExcel()
    {
        // Mengunduh file dengan nama Laporan_Stok_Gudang.xlsx
        return Excel::download(new StockExport, 'Laporan_Stok_Gudang_'.date('Ymd').'.xlsx');
    }
}