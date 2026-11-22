<?php

namespace App\Http\Controllers;

use App\Models\ShopSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('settings.index', [
            'shop' => ShopSetting::current(),
        ]);
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'current_password' => ['nullable', 'current_password'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        if (!empty($validated['password']) && empty($validated['current_password'])) {
            return back()
                ->withErrors(['current_password' => 'Masukkan password lama untuk mengganti password.'])
                ->withInput();
        }

        $user->name = $validated['name'];
        $user->username = $validated['username'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Konfigurasi akun berhasil diperbarui.');
    }

    public function updateShop(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_warung' => ['required', 'string', 'max:120'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:30'],
        ]);

        ShopSetting::current()->update($validated);

        return back()->with('success', 'Informasi warung berhasil diperbarui.');
    }
}
