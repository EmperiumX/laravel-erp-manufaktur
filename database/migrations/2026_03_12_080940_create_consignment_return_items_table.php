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
        Schema::create('consignment_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consignment_return_id')->constrained('consignment_returns')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products'); // Produk apa yang diretur
            $table->integer('quantity'); // Jumlah pack/biji yang diretur
            $table->enum('condition', ['Bagus', 'Rusak/Basi'])->default('Bagus'); // Kondisi barang saat diterima pabrik
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_return_items');
    }
};
