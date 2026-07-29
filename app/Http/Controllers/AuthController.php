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
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Step 1: Check credentials without status_akun constraint
        $credentialsOnly = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentialsOnly)) {
            // Email does not exist OR password is wrong — do not reveal which
            return back()->withErrors([
                'email' => 'Email atau kata sandi salah.',
            ])->onlyInput('email');
        }

        // Credentials are valid — now check account status
        $user = Auth::user();
        Auth::logout(); // Log them back out until we confirm active

        if ($user->status_akun !== 'Aktif') {
            return back()->withErrors([
                'email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi pengelola sistem.',
            ])->onlyInput('email');
        }

        // Step 2: Credentials valid + account active — log in properly
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Redirect berdasarkan role
        $role = $user->role->nama_role;

        return match ($role) {
            'Petugas UPTD' => redirect()->route('home'),
            'Staff Sarana dan Prasarana' => redirect()->route('home'),
            'Kepala Bidang' => redirect()->route('home'),
            'Kepala Dinas' => redirect()->route('home'),
            default => redirect()->route('home'),
        };
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