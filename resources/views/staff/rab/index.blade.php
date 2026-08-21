@extends('layouts.app')

@section('title', 'Rencana Anggaran Biaya (RAB) - SI-SARPRAS')

@section('breadcrumb')
    <span class="text-gray-600">Rencana Anggaran Biaya (RAB)</span>
@endsection

@section('content')
<div class="space-y-6 pb-12">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Rencana Anggaran Biaya (RAB)</h1>
            <p class="text-xs text-gray-500 mt-1">Kelola dan susun RAB untuk perbaikan sarana pasar yang telah disetujui Kabid.</p>
        </div>
        <a href="{{ route('staff.rab.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#114F72] to-[#16A394] hover:opacity-95 text-white text-xs font-bold rounded-xl shadow-md transition transform hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat RAB Baru
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
        </div>
    @endif

    <!-- Card Stats Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-[#114F72] flex items-center justify-center font-bold text-sm shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase text-gray-400">Total RAB</p>
                <p class="text-lg font-extrabold text-gray-800">{{ $rabList->total() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase text-gray-400">Menunggu</p>
                <p class="text-lg font-extrabold text-amber-700">{{ $rabList->where('status_verifikasi_rab', 'Menunggu')->count() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase text-gray-400">Disetujui</p>
                <p class="text-lg font-extrabold text-emerald-700">{{ $rabList->where('status_verifikasi_rab', 'Disetujui')->count() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-sm shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase text-gray-400">Dikembalikan</p>
                <p class="text-lg font-extrabold text-rose-700">{{ $rabList->where('status_verifikasi_rab', 'Dikembalikan')->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('staff.rab.index') }}" class="flex flex-col md:flex-row items-center justify-between gap-3">
            <div class="relative w-full md:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID RAB, pasar, rincian..." class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#114F72]/20 focus:border-[#114F72] focus:bg-white outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto justify-end">
                <select name="status" onchange="this.form.submit()" class="w-full md:w-48 py-2.5 px-3 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 focus:ring-2 focus:ring-[#114F72]/20 outline-none">
                    <option value="">Semua Status</option>
                    @foreach($statusList as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2.5 bg-gray-800 hover:bg-gray-900 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    Filter
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('staff.rab.index') }}" class="px-3 py-2.5 text-xs text-gray-500 hover:text-gray-700 font-semibold">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table List RAB -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 border-b border-gray-100 text-gray-500 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">ID RAB</th>
                        <th class="py-3.5 px-4">Lokasi Pasar</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Laporan</th>
                        <th class="py-3.5 px-4 text-right">Total Anggaran</th>
                        <th class="py-3.5 px-4 text-center">Status Verifikasi</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rabList as $rab)
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="py-4 px-4 font-bold text-[#114F72]">
                                <a href="{{ route('staff.rab.show', $rab->id_rab) }}" class="hover:underline flex items-center gap-1.5">
                                    <span>{{ $rab->id_rab }}</span>
                                </a>
                            </td>
                            <td class="py-4 px-4 text-gray-800 font-semibold">
                                {{ $rab->nama_pasar }}
                            </td>
                            <td class="py-4 px-4 text-center font-bold text-gray-700">
                                <span class="px-2.5 py-1 bg-gray-100 rounded-lg text-gray-800 text-[11px]">
                                    {{ $rab->laporan->count() }} Laporan
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right font-extrabold text-gray-800">
                                Rp {{ number_format($rab->total_biaya, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                @php
                                    $badge = match($rab->status_verifikasi_rab) {
                                        'Disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'Menunggu' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'Dikembalikan' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-blue-50 text-blue-700 border-blue-200',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[11px] font-extrabold border {{ $badge }}">
                                    {{ $rab->status_verifikasi_rab }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('staff.rab.show', $rab->id_rab) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-[11px] font-bold transition">
                                        Detail
                                    </a>
                                    @if(in_array($rab->status_verifikasi_rab, ['Draft', 'Dikembalikan', 'Disetujui']))
                                        <a href="{{ route('staff.rab.edit', $rab->id_rab) }}" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-lg text-[11px] font-bold transition">
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                <div class="space-y-2">
                                    <svg class="w-10 h-10 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                                    </svg>
                                    <p class="font-semibold text-gray-500">Belum ada data Rencana Anggaran Biaya (RAB).</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rabList->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $rabList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
