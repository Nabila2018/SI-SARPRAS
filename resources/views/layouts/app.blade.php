<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SI-SARPRAS')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .sidebar-gradient {
            background: linear-gradient(180deg, #115f8c 0%, #0D929F 70%, #16A394 100%);
        }
        .menu-item:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        .menu-item.active {
            background: rgba(255, 255, 255, 0.2);
            border-left: 3px solid white;
        }
    </style>
    @yield('styles')
</head>
<body class="bg-gray-100 min-h-screen flex">

    <!-- SIDEBAR KIRI -->
    <aside class="sidebar-gradient w-64 h-screen flex flex-col text-white fixed left-0 top-0 z-50">
        
        <!-- Logo -->
        <div class="p-6 flex justify-center border-b border-white/20">
            <img src="{{ asset('images/Logo Dinas Perdagangan Kota Padang.png') }}"
                 alt="Logo Dinas Perdagangan"
                 class="h-16 w-auto object-contain drop-shadow-md">
        </div>

                <!-- Menu -->
        <nav class="flex-1 py-6 px-4 space-y-1 overflow-y-auto">
            <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-3 px-2">Menu</p>
            
            {{-- DASHBOARD - Semua Role --}}
            <a href="{{ route('home') }}" class="menu-item {{ request()->is('home') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            {{-- MENU PETUGAS UPTD --}}
            @if(auth()->user()->role->nama_role === 'Petugas UPTD')
                <a href="{{ route('laporan.create') }}" class="menu-item {{ request()->is('laporan/create') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="font-medium">Buat Laporan</span>
                </a>
                <a href="{{ route('laporan.index') }}" class="menu-item {{ request()->is('laporan') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="font-medium">Riwayat Laporan</span>
                </a>
                <a href="{{ route('panduan') }}" class="menu-item {{ request()->is('panduan') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="font-medium">Panduan</span>
                </a>
            @endif

            {{-- MENU STAFF SARANA DAN PRASARANA --}}
            @if(auth()->user()->role->nama_role === 'Staff Sarana dan Prasarana')
                <a href="{{ route('staff.laporan.index') }}" class="menu-item {{ request()->is('staff/laporan*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="font-medium">Daftar Laporan Masuk</span>
                </a>

                 {{-- RAB --}}
                <a href="{{ route('staff.rab.index') }}" 
                   class="menu-item {{ request()->is('staff/rab*') || request()->is('staff/laporan/*/rab') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium">RAB</span>
                </a>

                <a href="#" class="menu-item {{ request()->is('staff/progress*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span class="font-medium">Progress Perbaikan</span>
                </a>
                <a href="{{ route('staff.spj.index') }}" class="menu-item {{ request()->is('staff/spj*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="font-medium">Dokumen Pertanggungjawaban</span>
                </a>
                <a href="{{ route('staff.akun.index') }}"
   class="menu-item {{ request()->is('staff/akun*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-all">   <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="font-medium">Pengelolaan Akun</span>
                </a>
            @endif

            {{-- MENU KEPALA BIDANG --}}
            @if(auth()->user()->role->nama_role === 'Kepala Bidang')

    {{-- Verifikasi Laporan --}}
    <a href="{{ route('kabid.laporan.index') }}"
       class="menu-item {{ request()->is('kabid/laporan*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 01-2-2h2a2 2 0 012 2"/>
        </svg>

        <span class="font-medium">Verifikasi Laporan</span>
    </a>


    {{-- Daftar RAB --}}
    <a href="{{ route('kabid.rab.index') }}"
       class="menu-item {{ request()->is('kabid/rab*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>

        <span class="font-medium">Daftar RAB</span>
    </a>


    {{-- SPJ & Realisasi --}}
    <a href="{{ route('staff.spj.index') }}"
       class="menu-item {{ request()->is('staff/spj*') || request()->is('kabid/spj*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>

        <span class="font-medium">Dokumen Pertanggungjawaban</span>
    </a>

@endif

            {{-- MENU KEPALA DINAS --}}
            @if(auth()->user()->role->nama_role === 'Kepala Dinas')
                <a href="#" class="menu-item {{ request()->is('kadin/monitoring*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="font-medium">Monitoring</span>
                </a>
                <a href="{{ route('staff.spj.index') }}" class="menu-item {{ request()->is('staff/spj*') || request()->is('kadin/spj*') ? 'active' : '' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="font-medium">Dokumen Pertanggungjawaban</span>
                </a>
            @endif
        </nav>
       

        <!-- Profil User + Keluar (Kiri Bawah) -->
        <div class="p-4 border-t border-white/20 space-y-3">
            <a href="{{ route('profil.show') }}"
               title="Lihat Profil"
               class="flex items-center gap-3 p-2.5 rounded-lg transition-all hover:bg-white/10 cursor-pointer">

                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-sm font-bold shrink-0">
                    {{ strtoupper(substr(auth()->user()->nama_lengkap ?? 'A', 0, 1)) }}
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate">
                        {{ auth()->user()->nama_lengkap ?? 'Admin' }}
                    </p>
                    <p class="text-xs text-white/70 truncate">
                        {{ auth()->user()->role->nama_role ?? 'Staff' }}
                    </p>
                </div>

            </a>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg py-2.5 transition-all text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 ml-64 flex flex-col min-h-screen">
        
        <!-- TOP NAVBAR -->
        <header class="bg-white shadow-sm sticky top-0 z-40">
            <div class="flex items-center justify-between px-8 py-4">
                
                <!-- Breadcrumb -->
                <div class="flex items-center gap-2 text-sm">
                    <span class="font-bold text-[#003366]">SI-SARPRAS</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-gray-600">{!! $__env->yieldContent('breadcrumb') ?: 'Dashboard' !!}</span>
                </div>

                <!-- Notifikasi -->
                <div class="flex items-center gap-4">
                    <button class="relative p-2 text-gray-500 hover:text-[#003366] transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                </div>
            </div>
        </header>

        <!-- CONTENT AREA -->
        <main class="p-8 flex-1">
            @yield('content')
        </main>

        <!-- FOOTER -->
        <footer class="bg-white border-t shrink-0">
            <div class="px-8 py-4 text-center text-sm text-gray-500">
                © 2026 Dinas Perdagangan Kota Padang
            </div>
        </footer>
    </div>

    @yield('scripts')
</body>
</html>