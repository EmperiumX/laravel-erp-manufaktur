<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrder;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Material;
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
            'backorder_policy' => 'required|in:backorder,no_backorder',
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
            $hasShortfall = false;
            $backorderItems = [];

            foreach ($request->items as $itemData) {
                GoodsReceiptItem::create([
                    'goods_receipt_id' => $gr->id,
                    'material_id' => $itemData['material_id'],
                    'quantity_ordered' => $itemData['quantity_ordered'],
                    'quantity_received' => $itemData['quantity_received'],
                    'notes' => $itemData['notes'] ?? null,
                ]);

                // Hitung subtotal berdasarkan quantity_received dan unit_price dari PO Item
                $poItem = $po->items->where('material_id', $itemData['material_id'])->first();
                $unitPrice = $poItem ? $poItem->unit_price : 0;
                $receivedSubtotal = $itemData['quantity_received'] * $unitPrice;
                $totalReceivedAmount += $receivedSubtotal;

                // Hitung selisih kurang untuk backorder
                if ($itemData['quantity_received'] < $itemData['quantity_ordered']) {
                    $hasShortfall = true;
                    $shortfallQty = $itemData['quantity_ordered'] - $itemData['quantity_received'];
                    if ($shortfallQty > 0) {
                        $backorderItems[] = [
                            'material_id' => $itemData['material_id'],
                            'quantity' => $shortfallQty,
                            'unit_price' => $unitPrice,
                            'subtotal' => $shortfallQty * $unitPrice
                        ];
                    }
                }

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

            // Jika ada shortfall dan user memilih 'backorder', buat PO Backorder baru
            if ($hasShortfall && $request->backorder_policy === 'backorder' && count($backorderItems) > 0) {
                // Tentukan nomor PO Backorder (flat hierarchy: PO-xxx-BO1, PO-xxx-BO2, etc.)
                $basePoNumber = preg_replace('/-BO\d+$/', '', $po->po_number);
                $boCount = PurchaseOrder::where('po_number', 'like', $basePoNumber . '-BO%')->count();
                $boSuffix = '-BO' . ($boCount + 1);
                $boNumber = $basePoNumber . $boSuffix;

                $backorderPO = PurchaseOrder::create([
                    'po_number' => $boNumber,
                    'supplier_id' => $po->supplier_id,
                    'order_date' => $request->receipt_date,
                    'status' => 'Pending',
                    'total_amount' => collect($backorderItems)->sum('subtotal'),
                    'notes' => 'Backorder dari PO: ' . $po->po_number,
                ]);

                foreach ($backorderItems as $boItem) {
                    \App\Models\PurchaseOrderItem::create([
                        'purchase_order_id' => $backorderPO->id,
                        'material_id' => $boItem['material_id'],
                        'quantity' => $boItem['quantity'],
                        'unit_price' => $boItem['unit_price'],
                        'subtotal' => $boItem['subtotal'],
                    ]);
                }
            }

            // 4. Update status PO asal menjadi Completed & update total_amount sesuai penerimaan aktual
            $po->update([
                'status' => 'Completed',
                'total_amount' => $totalReceivedAmount
            ]);

            // ===== 5. AUTO-GENERATE INVOICE PEMBELIAN (HUTANG) - HANYA JIKA ADA BARANG YANG DITERIMA =====
            $invoiceCreated = false;
            if ($totalReceivedAmount > 0) {
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
                    'subtotal' => $totalReceivedAmount,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => $totalReceivedAmount,
                    'paid_amount' => 0,
                    'status' => 'Sent',
                    'notes' => 'Auto-generated dari penerimaan barang ' . $grNumber,
                    'created_by' => Auth::id(),
                ]);

                // Buat detail item invoice hanya untuk yang kuantitas diterimanya > 0
                foreach ($request->items as $itemData) {
                    if ($itemData['quantity_received'] > 0) {
                        $material = Material::findOrFail($itemData['material_id']);
                        $poItem = $po->items->where('material_id', $itemData['material_id'])->first();
                        $unitPrice = $poItem ? $poItem->unit_price : 0;
                        $subtotal = $itemData['quantity_received'] * $unitPrice;

                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'description' => $material->name,
                            'material_id' => $itemData['material_id'],
                            'quantity' => $itemData['quantity_received'],
                            'unit' => $material->unit ?? 'pcs',
                            'unit_price' => $unitPrice,
                            'subtotal' => $subtotal,
                        ]);
                    }
                }
                $invoiceCreated = true;
            }

            DB::commit();

            if ($invoiceCreated) {
                $msg = 'Penerimaan barang berhasil! Invoice pembelian otomatis dibuat: ' . $invoiceNumber;
            } else {
                $msg = 'Penerimaan barang berhasil diproses (tidak ada barang yang diterima).';
            }
            
            if (isset($backorderPO)) {
                $msg .= '. Sisa barang yang kurang telah dibuatkan PO Backorder baru: ' . $backorderPO->po_number;
            }

            return redirect()->route('goods-receipts.show', $gr->id)
                ->with('success', $msg);

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
            'partyName' => $goodsReceipt->purchaseOrder?->supplier?->name ?? '-',
            'partyAddress' => $goodsReceipt->purchaseOrder?->supplier?->address ?? '-',
            'partyPhone' => $goodsReceipt->purchaseOrder?->supplier?->phone_number ?? '-',
            'reference' => 'PO: ' . ($goodsReceipt->purchaseOrder?->po_number ?? '-') . ' | GR: ' . $goodsReceipt->receipt_number,
        ]);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Invoice_' . $invoice->invoice_number . '.pdf');
    }
}
