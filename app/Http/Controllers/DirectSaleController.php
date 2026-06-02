<?php

namespace App\Http\Controllers;

use App\Models\DirectSale;
use App\Models\DirectSaleItem;
use App\Models\Store;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\SalesExport; 
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class DirectSaleController extends Controller
{
    public function index()
    {
        // Ambil data penjualan urut dari terbaru
        $sales = DirectSale::with('store')->latest()->get();
        return view('direct_sales.index', compact('sales'));
    }

    public function create()
    {
        // Ambil semua produk BESERTA harga 'End User' sebagai harga default
        // Nanti di JavaScript, kita bisa membebaskan kasir untuk mengubah harga ini
        $products = Product::with(['prices' => function($query) {
            $query->where('category', 'End User'); // Kita ambil patokan harga eceran tertinggi dulu
        }])->get();
        
        $stores = Store::all(); // Untuk opsi pembeli terdaftar (Reseller/dll)

        return view('direct_sales.create', compact('products', 'stores'));
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'sale_date' => 'required|date',
            'products' => 'required|array',
            'quantities' => 'required|array',
            'unit_prices' => 'required|array',
            // Pembeli bisa pilih toko, ATAU ketik nama manual
            'store_id' => 'nullable|exists:stores,id',
            'customer_name' => 'nullable|string|max:255',
        ]);

        // Cek agar setidaknya salah satu identitas pembeli diisi
        if (!$request->store_id && empty($request->customer_name)) {
            return back()->with('error', 'Silakan pilih Toko terdaftar, atau ketik nama Pembeli Umum!');
        }

        DB::beginTransaction();
        try {
            // 1. Generate Nomor Invoice Otomatis
            $today = date('Ymd');
            $countToday = DirectSale::whereDate('created_at', date('Y-m-d'))->count();
            $invNumber = 'INV-' . $today . '-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

            // 2. Simpan Header Penjualan
            $sale = DirectSale::create([
                'invoice_number' => $invNumber,
                'store_id' => $request->store_id,
                'customer_name' => $request->customer_name,
                'sale_date' => $request->sale_date,
                'total_amount' => 0,
                'notes' => $request->notes
            ]);

            $totalAmount = 0;

            // 3. Looping Item Penjualan
            foreach ($request->products as $key => $product_id) {
                $qty = $request->quantities[$key];
                $price = $request->unit_prices[$key];
                $subtotal = $qty * $price;

                // Simpan item
                DirectSaleItem::create([
                    'direct_sale_id' => $sale->id,
                    'product_id' => $product_id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $subtotal
                ]);

                $totalAmount += $subtotal;

                // 4. POTONG STOK GUDANG UTAMA
                $stock = StockItem::where('product_id', $product_id)->first();
                
                // Pengecekan stok
                if (!$stock || $stock->quantity < $qty) {
                    $productName = Product::find($product_id)->name;
                    throw new \Exception("Stok Gudang untuk produk '$productName' tidak mencukupi untuk penjualan ini! (Sisa: " . ($stock ? $stock->quantity : 0) . ")");
                }

                $stock->quantity -= $qty;
                $stock->save();

                // 5. Catat Mutasi Keluar
                StockMovement::create([
                    'stock_item_id' => $stock->id,
                    'type' => 'OUT',
                    'quantity' => $qty,
                    'reference' => $invNumber,
                    'notes' => 'Penjualan Langsung (Direct Sales)',
                    'user_id' => Auth::id(),
                ]);
            }

            // Update Total Nilai Invoice
            $sale->update(['total_amount' => $totalAmount]);

            // ===== AUTO-GENERATE INVOICE PENJUALAN =====
            $countInvToday = Invoice::where('type', 'sales')
                ->whereDate('created_at', date('Y-m-d'))
                ->count();
            $invoiceNumber = 'INV-S-' . $today . '-' . str_pad($countInvToday + 1, 3, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'type' => 'sales',
                'store_id' => $request->store_id,
                'direct_sale_id' => $sale->id,
                'invoice_date' => $request->sale_date,
                'due_date' => $request->sale_date,
                'subtotal' => $totalAmount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'paid_amount' => $totalAmount,
                'status' => 'Paid',
                'notes' => 'Auto-generated dari penjualan langsung ' . $invNumber,
                'created_by' => Auth::id(),
            ]);

            // Buat item invoice dari item penjualan langsung
            foreach ($request->products as $key => $product_id) {
                $product = Product::find($product_id);
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $product->name,
                    'product_id' => $product_id,
                    'quantity' => $request->quantities[$key],
                    'unit' => 'pcs',
                    'unit_price' => $request->unit_prices[$key],
                    'subtotal' => $request->quantities[$key] * $request->unit_prices[$key],
                ]);
            }

            DB::commit();

            return redirect()->route('direct-sales.index')->with('success', 'Transaksi Penjualan berhasil disimpan! Stok otomatis terpotong.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Transaksi Gagal: ' . $e->getMessage());
        }
    }

    // FUNGSI BARU: Download Laporan Penjualan
    public function exportExcel(Request $request)
    {
        // Validasi pastikan tanggal diisi
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $fileName = 'Laporan_Penjualan_' . $request->start_date . '_sd_' . $request->end_date . '.xlsx';
        
        // Lempar variabel tanggal ke class Export
        return Excel::download(new SalesExport($request->start_date, $request->end_date), $fileName);
    }

    // FUNGSI BARU: Cetak Nota PDF
    public function print(DirectSale $directSale)
    {
        // Panggil relasi item belanja dan relasi toko
        $directSale->load('items.product', 'store');

        // Load view khusus PDF dan kirim datanya
        $pdf = Pdf::loadView('direct_sales.print', compact('directSale'));
        
        // Atur ukuran kertas (misal A4 atau setruk kasir), di sini kita pakai A4 portrait
        $pdf->setPaper('A4', 'portrait');

        // Gunakan stream() agar PDF terbuka di tab baru browser (bisa diprint/didownload manual)
        // Gunakan download() jika ingin langsung terunduh ke komputer
        return $pdf->stream('Nota_Penjualan_' . $directSale->invoice_number . '.pdf');
    }
}