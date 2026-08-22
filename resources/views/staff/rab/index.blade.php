@extends('layouts.app')

@section('title', 'Rencana Anggaran Biaya (RAB) - SI-SARPRAS')

@section('breadcrumb')
    <span class="text-gray-600">Rencana Anggaran Biaya (RAB)</span>
@endsection

@section('content')
<div class="max-w-6xl mx-auto pb-12 space-y-6">
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



    <!-- Table Card dengan Search & Filter Menyatu -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <!-- Card Header (Menyatu dengan Search & Filter Popover) -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h2 class="text-lg font-semibold text-gray-800">
                    Daftar Rencana Anggaran Biaya (RAB)
                </h2>
            </div>

            <form method="GET" action="{{ route('staff.rab.index') }}" class="flex items-center gap-2">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari ID RAB, pasar..."
                           onkeydown="if(event.key === 'Enter'){ this.form.submit(); }"
                           class="w-[180px] sm:w-[220px] rounded-full border border-gray-300 pl-9 pr-3 py-2 text-sm text-gray-700 focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20">
                </div>

                <!-- Tombol Filter Icon Only -->
                <div class="relative">
                    <button type="button"
                            id="filterToggle"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 hover:text-[#114F72] transition-colors relative"
                            aria-label="Filter">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V20a1 1 0 01-1.447.894l-2-1A1 1 0 0110 19v-5.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        @if(request('status'))
                            <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-[#114F72] rounded-full ring-2 ring-white"></span>
                        @endif
                    </button>

                    <div id="filterPopover" class="absolute right-0 top-11 z-10 hidden w-52 rounded-2xl border border-gray-200 bg-white p-4 shadow-xl">
                        <p class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-400">Filter Status RAB</p>
                        <div class="space-y-2">
                            <select name="status"
                                    onchange="this.form.submit()"
                                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 bg-gray-50 focus:border-[#114F72] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#114F72]/20 cursor-pointer">
                                <option value="">Semua Status</option>
                                @foreach($statusList as $st)
                                    <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                                        {{ $st }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                @if(request('search') || request('status'))
                    <a href="{{ route('staff.rab.index') }}" class="px-2 py-1 text-xs text-rose-600 hover:underline font-semibold flex-shrink-0">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Table RAB -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase font-bold tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="py-3.5 px-4 w-12">No</th>
                        <th class="py-3.5 px-4">ID RAB</th>
                        <th class="py-3.5 px-4">Pasar</th>
                        <th class="py-3.5 px-4">Total Anggaran</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rabList as $index => $rab)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3.5 px-4 font-semibold text-gray-500">{{ $rabList->firstItem() + $index }}</td>
                            <td class="py-3.5 px-4 font-bold text-[#114F72]">
                                <a href="{{ route('staff.rab.show', $rab->id_rab) }}" class="hover:underline">
                                    {{ $rab->id_rab }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-gray-800">{{ $rab->nama_pasar }}</td>
                            <td class="py-3.5 px-4 font-extrabold text-gray-900">Rp {{ number_format($rab->total_biaya, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $badge = match($rab->status_verifikasi_rab) {
                                        'Disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'Menunggu' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'Dikembalikan' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-blue-50 text-blue-700 border-blue-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold border {{ $badge }}">
                                    {{ $rab->status_verifikasi_rab }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('staff.rab.show', $rab->id_rab) }}" class="px-3 py-1.5 bg-[#114F72]/5 hover:bg-[#114F72]/10 text-[#114F72] text-xs font-bold rounded-xl transition inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                <div class="space-y-2">
                                    <svg class="w-10 h-10 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                                    </svg>
                                    <p class="font-semibold text-gray-500 text-sm">Belum ada data Rencana Anggaran Biaya (RAB).</p>
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('filterToggle');
        const popover = document.getElementById('filterPopover');
        if (toggle && popover) {
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                popover.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e) {
                if (!popover.contains(e.target) && !toggle.contains(e.target)) {
                    popover.classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection

@endsection
