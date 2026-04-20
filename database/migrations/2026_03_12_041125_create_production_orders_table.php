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
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('production_number')->unique(); // Contoh: PROD-20260312-001
            $table->foreignId('product_id')->constrained('products'); // Produk jadi apa yang mau dibuat?
            $table->integer('quantity'); // Berapa jumlah (pack/biji) yang mau diproduksi?
            $table->date('production_date'); // Rencana tanggal produksi
            $table->enum('status', ['Pending', 'Completed', 'Canceled'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};
