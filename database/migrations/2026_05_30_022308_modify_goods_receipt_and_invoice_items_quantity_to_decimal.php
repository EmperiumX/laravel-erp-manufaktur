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
        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->decimal('quantity_ordered', 10, 4)->change();
            $table->decimal('quantity_received', 10, 4)->change();
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_receipt_items', function (Blueprint $table) {
            $table->integer('quantity_ordered')->change();
            $table->integer('quantity_received')->change();
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
    }
};
