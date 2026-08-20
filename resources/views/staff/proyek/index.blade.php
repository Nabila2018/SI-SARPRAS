@extends('layouts.app')

@section('title', 'Proyek Perbaikan - SI-SARPRAS')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Proyek Perbaikan</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar wadah proyek pengelompokan laporan perbaikan per pasar.</p>
        </div>

        <a href="{{ route('staff.proyek.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-semibold shadow-sm transition text-sm bg-gradient-to-r from-[#114F72] to-[#16A394] text-white hover:opacity-90">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Proyek Baru
        </a>
    </div>

    <!-- Alert Flash -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Card Filter Search -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('staff.proyek.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID Proyek, Nama Proyek, atau Nama Pasar..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#114F72] text-sm">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-[#114F72] text-white font-semibold rounded-xl text-sm hover:bg-[#0e405d] transition">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('staff.proyek.index') }}" class="px-4 py-2.5 border border-gray-200 text-gray-600 font-semibold rounded-xl text-sm hover:bg-gray-50 transition text-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table List Proyek -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/80 border-b border-gray-200 text-xs font-semibold text-gray-700 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">ID Proyek</th>
                        <th class="px-6 py-4">Nama Proyek</th>
                        <th class="px-6 py-4">Pasar</th>
                        <th class="px-6 py-4 text-center">Jumlah Laporan</th>
                        <th class="px-6 py-4">Pembuat Proyek</th>
                        <th class="px-6 py-4">Tanggal Dibuat</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($proyekList as $p)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-bold text-[#114F72]">{{ $p->id_proyek }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $p->nama_proyek }}</td>
                            <td class="px-6 py-4 font-medium text-gray-700">{{ $p->pasar->nama_pasar ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-[#114F72]/10 text-[#114F72]">
                                    {{ $p->laporan_count }} Laporan
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $p->pembuat->nama_lengkap ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                {{ $p->created_at ? $p->created_at->translatedFormat('d F Y H:i') : '-' }} WIB
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('staff.proyek.show', $p->id_proyek) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-[#114F72] bg-[#114F72]/5 hover:bg-[#114F72]/10 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail Proyek
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                Belum ada proyek perbaikan yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($proyekList->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $proyekList->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
