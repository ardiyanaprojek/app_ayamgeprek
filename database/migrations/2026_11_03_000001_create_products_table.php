<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('nama_menu', 120);
            $table->decimal('harga_jual', 10, 2)->default(0);
            $table->foreignId('ingredient_id')->nullable()->constrained('ingredients')->nullOnDelete();
            $table->integer('jumlah_potong')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
