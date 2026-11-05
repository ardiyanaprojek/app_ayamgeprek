<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_stock_summary(): void
    {
        Ingredient::create([
            'nama_bahan' => 'Ayam Potong',
            'satuan' => 'potong',
            'stok_saat_ini' => 8,
            'stok_minimum' => 5,
        ]);

        $this->get(route('stocks.index'))
            ->assertOk()
            ->assertSee('Ayam Potong')
            ->assertSee('Stok saat ini');
    }

    public function test_store_adds_stock_and_logs_it(): void
    {
        $ingredient = Ingredient::create([
            'nama_bahan' => 'Telur',
            'satuan' => 'butir',
            'stok_saat_ini' => 10,
            'stok_minimum' => 5,
        ]);

        $this->post(route('stocks.store'), [
            'ingredient_id' => $ingredient->id,
            'tipe' => 'masuk',
            'jumlah' => 20,
            'keterangan' => 'Belanja pagi',
        ])->assertRedirect(route('stocks.index'));

        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
            'stok_saat_ini' => 30,
        ]);

        $this->assertDatabaseHas('stock_logs', [
            'ingredient_id' => $ingredient->id,
            'tipe' => 'masuk',
            'jumlah' => 20,
            'keterangan' => 'Belanja pagi',
        ]);
    }

    public function test_store_reduces_stock_for_damage_or_opname(): void
    {
        $ingredient = Ingredient::create([
            'nama_bahan' => 'Usus Porsi',
            'satuan' => 'porsi',
            'stok_saat_ini' => 12,
            'stok_minimum' => 4,
        ]);

        $this->post(route('stocks.store'), [
            'ingredient_id' => $ingredient->id,
            'tipe' => 'rusak_basi',
            'jumlah' => 2,
            'keterangan' => 'Basi 2 porsi',
        ])->assertRedirect(route('stocks.index'));

        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
            'stok_saat_ini' => 10,
        ]);
    }

    public function test_store_rejects_sales_as_manual_stock_mutation(): void
    {
        $ingredient = Ingredient::create([
            'nama_bahan' => 'Ayam Potong',
            'satuan' => 'potong',
            'stok_saat_ini' => 10,
            'stok_minimum' => 3,
        ]);

        $this->from(route('stocks.index'))
            ->post(route('stocks.store'), [
                'ingredient_id' => $ingredient->id,
                'tipe' => 'penjualan',
                'jumlah' => 2,
            ])
            ->assertRedirect(route('stocks.index'))
            ->assertSessionHasErrors('tipe');

        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
            'stok_saat_ini' => 10,
        ]);
        $this->assertDatabaseCount('stock_logs', 0);
    }

    public function test_stock_history_is_available_on_its_own_page(): void
    {
        $ingredient = Ingredient::create([
            'nama_bahan' => 'Telur',
            'satuan' => 'butir',
            'stok_saat_ini' => 4,
            'stok_minimum' => 2,
        ]);

        \App\Models\StockLog::create([
            'ingredient_id' => $ingredient->id,
            'tipe' => 'penjualan',
            'jumlah' => 1,
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Penjualan Telur',
        ]);

        $this->get(route('stocks.history'))
            ->assertOk()
            ->assertSee('Riwayat Stok')
            ->assertSee('Penjualan kasir')
            ->assertSee('Telur');

        $this->get(route('stocks.index'))
            ->assertOk()
            ->assertDontSee('Riwayat terbaru');
    }
}
