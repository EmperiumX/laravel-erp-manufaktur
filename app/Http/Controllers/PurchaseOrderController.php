<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Material;
use App\Models\StockItem; 
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Untuk database transaction
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        // Ambil data PO urut dari yang terbaru, bawa serta data relasi supplier-nya
        $purchaseOrders = PurchaseOrder::with('supplier')->latest()->get();
        return view('purchase_orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $materials = Material::all();
        return view('purchase_orders.create', compact('suppliers', 'materials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'materials' => 'required|array', // Array id material
            'quantities' => 'required|array', // Array jumlah
            'unit_prices' => 'required|array', // Array harga beli
        ]);

        // Gunakan DB Transaction agar jika terjadi error di tengah jalan, data di-rollback (dibatalkan) semua
        DB::beginTransaction();
        try {
            // 1. Buat Nomor PO Otomatis (Format: PO-YYYYMMDD-001)
            $today = date('Ymd');
            $countToday = PurchaseOrder::whereDate('created_at', date('Y-m-d'))->count();
            $poNumber = 'PO-' . $today . '-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

            // 2. Simpan Header PO
            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'status' => 'Pending', // Barang belum datang
                'total_amount' => 0, // Akan dihitung di bawah
                'notes' => $request->notes
            ]);

            $totalAmount = 0;

            // 3. Simpan Detail Item (Looping)
            foreach ($request->materials as $key => $material_id) {
                $qty = $request->quantities[$key];
                $price = $request->unit_prices[$key];
                $subtotal = $qty * $price;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'material_id' => $material_id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $subtotal
                ]);

                $totalAmount += $subtotal;
            }

            // 4. Update Total Amount di Header PO
            $po->update(['total_amount' => $totalAmount]);

            DB::commit(); // Simpan permanen ke database

            // Poin 2: Redirect langsung ke halaman detail PO agar pengguna tidak bolak-balik menu
            return redirect()->route('purchase-orders.show', $po->id)->with('success', 'Purchase Order berhasil dibuat! Silakan lanjutkan ke langkah Penerimaan Barang.');
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan jika ada error
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        // Muat relasi supplier dan items (beserta materialnya)
        $purchaseOrder->load('supplier', 'items.material');

        // Cari invoice pembelian yang terkait dengan PO ini (jika sudah ada)
        $invoice = Invoice::with('items')->where('purchase_order_id', $purchaseOrder->id)->first();

        return view('purchase_orders.show', compact('purchaseOrder', 'invoice'));
    }

    public function markAsCompleted(PurchaseOrder $purchaseOrder)
    {
        // Cegah eksekusi ganda jika status sudah bukan Pending
        if ($purchaseOrder->status !== 'Pending') {
            return back()->with('error', 'PO ini sudah diproses atau dibatalkan.');
        }

        DB::beginTransaction();
        try {
            // 1. Ubah status PO menjadi Completed
            $purchaseOrder->update(['status' => 'Completed']);

            // 2. Looping semua barang yang dibeli di PO ini
            foreach ($purchaseOrder->items as $item) {
                
                // Cari apakah material ini sudah ada di gudang? Kalau belum, buatkan record baru (kuantitas 0)
                $stock = StockItem::firstOrCreate(['material_id' => $item->material_id],
                    ['quantity' => 0]
                );

                // Tambahkan kuantitas stok saat ini dengan kuantitas yang dibeli
                $stock->quantity += $item->quantity;
                $stock->save();

                // 3. Catat di Log Pergerakan Gudang (Audit Trail)
                StockMovement::create([
                    'stock_item_id' => $stock->id,
                    'type' => 'IN', // Barang Masuk
                    'quantity' => $item->quantity,
                    'reference' => $purchaseOrder->po_number, // Referensi dari nota PO ini
                    'notes' => 'Penerimaan barang dari PO Supplier',
                    'user_id' => Auth::id(), // ID Admin yang sedang login & mengeklik tombol
                ]);
            }

            DB::commit(); // Simpan permanen ke database
            return back()->with('success', 'Barang berhasil diterima! Stok gudang telah otomatis bertambah.');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua perubahan jika terjadi error
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function receipt(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'Pending') {
            return redirect()->route('purchase-orders.show', $purchaseOrder->id)->with('error', 'Penerimaan barang sudah diproses sebelumnya.');
        }
        
        $purchaseOrder->load('supplier', 'items.material');
        return view('purchase_orders.receipt', compact('purchaseOrder'));
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        // Proteksi Keamanan Ganda (Backend Security)
        if ($purchaseOrder->status !== 'Pending') {
            return back()->with('error', 'Akses Ditolak! PO yang sudah selesai atau diproses tidak boleh dihapus.');
        }

        // Karena di migration kita menggunakan onDelete('cascade') di purchase_order_items,
        // maka menghapus header PO ini akan otomatis menghapus semua item detailnya juga.
        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order (Pending) berhasil dihapus!');
    }
    
    // Cetak Invoice Pembelian PDF
    public function printInvoice(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'items.material');
        $invoice = Invoice::with('items')->where('purchase_order_id', $purchaseOrder->id)->firstOrFail();

        $pdf = Pdf::loadView('invoices.print', [
            'invoice' => $invoice,
            'title' => 'INVOICE PEMBELIAN',
            'partyLabel' => 'Supplier',
            'partyName' => $purchaseOrder->supplier->name ?? '-',
            'partyAddress' => $purchaseOrder->supplier->address ?? '-',
            'partyPhone' => $purchaseOrder->supplier->phone_number ?? '-',
            'reference' => 'PO: ' . $purchaseOrder->po_number,
        ]);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Invoice_' . $invoice->invoice_number . '.pdf');
    }

    // Cetak Nota Konsinyasi Mitra PDF
    public function printConsignment(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'items.material');
        $invoice = Invoice::with('items')->where('purchase_order_id', $purchaseOrder->id)->firstOrFail();

        $pdf = Pdf::loadView('invoices.print', [
            'invoice' => $invoice,
            'title' => 'NOTA KONSINYASI MITRA',
            'typeBadge' => 'KONSINYASI MASUK',
            'typeBadgeClass' => 'badge-consignment',
            'partyLabel' => 'Mitra / Supplier',
            'partyName' => $purchaseOrder->supplier->name ?? '-',
            'partyAddress' => $purchaseOrder->supplier->address ?? '-',
            'partyPhone' => $purchaseOrder->supplier->phone_number ?? '-',
            'reference' => 'PO: ' . $purchaseOrder->po_number,
        ]);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Consignment_' . $invoice->invoice_number . '.pdf');
    }
}