<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        // validasi input
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $search = $validated['q'] ?? '';
        // jika ada pencarian akan di proses disini
        $products = Product::query()
            ->with('ingredient')
            ->when($search !== '', fn ($query) => $query->where('nama_menu', 'like', '%'.$search.'%'))
            ->orderBy('nama_menu')
            ->paginate(10)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'search' => $search,
            'ingredients' => Ingredient::query()->orderBy('nama_bahan')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // validasi inpuut
        $validated = $request->validate([
            'nama_menu' => ['required', 'string', 'max:120'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'ingredient_id' => ['nullable', 'integer', 'exists:ingredients,id'],
            'jumlah_potong' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['ingredient_id'] = $validated['ingredient_id'] ?? null;
        $validated['jumlah_potong'] = $validated['ingredient_id'] ? ($validated['jumlah_potong'] ?? 1) : 0;
        // proses menambah menu
        $product = new Product($validated);
        $product->save();

        return redirect()
            ->route('products.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        // validasi input
        $validated = $request->validate([
            'nama_menu' => ['required', 'string', 'max:120'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'ingredient_id' => ['nullable', 'integer', 'exists:ingredients,id'],
            'jumlah_potong' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['ingredient_id'] = $validated['ingredient_id'] ?? null;
        $validated['jumlah_potong'] = $validated['ingredient_id'] ? ($validated['jumlah_potong'] ?? 1) : 0;
        // proeses edit data
        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // proses mengahpus menu
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Menu berhasil dihapus.');
    }
}
