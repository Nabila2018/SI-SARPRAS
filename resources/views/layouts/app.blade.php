<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SI-SARPRAS')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css"/>
    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1"></script>
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
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    @yield('styles')
</head>
<body class="bg-gray-100 min-h-screen flex">

    <!-- SIDEBAR KIRI -->
    <aside class="sidebar-gradient w-72 h-screen flex flex-col text-white fixed left-0 top-0 z-50">
        
        <!-- Logo -->
        <div class="p-6 flex justify-center border-b border-white/20">
            <img src="{{ asset('images/Logo Dinas Perdagangan Kota Padang.png') }}"
                 alt="Logo Dinas Perdagangan"
                 class="h-16 w-auto object-contain drop-shadow-md">
        </div>

        <!-- Menu -->
        <nav class="flex-1 py-6 px-3 space-y-1 overflow-y-auto no-scrollbar">
            
            {{-- DASHBOARD - Semua Role --}}
            <a href="{{ route('home') }}" class="menu-item {{ request()->is('home') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-lg transition-all">
                <i class="ph ph-squares-four text-[22px] w-5 h-5 flex items-center justify-center shrink-0 leading-none"></i>
                <span class="text-[16.5px] font-medium whitespace-nowrap">Dashboard</span>
            </a>

            {{-- MENU PETUGAS UPTD --}}
            @if(auth()->user()->role->nama_role === 'Petugas UPTD')
                <a href="{{ route('laporan.create') }}" class="menu-item {{ request()->is('laporan/create') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-lg transition-all">
                    <i class="ph ph-file-plus text-[22px] w-5 h-5 flex items-center justify-center shrink-0 leading-none"></i>
                    <span class="text-[16.5px] font-medium whitespace-nowrap">Buat Laporan</span>
                </a>
                <a href="{{ route('laporan.index') }}" class="menu-item {{ request()->is('laporan') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-lg transition-all">
                    <i class="ph ph-clock-counter-clockwise text-[22px] w-5 h-5 flex items-center justify-center shrink-0 leading-none"></i>
                    <span class="text-[16.5px] font-medium whitespace-nowrap">Riwayat Laporan</span>
                </a>
                <a href="{{ route('panduan') }}" class="menu-item {{ request()->is('panduan') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-lg transition-all">
                    <i class="ph ph-book-open text-[22px] w-5 h-5 flex items-center justify-center shrink-0 leading-none"></i>
                    <span class="text-[16.5px] font-medium whitespace-nowrap">Panduan</span>
                </a>
            @endif

            {{-- MENU STAFF SARANA DAN PRASARANA --}}
            @if(auth()->user()->role->nama_role === 'Staff Sarana dan Prasarana')
                <a href="{{ route('staff.laporan.index') }}" class="menu-item {{ request()->is('staff/laporan*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-lg transition-all">
                    <i class="ph ph-clipboard-text text-[22px] w-5 h-5 flex items-center justify-center shrink-0 leading-none"></i>
                    <span class="text-[16.5px] font-medium whitespace-nowrap">Daftar Laporan Kerusakan</span>
                </a>

                <a href="{{ route('staff.rab.index') }}" class="menu-item {{ request()->is('staff/rab*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-lg transition-all">
                    <i class="ph ph-coins text-[22px] w-5 h-5 flex items-center justify-center shrink-0 leading-none"></i>
                    <span class="text-[16.5px] font-medium whitespace-nowrap">Rencana Anggaran (RAB)</span>
                </a>

                <a href="{{ route('staff.spj.index') }}" class="menu-item {{ request()->is('staff/spj*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-lg transition-all">
                    <i class="ph ph-folder-open text-[22px] w-5 h-5 flex items-center justify-center shrink-0 leading-none"></i>
                    <span class="text-[16.5px] font-medium whitespace-nowrap">SPJ</span>
                </a>

                <a href="{{ route('staff.master.index') }}" class="menu-item {{ request()->is('staff/master*') || request()->is('staff/sab*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-lg transition-all">
                    <i class="ph ph-database text-[22px] w-5 h-5 flex items-center justify-center shrink-0 leading-none"></i>
                    <span class="text-[16.5px] font-medium whitespace-nowrap">Master Data</span>
                </a>

                <a href="{{ route('staff.akun.index') }}"
                   class="menu-item {{ request()->is('staff/akun*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-lg transition-all">
                    <i class="ph ph-user-gear text-[22px] w-5 h-5 flex items-center justify-center shrink-0 leading-none"></i>
                    <span class="text-[16.5px] font-medium whitespace-nowrap">Pengelolaan Akun</span>
                </a>
            @endif

            {{-- MENU KEPALA BIDANG --}}
            @if(auth()->user()->role->nama_role === 'Kepala Bidang')

                {{-- Verifikasi Evaluasi --}}
                <a href="{{ route('kabid.laporan.index') }}"
                   class="menu-item {{ request()->is('kabid/laporan*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-lg transition-all">
                    <i class="ph ph-seal-check text-[22px] w-5 h-5 flex items-center justify-center shrink-0 leading-none"></i>
                    <span class="text-[16.5px] font-medium whitespace-nowrap">Verifikasi Evaluasi</span>
                </a>

                {{-- Verifikasi RAB --}}
                <a href="{{ route('kabid.rab.index') }}"
                   class="menu-item {{ request()->is('kabid/rab*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-lg transition-all">
                    <i class="ph ph-currency-circle-dollar text-[22px] w-5 h-5 flex items-center justify-center shrink-0 leading-none"></i>
                    <span class="text-[16.5px] font-medium whitespace-nowrap">Verifikasi RAB</span>
                </a>

                {{-- SPJ & Realisasi --}}
                <a href="{{ route('staff.spj.index') }}"
                   class="menu-item {{ request()->is('staff/spj*') || request()->is('kabid/spj*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-lg transition-all">
                    <i class="ph ph-folder-open text-[22px] w-5 h-5 flex items-center justify-center shrink-0 leading-none"></i>
                    <span class="text-[16.5px] font-medium whitespace-nowrap">SPJ</span>
                </a>

            @endif

            {{-- MENU KEPALA DINAS --}}
            @if(auth()->user()->role->nama_role === 'Kepala Dinas')
                <a href="{{ route('kadin.laporan.index') }}" class="menu-item {{ request()->is('kadin/laporan*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-lg transition-all">
                    <i class="ph ph-clipboard-text text-[22px] w-5 h-5 flex items-center justify-center shrink-0 leading-none"></i>
                    <span class="text-[16.5px] font-medium whitespace-nowrap">Daftar Laporan</span>
                </a>
                <a href="{{ route('staff.spj.index') }}" class="menu-item {{ request()->is('staff/spj*') || request()->is('kadin/spj*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-lg transition-all">
                    <i class="ph ph-folder-open text-[22px] w-5 h-5 flex items-center justify-center shrink-0 leading-none"></i>
                    <span class="text-[16.5px] font-medium whitespace-nowrap">SPJ</span>
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
    <div class="flex-1 ml-72 flex flex-col min-h-screen">
        
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
                <div class="relative flex items-center gap-4">
                    <button id="notifBellBtn" onclick="toggleNotifDropdown()" class="relative p-2 text-gray-500 hover:text-[#003366] transition focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span id="notifBadge" class="hidden absolute top-0.5 right-0.5 min-w-[18px] h-[18px] px-1 bg-rose-500 text-white text-[10px] font-extrabold rounded-full flex items-center justify-center border-2 border-white shadow-sm">0</span>
                    </button>

                    <!-- Dropdown Popover -->
                    <div id="notifDropdown" class="hidden absolute right-0 top-12 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-extrabold text-gray-800">Notifikasi</h4>
                                <span id="notifHeaderCount" class="px-2 py-0.5 bg-sky-100 text-sky-800 rounded-full text-[10px] font-bold">0 Baru</span>
                            </div>
                            <form id="formMarkAllRead" action="{{ route('notifikasi.mark-all-read') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-[11px] font-bold text-[#114F72] hover:underline">Tandai Semua Dibaca</button>
                            </form>
                        </div>
                        <div id="notifListContainer" class="max-h-80 overflow-y-auto divide-y divide-gray-100 text-xs">
                            <div class="p-6 text-center text-gray-400">Memuat notifikasi...</div>
                        </div>
                    </div>
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

    <script>
        function toggleNotifDropdown() {
            const dropdown = document.getElementById('notifDropdown');
            dropdown.classList.toggle('hidden');
            if (!dropdown.classList.contains('hidden')) {
                fetchNotifications();
            }
        }

        document.addEventListener('click', function(event) {
            const btn = document.getElementById('notifBellBtn');
            const dropdown = document.getElementById('notifDropdown');
            if (btn && dropdown && !btn.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        function fetchNotifications() {
            fetch("{{ route('notifikasi.api') }}")
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('notifBadge');
                    const headerCount = document.getElementById('notifHeaderCount');
                    const container = document.getElementById('notifListContainer');

                    if (data.unread_count > 0) {
                        badge.innerText = data.unread_count > 99 ? '99+' : data.unread_count;
                        badge.classList.remove('hidden');
                        headerCount.innerText = data.unread_count + ' Baru';
                    } else {
                        badge.classList.add('hidden');
                        headerCount.innerText = '0 Baru';
                    }

                    if (!data.notifications || data.notifications.length === 0) {
                        container.innerHTML = '<div class="p-6 text-center text-gray-400">Belum ada notifikasi.</div>';
                        return;
                    }

                    let html = '';
                    data.notifications.forEach(n => {
                        const isUnread = n.is_read === 0;
                        const bgClass = isUnread ? 'bg-sky-50/50 font-medium' : 'bg-white';
                        const dotClass = isUnread ? '<span class="w-2 h-2 rounded-full bg-[#114F72] shrink-0"></span>' : '';
                        
                        html += `
                            <a href="/notifikasi/${n.id_notifikasi}/read" class="block p-3.5 hover:bg-gray-50 transition ${bgClass}">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <span class="font-bold text-gray-800 text-xs">${n.judul_notifikasi}</span>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <span class="text-[10px] text-gray-400">${n.time_ago}</span>
                                        ${dotClass}
                                    </div>
                                </div>
                                <p class="text-[11px] text-gray-600 leading-snug line-clamp-2">${n.pesan_notifikasi}</p>
                            </a>
                        `;
                    });

                    container.innerHTML = html;
                })
                .catch(() => {});
        }

        document.addEventListener('DOMContentLoaded', fetchNotifications);
    </script>

    @yield('scripts')
    @include('partials._photo_lightbox')
    @include('partials._toast')
</body>
</html>