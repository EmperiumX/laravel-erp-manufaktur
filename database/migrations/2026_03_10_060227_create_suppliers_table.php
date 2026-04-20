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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Perusahaan/Supplier
            $table->string('contact_person')->nullable(); // Nama orang yang bisa dihubungi
            $table->string('phone_number')->nullable(); // Nomor HP/Telepon
            $table->text('address')->nullable(); // Alamat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
