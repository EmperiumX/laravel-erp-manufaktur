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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('labor_cost', 15, 2)->default(2656.00)->after('hpp');
            $table->decimal('overhead_cost', 15, 2)->default(576.00)->after('labor_cost');
            $table->decimal('other_cost', 15, 2)->default(0.00)->after('overhead_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['labor_cost', 'overhead_cost', 'other_cost']);
        });
    }
};
