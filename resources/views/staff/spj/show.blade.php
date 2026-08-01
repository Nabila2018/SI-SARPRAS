@extends('layouts.app')

@section('title', 'Detail Dokumen SPJ - SI-SARPRAS')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-gray-600 hover:text-[#114F72] transition">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('staff.spj.index') }}" class="text-gray-600 hover:text-[#114F72] transition">Dokumen SPJ</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-600">Detail Dokumen SPJ</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto pb-12">

    {{-- HEADER HALAMAN & TOMBOL AKSI --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                Detail Dokumen SPJ
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Melihat informasi lengkap dan rincian berkas dokumen SPJ.
            </p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Tombol Kembali --}}
            <a href="{{ route('staff.spj.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>

            {{-- Tombol Edit (Staff Only) --}}
            @if(auth()->user()->role->nama_role === 'Staff Sarana dan Prasarana')
                <a href="{{ route('staff.spj.edit', $spj->id_spj) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-amber-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-7 7l8-8a2.828 2.828 0 114 4l-8 8H6v-4z"/>
                    </svg>
                    Edit SPJ
                </a>
            @endif
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- LAYOUT 2 KOLOM --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI: INFORMASI SPJ & DAFTAR LAPORAN --}}
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                {{-- Header Card --}}
                <div class="bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-[#114F72]/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-800">
                                Informasi SPJ
                            </h2>
                            <p class="text-xs text-gray-500">
                                Identitas dokumen dan daftar laporan perbaikan terkait.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    {{-- Grid Informasi Utama --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm mb-8">
                        {{-- Nama Pekerjaan --}}
                        <div class="sm:col-span-2">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">
                                Nama Pekerjaan
                            </p>
                            <p class="text-base font-bold text-gray-800">
                                {{ $spj->nama_pekerjaan }}
                            </p>
                        </div>

                        {{-- Periode --}}
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">
                                Periode Kegiatan
                            </p>
                            <p class="font-medium text-gray-800">
                                {{ \Carbon\Carbon::parse($spj->periode_mulai)->translatedFormat('d M Y') }}
                                <span class="text-gray-400 mx-1">s.d.</span>
                                {{ \Carbon\Carbon::parse($spj->periode_selesai)->translatedFormat('d M Y') }}
                            </p>
                        </div>

                        {{-- Tanggal Upload --}}
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">
                                Tanggal Upload
                            </p>
                            <p class="font-medium text-gray-800">
                                {{ \Carbon\Carbon::parse($spj->tanggal_upload)->translatedFormat('d M Y, H:i') }} WIB
                            </p>
                        </div>

                        {{-- Diunggah Oleh --}}
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">
                                Diunggah Oleh
                            </p>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-7 h-7 rounded-full bg-[#114F72] text-white flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr($spj->uploader->nama_lengkap ?? 'U', 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-800">
                                    {{ $spj->uploader->nama_lengkap ?? '-' }}
                                </span>
                            </div>
                        </div>

                        {{-- Keterangan --}}
                        <div class="sm:col-span-2">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">
                                Keterangan
                            </p>
                            <p class="font-normal text-gray-700 bg-gray-50 rounded-lg p-3 border border-gray-200">
                                {{ $spj->keterangan ?? 'Tidak ada keterangan tambahan.' }}
                            </p>
                        </div>
                    </div>

                    {{-- SUB-HEADER: DAFTAR LAPORAN TERKAIT --}}
                    <div class="border-t border-gray-200 pt-6 mb-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                                </svg>
                                <h3 class="text-base font-bold text-gray-800">
                                    Daftar Laporan Terkait
                                </h3>
                            </div>
                            <span class="rounded-full bg-[#114F72]/10 text-[#114F72] px-3 py-1 text-xs font-semibold">
                                Total: {{ $spj->laporan->count() }} Laporan
                            </span>
                        </div>
                    </div>

                    {{-- TABEL LAPORAN --}}
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500" style="width: 50px;">
                                        No
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Nomor Laporan
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Pasar
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Fasilitas
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Tanggal Lapor
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($spj->laporan as $index => $laporan)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-center text-sm font-medium text-gray-600">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold">
                                            <a href="{{ route('laporan.show', $laporan->id_laporan) }}"
                                               class="text-[#114F72] hover:underline inline-flex items-center gap-1">
                                                <span>{{ $laporan->nomor_laporan }}</span>
                                                <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ $laporan->lokasi->pasar->nama_pasar ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ $laporan->fasilitas->nama_fasilitas ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->translatedFormat('d M Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                                                </svg>
                                                <p class="text-sm font-medium">Tidak ada laporan yang terhubung ke dokumen SPJ ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: DOKUMEN SPJ --}}
        <div class="lg:col-span-1">

            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden sticky top-6">
                {{-- Header Card --}}
                <div class="bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-[#114F72]/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V7.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 1H7a2 2 0 00-2 2v16a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-800">
                                Dokumen SPJ
                            </h2>
                            <p class="text-xs text-gray-500">
                                Berkas pertanggungjawaban digital.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    {{-- Preview Card File PDF --}}
                    <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V7.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 1H7a2 2 0 00-2 2v16a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-sm font-semibold text-gray-800 truncate" title="{{ basename($spj->file_spj) }}">
                                {{ basename($spj->file_spj) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Format Berkas PDF
                            </p>
                        </div>
                    </div>

                    {{-- Tombol Aksi PDF --}}
                    <div class="space-y-3">
                        {{-- Lihat PDF --}}
                        <a href="{{ asset('storage/' . $spj->file_spj) }}"
                           target="_blank"
                           class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-[#114F72] text-[#114F72] px-4 py-2.5 text-sm font-semibold hover:bg-[#114F72] hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Lihat PDF
                        </a>

                        {{-- Unduh PDF --}}
                        <a href="{{ asset('storage/' . $spj->file_spj) }}"
                           download
                           class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#114F72] text-white px-4 py-2.5 text-sm font-semibold hover:bg-[#0d3f5c] shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Unduh PDF
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
