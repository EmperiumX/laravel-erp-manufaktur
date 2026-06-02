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
        Schema::create('cash_banks', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama akun: Kas Kecil, BCA 1234567890, Mandiri, dll
            $table->enum('type', ['Cash', 'Bank']); // Kas atau Bank
            $table->string('account_number')->nullable(); // Nomor rekening (untuk Bank)
            $table->string('bank_name')->nullable(); // Nama bank (BCA, Mandiri, BRI, dll)
            $table->decimal('balance', 15, 2)->default(0); // Saldo saat ini
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_banks');
    }
};
