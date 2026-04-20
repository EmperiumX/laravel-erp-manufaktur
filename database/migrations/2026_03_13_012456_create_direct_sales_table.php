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
        Schema::create('direct_sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // Nomor Nota, cth: INV-20260313-001
            
            // Kita buat fleksibel: Bisa pilih dari daftar Toko/Mitra, atau ketik nama pembeli umum (walk-in customer)
            $table->foreignId('store_id')->nullable()->constrained('stores'); 
            $table->string('customer_name')->nullable(); 
            
            $table->date('sale_date'); // Tanggal transaksi
            $table->decimal('total_amount', 15, 2)->default(0); // Total Rp belanja
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direct_sales');
    }
};
