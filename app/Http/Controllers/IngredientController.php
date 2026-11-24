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
        // validasi input
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $search = $validated['q'] ?? '';
        // ini query jika ada pencarian
        $ingredients = Ingredient::query()
            ->when($search !== '', fn ($query) => $query->where('nama_bahan', 'like', '%'.$search.'%'))
            ->orderBy('nama_bahan')
            ->paginate(10)
            ->withQueryString();

        // menampilkan data ke halaman index
        return view('ingredients.index', [
            'ingredients' => $ingredients,
            'search' => $search,
            'units' => Ingredient::UNITS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // validasi input
        $validated = $request->validate([
            'nama_bahan' => ['required', 'string', 'max:100'],
            'satuan' => ['required', Rule::in(Ingredient::UNITS)],
            'stok_minimum' => ['nullable', 'integer', 'min:0'],
        ]);

        // proses membuat data baru
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
        // validasi input
        $validated = $request->validate([
            'nama_bahan' => ['required', 'string', 'max:100'],
            'satuan' => ['required', Rule::in(Ingredient::UNITS)],
            'stok_minimum' => ['nullable', 'integer', 'min:0'],
        ]);

        // proses edit data
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
        // mengecek bahan baku dipake menu lain atau tidak
        if (
            Schema::hasTable('products')
            && Schema::hasColumn('products', 'ingredient_id')
            && DB::table('products')->where('ingredient_id', $ingredient->id)->exists()
        ) {
            return redirect()
                ->route('ingredients.index')
                ->with('error', 'Bahan sedang digunakan oleh menu jualan');
        }
        // jika tidak dia bakal dihapus

        $ingredient->delete();

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Bahan baku berhasil dihapus.');
    }
}