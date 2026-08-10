@extends('layouts.app')

@section('title', 'Verifikasi RAB - Kepala Bidang')

@section('content')
<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Verifikasi Rencana Anggaran Biaya (RAB)</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar RAB perbaikan fasilitas pasar yang diajukan oleh Staff Sarana dan Prasarana untuk diverifikasi.</p>
        </div>
    </div>

    <!-- Card Tabel Queue -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
        <!-- Filter & Search -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pb-2 border-b border-gray-100">
            <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto">
                <a href="{{ route('kabid.rab.index') }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ !request('status') || request('status') === 'Menunggu' ? 'bg-[#114F72] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Menunggu Verifikasi
                </a>
                <a href="{{ route('kabid.rab.index', ['status' => 'Disetujui']) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ request('status') === 'Disetujui' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Disetujui
                </a>
                <a href="{{ route('kabid.rab.index', ['status' => 'Dikembalikan']) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ request('status') === 'Dikembalikan' ? 'bg-rose-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Dikembalikan
                </a>
            </div>

            <form method="GET" action="{{ route('kabid.rab.index') }}" class="w-full sm:w-64">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari ID, pasar, fasilitas..."
                           class="w-full rounded-xl border border-gray-300 pl-9 pr-4 py-1.5 text-xs text-gray-700 focus:border-[#114F72] focus:ring-1 focus:ring-[#114F72]">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </form>
        </div>

        <!-- Table Queue -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/50">
                        <th class="py-3 px-3 text-left text-xs uppercase tracking-wider font-semibold text-gray-500 w-12">No</th>
                        <th class="py-3 px-3 text-left text-xs uppercase tracking-wider font-semibold text-gray-500">Pasar & Lokasi</th>
                        <th class="py-3 px-3 text-left text-xs uppercase tracking-wider font-semibold text-gray-500">Fasilitas / Item</th>
                        <th class="py-3 px-3 text-left text-xs uppercase tracking-wider font-semibold text-gray-500">Staff Sarpras</th>
                        <th class="py-3 px-3 text-left text-xs uppercase tracking-wider font-semibold text-gray-500">Tanggal Dikirim</th>
                        <th class="py-3 px-3 text-right text-xs uppercase tracking-wider font-semibold text-gray-500">Total RAB (Rp)</th>
                        <th class="py-3 px-3 text-center text-xs uppercase tracking-wider font-semibold text-gray-500">Status Verifikasi</th>
                        <th class="py-3 px-3 text-center text-xs uppercase tracking-wider font-semibold text-gray-500 w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rabList as $index => $laporan)
                        @php
                            $totalNominalRab = $laporan->detailRab ? $laporan->detailRab->sum(function($item) {
                                return ($item->volume ?? 0) * ($item->harga_satuan ?? 0);
                            }) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3.5 px-3 text-gray-500 text-xs font-medium">
                                {{ $rabList->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-3 text-xs">
                                <p class="font-bold text-gray-900">{{ $laporan->lokasi?->pasar?->nama_pasar ?? '-' }}</p>
                                <p class="text-gray-500 text-[11px] mt-0.5">{{ $laporan->lokasi?->nama_lokasi ?? '-' }}</p>
                            </td>
                            <td class="py-3.5 px-3 text-xs">
                                <p class="font-semibold text-gray-800">{{ $laporan->fasilitas?->nama_fasilitas ?? '-' }}</p>
                                @if($laporan->item_kerusakan)
                                    <p class="text-gray-500 text-[11px] mt-0.5">{{ $laporan->item_kerusakan }}</p>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 text-gray-700 text-xs">
                                Staff Sarana dan Prasarana
                            </td>
                            <td class="py-3.5 px-3 text-gray-600 text-xs whitespace-nowrap">
                                {{ $laporan->tanggal_input_rab ? \Carbon\Carbon::parse($laporan->tanggal_input_rab)->translatedFormat('d M Y H:i') : '-' }}
                            </td>
                            <td class="py-3.5 px-3 text-right font-bold text-gray-800 text-xs whitespace-nowrap">
                                Rp {{ number_format($totalNominalRab, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                @php
                                    $statusBadge = match($laporan->status_verifikasi_rab) {
                                        'Menunggu' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'Disetujui' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'Dikembalikan' => 'bg-rose-100 text-rose-800 border-rose-200',
                                        default => 'bg-gray-100 text-gray-600 border-gray-200',
                                    };
                                    $statusLabel = match($laporan->status_verifikasi_rab) {
                                        'Menunggu' => 'Menunggu Verifikasi',
                                        default => $laporan->status_verifikasi_rab ?? 'Draft',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $statusBadge }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <a href="{{ route('laporan.show', ['id' => $laporan->id_laporan, 'tab' => 'rab']) }}"
                                   class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-[#114F72] to-[#16A394] px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:opacity-90 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Verifikasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-sm text-gray-400">
                                Tidak ada RAB yang membutuhkan verifikasi saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rabList->hasPages())
            <div class="pt-4 border-t border-gray-100">
                {{ $rabList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
