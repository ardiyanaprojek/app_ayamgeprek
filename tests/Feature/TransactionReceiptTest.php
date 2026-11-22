<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionReceiptTest extends TestCase
{
    use RefreshDatabase;

    public function test_completed_transaction_redirects_to_printable_receipt(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'nama_menu' => 'Ayam Geprek',
            'harga_jual' => 15000,
            'ingredient_id' => null,
            'jumlah_potong' => 0,
        ]);

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 2,
                    'catatan' => 'Pedas',
                ],
            ],
            'nominal_diterima' => 40000,
        ]);

        $response->assertRedirect();

        $location = $response->headers->get('Location');
        $this->assertNotNull($location);
        $this->assertMatchesRegularExpression('/\/transactions\/\d+\/receipt$/', $location);

        $transactionId = (int) preg_replace('/.*\/transactions\/(\d+)\/receipt/', '$1', $location);
        $this->actingAs($user)
            ->get(route('transactions.receipt', $transactionId))
            ->assertOk()
            ->assertSee('AYAM GEPREK ISTIQOMAH')
            ->assertSee('Ayam Geprek')
            ->assertSee('Rp 30.000');
    }
}
