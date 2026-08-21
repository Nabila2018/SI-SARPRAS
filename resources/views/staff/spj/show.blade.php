@extends('layouts.app')

@section('title', 'Detail Dokumen SPJ - SI-SARPRAS')

@section('breadcrumb')
    <a href="{{ route('staff.spj.index') }}" class="text-gray-600 hover:text-[#114F72] transition">Dokumen SPJ</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-600">Detail Dokumen SPJ</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto pb-12 space-y-6">

    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Detail Dokumen SPJ</h1>
            <p class="mt-1 text-sm text-gray-500">
                Informasi Surat Pertanggungjawaban (SPJ) berbasis RAB.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('staff.spj.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>

            @if(auth()->user()->role->nama_role === 'Staff Sarana dan Prasarana')
                <a href="{{ route('staff.spj.edit', $spj->id_spj) }}" class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-amber-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-7 7l8-8a2.828 2.828 0 114 4l-8 8H6v-4z"/>
                    </svg>
                    Edit SPJ
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri: Informasi SPJ & RAB Terkait -->
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
                <h2 class="text-base font-bold text-gray-800 border-b pb-3">Informasi SPJ & RAB</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Nama Pekerjaan SPJ</p>
                        <p class="text-base font-bold text-gray-800">{{ $spj->nama_pekerjaan }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">RAB Terkait</p>
                        @if($spj->rab)
                            <a href="{{ route('staff.rab.show', $spj->rab->id_rab) }}" class="font-bold text-[#114F72] hover:underline flex items-center gap-1.5">
                                <span>{{ $spj->rab->id_rab }}</span>
                            </a>
                            <p class="text-xs text-gray-500 mt-0.5">Pasar: {{ $spj->rab->nama_pasar }} | Total: Rp {{ number_format($spj->rab->total_biaya, 0, ',', '.') }}</p>
                        @else
                            <p class="font-semibold text-gray-800">-</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Periode Kegiatan</p>
                        <p class="font-medium text-gray-800">
                            {{ \Carbon\Carbon::parse($spj->periode_mulai)->format('d F Y') }} — {{ \Carbon\Carbon::parse($spj->periode_selesai)->format('d F Y') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Pengunggah Dokumen</p>
                        <p class="font-medium text-gray-800">{{ $spj->uploader->nama_lengkap ?? '-' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Diunggah: {{ $spj->tanggal_upload ? \Carbon\Carbon::parse($spj->tanggal_upload)->format('d F Y H:i') : '-' }} WIB</p>
                    </div>

                    @if($spj->keterangan)
                        <div class="sm:col-span-2 pt-2 border-t border-gray-100">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Keterangan / Catatan</p>
                            <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-xl border border-gray-100 whitespace-pre-line">{{ $spj->keterangan }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card Daftar Laporan Dalam RAB -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
                <div class="flex items-center justify-between border-b pb-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Daftar Laporan Terhubung Dalam RAB</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Seluruh laporan perbaikan yang terikat dalam dokumen SPJ ini.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 w-12">No</th>
                                <th class="px-4 py-3">ID Laporan</th>
                                <th class="px-4 py-3">Fasilitas / Kerusakan</th>
                                <th class="px-4 py-3">Lokasi Spesifik</th>
                                <th class="px-4 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php
                                $reportsList = $spj->rab ? $spj->rab->laporan : collect();
                            @endphp
                            @forelse($reportsList as $idx => $laporan)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-4 py-3 text-xs text-gray-500 text-center font-medium">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3 font-bold text-[#114F72] text-xs">
                                        <a href="{{ route('laporan.show', $laporan->id_laporan) }}" class="hover:underline">{{ $laporan->id_laporan }}</a>
                                    </td>
                                    <td class="px-4 py-3 text-xs font-semibold text-gray-800">{{ $laporan->nama_fasilitas_display }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-600">{{ $laporan->lokasi->nama_lokasi ?? '-' }} ({{ $laporan->lokasi_spesifik }})</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            100% Selesai
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-xs text-gray-400">Belum ada laporan terhubung.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Kolom Kanan: Preview File PDF -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h3 class="text-base font-bold text-gray-800 border-b pb-3">File Dokumen PDF SPJ</h3>

                @if($spj->file_spj && Storage::disk('public')->exists($spj->file_spj))
                    <div class="space-y-4">
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-center">
                            <svg class="w-12 h-12 text-rose-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-xs font-bold text-gray-800 truncate">{{ basename($spj->file_spj) }}</p>
                        </div>

                        <a href="{{ asset('storage/' . $spj->file_spj) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#114F72] px-4 py-2.5 text-xs font-bold text-white hover:bg-[#114F72]/90 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Buka / Unduh File PDF
                        </a>
                    </div>
                @else
                    <p class="text-xs text-gray-400 text-center py-4">File PDF SPJ tidak ditemukan pada sistem.</p>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
