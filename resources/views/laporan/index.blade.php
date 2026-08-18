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

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-emerald-700">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Tabel Riwayat -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h2 class="text-lg font-semibold text-gray-800">Daftar Laporan</h2>
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
                                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-[#114F72] bg-[#114F72]/5 hover:bg-[#114F72]/10 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                @if($paginator->hasPages())
                    <nav class="flex flex-wrap items-center justify-end gap-2" aria-label="Pagination">
                        @if($paginator->onFirstPage())
                            <span class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Sebelumnya
                            </span>
                        @else
                            <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-[#114F72] bg-white border border-gray-200 rounded-lg hover:bg-[#114F72]/5 hover:border-[#114F72]/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Sebelumnya
                            </a>
                        @endif

                        @php
                            $window = 2;
                            $start = max(1, $paginator->currentPage() - $window);
                            $end = min($paginator->lastPage(), $paginator->currentPage() + $window);
                        @endphp

                        @if($start > 1)
                            <a href="{{ $paginator->url(1) }}" class="inline-flex items-center justify-center min-w-10 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">1</a>
                            @if($start > 2)
                                <span class="px-2 text-sm text-gray-400">...</span>
                            @endif
                        @endif

                        @for($page = $start; $page <= $end; $page++)
                            @if($page == $paginator->currentPage())
                                <span class="inline-flex items-center justify-center min-w-10 px-3 py-2 text-sm font-semibold text-white bg-gradient-to-r from-[#114F72] to-[#16A394] border border-transparent rounded-lg shadow-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $paginator->url($page) }}" class="inline-flex items-center justify-center min-w-10 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor

                        @if($end < $paginator->lastPage())
                            @if($end < $paginator->lastPage() - 1)
                                <span class="px-2 text-sm text-gray-400">...</span>
                            @endif
                            <a href="{{ $paginator->url($paginator->lastPage()) }}" class="inline-flex items-center justify-center min-w-10 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                {{ $paginator->lastPage() }}
                            </a>
                        @endif

                        @if($paginator->hasMorePages())
                            <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-[#114F72] bg-white border border-gray-200 rounded-lg hover:bg-[#114F72]/5 hover:border-[#114F72]/20 transition-colors">
                                Berikutnya
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <span class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                                Berikutnya
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        @endif
                    </nav>
                @endif
            </div>
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-800 mb-1">Belum Ada Laporan</h3>
                <p class="text-sm text-gray-500 mb-6">Anda belum pernah mengirimkan laporan kerusakan.</p>
                <a href="{{ route('laporan.create') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#114F72] to-[#16A394] text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Laporan Pertama
                </a>
            </div>
        @endif
    </div>
</div>
@endsection