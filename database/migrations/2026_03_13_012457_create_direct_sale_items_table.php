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
        Schema::create('direct_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direct_sale_id')->constrained('direct_sales')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products'); // Beli produk apa
            $table->integer('quantity'); // Jumlah beli
            $table->decimal('unit_price', 15, 2); // Harga satuan saat itu
            $table->decimal('subtotal', 15, 2); // qty * unit_price
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direct_sale_items');
    }
};
