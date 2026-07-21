<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// =======================
// PUBLIC (Tanpa Login)
// =======================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// =======================
// PROTECTED (Harus Login)
// =======================
Route::middleware('auth')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/home', function () {
        return view('home');
    })->name('home');
    
});

// Redirect root ke home (nanti middleware yang handle kalau belum login)
Route::get('/', function () {
    return redirect()->route('home');
});