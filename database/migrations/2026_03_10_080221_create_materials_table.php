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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Bahan Baku, cth: Bawang Putih
            $table->enum('type',['Bahan Pokok', 'Bahan Penolong', 'Packaging']); // Jenis material
            $table->string('unit'); // Satuan, cth: Gram, Lembar, Ekor, Tabung
            // Kita pakai decimal(15, 2) karena di PDF ada harga seperti 11666.67
            $table->decimal('unit_price', 15, 2); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
