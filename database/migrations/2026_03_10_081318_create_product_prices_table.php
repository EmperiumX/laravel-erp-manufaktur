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
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Relasi ke tabel products
            $table->enum('category',['Mitra', 'Agen', 'Distributor', 'Reseller', 'End User', 'Maklon']); // Sama seperti kategori Toko
            $table->decimal('price', 15, 2); // Harga Jual untuk kategori tersebut
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
