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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique(); // PAY-20260519-001
            $table->foreignId('invoice_id')->constrained('invoices');
            $table->enum('type', ['inbound', 'outbound']); // inbound = terima dari mitra, outbound = bayar ke supplier
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['Cash', 'Transfer', 'Giro', 'Cek', 'Lainnya'])->default('Transfer');
            $table->string('reference')->nullable(); // No rekening / no giro / referensi transfer
            $table->foreignId('cash_bank_id')->nullable()->constrained('cash_banks'); // Dari akun kas/bank mana
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
