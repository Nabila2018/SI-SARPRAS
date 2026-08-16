<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Menampilkan profil user yang sedang login.
     */
    public function show()
    {
        $user = auth()->user();

        // Load relasi role dan pasar
        $user->load(['role', 'pasar']);

        return view('profil.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
        ]);

        $user->update([
            'nama_lengkap' => $validated['nama_lengkap'],
        ]);

        return redirect()
            ->route('profil.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
    $user = auth()->user();

    $validated = $request->validate([
    'current_password' => ['required'],
    'password' => ['required', 'string', 'min:8', 'confirmed'],
    ], 
    [
    'current_password.required' => 'Password saat ini wajib diisi.',
    'password.required' => 'Password baru wajib diisi.',
    'password.min' => 'Password baru minimal 8 karakter.',
    'password.confirmed' => 'Konfirmasi password baru tidak sesuai.',
    ]);

    // Cek apakah password saat ini benar
    if (!Hash::check($validated['current_password'], $user->password)) {
        return back()
            ->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.'
            ])
            ->with('password_error', true);
    }

    // Simpan password baru
    $user->update([
        'password' => Hash::make($validated['password']),
    ]);

    return redirect()
        ->route('profil.show')
        ->with('success', 'Password berhasil diperbarui.');
    }
}