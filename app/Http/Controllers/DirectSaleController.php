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
        // Validasi Sesi Kasir Aktif
        $activeSession = \App\Models\CashierSession::where('user_id', Auth::id())
            ->where('status', 'Open')
            ->first();

        if (!$activeSession) {
            return redirect()->route('cashier-sessions.create')
                ->with('error', 'Akses Ditolak! Anda harus membuka Sesi Kasir terlebih dahulu sebelum melayani penjualan.');
        }

        $products = Product::with('prices')->get();
        $stores = Store::all(); // Untuk opsi pembeli terdaftar (Reseller/dll)

        return view('direct_sales.create', compact('products', 'stores'));
    }

    public function store(Request $request)
    {
        // Validasi Sesi Kasir Aktif
        $activeSession = \App\Models\CashierSession::where('user_id', Auth::id())
            ->where('status', 'Open')
            ->first();

        if (!$activeSession) {
            return back()->with('error', 'Transaksi gagal: Sesi kasir aktif tidak ditemukan. Silakan buka sesi terlebih dahulu.');
        }

        // Validasi
        $request->validate([
            'sale_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*' => 'required|exists:products,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|numeric|min:1',
            'unit_prices' => 'required|array|min:1',
            'unit_prices.*' => 'required|numeric|min:0',
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
                'notes' => $request->notes,
                'cashier_session_id' => $activeSession->id,
                'cashier_id' => Auth::id(),
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

            // ===== AUTO-GENERATE PAYMENT & MUTASI KAS MASUK =====
            $countPayToday = \App\Models\Payment::whereDate('created_at', date('Y-m-d'))->count();
            $paymentNumber = 'PAY-' . $today . '-' . str_pad($countPayToday + 1, 3, '0', STR_PAD_LEFT);

            $payment = \App\Models\Payment::create([
                'payment_number' => $paymentNumber,
                'invoice_id' => $invoice->id,
                'type' => 'inbound',
                'amount' => $totalAmount,
                'payment_date' => $request->sale_date,
                'payment_method' => 'Cash',
                'cash_bank_id' => $activeSession->cash_bank_id,
                'notes' => 'Pembayaran otomatis Penjualan Langsung ' . $invNumber,
                'created_by' => Auth::id(),
            ]);

            // Update Saldo Kas/Bank
            $cashBank = \App\Models\CashBank::findOrFail($activeSession->cash_bank_id);
            $cashBank->balance += $totalAmount;
            $cashBank->save();

            // Catat Transaksi Kas/Bank
            \App\Models\CashBankTransaction::create([
                'cash_bank_id' => $cashBank->id,
                'type' => 'Debit',
                'amount' => $totalAmount,
                'balance_after' => $cashBank->balance,
                'transaction_date' => $request->sale_date,
                'reference' => $paymentNumber,
                'description' => 'Penjualan langsung kasir #' . $sale->invoice_number,
                'category' => 'Penjualan',
                'is_reconciled' => false,
                'payment_id' => $payment->id,
                'created_by' => Auth::id(),
            ]);

            // Update Expected Cash Sesi Kasir
            $activeSession->expected_cash += $totalAmount;
            $activeSession->save();

            DB::commit();

            return redirect()->route('direct-sales.index')->with('success', 'Transaksi Penjualan berhasil disimpan! Stok otomatis terpotong dan mutasi kas berhasil dicatat.');

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