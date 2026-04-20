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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique()->nullable(); // PLU/SKU
            $table->string('name'); // Nama Produk (Contoh: Bandeng Retort)
            $table->integer('weight')->nullable(); // Berat Produk (Contoh: 250)
            $table->string('weight_unit')->nullable(); // Satuan Berat (Contoh: Gram)
            $table->string('packaging')->nullable(); // Kemasan (Contoh: Pack, Dus)
            $table->decimal('hpp', 15, 2)->default(0); // HPP Dasar (Akan dihitung otomatis nanti)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
