<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProductionOrderController extends Controller
{
    public function index()
    {
        $productions = ProductionOrder::with('product')->latest()->get();
        return view('productions.index', compact('productions'));
    }

    public function create()
    {
        // Hanya ambil produk yang sudah punya resep (BOM) agar tidak error saat produksi
        $products = Product::has('boms')->get(); 
        
        return view('productions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'production_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        // Buat Nomor Produksi Otomatis (Format: PROD-YYYYMMDD-001)
        $today = date('Ymd');
        $countToday = ProductionOrder::whereDate('created_at', date('Y-m-d'))->count();
        $prodNumber = 'PROD-' . $today . '-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

        ProductionOrder::create([
            'production_number' => $prodNumber,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'production_date' => $request->production_date,
            'status' => 'Pending', // Belum diproduksi
            'notes' => $request->notes
        ]);

        return redirect()->route('productions.index')->with('success', 'Rencana Produksi berhasil dibuat! Status saat ini: Pending.');
    }

    public function show(ProductionOrder $production)
    {
        // Panggil relasi produk dan resep (BOM) nya
        $production->load('product.boms.material');
        
        $materialsNeeded =[];
        $canProduce = true; // Flag penanda apakah stok cukup atau tidak

        // Kalkulasi kebutuhan vs ketersediaan stok
        foreach($production->product->boms as $bom) {
            $requiredQty = $bom->quantity * $production->quantity; // Rumus: Resep x Target Produksi
            
            // Cek stok bahan baku di gudang
            $stock = \App\Models\StockItem::where('material_id', $bom->material_id)->first();
            $availableQty = $stock ? $stock->quantity : 0;
            
            $isEnough = $availableQty >= $requiredQty;
            
            if(!$isEnough) {
                $canProduce = false; // Jika ada 1 saja yang kurang, batalkan kemampuan produksi
            }

            $materialsNeeded[] =[
                'name' => $bom->material->name,
                'unit' => $bom->material->unit,
                'required' => $requiredQty,
                'available' => $availableQty,
                'is_enough' => $isEnough
            ];
        }

        return view('productions.show', compact('production', 'materialsNeeded', 'canProduce'));
    }

    public function markAsCompleted(ProductionOrder $production)
    {
        if ($production->status !== 'Pending') {
            return back()->with('error', 'Produksi ini sudah diproses atau dibatalkan.');
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 1. KURANGI STOK BAHAN BAKU (OUT)
            foreach($production->product->boms as $bom) {
                $requiredQty = $bom->quantity * $production->quantity;
                $stock = \App\Models\StockItem::where('material_id', $bom->material_id)->first();
                
                // Kurangi stoknya
                $stock->quantity -= $requiredQty;
                $stock->save();

                // Catat Log Pengurangan Bahan Baku
                \App\Models\StockMovement::create([
                    'stock_item_id' => $stock->id,
                    'type' => 'OUT',
                    'quantity' => $requiredQty,
                    'reference' => $production->production_number,
                    'notes' => 'Penggunaan bahan baku untuk produksi ' . $production->product->name,
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                ]);
            }

            // 2. TAMBAH STOK BARANG JADI (IN)
            // Cari stok produk ini, jika belum ada di gudang, buatkan baris baru
            $productStock = \App\Models\StockItem::firstOrCreate(
                ['product_id' => $production->product_id],
                ['quantity' => 0]
            );
            
            $productStock->quantity += $production->quantity;
            $productStock->save();

            // Catat Log Penambahan Barang Jadi
            \App\Models\StockMovement::create([
                'stock_item_id' => $productStock->id,
                'type' => 'IN',
                'quantity' => $production->quantity,
                'reference' => $production->production_number,
                'notes' => 'Hasil produksi barang jadi',
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
            ]);

            // 3. UBAH STATUS PRODUKSI JADI SELESAI
            $production->update(['status' => 'Completed']);

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('productions.index')->with('success', 'Produksi berhasil dieksekusi! Stok Gudang otomatis diperbarui.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal mengeksekusi produksi: ' . $e->getMessage());
        }
    }
    
    // ... sisanya biarkan kosong dulu
}