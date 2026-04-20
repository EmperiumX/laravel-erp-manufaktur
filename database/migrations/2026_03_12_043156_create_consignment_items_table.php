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
        Schema::create('consignment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consignment_shipment_id')->constrained('consignment_shipments')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products'); // Produk jadi yang dikirim
            $table->integer('quantity'); // Jumlah pack/biji yang dikirim
            $table->decimal('unit_price', 15, 2); // Harga satuan (otomatis ditarik berdasarkan kategori toko nanti)
            $table->decimal('subtotal', 15, 2); // qty * unit_price
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_items');
    }
};
