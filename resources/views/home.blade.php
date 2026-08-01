@extends('layouts.app')

@section('title', 'Beranda - SI-SARPRAS')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="space-y-6">

    @php
        $now = \Carbon\Carbon::now('Asia/Jakarta');
        $hour = (int) $now->format('H');
        if ($hour >= 5 && $hour < 11) {
            $greeting = 'Selamat Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }

        $roleText = auth()->user()->role->nama_role ?? '';
        if (auth()->user()->role->nama_role === 'Petugas UPTD' && auth()->user()->pasar) {
            $roleText .= ' • ' . auth()->user()->pasar->nama_pasar;
        }
    @endphp

    {{-- WELCOME SECTION (Clean, Uncarded) --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                {{ $greeting }}, {{ auth()->user()->nama_lengkap }}
            </h1>
            <p class="text-base font-semibold text-[#114F72]">
                {{ $roleText }}
            </p>
        </div>
        <div class="text-left md:text-right">
            <p class="text-xs font-medium text-gray-400">
                {{ $now->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </p>
        </div>
    </div>    {{-- DASHBOARD PETUGAS UPTD --}}
    @if(auth()->user()->role->nama_role === 'Petugas UPTD')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- 1. Total Laporan (Blue) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[100px]">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-[#114F72] to-[#16A394]"></div>
                
                {{-- Header: Ikon Kiri + Judul --}}
                <div class="flex items-center gap-2 pl-1">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center text-[#114F72] shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Laporan</span>
                </div>

                {{-- Body: Angka Besar Di Tengah --}}
                <div class="text-center my-auto">
                    <span class="text-2xl font-extrabold text-[#114F72] tracking-tight">
                        {{ \App\Models\Laporan::whereHas('lokasi', fn($q) => $q->where('id_pasar', auth()->user()->id_pasar))->count() }}
                    </span>
                </div>
            </div>

            {{-- 2. Menunggu (Orange / Amber) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[100px]">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-amber-400 to-orange-500"></div>

                {{-- Header: Ikon Kiri + Judul --}}
                <div class="flex items-center gap-2 pl-1">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Menunggu</span>
                </div>

                {{-- Body: Angka Besar Di Tengah --}}
                <div class="text-center my-auto">
                    <span class="text-2xl font-extrabold text-amber-600 tracking-tight">
                        {{ \App\Models\Laporan::whereHas('lokasi', fn($q) => $q->where('id_pasar', auth()->user()->id_pasar))->where('status_laporan', 'Menunggu')->count() }}
                    </span>
                </div>
            </div>

            {{-- 3. Selesai (Green / Emerald) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[100px]">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-emerald-400 to-teal-500"></div>

                {{-- Header: Ikon Kiri + Judul --}}
                <div class="flex items-center gap-2 pl-1">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Selesai</span>
                </div>

                {{-- Body: Angka Besar Di Tengah --}}
                <div class="text-center my-auto">
                    <span class="text-2xl font-extrabold text-emerald-600 tracking-tight">
                        {{ \App\Models\Laporan::whereHas('lokasi', fn($q) => $q->where('id_pasar', auth()->user()->id_pasar))->where('status_laporan', 'Selesai')->count() }}
                    </span>
                </div>
            </div>

        </div>        {{-- 1. AKSI CEPAT --}}
        <div class="mt-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Card 1: Buat Laporan Baru --}}
                <a href="{{ route('laporan.create') }}" 
                   class="group block bg-gradient-to-r from-[#0B6B8A] via-[#0D8794] to-[#149887] py-5 px-6 rounded-xl text-white shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 text-center">
                    <div class="mb-2 text-white flex justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2v6h6M12 11v6m-3-3h6"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-white tracking-tight">Buat Laporan Baru</h3>
                    <p class="text-xs text-white/85 mt-0.5 font-medium">Laporkan kerusakan fasilitas pasar</p>
                </a>

                {{-- Card 2: Lihat Riwayat Laporan --}}
                <a href="{{ route('laporan.index') }}" 
                   class="group block bg-gradient-to-r from-[#0B6B8A] via-[#0D8794] to-[#149887] py-5 px-6 rounded-xl text-white shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 text-center">
                    <div class="mb-2 text-white flex justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h4m-4 4h4"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-white tracking-tight">Lihat Riwayat Laporan</h3>
                    <p class="text-xs text-white/85 mt-0.5 font-medium">Lihat seluruh laporan yang pernah dibuat</p>
                </a>

            </div>
        </div>

        {{-- 2. ALUR PELAPORAN (Single Container Card) --}}
        <div class="mt-8">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Alur Pelaporan</h2>
                <p class="text-sm text-gray-500 mt-1">Ikuti tahapan pelaporan mulai dari pengajuan laporan hingga proses perbaikan dinyatakan selesai.</p>
            </div>

            {{-- 1 Unified Container Card --}}
            <div class="mt-5 bg-sky-50/50 rounded-2xl p-6 border border-sky-100/80 shadow-sm">
                <div class="flex flex-col md:flex-row items-start justify-between gap-4">
                    
                    {{-- Step 1 (Blue) --}}
                    <div class="flex-1 w-full flex flex-col items-center text-center space-y-2">
                        <div class="relative flex items-center justify-center">
                            <div class="w-10 h-10 rounded-xl bg-blue-100/80 text-blue-700 flex items-center justify-center shrink-0 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <span class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-white text-blue-900 font-extrabold text-[10px] flex items-center justify-center shrink-0 shadow border border-blue-100">1</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">Buat Laporan</h4>
                            <p class="text-[11px] leading-snug text-gray-600 mt-1">Laporkan kerusakan sarana atau prasarana pasar melalui formulir pelaporan.</p>
                        </div>
                    </div>

                    {{-- Chevron Separator --}}
                    <div class="hidden md:flex items-center justify-center text-gray-300 shrink-0 self-center pt-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>

                    {{-- Step 2 (Amber) --}}
                    <div class="flex-1 w-full flex flex-col items-center text-center space-y-2">
                        <div class="relative flex items-center justify-center">
                            <div class="w-10 h-10 rounded-xl bg-amber-100/80 text-amber-700 flex items-center justify-center shrink-0 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <span class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-white text-blue-900 font-extrabold text-[10px] flex items-center justify-center shrink-0 shadow border border-blue-100">2</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">Survey Lapangan</h4>
                            <p class="text-[11px] leading-snug text-gray-600 mt-1">Laporan akan ditinjau dan dievaluasi melalui survey langsung di lokasi pasar.</p>
                        </div>
                    </div>

                    {{-- Chevron Separator --}}
                    <div class="hidden md:flex items-center justify-center text-gray-300 shrink-0 self-center pt-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>

                    {{-- Step 3 (Indigo) --}}
                    <div class="flex-1 w-full flex flex-col items-center text-center space-y-2">
                        <div class="relative flex items-center justify-center">
                            <div class="w-10 h-10 rounded-xl bg-indigo-100/80 text-indigo-700 flex items-center justify-center shrink-0 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <span class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-white text-blue-900 font-extrabold text-[10px] flex items-center justify-center shrink-0 shadow border border-blue-100">3</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">Verifikasi</h4>
                            <p class="text-[11px] leading-snug text-gray-600 mt-1">Hasil evaluasi akan diverifikasi oleh Kepala Bidang sebelum proses perbaikan dilakukan.</p>
                        </div>
                    </div>

                    {{-- Chevron Separator --}}
                    <div class="hidden md:flex items-center justify-center text-gray-300 shrink-0 self-center pt-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>

                    {{-- Step 4 (Orange - Kunci Inggris / Wrench) --}}
                    <div class="flex-1 w-full flex flex-col items-center text-center space-y-2">
                        <div class="relative flex items-center justify-center">
                            <div class="w-10 h-10 rounded-xl bg-orange-100/80 text-orange-700 flex items-center justify-center shrink-0 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/>
                                </svg>
                            </div>
                            <span class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-white text-blue-900 font-extrabold text-[10px] flex items-center justify-center shrink-0 shadow border border-blue-100">4</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">Perbaikan</h4>
                            <p class="text-[11px] leading-snug text-gray-600 mt-1">Perbaikan fasilitas dilaksanakan sesuai hasil evaluasi dan RAB yang telah disetujui.</p>
                        </div>
                    </div>

                    {{-- Chevron Separator --}}
                    <div class="hidden md:flex items-center justify-center text-gray-300 shrink-0 self-center pt-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>

                    {{-- Step 5 (Green) --}}
                    <div class="flex-1 w-full flex flex-col items-center text-center space-y-2">
                        <div class="relative flex items-center justify-center">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100/80 text-emerald-700 flex items-center justify-center shrink-0 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-white text-blue-900 font-extrabold text-[10px] flex items-center justify-center shrink-0 shadow border border-blue-100">5</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">Selesai</h4>
                            <p class="text-[11px] leading-snug text-gray-600 mt-1">Laporan dinyatakan selesai setelah seluruh proses perbaikan telah diselesaikan.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif

    {{-- DASHBOARD STAFF SARPRAS --}}
    @if(auth()->user()->role->nama_role === 'Staff Sarana dan Prasarana')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#114F72]">
                <p class="text-sm text-gray-500">Total Laporan Masuk</p>
                <p class="text-3xl font-bold text-[#114F72] mt-1">
                    {{ \App\Models\Laporan::count() }}
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-amber-500">
                <p class="text-sm text-gray-500">Menunggu Evaluasi</p>
                <p class="text-3xl font-bold text-amber-600 mt-1">
                    {{ \App\Models\Laporan::where('status_laporan', 'Menunggu')->count() }}
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                <p class="text-sm text-gray-500">Diproses</p>
                <p class="text-3xl font-bold text-blue-600 mt-1">
                    {{ \App\Models\Laporan::where('status_laporan', 'Diproses')->count() }}
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-emerald-500">
                <p class="text-sm text-gray-500">Selesai</p>
                <p class="text-3xl font-bold text-emerald-600 mt-1">
                    {{ \App\Models\Laporan::where('status_laporan', 'Selesai')->count() }}
                </p>
            </div>
        </div>
    @endif

    {{-- DASHBOARD KEPALA BIDANG --}}
    @if(auth()->user()->role->nama_role === 'Kepala Bidang')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#114F72]">
                <p class="text-sm text-gray-500">Evaluasi Menunggu</p>
                <p class="text-3xl font-bold text-[#114F72] mt-1">0</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-amber-500">
                <p class="text-sm text-gray-500">RAB Menunggu</p>
                <p class="text-3xl font-bold text-amber-600 mt-1">0</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-emerald-500">
                <p class="text-sm text-gray-500">Disetujui</p>
                <p class="text-3xl font-bold text-emerald-600 mt-1">0</p>
            </div>
        </div>
    @endif

    {{-- DASHBOARD KEPALA DINAS --}}
    @if(auth()->user()->role->nama_role === 'Kepala Dinas')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#114F72]">
                <p class="text-sm text-gray-500">Total Laporan Aktif</p>
                <p class="text-3xl font-bold text-[#114F72] mt-1">0</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-emerald-500">
                <p class="text-sm text-gray-500">Selesai Tahun Ini</p>
                <p class="text-3xl font-bold text-emerald-600 mt-1">0</p>
            </div>
        </div>
    @endif

</div>
@endsection