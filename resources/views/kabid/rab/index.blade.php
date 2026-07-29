@extends('layouts.app')

@section('title', 'Daftar RAB - Kepala Bidang')

@section('content')
<style>
    .page-header {
        margin-bottom: 24px;
    }

    .page-header h1 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 6px;
        color: #1f2937;
    }

    .page-header p {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
    }

    .report-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
    }

    .report-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .report-card-header h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
    }

    .report-table th {
        text-align: left;
        padding: 14px 12px;
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
        border-bottom: 1px solid #e5e7eb;
    }

    .report-table td {
        padding: 16px 12px;
        font-size: 14px;
        color: #374151;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .report-table tbody tr:last-child td {
        border-bottom: none;
    }

    .report-table tbody tr:hover {
        background: #f9fafb;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-menunggu {
        background: #fef3c7;
        color: #b45309;
    }

    .status-disetujui {
        background: #d1fae5;
        color: #047857;
    }

    .status-dikembalikan {
        background: #fee2e2;
        color: #b91c1c;
    }

    .detail-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 14px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        color: #ffffff;
        background: linear-gradient(135deg, #114F72 0%, #16A394 100%);
        transition: 0.2s ease;
    }

    .detail-btn:hover {
        transform: translateY(-1px);
        opacity: 0.92;
        color: #ffffff;
    }

    .empty-state {
        padding: 40px 20px !important;
        text-align: center;
        color: #9ca3af !important;
    }

    .table-wrapper {
        overflow-x: auto;
    }
</style>

<div class="page-header">
    <h1>Daftar Verifikasi RAB</h1>
    <p>Kelola dan verifikasi Rencana Anggaran Biaya (RAB) perbaikan sarana pasar.</p>
</div>

<!-- Alert Messages -->
@if(session('success'))
    <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ session('error') }}
    </div>
@endif

<div class="report-card">

    <div class="report-card-header">
        <h2>Daftar RAB Diteruskan</h2>

        <!-- Filter & Search Form -->
        <form method="GET" action="{{ route('kabid.rab.index') }}" class="flex flex-wrap items-center gap-3">
            <!-- Search -->
            <div class="relative">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari pasar, fasilitas..."
                       class="w-64 rounded-lg border border-gray-300 pl-9 pr-4 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-1 focus:ring-[#114F72]">
                <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Status Filter -->
            <select name="status" onchange="this.form.submit()" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-1 focus:ring-[#114F72]">
                <option value="">Semua Status</option>
                @foreach($statusList as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>

            @if(request('search') || request('status'))
                <a href="{{ route('kabid.rab.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 underline">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="table-wrapper">
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Pasar</th>
                    <th>Fasilitas</th>
                    <th>Item Kerusakan</th>
                    <th class="text-right">Total RAB</th>
                    <th>Tanggal Input</th>
                    <th>Status Verifikasi</th>
                    <th style="width: 90px;" class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($rabList as $index => $laporan)
                    @php
                        $totalRab = $laporan->detailRab->sum(function($item) {
                            return $item->volume * $item->harga_satuan;
                        });

                        $statusClass = match($laporan->status_verifikasi_rab) {
                            'Menunggu' => 'status-menunggu',
                            'Disetujui' => 'status-disetujui',
                            'Dikembalikan' => 'status-dikembalikan',
                            default => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <tr>
                        <td>{{ $rabList->firstItem() + $index }}</td>
                        <td class="font-medium text-gray-800">
                            {{ $laporan->lokasi?->pasar?->nama_pasar ?? '-' }}
                        </td>
                        <td>{{ $laporan->fasilitas?->nama_fasilitas ?? '-' }}</td>
                        <td>{{ $laporan->item_kerusakan }}</td>
                        <td class="text-right font-bold text-[#114F72]">
                            Rp {{ number_format($totalRab, 0, ',', '.') }}
                        </td>
                        <td>
                            {{ $laporan->tanggal_input_rab ? \Carbon\Carbon::parse($laporan->tanggal_input_rab)->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td>
                            <span class="status-badge {{ $statusClass }}">
                                {{ $laporan->status_verifikasi_rab }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('kabid.rab.show', $laporan->id_laporan) }}" class="detail-btn">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            Belum ada RAB yang diteruskan untuk verifikasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($rabList->hasPages())
        <div class="mt-6">
            {{ $rabList->links() }}
        </div>
    @endif

</div>
@endsection
