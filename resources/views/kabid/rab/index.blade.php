@extends('layouts.app')

@section('title', 'Verifikasi RAB - Kabid SI-SARPRAS')

@section('breadcrumb')
    <span class="text-gray-600">Verifikasi RAB</span>
@endsection

@section('content')
<div class="space-y-6 pb-12">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Verifikasi Rencana Anggaran Biaya (RAB)</h1>
            <p class="text-xs text-gray-500 mt-1">Daftar pengajuan RAB perbaikan sarana pasar yang memerlukan verifikasi Kepala Bidang.</p>
        </div>
    </div>



    <!-- Card Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase text-gray-400">Menunggu Verifikasi</p>
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
        <form method="GET" action="{{ route('kabid.rab.index') }}" class="flex flex-col md:flex-row items-center justify-between gap-3">
            <div class="relative w-full md:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID RAB, pasar, rincian..." class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-[#114F72]/20 focus:border-[#114F72] focus:bg-white outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div class="flex items-center gap-2">
                <!-- Tombol Filter Icon Only -->
                <div class="relative">
                    <button type="button"
                            id="filterToggleKabid"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 hover:text-[#114F72] transition-colors relative"
                            aria-label="Filter">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V20a1 1 0 01-1.447.894l-2-1A1 1 0 0110 19v-5.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        @if(request('status'))
                            <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-[#114F72] rounded-full ring-2 ring-white"></span>
                        @endif
                    </button>

                    <div id="filterPopoverKabid" class="absolute right-0 top-11 z-10 hidden w-52 rounded-2xl border border-gray-200 bg-white p-4 shadow-xl">
                        <p class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-400">Filter Status RAB</p>
                        <div class="space-y-2">
                            <select name="status"
                                    onchange="this.form.submit()"
                                    class="w-full rounded-xl border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 bg-gray-50 focus:border-[#114F72] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#114F72]/20 cursor-pointer">
                                <option value="">Semua Status</option>
                                @foreach($statusList as $st)
                                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>
                                        {{ $st }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                @if(request('search') || request('status'))
                    <a href="{{ route('kabid.rab.index') }}" class="px-2 py-1 text-xs text-rose-600 hover:underline font-semibold flex-shrink-0">
                        Reset
                    </a>
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
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rabList as $rab)
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="py-4 px-4 font-bold text-[#114F72]">
                                <a href="{{ route('kabid.rab.show', $rab->id_rab) }}" class="hover:underline">
                                    {{ $rab->id_rab }}
                                </a>
                            </td>
                            <td class="py-4 px-4 font-semibold text-gray-800">
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
                                        default => 'bg-gray-50 text-gray-600 border-gray-200',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[11px] font-extrabold border {{ $badge }}">
                                    {{ $rab->status_verifikasi_rab }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <a href="{{ route('kabid.rab.show', $rab->id_rab) }}" class="px-3.5 py-1.5 bg-[#114F72] hover:bg-[#114F72]/90 text-white rounded-lg text-[11px] font-bold transition shadow-sm inline-flex items-center gap-1">
                                    <span>Verifikasi</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                Tidak ada data pengajuan RAB yang ditemukan.
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
        const toggle = document.getElementById('filterToggleKabid');
        const popover = document.getElementById('filterPopoverKabid');
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
