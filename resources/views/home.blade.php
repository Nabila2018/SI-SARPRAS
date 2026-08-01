@extends('layouts.app')

@section('title', 'Beranda - SI-SARPRAS')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="space-y-6">

    @php
        $hour = (int) now()->format('H');
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
    <div class="mb-8 space-y-1">
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
            {{ $greeting }}, {{ auth()->user()->nama_lengkap }} 👋
        </h1>
        <p class="text-base font-semibold text-[#114F72]">
            {{ $roleText }}
        </p>
        <p class="text-sm font-medium text-gray-500">
            {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
        </p>
    </div>

    {{-- DASHBOARD PETUGAS UPTD --}}
    @if(auth()->user()->role->nama_role === 'Petugas UPTD')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#114F72]">
                <p class="text-sm text-gray-500">Total Laporan Pasar</p>
                <p class="text-3xl font-bold text-[#114F72] mt-1">
                    {{ \App\Models\Laporan::whereHas('lokasi', fn($q) => $q->where('id_pasar', auth()->user()->id_pasar))->count() }}
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-amber-500">
                <p class="text-sm text-gray-500">Menunggu</p>
                <p class="text-3xl font-bold text-amber-600 mt-1">
                    {{ \App\Models\Laporan::whereHas('lokasi', fn($q) => $q->where('id_pasar', auth()->user()->id_pasar))->where('status_laporan', 'Menunggu')->count() }}
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-emerald-500">
                <p class="text-sm text-gray-500">Selesai</p>
                <p class="text-3xl font-bold text-emerald-600 mt-1">
                    {{ \App\Models\Laporan::whereHas('lokasi', fn($q) => $q->where('id_pasar', auth()->user()->id_pasar))->where('status_laporan', 'Selesai')->count() }}
                </p>
            </div>
        </div>

        {{-- Quick Actions UPTD --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h2>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('laporan.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#114F72] to-[#16A394] text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Laporan Baru
                </a>
                <a href="{{ route('laporan.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Lihat Riwayat
                </a>
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