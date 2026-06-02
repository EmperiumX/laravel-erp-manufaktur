<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // INV-S-20260519-001 (Sales) atau INV-P-20260519-001 (Purchase)
            $table->enum('type', ['sales', 'purchase']); // sales = tagihan ke mitra, purchase = tagihan dari supplier
            
            // Relasi fleksibel - salah satu yang diisi
            $table->foreignId('store_id')->nullable()->constrained('stores'); // Untuk invoice sales ke toko/mitra
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers'); // Untuk invoice purchase dari supplier
            
            // Referensi sumber
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders'); // Dari PO mana
            $table->foreignId('consignment_shipment_id')->nullable()->constrained('consignment_shipments'); // Dari DO mana
            $table->foreignId('direct_sale_id')->nullable()->constrained('direct_sales'); // Dari penjualan langsung mana
            
            $table->date('invoice_date'); // Tanggal faktur
            $table->date('due_date'); // Jatuh tempo
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0); // PPN
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0); // Jumlah yang sudah dibayar
            $table->enum('status', ['Draft', 'Sent', 'Partial', 'Paid', 'Overdue', 'Canceled'])->default('Draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
