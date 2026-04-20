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
        Schema::create('bill_of_materials', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel products (Barang Jadi)
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            // Relasi ke tabel materials (Bahan Baku)
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            
            // Jumlah bahan baku yang dibutuhkan untuk 1 satuan produk.
            // Kita pakai decimal(10, 4) karena di PDF ada angka desimal seperti 16.67 (gram)
            $table->decimal('quantity', 10, 4); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_of_materials');
    }
};
