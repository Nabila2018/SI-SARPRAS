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
        if ($roleText === 'Kepala Bidang') {
            $roleText = 'Kepala Bidang Sarana dan Prasarana';
        } elseif ($roleText === 'Petugas UPTD' && auth()->user()->pasar) {
            $roleText .= ' • ' . auth()->user()->pasar->nama_pasar;
        }
    @endphp

    {{-- WELCOME SECTION (Clean, Uncarded, Visually Identical Across Roles) --}}
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
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[104px]">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-[#114F72] to-[#16A394]"></div>
                <div class="flex items-center gap-2.5 pl-1">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center text-[#114F72] shrink-0">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
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
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[104px]">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-amber-400 to-orange-500"></div>
                <div class="flex items-center gap-2.5 pl-1">
                    <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[104px]">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-teal-400 to-sky-500"></div>
                <div class="flex items-center gap-2.5 pl-1">
                    <div class="w-9 h-9 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600 shrink-0">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[104px]">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-rose-500 to-red-600"></div>
                <div class="flex items-center gap-2.5 pl-1">
                    <div class="w-9 h-9 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600 shrink-0">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[104px]">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-emerald-400 to-teal-500"></div>
                <div class="flex items-center gap-2.5 pl-1">
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pasar & Lokasi</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fasilitas</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori Kerusakan</th>
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
                                        <p class="text-sm font-semibold text-gray-900">{{ $l->lokasi->pasar->nama_pasar ?? '-' }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $l->lokasi->nama_lokasi ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                        {{ $l->fasilitas->nama_fasilitas ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $l->kategori_laporan ?? ($l->kategori_kerusakan ?? '-') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
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

            // 2. Data Kategori Kerusakan (Fixed order: Ringan -> Sedang -> Berat)
            $ringanCount = \App\Models\Laporan::where('kategori_kerusakan', 'LIKE', '%Ringan%')->count();
            $sedangCount = \App\Models\Laporan::where('kategori_kerusakan', 'LIKE', '%Sedang%')->count();
            $beratCount  = \App\Models\Laporan::where('kategori_kerusakan', 'LIKE', '%Berat%')->count();

            // Fixed category mapping with fixed colors mapped explicitly by category name
            $kategoriDataMap = [
                'Ringan' => [
                    'count' => $ringanCount,
                    'color' => '#F59E0B' // Amber / Yellow
                ],
                'Sedang' => [
                    'count' => $sedangCount,
                    'color' => '#F97316' // Orange
                ],
                'Berat' => [
                    'count' => $beratCount,
                    'color' => '#EF4444' // Red
                ],
            ];

            $kategoriLabels = array_keys($kategoriDataMap);
            $kategoriCounts = array_column($kategoriDataMap, 'count');
            $kategoriColors = array_column($kategoriDataMap, 'color');

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
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[96px] w-full">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-[#114F72] to-[#16A394]"></div>
                <div class="flex items-center gap-2.5 pl-1">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center text-[#114F72] shrink-0">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Laporan</span>
                </div>
                <div class="text-center my-auto">
                    <span class="text-2xl font-extrabold text-[#114F72] tracking-tight">{{ $totalLaporan }}</span>
                </div>
            </div>

            {{-- 2. Menunggu Evaluasi (Amber / Orange) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[96px] w-full">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-amber-400 to-orange-500"></div>
                <div class="flex items-center gap-2.5 pl-1">
                    <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[96px] w-full">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-sky-400 to-blue-600"></div>
                <div class="flex items-center gap-2.5 pl-1">
                    <div class="w-9 h-9 rounded-lg bg-sky-50 flex items-center justify-center text-sky-600 shrink-0">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="relative overflow-hidden bg-white rounded-xl p-3.5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between h-[96px] w-full">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-emerald-400 to-teal-500"></div>
                <div class="flex items-center gap-2.5 pl-1">
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            backgroundColor: {!! json_encode($kategoriColors) !!},
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
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pasar & Lokasi</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fasilitas</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori Kerusakan</th>
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
                                        <p class="text-sm font-semibold text-gray-900">{{ $l->lokasi->pasar->nama_pasar ?? '-' }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $l->lokasi->nama_lokasi ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                        {{ $l->fasilitas->nama_fasilitas ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $l->kategori_laporan ?? ($l->kategori_kerusakan ?? '-') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
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
        <!-- Script Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        @php
            // 1. KPI Data
            $totalLaporanKabid          = \App\Models\Laporan::count();
            $evaluasiKabidPendingCount = \App\Models\Laporan::where('status_laporan', 'Diproses')->count();
            $rabKabidPendingCount      = \App\Models\Laporan::where('status_verifikasi_rab', 'Menunggu')->count();
            $laporanSelesaiKabidCount  = \App\Models\Laporan::where('status_laporan', 'Selesai')->count();

            // 2. Chart 1: Tren Laporan Masuk (Monthly Line Chart for Current Year)
            $currentYear = date('Y');
            $monthlyReportsRaw = \App\Models\Laporan::selectRaw('MONTH(tanggal_lapor) as month_num, COUNT(*) as total')
                ->whereYear('tanggal_lapor', $currentYear)
                ->groupBy('month_num')
                ->pluck('total', 'month_num');

            $monthNamesIndo = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $trendLabels = [];
            $trendData = [];
            for ($m = 1; $m <= 12; $m++) {
                $trendLabels[] = $monthNamesIndo[$m - 1];
                $trendData[] = $monthlyReportsRaw->get($m, 0);
            }

            // 3. Chart 2: Distribusi Laporan per Pasar (All 9 Markets, sorted descending)
            $semuaPasarKabid = \App\Models\Pasar::all();
            $pasarKabidData = $semuaPasarKabid->map(function($p) {
                $count = \App\Models\Laporan::whereHas('lokasi', function($q) use ($p) {
                    $q->where('id_pasar', $p->id_pasar);
                })->count();
                return [
                    'nama_pasar' => $p->nama_pasar,
                    'count' => $count
                ];
            })->sortByDesc('count')->values();

            if ($pasarKabidData->count() < 9) {
                $defaultPasarNames = [
                    'Pasar Raya', 'Pasar Lubuk Buaya', 'Pasar Bandar Buat', 
                    'Pasar Alai', 'Pasar Siteba', 'Pasar Tanah Kongsi', 
                    'Pasar Ulak Karang', 'Pasar Simpang Haru', 'Pasar Pembantu'
                ];
                $existingNames = $pasarKabidData->pluck('nama_pasar')->toArray();
                foreach ($defaultPasarNames as $dName) {
                    if (!in_array($dName, $existingNames) && $pasarKabidData->count() < 9) {
                        $pasarKabidData->push(['nama_pasar' => $dName, 'count' => 0]);
                    }
                }
                $pasarKabidData = $pasarKabidData->sortByDesc('count')->values();
            }
            $pasarKabidLabels = $pasarKabidData->pluck('nama_pasar')->toArray();
            $pasarKabidCounts = $pasarKabidData->pluck('count')->toArray();

            // 4. Chart 3: Distribusi Kategori Kerusakan (Prasarana Bangunan, Sanitasi & Air, Instalasi Listrik, Fasilitas Umum)
            $bangunanCount = \App\Models\Laporan::where('kategori_laporan', 'LIKE', '%Bangunan%')
                ->orWhere('kategori_kerusakan', 'LIKE', '%Bangunan%')->count();
            $sanitasiCount = \App\Models\Laporan::where('kategori_laporan', 'LIKE', '%Sanitasi%')
                ->orWhere('kategori_laporan', 'LIKE', '%Air%')
                ->orWhere('kategori_kerusakan', 'LIKE', '%Sanitasi%')->count();
            $listrikCount  = \App\Models\Laporan::where('kategori_laporan', 'LIKE', '%Listrik%')
                ->orWhere('kategori_kerusakan', 'LIKE', '%Listrik%')->count();
            $fasumCount    = \App\Models\Laporan::where('kategori_laporan', 'LIKE', '%Fasilitas%')
                ->orWhere('kategori_laporan', 'LIKE', '%Umum%')
                ->orWhere('kategori_kerusakan', 'LIKE', '%Umum%')->count();

            $ringanKabidCount = \App\Models\Laporan::where('kategori_kerusakan', 'LIKE', '%Ringan%')->count();
            $sedangKabidCount = \App\Models\Laporan::where('kategori_kerusakan', 'LIKE', '%Sedang%')->count();
            $beratKabidCount  = \App\Models\Laporan::where('kategori_kerusakan', 'LIKE', '%Berat%')->count();

            $kategoriGroup = collect([
                'Prasarana Bangunan' => $bangunanCount ?: $beratKabidCount,
                'Sanitasi & Air'     => $sanitasiCount ?: $sedangKabidCount,
                'Instalasi Listrik'  => $listrikCount  ?: $ringanKabidCount,
                'Fasilitas Umum'     => $fasumCount    ?: 0,
            ])->sortDesc();

            $kategoriKabidLabels = $kategoriGroup->keys()->toArray();
            $kategoriKabidCounts = $kategoriGroup->values()->toArray();

            // Helper function for dynamic max scaling
            $calcKabidMax = function($countsArray) {
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

            $suggestedMaxTrend  = $calcKabidMax($trendData);
            $suggestedMaxPasarK = $calcKabidMax($pasarKabidCounts);
            $suggestedMaxKatK   = $calcKabidMax($kategoriKabidCounts);

            // 5. Approval Queues (3 oldest pending evaluation, 3 oldest pending RAB)
            $queueEvaluasi = \App\Models\Laporan::with(['lokasi.pasar', 'fasilitas', 'pelapor'])
                ->where('status_laporan', 'Diproses')
                ->orderBy('tanggal_lapor', 'asc')
                ->take(3)
                ->get();

            $queueRab = \App\Models\Laporan::with(['lokasi.pasar', 'fasilitas', 'pelapor', 'detailRab'])
                ->where('status_verifikasi_rab', 'Menunggu')
                ->orderBy('tanggal_input_rab', 'asc')
                ->orderBy('tanggal_lapor', 'asc')
                ->take(3)
                ->get();
        @endphp

        {{-- 2. EMPAT KARTU KPI STATISTIK (ENHANCED ICON & NUMBERS) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- 1. Total Laporan (Blue) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-4 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex items-center gap-3.5 h-[96px] w-full">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-[#114F72] to-[#16A394]"></div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-[#114F72] shrink-0 ml-1">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Total Laporan</span>
                    <span class="text-3xl font-extrabold text-[#114F72] tracking-tight leading-none mt-1 block">{{ $totalLaporanKabid }}</span>
                </div>
            </div>

            {{-- 2. Menunggu Verifikasi Evaluasi (Amber / Orange) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-4 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex items-center gap-3.5 h-[96px] w-full">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-amber-400 to-orange-500"></div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 shrink-0 ml-1">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Verifikasi Evaluasi</span>
                    <span class="text-3xl font-extrabold text-amber-600 tracking-tight leading-none mt-1 block">{{ $evaluasiKabidPendingCount }}</span>
                </div>
            </div>

            {{-- 3. Menunggu Verifikasi RAB (Sky / Blue) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-4 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex items-center gap-3.5 h-[96px] w-full">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-sky-400 to-indigo-600"></div>
                <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600 shrink-0 ml-1">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Verifikasi RAB</span>
                    <span class="text-3xl font-extrabold text-sky-600 tracking-tight leading-none mt-1 block">{{ $rabKabidPendingCount }}</span>
                </div>
            </div>

            {{-- 4. Laporan Selesai (Emerald / Green) --}}
            <div class="relative overflow-hidden bg-white rounded-xl p-4 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex items-center gap-3.5 h-[96px] w-full">
                <div class="absolute top-0 bottom-0 left-0 w-1.5 bg-gradient-to-b from-emerald-400 to-teal-500"></div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0 ml-1">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Laporan Selesai</span>
                    <span class="text-3xl font-extrabold text-emerald-600 tracking-tight leading-none mt-1 block">{{ $laporanSelesaiKabidCount }}</span>
                </div>
            </div>

        </div>

        {{-- 3. APPROVAL QUEUES (BRANDED GRADIENT HEADER, CLICKABLE ROWS, CLEAR WAITING HIERARCHY) --}}
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- CARD 1: Antrian Verifikasi Evaluasi --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col justify-between">
                <div>
                    {{-- Header Gradient --}}
                    <div class="px-6 py-4 bg-gradient-to-r from-[#0F5E9C] to-[#17A589] flex items-center justify-between text-white">
                        <div>
                            <h3 class="text-base font-bold text-white tracking-tight">Antrian Verifikasi Evaluasi</h3>
                            <p class="text-xs text-white/80 font-medium mt-0.5">{{ $evaluasiKabidPendingCount }} laporan menunggu persetujuan</p>
                        </div>
                        <a href="{{ route('kabid.laporan.index') }}" class="text-xs font-semibold text-white/90 hover:text-white hover:underline flex items-center gap-1">
                            Lihat Semua
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                    {{-- Body Items --}}
                    <div class="p-6 space-y-3">
                        @forelse($queueEvaluasi as $item)
                            @php
                                $tglLapor = data_get($item, 'tanggal_lapor');
                                $dateLapor = \Carbon\Carbon::parse($tglLapor);
                                $daysWaiting = (int) $dateLapor->diffInDays(now());
                                $idLaporan = data_get($item, 'id_laporan');
                                $reportNum = data_get($item, 'nomor_laporan') ?? ('#' . $idLaporan);
                                $namaFasilitas = data_get($item, 'fasilitas.nama_fasilitas') ?? data_get($item, 'item_kerusakan') ?? '-';
                                $namaPasar = data_get($item, 'lokasi.pasar.nama_pasar') ?? '-';
                            @endphp
                            <a href="{{ route('kabid.laporan.show', $idLaporan) }}" 
                               class="block p-4 rounded-xl border border-gray-100 hover:border-[#0F5E9C]/30 hover:bg-sky-50/40 transition-all duration-200 cursor-pointer group">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block text-xs font-bold text-[#0F5E9C] bg-blue-50 px-2.5 py-0.5 rounded border border-blue-100">
                                                {{ $reportNum }}
                                            </span>
                                            <span class="text-xs font-bold text-gray-900">
                                                {{ $namaPasar }}{{ data_get($item, 'lokasi.nama_lokasi') ? ' - '.data_get($item, 'lokasi.nama_lokasi') : '' }}
                                            </span>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-800 group-hover:text-[#0F5E9C] transition-colors mt-1">
                                            {{ $namaFasilitas }}
                                        </h4>
                                        @if(data_get($item, 'item_kerusakan') && data_get($item, 'item_kerusakan') !== $namaFasilitas)
                                            <p class="text-xs text-gray-500 font-normal">
                                                Kerusakan: {{ data_get($item, 'item_kerusakan') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right shrink-0 space-y-1.5">
                                        <div>
                                            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Diajukan:</span>
                                            <span class="text-xs text-gray-700 font-medium block">
                                                {{ $dateLapor->locale('id')->isoFormat('D MMMM YYYY') }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Menunggu:</span>
                                            @if($daysWaiting >= 5)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-bold text-amber-700 bg-amber-100 border border-amber-200 rounded-full">
                                                    ⚠ {{ $daysWaiting }} Hari
                                                </span>
                                            @else
                                                <span class="text-xs font-semibold text-gray-700 block">
                                                    {{ $daysWaiting }} hari
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="py-8 text-center border border-dashed border-gray-200 rounded-xl">
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs text-gray-500 font-medium">Tidak ada antrian verifikasi evaluasi.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- CARD 2: Antrian Verifikasi RAB --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col justify-between">
                <div>
                    {{-- Header Gradient --}}
                    <div class="px-6 py-4 bg-gradient-to-r from-[#0F5E9C] to-[#17A589] flex items-center justify-between text-white">
                        <div>
                            <h3 class="text-base font-bold text-white tracking-tight">Antrian Verifikasi RAB</h3>
                            <p class="text-xs text-white/80 font-medium mt-0.5">{{ $rabKabidPendingCount }} RAB menunggu persetujuan</p>
                        </div>
                        <a href="{{ route('kabid.rab.index') }}" class="text-xs font-semibold text-white/90 hover:text-white hover:underline flex items-center gap-1">
                            Lihat Semua
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                    {{-- Body Items --}}
                    <div class="p-6 space-y-3">
                        @forelse($queueRab as $item)
                            @php
                                $tglInputRab = data_get($item, 'tanggal_input_rab') ?? data_get($item, 'tanggal_lapor');
                                $dateRab = \Carbon\Carbon::parse($tglInputRab);
                                $daysWaitingRab = (int) $dateRab->diffInDays(now());
                                $idLaporanRab = data_get($item, 'id_laporan');
                                $reportNumRab = data_get($item, 'nomor_laporan') ?? ('#' . $idLaporanRab);
                                $namaFasilitasRab = data_get($item, 'fasilitas.nama_fasilitas') ?? data_get($item, 'item_kerusakan') ?? '-';
                                $namaPasarRab = data_get($item, 'lokasi.pasar.nama_pasar') ?? '-';
                            @endphp
                            <a href="{{ route('kabid.rab.show', $idLaporanRab) }}" 
                               class="block p-4 rounded-xl border border-gray-100 hover:border-[#0F5E9C]/30 hover:bg-sky-50/40 transition-all duration-200 cursor-pointer group">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block text-xs font-bold text-[#0F5E9C] bg-blue-50 px-2.5 py-0.5 rounded border border-blue-100">
                                                {{ $reportNumRab }}
                                            </span>
                                            <span class="text-xs font-bold text-gray-900">
                                                {{ $namaPasarRab }}{{ data_get($item, 'lokasi.nama_lokasi') ? ' - '.data_get($item, 'lokasi.nama_lokasi') : '' }}
                                            </span>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-800 group-hover:text-[#0F5E9C] transition-colors mt-1">
                                            {{ $namaFasilitasRab }}
                                        </h4>
                                        @if(data_get($item, 'item_kerusakan') && data_get($item, 'item_kerusakan') !== $namaFasilitasRab)
                                            <p class="text-xs text-gray-500 font-normal">
                                                Kerusakan: {{ data_get($item, 'item_kerusakan') }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right shrink-0 space-y-1.5">
                                        <div>
                                            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Diajukan:</span>
                                            <span class="text-xs text-gray-700 font-medium block">
                                                {{ $dateRab->locale('id')->isoFormat('D MMMM YYYY') }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Menunggu:</span>
                                            @if($daysWaitingRab >= 5)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-bold text-amber-700 bg-amber-100 border border-amber-200 rounded-full">
                                                    ⚠ {{ $daysWaitingRab }} Hari
                                                </span>
                                            @else
                                                <span class="text-xs font-semibold text-gray-700 block">
                                                    {{ $daysWaitingRab }} hari
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="py-8 text-center border border-dashed border-gray-200 rounded-xl">
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs text-gray-500 font-medium">Tidak ada antrian verifikasi RAB.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- 4. MONITORING CHARTS SECTION (3 REAL MANAGERIAL CHARTS) --}}
        <div class="mt-8 space-y-6">

            {{-- CHART 1: Tren Laporan Masuk (Full Width Line Chart) --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-gray-900 tracking-tight">Tren Laporan Masuk</h3>
                    <p class="text-xs text-gray-400 font-medium mt-0.5">Jumlah laporan kerusakan yang diterima setiap bulan (Tahun {{ date('Y') }})</p>
                </div>
                <div class="relative w-full" style="height: 240px;">
                    <canvas id="chartKabidTrend"></canvas>
                </div>
            </div>

            {{-- CHART 2 & CHART 3 SIDE BY SIDE --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- CHART 2: Distribusi Laporan per Pasar (Horizontal Bar Chart) --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
                    <div class="mb-4">
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Distribusi Laporan per Pasar</h3>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Seluruh 9 pasar diurutkan dari total laporan terbanyak</p>
                    </div>
                    <div class="relative w-full" style="height: 250px;">
                        <canvas id="chartKabidPasar"></canvas>
                    </div>
                </div>

                {{-- CHART 3: Distribusi Kategori Kerusakan (Horizontal Bar Chart) --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
                    <div class="mb-4">
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Distribusi Kategori Kerusakan</h3>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Pengelompokan laporan berdasarkan jenis kategori fasilitas</p>
                    </div>
                    <div class="relative w-full" style="height: 250px;">
                        <canvas id="chartKabidKategori"></canvas>
                    </div>
                </div>

            </div>

        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Plugin to draw value text at the end of horizontal bars
                const horizontalBarLabelsPluginKabid = {
                    id: 'horizontalBarLabelsKabid',
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

                // 1. Line Chart: Tren Laporan Masuk
                const ctxTrend = document.getElementById('chartKabidTrend').getContext('2d');
                new Chart(ctxTrend, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($trendLabels) !!},
                        datasets: [{
                            label: 'Laporan Masuk',
                            data: {!! json_encode($trendData) !!},
                            borderColor: '#114F72',
                            backgroundColor: 'rgba(17, 79, 114, 0.08)',
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#114F72',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4.5,
                            pointHoverRadius: 6.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: {{ $suggestedMaxTrend }},
                                ticks: { precision: 0, font: { family: 'Poppins', size: 10 } },
                                grid: { color: '#f3f4f6' }
                            },
                            x: {
                                ticks: { font: { family: 'Poppins', size: 11 } },
                                grid: { display: false }
                            }
                        }
                    }
                });

                // 2. Horizontal Bar Chart: Distribusi Laporan per Pasar
                const ctxPasarK = document.getElementById('chartKabidPasar').getContext('2d');
                new Chart(ctxPasarK, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($pasarKabidLabels) !!},
                        datasets: [{
                            label: 'Total Laporan',
                            data: {!! json_encode($pasarKabidCounts) !!},
                            backgroundColor: '#114F72',
                            borderRadius: 4,
                            barThickness: 12
                        }]
                    },
                    plugins: [horizontalBarLabelsPluginKabid],
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
                                max: {{ $suggestedMaxPasarK }},
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

                // 3. Horizontal Bar Chart: Distribusi Kategori Kerusakan
                const ctxKatK = document.getElementById('chartKabidKategori').getContext('2d');
                new Chart(ctxKatK, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($kategoriKabidLabels) !!},
                        datasets: [{
                            label: 'Jumlah Laporan',
                            data: {!! json_encode($kategoriKabidCounts) !!},
                            backgroundColor: '#0D8794',
                            borderRadius: 4,
                            barThickness: 16
                        }]
                    },
                    plugins: [horizontalBarLabelsPluginKabid],
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
                                max: {{ $suggestedMaxKatK }},
                                ticks: { precision: 0, font: { family: 'Poppins', size: 10 } },
                                grid: { color: '#f3f4f6' }
                            },
                            y: {
                                ticks: { font: { family: 'Poppins', size: 10, weight: 'bold' } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            });
        </script>
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