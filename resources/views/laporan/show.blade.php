@extends('layouts.app')

@section('title', 'Detail Laporan - SI-SARPRAS')
@section('breadcrumb', 'Detail Laporan')

@section('content')
<div class="max-w-4xl mx-auto pb-12">

    <!-- Header -->
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('laporan.index') }}" class="p-2 text-gray-500 hover:text-[#114F72] hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Laporan</h1>
            <p class="text-gray-500 text-sm mt-0.5">ID Laporan: #{{ $laporan->id_laporan }}</p>
        </div>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-emerald-700">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Status Banner -->
    @php
        $statusBanner = [
            'Menunggu' => ['bg-amber-50 border-amber-200 text-amber-800', 'Menunggu peninjauan dari Staff Sarana dan Prasarana'],
            'Diproses' => ['bg-blue-50 border-blue-200 text-blue-800', 'Sedang dalam proses perbaikan'],
            'Selesai' => ['bg-emerald-50 border-emerald-200 text-emerald-800', 'Laporan telah selesai ditangani'],
            'Dikembalikan' => ['bg-red-50 border-red-200 text-red-800', 'Laporan dikembalikan untuk revisi'],
        ];
        $banner = $statusBanner[$laporan->status_laporan] ?? ['bg-gray-50 border-gray-200 text-gray-800', 'Status tidak diketahui'];
    @endphp
    <div class="rounded-xl border p-4 mb-6 flex items-start gap-3 {{ $banner[0] }}">
        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="font-semibold text-sm">Status: {{ $laporan->status_laporan }}</p>
            <p class="text-sm opacity-80">{{ $banner[1] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Info Laporan -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Informasi Dasar -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Informasi Laporan
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Item Kerusakan</p>
                            <p class="text-sm font-medium text-gray-800">{{ $laporan->item_kerusakan }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kategori</p>
                            <p class="text-sm font-medium text-gray-800">{{ $laporan->kategori_laporan }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Fasilitas</p>
                            <p class="text-sm font-medium text-gray-800">{{ $laporan->fasilitas->nama_fasilitas ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tanggal Lapor</p>
                            <p class="text-sm font-medium text-gray-800">{{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d F Y, H:i') }}</p>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Lokasi Spesifik</p>
                        <p class="text-sm text-gray-700">
                            {{ $laporan->lokasi->nama_lokasi ?? '-' }} 
                            <span class="text-gray-400">({{ $laporan->lokasi->pasar->nama_pasar ?? '-' }})</span>
                        </p>
                        @if($laporan->lokasi_spesifik)
                            <p class="text-xs text-gray-500 mt-1">Detail: {{ $laporan->lokasi_spesifik }}</p>
                        @endif
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Deskripsi Kerusakan</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $laporan->deskripsi_kerusakan }}</p>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kondisi yang Diharapkan</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $laporan->kondisi_diharapkan }}</p>
                    </div>
                </div>
            </div>

            <!-- Foto Laporan -->
            @if($laporan->fotoLaporan->count() > 0)
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Foto Dokumentasi
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach($laporan->fotoLaporan as $foto)
                        <a href="{{ asset('storage/' . $foto->file_foto) }}" target="_blank" class="group relative aspect-square rounded-xl overflow-hidden border border-gray-200 hover:shadow-lg transition-all">
                            <img src="{{ asset('storage/' . $foto->file_foto) }}" alt="Foto Laporan" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Hasil Evaluasi (Kalau sudah dievaluasi) -->
            @if($laporan->kategori_kerusakan)
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Hasil Evaluasi
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kategori Kerusakan</p>
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium
                                @if($laporan->kategori_kerusakan === 'Ringan') bg-green-100 text-green-700
                                @elseif($laporan->kategori_kerusakan === 'Sedang') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $laporan->kategori_kerusakan }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status Verifikasi</p>
                            <p class="text-sm font-medium text-gray-800">{{ $laporan->status_verifikasi_evaluasi ?? 'Menunggu' }}</p>
                        </div>
                    </div>
                    @if($laporan->catatan_pemeriksaan)
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Catatan Pemeriksaan</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $laporan->catatan_pemeriksaan }}</p>
                    </div>
                    @endif
                    @if($laporan->catatan_revisi_evaluasi)
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Catatan Revisi</p>
                        <p class="text-sm text-red-600 leading-relaxed">{{ $laporan->catatan_revisi_evaluasi }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>

        <!-- Kolom Kanan: Info Pelapor & Ringkasan -->
        <div class="space-y-6">
            
            <!-- Info Pelapor -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-700">Informasi Pelapor</h3>
                </div>
                <div class="p-6 space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Nama</p>
                        <p class="text-sm font-medium text-gray-800">{{ $laporan->pelapor->nama_lengkap ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Role</p>
                        <p class="text-sm font-medium text-gray-800">{{ $laporan->pelapor->role->nama_role ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Status -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-700">Ringkasan Status</h3>
                </div>
                <div class="p-6 space-y-4">
                    
                    <!-- Step 1 -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 text-xs font-bold">1</div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">Laporan Dibuat</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d M Y') }}</p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full {{ $laporan->kategori_kerusakan ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center flex-shrink-0 text-xs font-bold">2</div>
                        <div>
                            <p class="text-sm font-medium {{ $laporan->kategori_kerusakan ? 'text-gray-800' : 'text-gray-400' }}">Evaluasi Staff</p>
                            <p class="text-xs text-gray-500">{{ $laporan->tanggal_verifikasi_evaluasi ? \Carbon\Carbon::parse($laporan->tanggal_verifikasi_evaluasi)->format('d M Y') : 'Menunggu' }}</p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full {{ $laporan->status_verifikasi_rab === 'Disetujui' ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center flex-shrink-0 text-xs font-bold">3</div>
                        <div>
                            <p class="text-sm font-medium {{ $laporan->status_verifikasi_rab === 'Disetujui' ? 'text-gray-800' : 'text-gray-400' }}">Verifikasi RAB</p>
                            <p class="text-xs text-gray-500">{{ $laporan->tanggal_verifikasi_rab ? \Carbon\Carbon::parse($laporan->tanggal_verifikasi_rab)->format('d M Y') : 'Menunggu' }}</p>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full {{ $laporan->status_laporan === 'Selesai' ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center flex-shrink-0 text-xs font-bold">4</div>
                        <div>
                            <p class="text-sm font-medium {{ $laporan->status_laporan === 'Selesai' ? 'text-gray-800' : 'text-gray-400' }}">Perbaikan Selesai</p>
                            <p class="text-xs text-gray-500">{{ $laporan->status_laporan === 'Selesai' ? 'Selesai' : 'Menunggu' }}</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
@endsection