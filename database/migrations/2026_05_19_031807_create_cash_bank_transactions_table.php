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
        Schema::create('cash_bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_bank_id')->constrained('cash_banks');
            $table->enum('type', ['Debit', 'Credit']); // Debit = masuk, Credit = keluar
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2); // Saldo setelah transaksi
            $table->date('transaction_date');
            $table->string('reference')->nullable(); // Referensi (No. Payment, No. Invoice, dll)
            $table->string('description'); // Deskripsi transaksi
            $table->enum('category', [
                'Penjualan', 'Pembelian', 'Pembayaran Piutang', 'Pembayaran Hutang',
                'Biaya Operasional', 'Gaji', 'Setoran Modal', 'Penarikan',
                'Transfer Antar Akun', 'Lainnya'
            ])->default('Lainnya');
            $table->boolean('is_reconciled')->default(false); // Status rekonsiliasi
            $table->foreignId('payment_id')->nullable()->constrained('payments');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_bank_transactions');
    }
};
