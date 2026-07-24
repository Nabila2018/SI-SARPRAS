@extends('layouts.app')

@section('title', 'Daftar RAB - SI-SARPRAS')
@section('breadcrumb', 'Daftar RAB')

@section('content')
<div class="max-w-6xl mx-auto pb-12">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Daftar RAB</h1>

            <!-- Search & Filter -->
    <form method="GET" action="{{ route('staff.rab.index') }}" class="flex items-center gap-2 mb-6">
        <div class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text"
                   id="search"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari"
                   onkeydown="if(event.key === 'Enter'){ this.form.submit(); }"
                   class="w-[220px] rounded-full border border-gray-300 pl-9 pr-3 py-2 text-sm text-gray-700 focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20">
        </div>

        <div class="relative">
            <button type="button"
                    id="filterToggle"
                    onclick="toggleFilter()"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 hover:text-[#114F72] transition-colors"
                    aria-label="Filter">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V20a1 1 0 01-1.447.894l-2-1A1 1 0 0110 19v-5.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
            </button>

            <div id="filterPopover" class="absolute right-0 top-11 z-10 hidden w-52 rounded-lg border border-gray-200 bg-white p-3 shadow-lg">
                <div class="space-y-3">
                    <div>
                        <label for="status" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Status RAB
                        </label>
                        <select id="status"
                                name="status"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20"
                                onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            @foreach($statusList as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                            <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- Tabel RAB -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-gray-500 text-xs uppercase tracking-wider font-medium">No</th>
                        <th class="text-left py-3 px-4 text-gray-500 text-xs uppercase tracking-wider font-medium">Pasar</th>
                        <th class="text-left py-3 px-4 text-gray-500 text-xs uppercase tracking-wider font-medium">Fasilitas</th>
                        <th class="text-left py-3 px-4 text-gray-500 text-xs uppercase tracking-wider font-medium">Item Kerusakan</th>
                        <th class="text-left py-3 px-4 text-gray-500 text-xs uppercase tracking-wider font-medium">Tanggal Input</th>
                        <th class="text-left py-3 px-4 text-gray-500 text-xs uppercase tracking-wider font-medium">Status</th>
                        <th class="text-right py-3 px-4 text-gray-500 text-xs uppercase tracking-wider font-medium">Total RAB</th>
                        <th class="text-center py-3 px-4 text-gray-500 text-xs uppercase tracking-wider font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rabList as $index => $rab)
                        @php
                            $totalRab = $rab->detailRab->sum(function($d) {
                                return $d->volume * $d->harga_satuan;
                            });
                            $statusBadge = match($rab->status_verifikasi_rab) {
                                'Menunggu' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'Disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                'Dikembalikan' => 'bg-red-100 text-red-700 border-red-200',
                                default => 'bg-blue-100 text-blue-700 border-blue-200',
                            };
                            $statusText = $rab->status_verifikasi_rab ?? 'Draft';
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="py-3 px-4 text-gray-600">{{ $rabList->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-medium text-gray-800">{{ optional($rab->lokasi->pasar)->nama_pasar ?? '-' }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $rab->fasilitas->nama_fasilitas ?? '-' }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $rab->item_kerusakan }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ \Carbon\Carbon::parse($rab->tanggal_input_rab)->format('d M Y') }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium {{ $statusBadge }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-medium text-gray-800">
                                Rp {{ number_format($totalRab, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('staff.laporan.rab.show', $rab->id_laporan) }}"
                                    class="inline-flex items-center gap-1 text-[#114F72] hover:text-[#16A394] font-medium text-xs transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-sm">Belum ada RAB yang dibuat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($rabList->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $rabList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleFilter() {
        const popover = document.getElementById('filterPopover');
        if (popover) {
            popover.classList.toggle('hidden');
        }
    }

    document.addEventListener('click', function(e) {
        const popover = document.getElementById('filterPopover');
        const toggle = document.getElementById('filterToggle');
        if (popover && toggle && !popover.contains(e.target) && e.target !== toggle) {
            popover.classList.add('hidden');
        }
    });
</script>
@endsection
