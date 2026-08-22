@extends('layouts.app')

@section('title', 'Riwayat Pelaporan - SI-SARPRAS')
@section('breadcrumb', 'Riwayat Pelaporan')

@section('content')
<div class="max-w-6xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Riwayat Pelaporan</h1>
            <p class="text-gray-500 mt-1">Daftar laporan kerusakan yang telah Anda kirimkan.</p>
        </div>
        <a href="{{ route('laporan.create') }}" 
           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#114F72] to-[#16A394] text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Laporan Baru
        </a>
    </div>



    <!-- Tabel Riwayat -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h2 class="text-lg font-semibold text-gray-800">Daftar Laporan</h2>
            </div>

            <!-- Filter & Search Controls -->
            <form method="GET" action="{{ route('laporan.index') }}" class="flex items-center gap-2">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
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
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full border {{ request('status_laporan') ? 'border-[#114F72] bg-[#114F72]/10 text-[#114F72]' : 'border-gray-300 bg-white text-gray-600' }} hover:bg-gray-50 hover:text-[#114F72] transition-colors"
                            aria-label="Filter">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V20a1 1 0 01-1.447.894l-2-1A1 1 0 0110 19v-5.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                    </button>

                    <div id="filterPopover" class="absolute right-0 top-11 z-10 hidden w-48 rounded-xl border border-gray-200 bg-white p-2 shadow-xl">
                        <div class="px-2 py-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100 mb-1">
                            Pilih Status
                        </div>
                        <div class="space-y-0.5">
                            <a href="{{ route('laporan.index', array_merge(request()->except('status_laporan'), ['status_laporan' => ''])) }}"
                               class="flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-lg transition-colors {{ !request('status_laporan') ? 'bg-[#114F72]/10 text-[#114F72]' : 'text-gray-700 hover:bg-gray-50' }}">
                                <span>Semua Status</span>
                                @if(!request('status_laporan'))
                                    <svg class="w-3.5 h-3.5 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @endif
                            </a>
                            @foreach(['Menunggu', 'Diproses', 'Selesai', 'Dikembalikan', 'Ditolak'] as $st)
                                <a href="{{ route('laporan.index', array_merge(request()->except(['status_laporan', 'page']), ['status_laporan' => $st])) }}"
                                   class="flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-lg transition-colors {{ request('status_laporan') === $st ? 'bg-[#114F72]/10 text-[#114F72]' : 'text-gray-700 hover:bg-gray-50' }}">
                                    <span>{{ $st }}</span>
                                    @if(request('status_laporan') === $st)
                                        <svg class="w-3.5 h-3.5 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </form>
        </div>

        @if($laporan->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pasar & Lokasi</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fasilitas</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori Kerusakan</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($laporan as $index => $l)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $laporan->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900">{{ $l->lokasi->pasar->nama_pasar ?? '-' }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $l->lokasi->nama_lokasi ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                {{ $l->nama_fasilitas_display }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $l->kategori_laporan_display }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($l->tanggal_lapor)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'Menunggu' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'Diproses' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'Selesai' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'Dikembalikan' => 'bg-red-100 text-red-700 border-red-200',
                                        'Ditolak' => 'bg-red-100 text-red-700 border-red-200',
                                    ];
                                    $statusIcon = [
                                        'Menunggu' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                        'Diproses' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                                        'Selesai' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                        'Dikembalikan' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
                                        'Ditolak' => 'M6 18L18 6M6 6l12 12',
                                    ];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium border {{ $statusColors[$l->status_laporan] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusIcon[$l->status_laporan] ?? '' }}"/>
                                    </svg>
                                    {{ $l->status_laporan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('laporan.show', $l->id_laporan) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-[#114F72] bg-[#114F72]/5 hover:bg-[#114F72]/10 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @php
                $paginator = $laporan->onEachSide(1)->appends(request()->query());
            @endphp
            <div class="flex flex-col gap-4 px-6 py-4 border-t border-gray-100 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-gray-500">
                    Menampilkan {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} dari {{ $paginator->total() }} laporan
                </div>

                {{ $paginator->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                @if(request('status_laporan'))
                    <h3 class="text-lg font-medium text-gray-800 mb-1">Laporan Tidak Ditemukan</h3>
                    <p class="text-sm text-gray-500 mb-6">Tidak ada laporan dengan status "<strong>{{ request('status_laporan') }}</strong>".</p>
                    <a href="{{ route('laporan.index') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">
                        Tampilkan Semua Status
                    </a>
                @else
                    <h3 class="text-lg font-medium text-gray-800 mb-1">Belum Ada Laporan</h3>
                    <p class="text-sm text-gray-500 mb-6">Anda belum pernah mengirimkan laporan kerusakan.</p>
                    <a href="{{ route('laporan.create') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#114F72] to-[#16A394] text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Laporan Pertama
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

@section('scripts')
<script>
    const filterToggle = document.getElementById('filterToggle');
    const filterPopover = document.getElementById('filterPopover');
    if (filterToggle && filterPopover) {
        filterToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            filterPopover.classList.toggle('hidden');
        });
        document.addEventListener('click', function(e) {
            if (!filterPopover.contains(e.target) && !filterToggle.contains(e.target)) {
                filterPopover.classList.add('hidden');
            }
        });
    }
</script>
@endsection
@endsection