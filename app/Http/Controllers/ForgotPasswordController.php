<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Tampilkan form minta tautan reset kata sandi (Lupa Password).
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Kirim email tautan reset kata sandi.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        // Kirim tautan reset via Laravel Password broker
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Untuk keamanan, tampilkan pesan sukses generik
        // tanpa membocorkan apakah email terdaftar di sistem atau tidak
        return back()->with(
            'status',
            'Jika email Anda terdaftar dalam sistem, tautan untuk mereset kata sandi telah dikirimkan ke email Anda.'
        );
    }

    /**
     * Tampilkan form reset kata sandi setelah tautan di email diklik.
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Proses eksekusi reset kata sandi baru.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with('status', 'Kata sandi berhasil diperbarui. Silakan masuk menggunakan kata sandi baru Anda.');
        }

        return back()
            ->withErrors(['email' => 'Tautan reset kata sandi tidak valid atau telah kedaluwarsa.'])
            ->withInput($request->only('email'));
    }
}
