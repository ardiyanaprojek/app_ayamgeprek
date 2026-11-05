<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_closings', function (Blueprint $table) {
            $table->id();
            $table->date('closing_date')->unique();
            $table->decimal('total_sales', 12, 2)->default(0);
            $table->unsignedInteger('transaction_count')->default(0);
            $table->decimal('cash_counted', 12, 2);
            $table->decimal('cash_difference', 12, 2)->default(0);
            $table->json('stock_snapshot');
            $table->text('notes')->nullable();
            $table->timestamp('closed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_closings');
    }
};
