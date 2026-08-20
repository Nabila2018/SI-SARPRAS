@extends('layouts.app')

@section('title', 'Detail Proyek ' . $proyek->id_proyek . ' - SI-SARPRAS')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Nav -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('staff.proyek.index') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-[#114F72] transition font-medium mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Proyek
            </a>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                <span>{{ $proyek->nama_proyek }}</span>
                <span class="text-sm font-semibold px-3 py-1 bg-[#114F72]/10 text-[#114F72] rounded-full border border-[#114F72]/20">
                    {{ $proyek->id_proyek }}
                </span>
            </h1>
        </div>
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

    <!-- Card Informasi Proyek -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <p class="text-xs uppercase tracking-wider font-semibold text-gray-400 mb-1">Pasar Terkait</p>
            <p class="text-base font-bold text-gray-800">{{ $proyek->pasar->nama_pasar ?? '-' }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $proyek->pasar->alamat ?? '' }}</p>
        </div>

        <div>
            <p class="text-xs uppercase tracking-wider font-semibold text-gray-400 mb-1">Pembuat Proyek (Audit)</p>
            <p class="text-base font-bold text-gray-800">{{ $proyek->pembuat->nama_lengkap ?? '-' }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Dibuat: {{ $proyek->created_at ? $proyek->created_at->translatedFormat('d F Y H:i') : '-' }} WIB</p>
        </div>

        <div>
            <p class="text-xs uppercase tracking-wider font-semibold text-gray-400 mb-1">Jumlah Laporan Tergabung</p>
            <p class="text-2xl font-bold text-[#114F72]">{{ $proyek->laporan->count() }} <span class="text-sm font-normal text-gray-500">Laporan</span></p>
        </div>

        @if($proyek->deskripsi_proyek)
            <div class="md:col-span-3 pt-3 border-t border-gray-100">
                <p class="text-xs uppercase tracking-wider font-semibold text-gray-400 mb-1">Deskripsi / Catatan Proyek</p>
                <p class="text-sm text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100 whitespace-pre-line">{{ $proyek->deskripsi_proyek }}</p>
            </div>
        @endif
    </div>

    <!-- Table Daftar Laporan Dalam Proyek -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-800">Daftar Laporan Dalam Proyek Ini</h3>
                <p class="text-xs text-gray-500 mt-0.5">Seluruh laporan perbaikan yang terdaftar dalam proyek perbaikan {{ $proyek->id_proyek }}.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-700 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">ID Laporan</th>
                        <th class="px-6 py-4">Kategori Laporan</th>
                        <th class="px-6 py-4">Fasilitas / Item Kerusakan</th>
                        <th class="px-6 py-4">Lokasi Spesifik</th>
                        <th class="px-6 py-4">Pelapor</th>
                        <th class="px-6 py-4 text-center">Status Laporan</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($proyek->laporan as $laporan)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-bold text-[#114F72]">{{ $laporan->id_laporan }}</td>
                            <td class="px-6 py-4 font-medium text-gray-700">{{ $laporan->kategori_laporan_display }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-800">{{ $laporan->nama_fasilitas_display }}</div>
                                <div class="text-xs text-gray-500">{{ $laporan->item_kerusakan }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                {{ $laporan->lokasi->nama_lokasi ?? '-' }} ({{ $laporan->lokasi_spesifik }})
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                {{ $laporan->pelapor->nama_lengkap ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                    {{ $laporan->status_laporan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('laporan.show', $laporan->id_laporan) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-[#114F72] bg-[#114F72]/5 hover:bg-[#114F72]/10 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail Laporan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                Belum ada laporan yang terhubung dengan proyek ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
