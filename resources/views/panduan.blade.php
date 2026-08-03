@extends('layouts.app')

@section('title', 'Panduan Pelaporan - SI-SARPRAS')

@section('content')
<div class="max-w-7xl mx-auto pb-12">
    
    {{-- BREADCRUMB & HEADER --}}
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('home') }}" class="hover:text-[#114F72] transition-colors">Dashboard</a>
            <span>/</span>
            <span class="font-medium text-gray-800">Panduan</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Panduan Prosedur Pelaporan</h1>
        <p class="text-gray-500 text-sm mt-1">Sistem Informasi Manajemen Pelaporan Kerusakan Sarana dan Prasarana Pasar (SI-SARPRAS).</p>
    </div>

    {{-- CARD OVERVIEW --}}
    <div class="bg-gradient-to-r from-[#114F72] via-[#0D8794] to-[#16A394] rounded-2xl p-6 md:p-8 text-white shadow-lg mb-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-3 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-xs font-semibold backdrop-blur-sm">
                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Informasi Pelaporan</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight">Alur Prosedur Pelaporan Sarana & Prasarana</h2>
                <p class="text-sm md:text-base text-white/90 leading-relaxed font-normal">
                    Setiap laporan kerusakan fasilitas pasar yang diajukan oleh Petugas UPTD akan diproses secara transparan melalui 5 tahapan utama hingga tindakan perbaikan selesai dilaksanakan.
                </p>
            </div>
            <div class="hidden lg:flex shrink-0">
                <div class="w-24 h-24 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white shadow-inner">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ALUR PELAPORAN CONTAINER CARD --}}
    <div class="bg-white rounded-2xl p-6 md:p-8 border border-gray-200 shadow-sm mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-1">Alur Pelaporan Laporan Kerusakan</h2>
        <p class="text-sm text-gray-500 mb-6">Tahapan lengkap penanganan laporan dari awal pengajuan hingga perbaikan dinyatakan selesai.</p>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 relative">
            
            {{-- Step 1 (Buat Laporan) --}}
            <div class="flex flex-col items-center text-center p-5 rounded-xl bg-blue-50/60 border border-blue-100 hover:shadow-md transition-all relative">
                <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg mb-3 shadow-md">
                    1
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1.5">Buat Laporan</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Petugas UPTD mengisi formulir pengajuan laporan kerusakan sarana/prasarana pasar beserta foto lokasi.
                </p>
            </div>

            {{-- Step 2 (Survey Lapangan) --}}
            <div class="flex flex-col items-center text-center p-5 rounded-xl bg-amber-50/60 border border-amber-100 hover:shadow-md transition-all relative">
                <div class="w-12 h-12 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-lg mb-3 shadow-md">
                    2
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1.5">Survey Lapangan</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Staff Sarpras meninjau lokasi, mengevaluasi tingkat kerusakan, dan menyusun Rencana Anggaran Biaya (RAB).
                </p>
            </div>

            {{-- Step 3 (Verifikasi Kabid) --}}
            <div class="flex flex-col items-center text-center p-5 rounded-xl bg-indigo-50/60 border border-indigo-100 hover:shadow-md transition-all relative">
                <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-lg mb-3 shadow-md">
                    3
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1.5">Verifikasi</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Hasil evaluasi dan usulan RAB diverifikasi serta disetujui oleh Kepala Bidang Sarana dan Prasarana.
                </p>
            </div>

            {{-- Step 4 (Perbaikan) --}}
            <div class="flex flex-col items-center text-center p-5 rounded-xl bg-orange-50/60 border border-orange-100 hover:shadow-md transition-all relative">
                <div class="w-12 h-12 rounded-xl bg-orange-500 text-white flex items-center justify-center font-bold text-lg mb-3 shadow-md">
                    4
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1.5">Perbaikan</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Pelaksanaan pekerjaan fisik perbaikan fasilitas pasar sesuai rencana kerja dan anggaran yang disetujui.
                </p>
            </div>

            {{-- Step 5 (Selesai) --}}
            <div class="flex flex-col items-center text-center p-5 rounded-xl bg-emerald-50/60 border border-emerald-100 hover:shadow-md transition-all relative">
                <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg mb-3 shadow-md">
                    5
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1.5">Selesai</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Proses perbaikan dikonfirmasi selesai dan Dokumen Pertanggungjawaban (SPJ) diterbitkan.
                </p>
            </div>

        </div>
    </div>

</div>
@endsection
