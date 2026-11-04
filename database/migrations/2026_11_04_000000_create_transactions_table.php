<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_struk', 50)->unique();
            $table->decimal('total_bayar', 10, 2)->default(0);
            $table->decimal('nominal_diterima', 10, 2)->default(0);
            $table->decimal('kembalian', 10, 2)->default(0);
            $table->string('metode_bayar', 10)->default('TUNAI');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
