@extends('layouts.app')

@section('title', 'Detail Laporan #' . $laporan->id_laporan . ' - SI-SARPRAS')

@section('breadcrumb')
    <a href="{{ route('laporan.index') }}" class="hover:text-[#114F72] transition">Daftar Laporan Masuk</a>
    <svg class="w-4 h-4 mx-2 text-gray-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-gray-600">Detail Laporan</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto pb-12">

    @php
        $statusBadge = match($laporan->status_laporan) {
            'Menunggu' => 'bg-amber-100 text-amber-700 border-amber-200',
            'Diproses' => 'bg-blue-100 text-blue-700 border-blue-200',
            'Selesai' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'Dikembalikan' => 'bg-red-100 text-red-700 border-red-200',
            default => 'bg-gray-100 text-gray-600 border-gray-200',
        };

        $kategoriBadge = match($laporan->kategori_kerusakan) {
            'Ringan' => 'bg-amber-100 text-amber-700 border-amber-200',
            'Sedang' => 'bg-orange-100 text-orange-700 border-orange-200',
            'Berat' => 'bg-red-100 text-red-700 border-red-200',
            default => 'bg-gray-100 text-gray-600 border-gray-200',
        };
    @endphp

    <!-- Header Workspace -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="{{ route('laporan.index') }}" class="p-2 text-gray-500 hover:text-[#114F72] hover:bg-gray-100 rounded-xl transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">{{ $laporan->id_laporan }} — {{ $laporan->fasilitas->nama_fasilitas ?? 'Laporan Kerusakan' }}</h1>
                <p class="text-xs text-gray-500 mt-1">Dibuat pada {{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->translatedFormat('d F Y') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $statusBadge }}">
                Status: {{ $laporan->status_laporan }}
            </span>
            @if($laporan->kategori_kerusakan)
                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $kategoriBadge }}">
                    Kerusakan {{ $laporan->kategori_kerusakan }}
                </span>
            @endif
        </div>
    </div>

    <!-- Workflow Timeline -->
    @include('laporan.partials._workflow_timeline')

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Tabs Navigation Bar -->
    @php
        $roleName = auth()->user()->role->nama_role ?? '';

        $isReportActive = !in_array($laporan->status_laporan, ['Menunggu', 'Dikembalikan', 'Ditolak']);
        $evaluasiDone = !is_null($laporan->kategori_kerusakan) && $isReportActive;
        $rabApproved = $laporan->status_verifikasi_rab === 'Disetujui' && $isReportActive;

        $tabEnabled = [
            'informasi' => true,
            'evaluasi' => true,
            'rab' => $evaluasiDone,
            'progress' => $rabApproved,
            'bukti' => $rabApproved,
        ];

        $allowedTabs = match($roleName) {
            'Petugas UPTD' => ['informasi', 'evaluasi', 'progress'],
            'Staff Sarana dan Prasarana' => ['informasi', 'evaluasi', 'rab', 'progress', 'bukti'],
            'Kepala Bidang' => ['informasi', 'evaluasi', 'rab', 'progress', 'bukti'],
            'Kepala Dinas' => ['informasi', 'evaluasi', 'rab', 'progress', 'bukti'],
            default => ['informasi', 'evaluasi', 'progress']
        };

        $requestedTab = request()->query('tab', 'informasi');
        if (in_array($requestedTab, $allowedTabs) && ($tabEnabled[$requestedTab] ?? false)) {
            $activeTab = $requestedTab;
        } else {
            $activeTab = 'informasi';
        }

        $tabLabels = [
            'informasi' => 'Informasi',
            'evaluasi' => 'Evaluasi',
            'rab' => 'RAB',
            'progress' => 'Progress',
            'bukti' => 'Bukti Pembelian',
        ];
    @endphp

    <div class="mb-6 border-b border-gray-200 bg-white px-4 rounded-xl border shadow-sm">
        <nav class="-mb-px flex gap-6 overflow-x-auto" aria-label="Tabs">
            @foreach($allowedTabs as $tabKey)
                @php
                    $isEnabled = $tabEnabled[$tabKey] ?? false;
                    $isActive = ($activeTab === $tabKey);
                    $currentUrl = request()->fullUrlWithQuery(['tab' => $tabKey]);
                @endphp

                @if($isEnabled)
                    <a href="{{ $currentUrl }}"
                       class="inline-flex items-center gap-2 border-b-2 py-4 px-1 text-sm font-semibold transition-colors whitespace-nowrap {{ $isActive ? 'border-[#114F72] text-[#114F72]' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                        {{ $tabLabels[$tabKey] ?? ucfirst($tabKey) }}
                    </a>
                @else
                    <span class="inline-flex items-center gap-1.5 border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-300 cursor-not-allowed whitespace-nowrap opacity-60"
                          title="Selesaikan tahap sebelumnya terlebih dahulu">
                        <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        {{ $tabLabels[$tabKey] ?? ucfirst($tabKey) }}
                    </span>
                @endif
            @endforeach
        </nav>
    </div>

    <!-- Grid Layout: Content Area + Sticky Sidebar -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content Area -->
        <div class="lg:col-span-2 space-y-6">
            @include('laporan.partials._' . $activeTab)
        </div>

        <!-- Sticky Right Sidebar -->
        <div class="lg:col-span-1">
            <div class="sticky top-6">
                @include('laporan.partials._sidebar')
            </div>
        </div>
    </div>

</div>
@endsection