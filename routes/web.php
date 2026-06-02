<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BillOfMaterialController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\ConsignmentController;
use App\Http\Controllers\ConsignmentReturnController;
use App\Http\Controllers\DirectSaleController;
use App\Http\Controllers\GoodsReceiptController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CashBankController;
use App\Http\Controllers\FinancialReportController;

// Rute Publik (Welcome & Dashboard Bawaan)
Route::get('/', function () { return view('welcome'); });

// RUTE YANG HARUS LOGIN DULU
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard & Profile (Semua Role Boleh Buka)
    Route::get('/dashboard', function () {
        // Logika dashboard yang sudah kita buat sebelumnya...
        $currentMonth = \Carbon\Carbon::now()->month;
        $currentYear = \Carbon\Carbon::now()->year;
        $totalSalesMonth = \App\Models\DirectSale::whereMonth('sale_date', $currentMonth)->whereYear('sale_date', $currentYear)->sum('total_amount');
        $totalPurchaseMonth = \App\Models\PurchaseOrder::where('status', 'Completed')->whereMonth('order_date', $currentMonth)->whereYear('order_date', $currentYear)->sum('total_amount');
        $activeConsignments = \App\Models\ConsignmentShipment::where('status', 'Sent')->count();
        $lowStocks = \App\Models\StockItem::with('material', 'product')->where('quantity', '<=', 10)->get();
        return view('dashboard', compact('totalSalesMonth', 'totalPurchaseMonth', 'activeConsignments', 'lowStocks'));
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // 1. ZONA KHUSUS SUPERADMIN
    Route::middleware(['role:Superadmin'])->group(function () {
        Route::resource('users', UserController::class)->except(['show', 'edit', 'update']);
    });

    // 2. ZONA MASTER DATA (Hanya Superadmin & Admin)
    Route::middleware(['role:Superadmin|Admin'])->group(function () {
        // Supplier
        Route::get('/suppliers/export', [SupplierController::class, 'export'])->name('suppliers.export');
        Route::get('/suppliers/template', [SupplierController::class, 'downloadTemplate'])->name('suppliers.template');
        Route::post('/suppliers/import', [SupplierController::class, 'import'])->name('suppliers.import');
        Route::post('/suppliers/bulk-delete', [SupplierController::class, 'bulkDestroy'])->name('suppliers.bulk-destroy');
        Route::resource('suppliers', SupplierController::class);
        
        // Toko/Mitra
        Route::get('/stores/export', [StoreController::class, 'export'])->name('stores.export');
        Route::get('/stores/template', [StoreController::class, 'downloadTemplate'])->name('stores.template');
        Route::post('/stores/import', [StoreController::class, 'import'])->name('stores.import');
        Route::post('/stores/bulk-delete', [StoreController::class, 'bulkDestroy'])->name('stores.bulk-destroy');
        Route::resource('stores', StoreController::class);
    });

    // 3. ZONA PRODUKSI & GUDANG (Superadmin, Admin, Produser)
    Route::middleware(['role:Superadmin|Admin|Produser'])->group(function () {
        Route::get('/materials/export', [MaterialController::class, 'export'])->name('materials.export');
        Route::get('/materials/template', [MaterialController::class, 'downloadTemplate'])->name('materials.template');
        Route::post('/materials/import', [MaterialController::class, 'import'])->name('materials.import');
        Route::post('/materials/bulk-delete', [MaterialController::class, 'bulkDestroy'])->name('materials.bulk-destroy');
        Route::resource('materials', MaterialController::class);
        Route::post('/products/bulk-delete', [ProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');
        Route::resource('products', ProductController::class);
        Route::post('/products/{product}/bom',[BillOfMaterialController::class, 'store'])->name('boms.store');
        Route::delete('/boms/{bom}',[BillOfMaterialController::class, 'destroy'])->name('boms.destroy');
        
        Route::get('/purchase-orders/{purchase_order}/print-invoice', [PurchaseOrderController::class, 'printInvoice'])->name('purchase-orders.print-invoice');
        Route::get('/purchase-orders/{purchase_order}/print-consignment', [PurchaseOrderController::class, 'printConsignment'])->name('purchase-orders.print-consignment');
        Route::resource('purchase-orders', PurchaseOrderController::class);
        Route::get('/purchase-orders/{purchase_order}/receipt', [PurchaseOrderController::class, 'receipt'])->name('purchase-orders.receipt');
        Route::post('/purchase-orders/{purchase_order}/complete', [PurchaseOrderController::class, 'markAsCompleted'])->name('purchase-orders.complete');
        
        // Penerimaan Barang (Goods Receipt)
        Route::get('/goods-receipts/{goods_receipt}/print-invoice', [GoodsReceiptController::class, 'printInvoice'])->name('goods-receipts.print-invoice');
        Route::resource('goods-receipts', GoodsReceiptController::class)->only(['index', 'create', 'store', 'show']);

        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/history', [InventoryController::class, 'history'])->name('inventory.history');
        Route::get('/inventory/export', [InventoryController::class, 'exportExcel'])->name('inventory.export');

        Route::resource('productions', ProductionOrderController::class);
        Route::get('/productions/{production}/show',[ProductionOrderController::class, 'show'])->name('productions.show');
        Route::post('/productions/{production}/complete', [ProductionOrderController::class, 'markAsCompleted'])->name('productions.complete');
    });

    // 4. ZONA PENJUALAN (Superadmin, Admin, Sales)
    Route::middleware(['role:Superadmin|Admin|Sales'])->group(function () {
        Route::get('/consignments/{consignment}/print',[ConsignmentController::class, 'print'])->name('consignments.print');
        Route::get('/consignments/{consignment}/print-invoice',[ConsignmentController::class, 'printInvoice'])->name('consignments.print-invoice');
        Route::resource('consignments', ConsignmentController::class);
        
        Route::resource('returns', ConsignmentReturnController::class);
        
        Route::get('/direct-sales/export',[DirectSaleController::class, 'exportExcel'])->name('direct-sales.export');
        Route::get('/direct-sales/{direct_sale}/print',[DirectSaleController::class, 'print'])->name('direct-sales.print');
        Route::resource('direct-sales', DirectSaleController::class);
    });

    // 5. ZONA KEUANGAN (Superadmin, Admin)
    Route::middleware(['role:Superadmin|Admin'])->group(function () {
        // Invoice (Faktur)
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::post('/invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

        // Pembayaran
        Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'show']);

        // Kas & Bank
        Route::post('/cash-banks/{cash_bank}/transaction', [CashBankController::class, 'addTransaction'])->name('cash-banks.transaction');
        Route::post('/cash-banks/{cash_bank}/reconcile', [CashBankController::class, 'reconcile'])->name('cash-banks.reconcile');
        Route::resource('cash-banks', CashBankController::class);

        // Laporan Keuangan
        Route::get('/reports', [FinancialReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/profit-loss', [FinancialReportController::class, 'profitLoss'])->name('reports.profit-loss');
        Route::get('/reports/accounts-receivable', [FinancialReportController::class, 'accountsReceivable'])->name('reports.accounts-receivable');
        Route::get('/reports/accounts-payable', [FinancialReportController::class, 'accountsPayable'])->name('reports.accounts-payable');
        Route::get('/reports/cash-flow', [FinancialReportController::class, 'cashFlow'])->name('reports.cash-flow');
    });

});

require __DIR__.'/auth.php';