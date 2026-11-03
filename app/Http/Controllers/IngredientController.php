<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IngredientController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $search = $validated['q'] ?? '';

        $ingredients = Ingredient::query()
            ->when($search !== '', fn ($query) => $query->where('nama_bahan', 'like', '%'.$search.'%'))
            ->orderBy('nama_bahan')
            ->paginate(10)
            ->withQueryString();

        return view('ingredients.index', [
            'ingredients' => $ingredients,
            'search' => $search,
            'units' => Ingredient::UNITS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_bahan' => ['required', 'string', 'max:100'],
            'satuan' => ['required', Rule::in(Ingredient::UNITS)],
            'stok_minimum' => ['nullable', 'integer', 'min:0'],
        ]);

        $ingredient = new Ingredient($validated);
        $ingredient->stok_saat_ini = 0;
        $ingredient->stok_minimum = $validated['stok_minimum'] ?? 0;
        $ingredient->save();

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function update(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $validated = $request->validate([
            'nama_bahan' => ['required', 'string', 'max:100'],
            'satuan' => ['required', Rule::in(Ingredient::UNITS)],
            'stok_minimum' => ['nullable', 'integer', 'min:0'],
        ]);

        $ingredient->update([
            'nama_bahan' => $validated['nama_bahan'],
            'satuan' => $validated['satuan'],
            'stok_minimum' => $validated['stok_minimum'] ?? 0,
        ]);

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy(Ingredient $ingredient): RedirectResponse
    {
        if (
            Schema::hasTable('products')
            && Schema::hasColumn('products', 'ingredient_id')
            && DB::table('products')->where('ingredient_id', $ingredient->id)->exists()
        ) {
            return redirect()
                ->route('ingredients.index')
                ->with('error', 'Bahan sedang digunakan oleh menu jualan');
        }

        $ingredient->delete();

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Bahan baku berhasil dihapus.');
    }
}
