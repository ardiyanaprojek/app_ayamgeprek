<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_transaction_and_reduces_stock(): void
    {
        $ingredient = Ingredient::create([
            'nama_bahan' => 'Ayam Potong',
            'satuan' => 'potong',
            'stok_saat_ini' => 10,
            'stok_minimum' => 5,
        ]);

        $product = Product::create([
            'nama_menu' => 'Ayam Geprek Dada',
            'harga_jual' => 15000,
            'ingredient_id' => $ingredient->id,
            'jumlah_potong' => 2,
        ]);

        $this->post(route('transactions.store'), [
            'items' => [[
                'product_id' => $product->id,
                'qty' => 2,
                'catatan' => 'Pedas sedang',
            ]],
            'nominal_diterima' => 50000,
        ])->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'total_bayar' => 30000.00,
            'nominal_diterima' => 50000.00,
            'kembalian' => 20000.00,
            'metode_bayar' => 'TUNAI',
        ]);

        $this->assertDatabaseHas('transaction_items', [
            'product_id' => $product->id,
            'qty' => 2,
            'subtotal' => 30000.00,
            'catatan' => 'Pedas sedang',
        ]);

        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
            'stok_saat_ini' => 6,
        ]);
    }

    public function test_store_rejects_when_stock_is_insufficient(): void
    {
        $ingredient = Ingredient::create([
            'nama_bahan' => 'Usus Porsi',
            'satuan' => 'porsi',
            'stok_saat_ini' => 2,
            'stok_minimum' => 1,
        ]);

        $product = Product::create([
            'nama_menu' => 'Usus Geprek',
            'harga_jual' => 12000,
            'ingredient_id' => $ingredient->id,
            'jumlah_potong' => 2,
        ]);

        $this->from(route('transactions.index'))
            ->post(route('transactions.store'), [
                'items' => [[
                    'product_id' => $product->id,
                    'qty' => 2,
                ]],
                'nominal_diterima' => 50000,
            ])
            ->assertRedirect(route('transactions.index'))
            ->assertSessionHas('error', 'Stok bahan tidak mencukupi untuk beberapa menu.');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
            'stok_saat_ini' => 2,
        ]);
    }
}
