<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\OperationalClosing;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationalClosingTest extends TestCase
{
    use RefreshDatabase;

    public function test_closing_saves_daily_sales_cash_difference_and_stock_snapshot(): void
    {
        $ingredient = Ingredient::create([
            'nama_bahan' => 'Ayam Potong',
            'satuan' => 'potong',
            'stok_saat_ini' => 4,
            'stok_minimum' => 2,
        ]);

        Transaction::create([
            'nomor_struk' => 'NOTA-TEST-1',
            'total_bayar' => 30000,
            'nominal_diterima' => 50000,
            'kembalian' => 20000,
            'metode_bayar' => 'TUNAI',
        ]);

        $this->post(route('closings.store'), [
            'cash_counted' => 28000,
            'notes' => 'Selisih kas dicatat',
        ])->assertRedirect(route('closings.index'))
            ->assertSessionHas('success', 'Tutup operasional berhasil disimpan.');

        $this->assertDatabaseHas('operational_closings', [
            'closing_date' => now()->toDateString(),
            'total_sales' => 30000,
            'transaction_count' => 1,
            'cash_counted' => 28000,
            'cash_difference' => -2000,
            'notes' => 'Selisih kas dicatat',
        ]);

        $closing = OperationalClosing::firstOrFail();
        $this->assertSame([
            [
                'name' => 'Ayam Potong',
                'unit' => 'potong',
                'quantity' => 4,
            ],
        ], $closing->stock_snapshot);
        $this->assertSame($ingredient->id, Ingredient::firstOrFail()->id);
    }

    public function test_daily_closing_cannot_be_submitted_twice(): void
    {
        OperationalClosing::create([
            'closing_date' => now()->toDateString(),
            'total_sales' => 0,
            'transaction_count' => 0,
            'cash_counted' => 0,
            'cash_difference' => 0,
            'stock_snapshot' => [],
            'closed_at' => now(),
        ]);

        $this->post(route('closings.store'), ['cash_counted' => 0])
            ->assertRedirect(route('closings.index'))
            ->assertSessionHas('error', 'Operasional hari ini sudah ditutup.');

        $this->assertDatabaseCount('operational_closings', 1);
    }
}
