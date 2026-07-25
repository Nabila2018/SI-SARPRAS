<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
{
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    $credentials = [
        'username' => $request->username,
        'password' => $request->password,
        'status_akun' => 'Aktif',
    ];

    if (Auth::attempt($credentials)) {

        $request->session()->regenerate();

        // Redirect berdasarkan role
        $role = auth()->user()->role->nama_role;

        return match ($role) {
            'Petugas UPTD' => redirect()->route('home'),
            'Staff Sarana dan Prasarana' => redirect()->route('home'),
            'Kepala Bidang' => redirect()->route('home'),
            'Kepala Dinas' => redirect()->route('home'),
            default => redirect()->route('home'),
        };
    }

    return back()->withErrors([
        'username' => 'Username atau password salah, atau akun sedang nonaktif.',
    ])->onlyInput('username');
}

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}