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
        Route::resource('suppliers', SupplierController::class);
        Route::resource('stores', StoreController::class);
    });

    // 3. ZONA PRODUKSI & GUDANG (Superadmin, Admin, Produser)
    Route::middleware(['role:Superadmin|Admin|Produser'])->group(function () {
        Route::resource('materials', MaterialController::class);
        Route::resource('products', ProductController::class);
        Route::post('/products/{product}/bom',[BillOfMaterialController::class, 'store'])->name('boms.store');
        Route::delete('/boms/{bom}',[BillOfMaterialController::class, 'destroy'])->name('boms.destroy');
        
        Route::resource('purchase-orders', PurchaseOrderController::class);
        Route::post('/purchase-orders/{purchase_order}/complete', [PurchaseOrderController::class, 'markAsCompleted'])->name('purchase-orders.complete');
        
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
        Route::resource('consignments', ConsignmentController::class);
        
        Route::resource('returns', ConsignmentReturnController::class);
        
        Route::get('/direct-sales/export',[DirectSaleController::class, 'exportExcel'])->name('direct-sales.export');
        Route::get('/direct-sales/{direct_sale}/print',[DirectSaleController::class, 'print'])->name('direct-sales.print');
        Route::resource('direct-sales', DirectSaleController::class);
    });

});

require __DIR__.'/auth.php';