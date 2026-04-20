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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique(); // Nomor Nota Pembelian, cth: PO-20260311-001
            $table->foreignId('supplier_id')->constrained('suppliers'); // Beli dari supplier mana
            $table->date('order_date'); // Tanggal pembelian
            $table->enum('status', ['Pending', 'Completed', 'Canceled'])->default('Pending'); // Status barang sudah masuk gudang atau belum
            $table->decimal('total_amount', 15, 2)->default(0); // Total Rp nota
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
