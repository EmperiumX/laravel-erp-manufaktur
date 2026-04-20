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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained('stock_items')->onDelete('cascade');
            $table->enum('type', ['IN', 'OUT']); // Barang Masuk / Keluar
            $table->decimal('quantity', 10, 4); // Jumlah yang dipindah
            $table->string('reference'); // Nomor Referensi, cth: "PO-20260311-001" atau "PROD-005" atau "RETUR-01"
            $table->text('notes')->nullable(); // Keterangan tambahan
            $table->foreignId('user_id')->constrained('users'); // Siapa admin yang melakukan mutasi ini
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
