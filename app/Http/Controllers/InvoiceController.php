<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\ConsignmentShipment;
use App\Models\DirectSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Daftar semua invoice (piutang & hutang)
     */
    public function index(Request $request)
    {
        $type = $request->query('type'); // 'sales' atau 'purchase'

        $query = Invoice::with(['store', 'supplier', 'creator', 'directSale']);

        if ($type === 'sales') {
            $query->sales();
        } elseif ($type === 'purchase') {
            $query->purchase();
        }

        $invoices = $query->latest()->get();
        
        // Hitung summary
        $totalPiutang = Invoice::sales()->unpaid()->sum(DB::raw('total_amount - paid_amount'));
        $totalHutang = Invoice::purchase()->unpaid()->sum(DB::raw('total_amount - paid_amount'));
        
        // Hitung total overdue
        $totalOverdueSales = Invoice::sales()->unpaid()->get()->filter(fn($inv) => $inv->is_overdue)->sum(fn($inv) => $inv->total_amount - $inv->paid_amount);
        $totalOverduePurchase = Invoice::purchase()->unpaid()->get()->filter(fn($inv) => $inv->is_overdue)->sum(fn($inv) => $inv->total_amount - $inv->paid_amount);
        $totalOverdue = $totalOverdueSales + $totalOverduePurchase;

        return view('invoices.index', compact('invoices', 'type', 'totalPiutang', 'totalHutang', 'totalOverdue'));
    }

    /**
     * Form buat invoice baru
     */
    public function create(Request $request)
    {
        $type = $request->query('type', 'sales');
        $source = $request->query('source'); // 'po', 'consignment', 'direct-sale'
        $sourceId = $request->query('source_id');

        $stores = Store::all();
        $suppliers = Supplier::all();

        // Data sumber yang bisa dijadikan invoice
        $purchaseOrder = null;
        $consignment = null;
        $directSale = null;

        if ($source === 'po' && $sourceId) {
            $purchaseOrder = PurchaseOrder::with(['supplier', 'items.material'])->findOrFail($sourceId);
        } elseif ($source === 'consignment' && $sourceId) {
            $consignment = ConsignmentShipment::with(['store', 'items.product'])->findOrFail($sourceId);
        } elseif ($source === 'direct-sale' && $sourceId) {
            $directSale = DirectSale::with(['store', 'items.product'])->findOrFail($sourceId);
        }

        // PO Completed yang belum punya invoice (untuk dropdown)
        $availablePOs = PurchaseOrder::where('status', 'Completed')
            ->whereDoesntHave('invoices')
            ->with('supplier')
            ->get();

        // DO yang sudah terkirim tapi belum di-invoice
        $availableConsignments = ConsignmentShipment::where('status', 'Sent')
            ->with('store')
            ->get();

        return view('invoices.create', compact(
            'type', 'stores', 'suppliers',
            'purchaseOrder', 'consignment', 'directSale',
            'availablePOs', 'availableConsignments'
        ));
    }

    /**
     * Simpan invoice baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:sales,purchase',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Validasi tambahan berdasarkan type
        if ($request->type === 'sales') {
            $request->validate(['store_id' => 'required|exists:stores,id']);
        } else {
            $request->validate(['supplier_id' => 'required|exists:suppliers,id']);
        }

        DB::beginTransaction();
        try {
            // Generate nomor invoice
            $prefix = $request->type === 'sales' ? 'INV-S' : 'INV-P';
            $today = date('Ymd');
            $countToday = Invoice::where('type', $request->type)
                ->whereDate('created_at', date('Y-m-d'))
                ->count();
            $invoiceNumber = $prefix . '-' . $today . '-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

            // Hitung subtotal
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $taxAmount = $request->input('tax_amount', 0);
            $discountAmount = $request->input('discount_amount', 0);
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            // Simpan header invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'type' => $request->type,
                'store_id' => $request->store_id,
                'supplier_id' => $request->supplier_id,
                'purchase_order_id' => $request->purchase_order_id,
                'consignment_shipment_id' => $request->consignment_shipment_id,
                'direct_sale_id' => $request->direct_sale_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'status' => 'Draft',
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            // Simpan detail items
            foreach ($request->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'material_id' => $item['material_id'] ?? null,
                    'product_id' => $item['product_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? 'pcs',
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            // Update status consignment jika dari DO
            if ($request->consignment_shipment_id) {
                ConsignmentShipment::where('id', $request->consignment_shipment_id)
                    ->update(['status' => 'Invoiced']);
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Invoice berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Detail invoice
     */
    public function show(Invoice $invoice)
    {
        $invoice->load([
            'store', 'supplier', 'creator',
            'items.material', 'items.product',
            'payments.cashBank', 'payments.creator',
            'purchaseOrder', 'consignmentShipment', 'directSale'
        ]);
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Update status invoice (kirim ke pelanggan)
     */
    public function send(Invoice $invoice)
    {
        if ($invoice->status !== 'Draft') {
            return back()->with('error', 'Invoice sudah dikirim sebelumnya.');
        }

        $invoice->update(['status' => 'Sent']);

        return back()->with('success', 'Invoice berhasil dikirim!');
    }

    /**
     * Batalkan invoice
     */
    public function cancel(Invoice $invoice)
    {
        if ($invoice->paid_amount > 0) {
            return back()->with('error', 'Invoice yang sudah ada pembayaran tidak bisa dibatalkan.');
        }

        $invoice->update(['status' => 'Canceled']);

        return back()->with('success', 'Invoice berhasil dibatalkan.');
    }

    /**
     * Hapus invoice (hanya Draft)
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->status !== 'Draft') {
            return back()->with('error', 'Hanya invoice berstatus Draft yang bisa dihapus.');
        }

        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice berhasil dihapus.');
    }

    /**
     * Cetak invoice generik ke PDF
     */
    public function print(Invoice $invoice)
    {
        $invoice->load(['store', 'supplier', 'items', 'purchaseOrder', 'consignmentShipment', 'directSale']);
        
        $pdf = Pdf::loadView('invoices.print', [
            'invoice' => $invoice,
            'title' => $invoice->type === 'sales' ? 'INVOICE PENJUALAN' : 'INVOICE PEMBELIAN',
            'partyLabel' => $invoice->type === 'sales' ? 'Kepada' : 'Supplier',
            'partyName' => $invoice->type === 'sales' ? ($invoice->store->name ?? ($invoice->directSale->customer_name ?? 'Pelanggan Umum')) : ($invoice->supplier->name ?? '-'),
            'partyAddress' => $invoice->type === 'sales' ? ($invoice->store->address ?? 'Penjualan Langsung / Eceran') : ($invoice->supplier->address ?? '-'),
            'partyPhone' => $invoice->type === 'sales' ? ($invoice->store->phone_number ?? '-') : ($invoice->supplier->phone_number ?? '-'),
            'reference' => $invoice->purchase_order_id ? ('PO: ' . ($invoice->purchaseOrder->po_number ?? '-')) : ($invoice->consignment_shipment_id ? ('DO: ' . ($invoice->consignmentShipment->shipment_number ?? '-')) : ($invoice->direct_sale_id ? ('Direct Sale: ' . ($invoice->directSale->invoice_number ?? '-')) : '-')),
        ]);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Invoice_' . $invoice->invoice_number . '.pdf');
    }
}
