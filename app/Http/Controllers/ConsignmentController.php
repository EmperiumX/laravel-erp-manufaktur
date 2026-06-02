<?php

namespace App\Http\Controllers;

use App\Models\ConsignmentShipment;
use App\Models\ConsignmentItem;
use App\Models\Store;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsignmentController extends Controller
{
    public function index()
    {
        $shipments = ConsignmentShipment::with('store', 'invoice.items')->latest()->get();

        // Prepare invoice data for the preview modal (avoids complex PHP in Blade @json)
        $invoiceDataMap = [];
        foreach ($shipments as $do) {
            if (!$do->invoice) {
                continue;
            }
            $invoiceDataMap[$do->id] = [
                'invoice_number' => $do->invoice->invoice_number,
                'invoice_date' => \Carbon\Carbon::parse($do->invoice->invoice_date)->format('d F Y'),
                'due_date' => \Carbon\Carbon::parse($do->invoice->due_date)->format('d F Y'),
                'shipment_number' => $do->shipment_number,
                'store_name' => $do->store->name ?? '-',
                'store_address' => $do->store->address ?? '-',
                'store_phone' => $do->store->phone_number ?? '-',
                'status' => $do->invoice->status,
                'subtotal' => $do->invoice->subtotal,
                'tax_amount' => $do->invoice->tax_amount,
                'discount_amount' => $do->invoice->discount_amount,
                'total_amount' => $do->invoice->total_amount,
                'paid_amount' => $do->invoice->paid_amount,
                'notes' => $do->invoice->notes,
                'print_url' => route('consignments.print-invoice', $do->id),
                'items' => $do->invoice->items->map(function ($item) {
                    return [
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal' => $item->subtotal,
                    ];
                })->values()->toArray(),
            ];
        }

        return view('consignments.index', compact('shipments', 'invoiceDataMap'));
    }

    public function create()
    {
        $stores = Store::all();
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
            // 1. Generate Nomor DO
            $today = date('Ymd');
            $countToday = ConsignmentShipment::whereDate('created_at', date('Y-m-d'))->count();
            $doNumber = 'DO-' . $today . '-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

            // 2. Simpan Header Pengiriman
            $shipment = ConsignmentShipment::create([
                'shipment_number' => $doNumber,
                'store_id' => $request->store_id,
                'shipment_date' => $request->shipment_date,
                'status' => 'Sent',
                'total_amount' => 0,
                'notes' => $request->notes
            ]);

            $totalAmount = 0;

            // 3. Looping Barang yang Dikirim
            foreach ($request->products as $key => $product_id) {
                $qty = $request->quantities[$key];
                $price = $request->unit_prices[$key];
                $subtotal = $qty * $price;

                ConsignmentItem::create([
                    'consignment_shipment_id' => $shipment->id,
                    'product_id' => $product_id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $subtotal
                ]);

                $totalAmount += $subtotal;

                // Kurangi Stok
                $stock = StockItem::where('product_id', $product_id)->first();
                if (!$stock || $stock->quantity < $qty) {
                    $productName = Product::find($product_id)->name;
                    throw new \Exception("Stok Gudang untuk produk '$productName' tidak mencukupi! (Sisa: " . ($stock ? $stock->quantity : 0) . ")");
                }

                $stock->quantity -= $qty;
                $stock->save();

                StockMovement::create([
                    'stock_item_id' => $stock->id,
                    'type' => 'OUT',
                    'quantity' => $qty,
                    'reference' => $doNumber,
                    'notes' => 'Pengiriman barang ke Toko / Konsinyasi',
                    'user_id' => Auth::id(),
                ]);
            }

            // 4. Update Total Nilai Surat Jalan
            $shipment->update(['total_amount' => $totalAmount]);

            // ===== 5. AUTO-GENERATE INVOICE PENJUALAN (PIUTANG) =====
            $countInvToday = Invoice::where('type', 'sales')
                ->whereDate('created_at', date('Y-m-d'))
                ->count();
            $invoiceNumber = 'INV-S-' . $today . '-' . str_pad($countInvToday + 1, 3, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'type' => 'sales',
                'store_id' => $request->store_id,
                'consignment_shipment_id' => $shipment->id,
                'invoice_date' => $request->shipment_date,
                'due_date' => date('Y-m-d', strtotime($request->shipment_date . ' +30 days')),
                'subtotal' => $totalAmount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'status' => 'Sent',
                'notes' => 'Auto-generated dari konsinyasi ' . $doNumber,
                'created_by' => Auth::id(),
            ]);

            // Buat item invoice dari item konsinyasi
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

            return redirect()->route('consignments.index')->with('success', 'Surat Jalan (DO) berhasil dibuat! Invoice piutang otomatis: ' . $invoiceNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengiriman: ' . $e->getMessage());
        }
    }

    // Cetak Surat Jalan PDF
    public function print(ConsignmentShipment $consignment)
    {
        $consignment->load('items.product', 'store');
        $pdf = Pdf::loadView('consignments.print', compact('consignment'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Surat_Jalan_' . $consignment->shipment_number . '.pdf');
    }

    // Cetak Invoice Konsinyasi PDF
    public function printInvoice(ConsignmentShipment $consignment)
    {
        $consignment->load('items.product', 'store');
        $invoice = Invoice::with('items')->where('consignment_shipment_id', $consignment->id)->firstOrFail();

        $pdf = Pdf::loadView('invoices.print', [
            'invoice' => $invoice,
            'title' => 'INVOICE PENJUALAN',
            'partyLabel' => 'Kepada',
            'partyName' => $consignment->store->name ?? '-',
            'partyAddress' => $consignment->store->address ?? '-',
            'partyPhone' => $consignment->store->phone_number ?? '-',
            'reference' => 'DO: ' . $consignment->shipment_number,
        ]);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Invoice_' . $invoice->invoice_number . '.pdf');
    }
}
