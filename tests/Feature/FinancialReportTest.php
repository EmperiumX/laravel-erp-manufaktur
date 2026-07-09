<?php

use App\Models\User;
use App\Models\CashBank;
use App\Models\CashBankTransaction;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\DirectSale;
use App\Models\ConsignmentShipment;
use App\Models\PurchaseOrder;
use App\Models\Invoice;
use App\Models\Payment;
use Spatie\Permission\Models\Role;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    // Run the role seeder
    $this->seed(RolePermissionSeeder::class);
    
    // Create an admin user
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Superadmin');
    
    // Create Cash & Bank account
    $this->cashBank = CashBank::create([
        'name' => 'Kas Toko Utama',
        'type' => 'Cash',
        'balance' => 10000000, // 10 Million Rupiah
        'is_active' => true,
    ]);
});

test('unauthorized users cannot access reports or journal pages', function () {
    $user = User::factory()->create(); // standard user with no roles
    
    $this->actingAs($user)->get(route('reports.index'))->assertStatus(403);
    $this->actingAs($user)->get(route('reports.profit-loss'))->assertStatus(403);
    $this->actingAs($user)->get(route('reports.general-journal'))->assertStatus(403);
});

test('admin can access financial reports dashboard', function () {
    $response = $this->actingAs($this->admin)->get(route('reports.index'));
    $response->assertStatus(200);
});

test('profit and loss calculates revenue, cogs, operational expenses, and shows details', function () {
    $store = Store::create([
        'name' => 'Toko Cabang A',
        'category' => 'Mitra',
        'address' => 'Jl. A',
        'phone_number' => '12345',
    ]);
    
    $directSale = DirectSale::create([
        'invoice_number' => 'DS-202606-001',
        'sale_date' => now()->format('Y-m-d'),
        'customer_name' => 'Pelanggan Umum',
        'store_id' => $store->id,
        'cashier_id' => $this->admin->id,
        'total_amount' => 1500000,
        'payment_method' => 'Cash',
        'notes' => 'Sale notes',
    ]);

    // 2. Operational Expense
    $expense = CashBankTransaction::create([
        'cash_bank_id' => $this->cashBank->id,
        'type' => 'Credit',
        'amount' => 500000,
        'balance_after' => 9500000,
        'transaction_date' => now()->format('Y-m-d'),
        'reference' => 'OP-001',
        'description' => 'Pembayaran Tagihan Listrik',
        'category' => 'Biaya Listrik',
        'is_reconciled' => false,
        'created_by' => $this->admin->id,
    ]);

    $response = $this->actingAs($this->admin)->get(route('reports.profit-loss'));
    $response->assertStatus(200);
    
    // Total Revenue should include $directSale
    $response->assertSee('1.500.000');
    // Operational Expense should display $expense
    $response->assertSee('500.000');
    // Category should be visible
    $response->assertSee('Biaya Listrik');
    // Detailed list description should be visible
    $response->assertSee('Pembayaran Tagihan Listrik');
});

test('general journal combines all transaction logs chronologically and supports filters', function () {
    $store = Store::create([
        'name' => 'Toko Cabang B',
        'category' => 'Mitra',
        'address' => 'Jl. B',
        'phone_number' => '12345',
    ]);

    $supplier = Supplier::create([
        'name' => 'Supplier Kain Utama',
        'code' => 'SUP-K',
        'address' => 'Jl. K',
        'phone' => '12345',
        'is_active' => true,
    ]);

    // 1. Direct Sale (Toko Cabang B)
    DirectSale::create([
        'invoice_number' => 'DS-202606-002',
        'sale_date' => now()->format('Y-m-d'),
        'customer_name' => 'Pelanggan B',
        'store_id' => $store->id,
        'cashier_id' => $this->admin->id,
        'total_amount' => 800000,
        'payment_method' => 'Cash',
    ]);

    // 2. Purchase Order (Supplier Kain Utama)
    PurchaseOrder::create([
        'po_number' => 'PO-202606-001',
        'supplier_id' => $supplier->id,
        'order_date' => now()->format('Y-m-d'),
        'total_amount' => 2000000,
        'status' => 'Pending',
        'created_by' => $this->admin->id,
    ]);

    // Request full journal
    $response = $this->actingAs($this->admin)->get(route('reports.general-journal'));
    $response->assertStatus(200);
    $response->assertSee('DS-202606-002');
    $response->assertSee('PO-202606-001');

    // Filter by store - should see direct sale but NOT PO
    $responseStore = $this->actingAs($this->admin)->get(route('reports.general-journal', ['store_id' => $store->id]));
    $responseStore->assertStatus(200);
    $responseStore->assertSee('DS-202606-002');
    $responseStore->assertDontSee('PO-202606-001');

    // Filter by supplier - should see PO but NOT direct sale
    $responseSupplier = $this->actingAs($this->admin)->get(route('reports.general-journal', ['supplier_id' => $supplier->id]));
    $responseSupplier->assertStatus(200);
    $responseSupplier->assertSee('PO-202606-001');
    $responseSupplier->assertDontSee('DS-202606-002');
});

test('admin can create manual journal voucher entry', function () {
    // Initial cash bank balance is 10,000,000
    $response = $this->actingAs($this->admin)
        ->from(route('reports.general-journal'))
        ->post(route('reports.store-journal'), [
            'cash_bank_id' => $this->cashBank->id,
            'type' => 'Credit', // Expense/Credit
            'amount' => 1250000, // Rp 1.250.000
            'transaction_date' => now()->format('Y-m-d'),
            'description' => 'Sewa Gudang Tambahan',
            'category' => 'Sewa Tempat',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('reports.general-journal'));

    // Check balance updated: 10,000,000 - 1,250,000 = 8,750,000
    $this->cashBank->refresh();
    expect($this->cashBank->balance)->toEqual(8750000);

    // Check transaction created in DB
    $transaction = CashBankTransaction::where('category', 'Sewa Tempat')->first();
    expect($transaction)->not->toBeNull();
    expect($transaction->reference)->toStartWith('JV-');
    expect($transaction->description)->toBe('Sewa Gudang Tambahan');
    expect($transaction->created_by)->toBe($this->admin->id);
    expect($transaction->balance_after)->toEqual(8750000);
});
