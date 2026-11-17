<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class IngredientTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create([
            'name' => 'Pemilik Warung',
            'username' => 'owner',
        ]));
    }

    public function test_index_displays_and_searches_ingredients(): void
    {
        Ingredient::create([
            'nama_bahan' => 'Ayam Potong',
            'satuan' => 'potong',
            'stok_minimum' => 5,
        ]);
        Ingredient::create([
            'nama_bahan' => 'Usus Bersih',
            'satuan' => 'porsi',
            'stok_minimum' => 2,
        ]);

        $this->get(route('ingredients.index', ['q' => 'Ayam']))
            ->assertOk()
            ->assertSee('Ayam Potong')
            ->assertDontSee('Usus Bersih');
    }

    public function test_store_creates_ingredient_with_zero_physical_stock(): void
    {
        $this->post(route('ingredients.store'), [
            'nama_bahan' => 'Kulit Ayam',
            'satuan' => 'potong',
            'stok_minimum' => 5,
        ])->assertRedirect(route('ingredients.index'));

        $this->assertDatabaseHas('ingredients', [
            'nama_bahan' => 'Kulit Ayam',
            'satuan' => 'potong',
            'stok_saat_ini' => 0,
            'stok_minimum' => 5,
        ]);
    }

    public function test_store_rejects_units_outside_the_allowed_options(): void
    {
        $this->from(route('ingredients.index'))
            ->post(route('ingredients.store'), [
                'nama_bahan' => 'Tepung',
                'satuan' => 'gram',
            ])
            ->assertRedirect(route('ingredients.index'))
            ->assertSessionHasErrors('satuan');

        $this->assertDatabaseCount('ingredients', 0);
    }

    public function test_update_changes_ingredient_details(): void
    {
        $ingredient = Ingredient::create([
            'nama_bahan' => 'Ayam',
            'satuan' => 'potong',
            'stok_minimum' => 0,
        ]);

        $this->put(route('ingredients.update', $ingredient), [
            'nama_bahan' => 'Ayam Potong',
            'satuan' => 'potong',
            'stok_minimum' => 5,
        ])->assertRedirect(route('ingredients.index'));

        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
            'nama_bahan' => 'Ayam Potong',
            'stok_minimum' => 5,
        ]);
    }

    public function test_destroy_rejects_ingredients_used_by_products(): void
    {
        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ingredient_id')->nullable();
            });
        }

        $ingredient = Ingredient::create([
            'nama_bahan' => 'Ayam Potong',
            'satuan' => 'potong',
            'stok_minimum' => 0,
        ]);

        $productData = ['ingredient_id' => $ingredient->id];
        if (Schema::hasColumn('products', 'nama_menu')) {
            $productData['nama_menu'] = 'Ayam Geprek';
            $productData['harga_jual'] = 15000;
            $productData['jumlah_potong'] = 1;
        }

        DB::table('products')->insert($productData);

        $this->delete(route('ingredients.destroy', $ingredient))
            ->assertRedirect(route('ingredients.index'))
            ->assertSessionHas('error', 'Bahan sedang digunakan oleh menu jualan');

        $this->assertDatabaseHas('ingredients', ['id' => $ingredient->id]);
    }

    public function test_destroy_deletes_unused_ingredient(): void
    {
        $ingredient = Ingredient::create([
            'nama_bahan' => 'Tahu Mentah',
            'satuan' => 'biji',
            'stok_minimum' => 0,
        ]);

        $this->delete(route('ingredients.destroy', $ingredient))
            ->assertRedirect(route('ingredients.index'));

        $this->assertDatabaseMissing('ingredients', ['id' => $ingredient->id]);
    }
}
