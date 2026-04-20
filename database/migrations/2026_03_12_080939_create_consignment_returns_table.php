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
        Schema::create('consignment_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique(); // Nomor Bukti Retur, cth: RET-20260312-001
            $table->foreignId('store_id')->constrained('stores'); // Toko mana yang mengembalikan
            $table->date('return_date'); // Tanggal retur
            $table->text('notes')->nullable(); // Alasan retur (cth: "Barang kedaluwarsa")
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consignment_returns');
    }
};
