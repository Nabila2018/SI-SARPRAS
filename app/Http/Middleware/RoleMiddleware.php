<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Cek role sesuai
        $userRole = auth()->user()->role->nama_role;
        
        if ($userRole !== $role) {
            abort(403, 'Akses ditolak. Role tidak sesuai.');
        }

        return $next($request);
    }
}