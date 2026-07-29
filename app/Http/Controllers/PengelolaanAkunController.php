<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Pasar;

class PengelolaanAkunController extends Controller
{
    /**
     * Menampilkan daftar akun pengguna.
     */
    public function index(Request $request)
    {
        $query = User::with(['role', 'pasar']);

        // Pencarian berdasarkan nama atau email
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('id_role', $request->role);
        }

        $users = $query
            ->orderBy('nama_lengkap')
            ->get();

        $roles = Role::orderBy('id_role')->get();
        $pasars = Pasar::orderBy('nama_pasar')->get();

        return view('staff.akun.index', compact('users', 'roles', 'pasars'));
    
    }

    /**
     * Mengaktifkan atau menonaktifkan akun pengguna.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Staff tidak boleh menonaktifkan akun yang sedang digunakan sendiri
        if ($user->id_user === auth()->id()) {
            return back()->with(
                'error',
                'Anda tidak dapat mengubah status akun sendiri.'
            );
        }

        $user->update([
            'status_akun' => $user->status_akun === 'Aktif'
                ? 'Nonaktif'
                : 'Aktif'
        ]);

        return back()->with(
            'success',
            'Status akun berhasil diperbarui.'
        );
    }

    /**
     * Memperbarui data akun pengguna.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->merge(['_form' => 'edit', '_edit_id' => $id]);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('user', 'email')->ignore($user->id_user, 'id_user'),
            ],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
        ]);

        $user->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
        ]);

        return back()->with(
            'success',
            'Data akun berhasil diperbarui.'
        );
    }

    public function store(Request $request)
    {
        $request->merge(['_form' => 'tambah']);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:user,email',
            'id_role' => 'required|exists:role,id_role',
            'id_pasar' => [
                'nullable',
                'exists:pasar,id_pasar',
                Rule::requiredIf(function () use ($request) {
                    return (int) $request->id_role === 1;
                }),
            ],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'id_role.required' => 'Role wajib dipilih.',
            'id_pasar.required_if' => 'Pasar wajib dipilih untuk Petugas UPTD.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
        ]);

        // Pasar hanya boleh dimiliki Petugas UPTD
        if ((int) $validated['id_role'] !== 1) {
            $validated['id_pasar'] = null;
        }

        User::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'id_role' => $validated['id_role'],
            'id_pasar' => $validated['id_pasar'] ?? null,
            'password' => Hash::make($validated['password']),
            'status_akun' => 'Aktif',
        ]);

        return redirect()
            ->route('staff.akun.index')
            ->with('success', 'Akun berhasil ditambahkan.');
    }

}