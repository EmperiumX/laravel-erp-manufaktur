<?php

namespace App\Http\Controllers;

use App\Models\ConsignmentShipment;
use App\Models\ConsignmentItem;
use App\Models\Store;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsignmentController extends Controller
{
    public function index()
    {
        // Ambil data Surat Jalan (DO) urut dari yang terbaru
        $shipments = ConsignmentShipment::with('store')->latest()->get();
        return view('consignments.index', compact('shipments'));
    }

    public function create()
    {
        $stores = Store::all();
        // SANGAT PENTING: Kita memuat data produk BESERTA struktur harganya
        // Ini akan digunakan oleh JavaScript nanti untuk menentukan harga otomatis
        $products = Product::with('prices')->get(); 
        
        return view('consignments.create', compact('stores', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'shipment_date' => 'required|date',
            'products' => 'required|array',
            'quantities' => 'required|array',
            'unit_prices' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            // 1. Generate Nomor DO Otomatis (Contoh: DO-20260312-001)
            $today = date('Ymd');
            $countToday = ConsignmentShipment::whereDate('created_at', date('Y-m-d'))->count();
            $doNumber = 'DO-' . $today . '-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

            // 2. Simpan Header Pengiriman
            $shipment = ConsignmentShipment::create([
                'shipment_number' => $doNumber,
                'store_id' => $request->store_id,
                'shipment_date' => $request->shipment_date,
                'status' => 'Sent', // Barang dikirim/dititipkan
                'total_amount' => 0,
                'notes' => $request->notes
            ]);

            $totalAmount = 0;

            // 3. Looping Barang yang Dikirim
            foreach ($request->products as $key => $product_id) {
                $qty = $request->quantities[$key];
                $price = $request->unit_prices[$key];
                $subtotal = $qty * $price;

                // A. Simpan detail barang ke surat jalan
                ConsignmentItem::create([
                    'consignment_shipment_id' => $shipment->id,
                    'product_id' => $product_id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $subtotal
                ]);

                $totalAmount += $subtotal;

                // B. Kurangi Stok Barang Jadi di Gudang Utama (OUT)
                $stock = StockItem::where('product_id', $product_id)->first();
                
                // Jika stok belum ada, atau stok kurang dari yang mau dikirim: BATALKAN!
                if (!$stock || $stock->quantity < $qty) {
                    $productName = Product::find($product_id)->name;
                    throw new \Exception("Stok Gudang untuk produk '$productName' tidak mencukupi! (Sisa: " . ($stock ? $stock->quantity : 0) . ")");
                }

                $stock->quantity -= $qty;
                $stock->save();

                // C. Catat Log Pergerakan Gudang
                StockMovement::create([
                    'stock_item_id' => $stock->id,
                    'type' => 'OUT',
                    'quantity' => $qty,
                    'reference' => $doNumber, // Catat nomor surat jalannya
                    'notes' => 'Pengiriman barang ke Toko / Konsinyasi',
                    'user_id' => Auth::id(),
                ]);
            }

            // 4. Update Total Nilai Surat Jalan
            $shipment->update(['total_amount' => $totalAmount]);

            DB::commit(); // Simpan permanen!

            return redirect()->route('consignments.index')->with('success', 'Surat Jalan (DO) berhasil dibuat! Stok barang jadi di gudang telah otomatis dikurangi.');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua jika ada error/stok kurang
            return back()->with('error', 'Gagal memproses pengiriman: ' . $e->getMessage());
        }
    }

    // FUNGSI BARU: Cetak Nota PDF
    // FUNGSI BARU: Cetak Surat Jalan PDF
    public function print(ConsignmentShipment $consignment)
    {
        // Panggil relasi item dan toko
        $consignment->load('items.product', 'store');

        // Load view khusus PDF dan kirim datanya
        $pdf = Pdf::loadView('consignments.print', compact('consignment'));
        
        // Atur ukuran kertas (A4 Portrait)
        $pdf->setPaper('A4', 'portrait');

        // Tampilkan di tab baru
        return $pdf->stream('Surat_Jalan_' . $consignment->shipment_number . '.pdf');
    }
}