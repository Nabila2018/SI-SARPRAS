@extends('layouts.app')

@section('title', 'Beranda - SI-SARPRAS')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="space-y-6">

    @php
        $now = \Carbon\Carbon::now('Asia/Jakarta');
        $hour = (int) $now->format('H');
        if ($hour >= 5 && $hour < 11) {
            $greeting = 'Selamat Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }

        $roleText = auth()->user()->role->nama_role ?? '';
        if (auth()->user()->role->nama_role === 'Petugas UPTD' && auth()->user()->pasar) {
            $roleText .= ' • ' . auth()->user()->pasar->nama_pasar;
        }
    @endphp

    {{-- WELCOME SECTION (Clean, Uncarded) --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="space-y-1">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                {{ $greeting }}, {{ auth()->user()->nama_lengkap }}
            </h1>
            <p class="text-base font-semibold text-[#114F72]">
                {{ $roleText }}
            </p>
        </div>
        <div class="text-left md:text-right">
            <p class="text-xs font-medium text-gray-400">
                {{ $now->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </p>
        </div>
    </div>
    {{-- DASHBOARD PETUGAS UPTD --}}
    @if(auth()->user()->role->nama_role === 'Petugas UPTD')
        @php
            $laporanUptdRecent = \App\Models\Laporan::whereHas('lokasi', fn($q) => $q->where('id_pasar', auth()->user()->id_pasar))
                ->with(['lokasi.pasar', 'fasilitas'])
                ->orderByDesc('tanggal_lapor')
                ->orderByDesc('id_laporan')
                ->take(3)
                ->get();
        @endphp

        {{-- 1. KARTU STATISTIK KPI (5 KARTU) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

            {{-- 1. Total Laporan (Blue) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[100px]">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-[#114F72] to-[#16A394]"></div>
                <div class="flex items-center gap-2 pl-1">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center text-[#114F72] shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Laporan</span>
                </div>
                <div class="text-center my-auto">
                    <span class="text-2xl font-extrabold text-[#114F72] tracking-tight">
                        {{ \App\Models\Laporan::whereHas('lokasi', fn($q) => $q->where('id_pasar', auth()->user()->id_pasar))->count() }}
                    </span>
                </div>
            </div>

            {{-- 2. Menunggu (Orange / Amber) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[100px]">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-amber-400 to-orange-500"></div>
                <div class="flex items-center gap-2 pl-1">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Menunggu</span>
                </div>
                <div class="text-center my-auto">
                    <span class="text-2xl font-extrabold text-amber-600 tracking-tight">
                        {{ \App\Models\Laporan::whereHas('lokasi', fn($q) => $q->where('id_pasar', auth()->user()->id_pasar))->where('status_laporan', 'Menunggu')->count() }}
                    </span>
                </div>
            </div>

            {{-- 3. Disetujui (Teal / Sky) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[100px]">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-teal-400 to-sky-500"></div>
                <div class="flex items-center gap-2 pl-1">
                    <div class="w-7 h-7 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600 shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Disetujui</span>
                </div>
                <div class="text-center my-auto">
                    <span class="text-2xl font-extrabold text-teal-600 tracking-tight">
                        {{ \App\Models\Laporan::whereHas('lokasi', fn($q) => $q->where('id_pasar', auth()->user()->id_pasar))->where('status_laporan', 'Disetujui')->count() }}
                    </span>
                </div>
            </div>

            {{-- 4. Dikembalikan (Red / Rose Accent) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[100px]">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-rose-500 to-red-600"></div>
                <div class="flex items-center gap-2 pl-1">
                    <div class="w-7 h-7 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600 shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Dikembalikan</span>
                </div>
                <div class="text-center my-auto">
                    <span class="text-2xl font-extrabold text-rose-600 tracking-tight">
                        {{ \App\Models\Laporan::whereHas('lokasi', fn($q) => $q->where('id_pasar', auth()->user()->id_pasar))->where('status_laporan', 'Dikembalikan')->count() }}
                    </span>
                </div>
            </div>

            {{-- 5. Selesai (Green / Emerald) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[100px]">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-emerald-400 to-teal-500"></div>
                <div class="flex items-center gap-2 pl-1">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Selesai</span>
                </div>
                <div class="text-center my-auto">
                    <span class="text-2xl font-extrabold text-emerald-600 tracking-tight">
                        {{ \App\Models\Laporan::whereHas('lokasi', fn($q) => $q->where('id_pasar', auth()->user()->id_pasar))->where('status_laporan', 'Selesai')->count() }}
                    </span>
                </div>
            </div>

        </div>

        {{-- 2. AKSI CEPAT --}}
        <div class="mt-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Card 1: Buat Laporan Baru --}}
                <a href="{{ route('laporan.create') }}" 
                   class="group block bg-gradient-to-r from-[#0B6B8A] via-[#0D8794] to-[#149887] py-5 px-6 rounded-xl text-white shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 text-center">
                    <div class="mb-2 text-white flex justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2v6h6M12 11v6m-3-3h6"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-white tracking-tight">Buat Laporan Baru</h3>
                    <p class="text-xs text-white/85 mt-0.5 font-medium">Laporkan kerusakan fasilitas pasar</p>
                </a>

                {{-- Card 2: Lihat Riwayat Laporan (History Clock Rewind Icon) --}}
                <a href="{{ route('laporan.index') }}" 
                   class="group block bg-gradient-to-r from-[#0B6B8A] via-[#0D8794] to-[#149887] py-5 px-6 rounded-xl text-white shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 text-center">
                    <div class="mb-2 text-white flex justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M3.05 11a9 9 0 11.5 4m-.5-4H7m-4 0V7"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-white tracking-tight">Lihat Riwayat Laporan</h3>
                    <p class="text-xs text-white/85 mt-0.5 font-medium">Lihat seluruh laporan yang pernah dibuat</p>
                </a>

            </div>
        </div>

        {{-- 3. LAPORAN TERBARU --}}
        <div class="mt-8 bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-800">Laporan Terbaru</h2>
                </div>
                <a href="{{ route('laporan.index') }}" class="text-sm font-semibold text-[#114F72] hover:underline flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            @if($laporanUptdRecent->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Item Kerusakan</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Lokasi</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($laporanUptdRecent as $index => $l)
                                @php
                                    $statusColors = [
                                        'Menunggu' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'Diproses' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'Disetujui' => 'bg-teal-100 text-teal-700 border-teal-200',
                                        'Selesai' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'Dikembalikan' => 'bg-red-100 text-red-700 border-red-200',
                                        'Ditolak' => 'bg-red-100 text-red-700 border-red-200',
                                    ];
                                    $statusIcon = [
                                        'Menunggu' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                        'Diproses' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                                        'Disetujui' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                        'Selesai' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                        'Dikembalikan' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
                                        'Ditolak' => 'M6 18L18 6M6 6l12 12',
                                    ];
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-gray-800">{{ $l->item_kerusakan }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $l->kategori_laporan }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-700">{{ $l->lokasi->nama_lokasi ?? '-' }}</p>
                                        <p class="text-xs text-gray-400">{{ $l->lokasi->pasar->nama_pasar ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($l->tanggal_lapor)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4">
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
            @else
                <div class="py-10 text-center border-t border-gray-100">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm text-gray-500">Belum ada laporan yang diajukan.</p>
                </div>
            @endif
        </div>
    @endif
    {{-- DASHBOARD STAFF SARPRAS --}}
    @if(auth()->user()->role->nama_role === 'Staff Sarana dan Prasarana')
        <!-- Script Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        @php
            $totalLaporan   = \App\Models\Laporan::count();
            $evaluasiCount  = \App\Models\Laporan::where('status_laporan', 'Menunggu')->count();
            $sedangDiproses = \App\Models\Laporan::where('status_laporan', 'Diproses')->count();
            $selesaiCount   = \App\Models\Laporan::where('status_laporan', 'Selesai')->count();

            // 1. Data All 9 Markets active report count (Real Horizontal Bar Chart)
            $semuaPasar = \App\Models\Pasar::all();
            $pasarAktifData = $semuaPasar->map(function($p) {
                $count = \App\Models\Laporan::whereHas('lokasi', function($q) use ($p) {
                    $q->where('id_pasar', $p->id_pasar);
                })->whereIn('status_laporan', ['Menunggu', 'Diproses', 'Dikembalikan'])->count();

                return [
                    'nama_pasar' => $p->nama_pasar,
                    'count' => $count
                ];
            })->sortByDesc('count')->values();

            // Ensure 9 markets are listed cleanly if database has fewer
            if ($pasarAktifData->count() < 9) {
                $defaultNamaPasar = [
                    'Pasar Raya', 'Pasar Lubuk Buaya', 'Pasar Bandar Buat', 
                    'Pasar Alai', 'Pasar Siteba', 'Pasar Tanah Kongsi', 
                    'Pasar Ulak Karang', 'Pasar Simpang Haru', 'Pasar Pembantu'
                ];
                $existingNames = $pasarAktifData->pluck('nama_pasar')->toArray();
                foreach ($defaultNamaPasar as $dName) {
                    if (!in_array($dName, $existingNames) && $pasarAktifData->count() < 9) {
                        $pasarAktifData->push(['nama_pasar' => $dName, 'count' => 0]);
                    }
                }
                $pasarAktifData = $pasarAktifData->sortByDesc('count')->values();
            }

            $pasarLabels = $pasarAktifData->pluck('nama_pasar')->toArray();
            $pasarCounts = $pasarAktifData->pluck('count')->toArray();

            // 2. Data Kategori Kerusakan (Ringan, Sedang, Berat)
            $ringanCount = \App\Models\Laporan::where('kategori_kerusakan', 'LIKE', '%Ringan%')->count();
            $sedangCount = \App\Models\Laporan::where('kategori_kerusakan', 'LIKE', '%Sedang%')->count();
            $beratCount  = \App\Models\Laporan::where('kategori_kerusakan', 'LIKE', '%Berat%')->count();

            $kategoriList = collect([
                'Ringan' => $ringanCount,
                'Sedang' => $sedangCount,
                'Berat'  => $beratCount,
            ])->sortDesc();

            $kategoriLabels = $kategoriList->keys()->toArray();
            $kategoriCounts = $kategoriList->values()->toArray();

            // Dynamic Max Scaling Function to prevent bars from taking 100% width when values are low
            $calcDynamicMax = function($countsArray) {
                $maxVal = count($countsArray) ? max($countsArray) : 0;
                if ($maxVal <= 3) {
                    return 5;
                } elseif ($maxVal <= 7) {
                    return 10;
                } elseif ($maxVal <= 18) {
                    return 20;
                } else {
                    return (int) (ceil(($maxVal + 1) / 5) * 5);
                }
            };

            $suggestedMaxPasar = $calcDynamicMax($pasarCounts);
            $suggestedMaxKategori = $calcDynamicMax($kategoriCounts);

            // 3. Three (3) Latest Reports
            $laporanTerbaruStaff = \App\Models\Laporan::with(['lokasi.pasar', 'fasilitas'])
                ->orderByDesc('tanggal_lapor')
                ->orderByDesc('id_laporan')
                ->take(3)
                ->get();
        @endphp

        {{-- 2. EMPAT KPI CARDS (SATU BARIS) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- 1. Total Laporan (Blue) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[90px] w-full">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-[#114F72] to-[#16A394]"></div>
                <div class="flex items-center gap-2 pl-1">
                    <div class="w-6.5 h-6.5 rounded-md bg-blue-50 flex items-center justify-center text-[#114F72] shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Laporan</span>
                </div>
                <div class="text-center my-auto">
                    <span class="text-2xl font-extrabold text-[#114F72] tracking-tight">{{ $totalLaporan }}</span>
                </div>
            </div>

            {{-- 2. Menunggu Evaluasi (Amber / Orange) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[90px] w-full">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-amber-400 to-orange-500"></div>
                <div class="flex items-center gap-2 pl-1">
                    <div class="w-6.5 h-6.5 rounded-md bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Menunggu Evaluasi</span>
                </div>
                <div class="text-center my-auto">
                    <span class="text-2xl font-extrabold text-amber-600 tracking-tight">{{ $evaluasiCount }}</span>
                </div>
            </div>

            {{-- 3. Diproses (Sky / Blue) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[90px] w-full">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-sky-400 to-blue-600"></div>
                <div class="flex items-center gap-2 pl-1">
                    <div class="w-6.5 h-6.5 rounded-md bg-sky-50 flex items-center justify-center text-sky-600 shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Diproses</span>
                </div>
                <div class="text-center my-auto">
                    <span class="text-2xl font-extrabold text-sky-600 tracking-tight">{{ $sedangDiproses }}</span>
                </div>
            </div>

            {{-- 4. Selesai (Emerald / Green) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[90px] w-full">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-emerald-400 to-teal-500"></div>
                <div class="flex items-center gap-2 pl-1">
                    <div class="w-6.5 h-6.5 rounded-md bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Selesai</span>
                </div>
                <div class="text-center my-auto">
                    <span class="text-2xl font-extrabold text-emerald-600 tracking-tight">{{ $selesaiCount }}</span>
                </div>
            </div>

        </div>

        {{-- 3. CHART SECTION (REAL HORIZONTAL & VERTICAL BAR CHARTS SIDE BY SIDE) --}}
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- LEFT CARD: Laporan Aktif per Pasar (Chart.js Real Horizontal Bar Chart - All 9 Markets) --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="mb-4">
                    <h2 class="text-base font-bold text-gray-900 tracking-tight">Laporan Aktif per Pasar</h2>
                    <p class="text-xs text-gray-400 font-medium mt-0.5">Seluruh 9 pasar diurutkan dari laporan aktif terbanyak</p>
                </div>
                <div class="relative w-full" style="height: 250px;">
                    <canvas id="chartPasarAktif"></canvas>
                </div>
            </div>

            {{-- RIGHT CARD: Kategori Kerusakan (Chart.js Real Vertical Bar Chart - Compact) --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="mb-4">
                    <h2 class="text-base font-bold text-gray-900 tracking-tight">Kategori Kerusakan</h2>
                    <p class="text-xs text-gray-400 font-medium mt-0.5">Jumlah laporan berdasarkan tingkat kerusakan</p>
                </div>
                <div class="relative w-full" style="height: 250px;">
                    <canvas id="chartKategoriKerusakan"></canvas>
                </div>
            </div>

        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Inline plugin to draw numbers at the end of horizontal bars
                const horizontalBarLabelsPlugin = {
                    id: 'horizontalBarLabels',
                    afterDatasetsDraw(chart) {
                        const { ctx } = chart;
                        chart.data.datasets.forEach((dataset, datasetIndex) => {
                            const meta = chart.getDatasetMeta(datasetIndex);
                            meta.data.forEach((bar, index) => {
                                const value = dataset.data[index];
                                if (value !== undefined && value !== null) {
                                    ctx.save();
                                    ctx.font = '600 11px Poppins, sans-serif';
                                    ctx.fillStyle = '#114F72';
                                    ctx.textAlign = 'left';
                                    ctx.textBaseline = 'middle';
                                    ctx.fillText(value, bar.x + 6, bar.y);
                                    ctx.restore();
                                }
                            });
                        });
                    }
                };

                // Inline plugin to draw numbers at the top of vertical bars
                const verticalBarLabelsPlugin = {
                    id: 'verticalBarLabels',
                    afterDatasetsDraw(chart) {
                        const { ctx } = chart;
                        chart.data.datasets.forEach((dataset, datasetIndex) => {
                            const meta = chart.getDatasetMeta(datasetIndex);
                            meta.data.forEach((bar, index) => {
                                const value = dataset.data[index];
                                if (value !== undefined && value !== null) {
                                    ctx.save();
                                    ctx.font = '600 11px Poppins, sans-serif';
                                    ctx.fillStyle = '#374151';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';
                                    ctx.fillText(value, bar.x, bar.y - 4);
                                    ctx.restore();
                                }
                            });
                        });
                    }
                };

                // 1. Horizontal Bar Chart All 9 Pasar
                const ctxPasar = document.getElementById('chartPasarAktif').getContext('2d');
                new Chart(ctxPasar, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($pasarLabels) !!},
                        datasets: [{
                            label: 'Laporan Aktif',
                            data: {!! json_encode($pasarCounts) !!},
                            backgroundColor: '#114F72',
                            borderRadius: 4,
                            barThickness: 12
                        }]
                    },
                    plugins: [horizontalBarLabelsPlugin],
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                max: {{ $suggestedMaxPasar }},
                                ticks: { precision: 0, font: { family: 'Poppins', size: 10 } },
                                grid: { color: '#f3f4f6' }
                            },
                            y: {
                                ticks: { font: { family: 'Poppins', size: 10 } },
                                grid: { display: false }
                            }
                        }
                    }
                });

                // 2. Vertical Bar Chart Kategori Kerusakan
                const ctxKategori = document.getElementById('chartKategoriKerusakan').getContext('2d');
                new Chart(ctxKategori, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($kategoriLabels) !!},
                        datasets: [{
                            label: 'Jumlah Laporan',
                            data: {!! json_encode($kategoriCounts) !!},
                            backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                            borderRadius: 4,
                            barThickness: 32
                        }]
                    },
                    plugins: [verticalBarLabelsPlugin],
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: {{ $suggestedMaxKategori }},
                                ticks: { precision: 0, font: { family: 'Poppins', size: 10 } },
                                grid: { color: '#f3f4f6' }
                            },
                            x: {
                                ticks: { font: { family: 'Poppins', size: 11, weight: 'bold' } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            });
        </script>

        {{-- 4. LAPORAN TERBARU (EXACT LAPORAN.INDEX STYLE, 3 LATEST RECORDS) --}}
        <div class="mt-8 bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            
            {{-- Header Tabel --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-800">Laporan Terbaru</h2>
                </div>
                <a href="{{ route('staff.laporan.index') }}" class="text-sm font-semibold text-[#114F72] hover:underline flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            @if($laporanTerbaruStaff->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Item Kerusakan</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Lokasi</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($laporanTerbaruStaff as $index => $l)
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
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-gray-800">{{ $l->item_kerusakan }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $l->kategori_laporan }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-700">{{ $l->lokasi->nama_lokasi ?? '-' }}</p>
                                        <p class="text-xs text-gray-400">{{ $l->lokasi->pasar->nama_pasar ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($l->tanggal_lapor)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4">
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
            @else
                <div class="py-10 text-center border-t border-gray-100">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm text-gray-500">Belum ada laporan yang diajukan.</p>
                </div>
            @endif
        </div>
    @endif

    {{-- DASHBOARD KEPALA BIDANG --}}
    @if(auth()->user()->role->nama_role === 'Kepala Bidang')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#114F72]">
                <p class="text-sm text-gray-500">Evaluasi Menunggu</p>
                <p class="text-3xl font-bold text-[#114F72] mt-1">0</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-amber-500">
                <p class="text-sm text-gray-500">RAB Menunggu</p>
                <p class="text-3xl font-bold text-amber-600 mt-1">0</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-emerald-500">
                <p class="text-sm text-gray-500">Disetujui</p>
                <p class="text-3xl font-bold text-emerald-600 mt-1">0</p>
            </div>
        </div>
    @endif

    {{-- DASHBOARD KEPALA DINAS --}}
    @if(auth()->user()->role->nama_role === 'Kepala Dinas')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#114F72]">
                <p class="text-sm text-gray-500">Total Laporan Aktif</p>
                <p class="text-3xl font-bold text-[#114F72] mt-1">0</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-emerald-500">
                <p class="text-sm text-gray-500">Selesai Tahun Ini</p>
                <p class="text-3xl font-bold text-emerald-600 mt-1">0</p>
            </div>
        </div>
    @endif

</div>
@endsection