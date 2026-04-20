<?php

namespace App\Http\Controllers;

use App\Models\ConsignmentReturn;
use App\Models\ConsignmentReturnItem;
use App\Models\Store;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ConsignmentReturnController extends Controller
{
    public function index()
    {
        // Ambil data Retur urut dari yang terbaru
        $returns = ConsignmentReturn::with('store')->latest()->get();
        return view('returns.index', compact('returns'));
    }

    public function create()
    {
        $stores = Store::all();
        $products = Product::all(); // Untuk retur, kita tidak butuh harga, hanya nama produknya
        return view('returns.create', compact('stores', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'return_date' => 'required|date',
            'products' => 'required|array',
            'quantities' => 'required|array',
            'conditions' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            // 1. Generate Nomor Retur Otomatis
            $today = date('Ymd');
            $countToday = ConsignmentReturn::whereDate('created_at', date('Y-m-d'))->count();
            $retNumber = 'RET-' . $today . '-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

            // 2. Simpan Header Retur
            $return = ConsignmentReturn::create([
                'return_number' => $retNumber,
                'store_id' => $request->store_id,
                'return_date' => $request->return_date,
                'notes' => $request->notes
            ]);

            // 3. Looping Barang yang Diretur
            foreach ($request->products as $key => $product_id) {
                $qty = $request->quantities[$key];
                $condition = $request->conditions[$key];

                // A. Simpan item retur
                ConsignmentReturnItem::create([
                    'consignment_return_id' => $return->id,
                    'product_id' => $product_id,
                    'quantity' => $qty,
                    'condition' => $condition // Bagus atau Rusak/Basi
                ]);

                // B. Kembalikan Stok ke Gudang Utama (IN)
                $stock = StockItem::firstOrCreate(['product_id' => $product_id],
                    ['quantity' => 0]
                );

                $stock->quantity += $qty;
                $stock->save();

                // C. Catat Log Pergerakan Gudang
                StockMovement::create([
                    'stock_item_id' => $stock->id,
                    'type' => 'IN',
                    'quantity' => $qty,
                    'reference' => $retNumber,
                    'notes' => 'Retur barang dari toko (' . $condition . ')',
                    'user_id' => Auth::id(),
                ]);
            }

            DB::commit(); // Simpan permanen

            return redirect()->route('returns.index')->with('success', 'Data Retur berhasil diproses! Stok barang jadi di gudang telah otomatis bertambah kembali.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses retur: ' . $e->getMessage());
        }
    }
}