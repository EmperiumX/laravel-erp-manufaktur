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
        Schema::create('consignment_shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique(); // Nomor Surat Jalan, cth: DO-20260312-001
            $table->foreignId('store_id')->constrained('stores'); // Dikirim ke toko mana
            $table->date('shipment_date'); // Tanggal pengiriman
            $table->enum('status', ['Sent', 'Invoiced', 'Canceled'])->default('Sent'); // Sent = Terkirim/Dititipkan
            $table->decimal('total_amount', 15, 2)->default(0); // Total nilai rupiah barang yang dititipkan
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_shipments');
    }
};
