<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Material;
use App\Models\StockItem;
use App\Models\ProductionOrder;
use Spatie\Permission\Models\Role;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Superadmin');
});

test('production completion throws exception on insufficient material stock', function () {
    // Create product
    $product = Product::create([
        'sku' => 'PROD-T-001',
        'name' => 'Produk Test',
        'price' => 10000,
        'hpp' => 5000,
    ]);

    // Create material
    $material = Material::create([
        'name' => 'Bahan Baku Test',
        'type' => 'Bahan Pokok',
        'unit' => 'kg',
        'unit_price' => 5000,
    ]);

    // Link material to product BOM (needs 2kg of material for 1 unit of product)
    $product->boms()->create([
        'material_id' => $material->id,
        'quantity' => 2.0,
    ]);

    // Create a production order of 5 units (requires 10kg of material)
    $production = ProductionOrder::create([
        'production_number' => 'PROD-202606-001',
        'product_id' => $product->id,
        'quantity' => 5,
        'production_date' => now()->format('Y-m-d'),
        'status' => 'Pending',
    ]);

    // Material stock has only 5kg (need 10kg)
    StockItem::create([
        'material_id' => $material->id,
        'quantity' => 5.0,
    ]);

    // Completing production should redirect back with error message
    $response = $this->actingAs($this->admin)
        ->from(route('productions.show', $production->id))
        ->post(route('productions.complete', $production->id));

    $response->assertRedirect(route('productions.show', $production->id));
    $response->assertSessionHas('error');
    
    // Check that production status remains Pending
    $production->refresh();
    expect($production->status)->toEqual('Pending');
});

test('production completion succeeds when material stock is sufficient', function () {
    $product = Product::create([
        'sku' => 'PROD-T-002',
        'name' => 'Produk Test 2',
        'price' => 15000,
        'hpp' => 8000,
    ]);

    $material = Material::create([
        'name' => 'Bahan Baku Test 2',
        'type' => 'Bahan Pokok',
        'unit' => 'pcs',
        'unit_price' => 2000,
    ]);

    $product->boms()->create([
        'material_id' => $material->id,
        'quantity' => 3.0,
    ]);

    $production = ProductionOrder::create([
        'production_number' => 'PROD-202606-002',
        'product_id' => $product->id,
        'quantity' => 10, // requires 30 units of material
        'production_date' => now()->format('Y-m-d'),
        'status' => 'Pending',
    ]);

    // Material stock has 50 units (need 30)
    $materialStock = StockItem::create([
        'material_id' => $material->id,
        'quantity' => 50.0,
    ]);

    // Complete production
    $response = $this->actingAs($this->admin)
        ->post(route('productions.complete', $production->id));

    $response->assertRedirect(route('productions.index'));
    $response->assertSessionHas('success');

    $production->refresh();
    expect($production->status)->toEqual('Completed');

    // Check material stock is reduced: 50 - 30 = 20
    $materialStock->refresh();
    expect($materialStock->quantity)->toEqual(20.0);

    // Check product stock is created/increased by 10
    $productStock = StockItem::where('product_id', $product->id)->first();
    expect($productStock)->not->toBeNull();
    expect($productStock->quantity)->toEqual(10.0);
});
