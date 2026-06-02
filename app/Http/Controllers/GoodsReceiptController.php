<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrder;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class GoodsReceiptController extends Controller
{
    /**
     * Daftar semua penerimaan barang
     */
    public function index()
    {
        $goodsReceipts = GoodsReceipt::with(['purchaseOrder.supplier', 'receiver'])
            ->latest()
            ->get();
        return view('goods_receipts.index', compact('goodsReceipts'));
    }

    /**
     * Form buat penerimaan barang dari PO tertentu
     */
    public function create(Request $request)
    {
        $purchaseOrderId = $request->query('po_id');
        
        if ($purchaseOrderId) {
            $purchaseOrder = PurchaseOrder::with(['supplier', 'items.material'])->findOrFail($purchaseOrderId);
            
            if ($purchaseOrder->status !== 'Pending') {
                return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                    ->with('error', 'PO ini sudah diproses atau dibatalkan.');
            }
            
            return view('goods_receipts.create', compact('purchaseOrder'));
        }
        
        $pendingPOs = PurchaseOrder::with('supplier')
            ->where('status', 'Pending')
            ->latest()
            ->get();
        
        return view('goods_receipts.select_po', compact('pendingPOs'));
    }

    /**
     * Simpan penerimaan barang, update stok, & AUTO-GENERATE INVOICE
     */
    public function store(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'receipt_date' => 'required|date',
            'items' => 'required|array',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.quantity_ordered' => 'required|numeric|min:0',
            'items.*.quantity_received' => 'required|numeric|min:0',
        ]);

        $po = PurchaseOrder::with(['supplier', 'items.material'])->findOrFail($request->purchase_order_id);

        if ($po->status !== 'Pending') {
            return back()->with('error', 'PO ini sudah diproses sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // 1. Generate nomor GR
            $today = date('Ymd');
            $countToday = GoodsReceipt::whereDate('created_at', date('Y-m-d'))->count();
            $grNumber = 'GR-' . $today . '-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

            // 2. Buat header Goods Receipt
            $gr = GoodsReceipt::create([
                'receipt_number' => $grNumber,
                'purchase_order_id' => $po->id,
                'receipt_date' => $request->receipt_date,
                'received_by' => Auth::id(),
                'notes' => $request->notes,
            ]);

            // 3. Simpan detail item & update stok
            $totalReceivedAmount = 0;

            foreach ($request->items as $itemData) {
                GoodsReceiptItem::create([
                    'goods_receipt_id' => $gr->id,
                    'material_id' => $itemData['material_id'],
                    'quantity_ordered' => $itemData['quantity_ordered'],
                    'quantity_received' => $itemData['quantity_received'],
                    'notes' => $itemData['notes'] ?? null,
                ]);

                if ($itemData['quantity_received'] > 0) {
                    $stock = StockItem::firstOrCreate(
                        ['material_id' => $itemData['material_id']],
                        ['quantity' => 0]
                    );
                    $stock->quantity += $itemData['quantity_received'];
                    $stock->save();

                    StockMovement::create([
                        'stock_item_id' => $stock->id,
                        'type' => 'IN',
                        'quantity' => $itemData['quantity_received'],
                        'reference' => $grNumber,
                        'notes' => 'Penerimaan barang dari PO ' . $po->po_number,
                        'user_id' => Auth::id(),
                    ]);
                }
            }

            // 4. Update status PO menjadi Completed
            $po->update(['status' => 'Completed']);

            // ===== 5. AUTO-GENERATE INVOICE PEMBELIAN (HUTANG) =====
            $countInvToday = Invoice::where('type', 'purchase')
                ->whereDate('created_at', date('Y-m-d'))
                ->count();
            $invoiceNumber = 'INV-P-' . $today . '-' . str_pad($countInvToday + 1, 3, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'type' => 'purchase',
                'supplier_id' => $po->supplier_id,
                'purchase_order_id' => $po->id,
                'invoice_date' => $request->receipt_date,
                'due_date' => date('Y-m-d', strtotime($request->receipt_date . ' +30 days')),
                'subtotal' => $po->total_amount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $po->total_amount,
                'paid_amount' => 0,
                'status' => 'Sent',
                'notes' => 'Auto-generated dari penerimaan barang ' . $grNumber,
                'created_by' => Auth::id(),
            ]);

            // Buat detail item invoice dari PO items
            foreach ($po->items as $poItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $poItem->material->name,
                    'material_id' => $poItem->material_id,
                    'quantity' => $poItem->quantity,
                    'unit' => $poItem->material->unit ?? 'pcs',
                    'unit_price' => $poItem->unit_price,
                    'subtotal' => $poItem->subtotal,
                ]);
            }

            DB::commit();

            return redirect()->route('goods-receipts.show', $gr->id)
                ->with('success', 'Penerimaan barang berhasil! Invoice pembelian otomatis dibuat: ' . $invoiceNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Detail penerimaan barang (+ invoice yang terhubung)
     */
    public function show(GoodsReceipt $goodsReceipt)
    {
        $goodsReceipt->load(['purchaseOrder.supplier', 'receiver', 'items.material']);

        // Cari invoice yang terkait dengan PO ini
        $invoice = Invoice::where('purchase_order_id', $goodsReceipt->purchase_order_id)->first();

        return view('goods_receipts.show', compact('goodsReceipt', 'invoice'));
    }

    /**
     * Cetak Invoice Pembelian dari Penerimaan Barang
     */
    public function printInvoice(GoodsReceipt $goodsReceipt)
    {
        $goodsReceipt->load(['purchaseOrder.supplier', 'purchaseOrder.items.material', 'receiver']);
        $invoice = Invoice::with('items')->where('purchase_order_id', $goodsReceipt->purchase_order_id)->firstOrFail();

        $pdf = Pdf::loadView('invoices.print', [
            'invoice' => $invoice,
            'title' => 'INVOICE PEMBELIAN',
            'partyLabel' => 'Supplier',
            'partyName' => $goodsReceipt->purchaseOrder->supplier->name ?? '-',
            'partyAddress' => $goodsReceipt->purchaseOrder->supplier->address ?? '-',
            'partyPhone' => $goodsReceipt->purchaseOrder->supplier->phone_number ?? '-',
            'reference' => 'PO: ' . $goodsReceipt->purchaseOrder->po_number . ' | GR: ' . $goodsReceipt->receipt_number,
        ]);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Invoice_' . $invoice->invoice_number . '.pdf');
    }
}
