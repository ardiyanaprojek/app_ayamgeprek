<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_and_searches_products(): void
    {
        $ingredient = Ingredient::create([
            'nama_bahan' => 'Ayam Potong',
            'satuan' => 'potong',
            'stok_minimum' => 5,
        ]);

        Product::create([
            'nama_menu' => 'Ayam Geprek Dada',
            'harga_jual' => 15000,
            'ingredient_id' => $ingredient->id,
            'jumlah_potong' => 1,
        ]);

        Product::create([
            'nama_menu' => 'Es Teh Manis',
            'harga_jual' => 4000,
            'ingredient_id' => null,
            'jumlah_potong' => 0,
        ]);

        $this->get(route('products.index', ['q' => 'Ayam']))
            ->assertOk()
            ->assertSee('Ayam Geprek Dada')
            ->assertDontSee('Es Teh Manis');
    }

    public function test_store_creates_product(): void
    {
        $ingredient = Ingredient::create([
            'nama_bahan' => 'Usus Porsi',
            'satuan' => 'porsi',
            'stok_minimum' => 3,
        ]);

        $this->post(route('products.store'), [
            'nama_menu' => 'Usus Geprek',
            'harga_jual' => 14000,
            'ingredient_id' => $ingredient->id,
            'jumlah_potong' => 1,
        ])->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'nama_menu' => 'Usus Geprek',
            'harga_jual' => 14000.00,
            'ingredient_id' => $ingredient->id,
            'jumlah_potong' => 1,
        ]);
    }

    public function test_update_changes_product_details(): void
    {
        $ingredient = Ingredient::create([
            'nama_bahan' => 'Ayam Potong',
            'satuan' => 'potong',
            'stok_minimum' => 5,
        ]);

        $product = Product::create([
            'nama_menu' => 'Ayam Geprek',
            'harga_jual' => 12000,
            'ingredient_id' => $ingredient->id,
            'jumlah_potong' => 1,
        ]);

        $this->put(route('products.update', $product), [
            'nama_menu' => 'Ayam Geprek Dada',
            'harga_jual' => 16000,
            'ingredient_id' => $ingredient->id,
            'jumlah_potong' => 2,
        ])->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'nama_menu' => 'Ayam Geprek Dada',
            'harga_jual' => 16000.00,
            'jumlah_potong' => 2,
        ]);
    }

    public function test_destroy_deletes_product(): void
    {
        $ingredient = Ingredient::create([
            'nama_bahan' => 'Ayam Potong',
            'satuan' => 'potong',
            'stok_minimum' => 5,
        ]);

        $product = Product::create([
            'nama_menu' => 'Ayam Geprek',
            'harga_jual' => 12000,
            'ingredient_id' => $ingredient->id,
            'jumlah_potong' => 1,
        ]);

        $this->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
