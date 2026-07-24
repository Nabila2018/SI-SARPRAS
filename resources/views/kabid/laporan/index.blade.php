@extends('layouts.app')

@section('title', 'Daftar Laporan - Kepala Bidang')

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

    .status-diproses {
        background: #fff7ed;
        color: #c2410c;
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    <h1>Daftar Laporan Masuk</h1>
    <p>Laporan yang telah dievaluasi Staff dan diteruskan untuk verifikasi.</p>
</div>

<div class="report-card">

    <div class="report-card-header">
        <h2>Laporan Menunggu Verifikasi</h2>
    </div>

    <div class="table-wrapper">
        <table class="report-table">

            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Pasar</th>
                    <th>Fasilitas</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th style="width: 100px;">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($laporans as $index => $laporan)
                    <tr>

                        <td>
                            {{ $index + 1 }}
                        </td>

                        <td>
                            {{ $laporan->lokasi?->pasar?->nama_pasar ?? '-' }}
                        </td>

                        <td>
                            {{ $laporan->fasilitas?->nama_fasilitas ?? '-' }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d/m/Y H:i') }}
                        </td>

                        <td>
                            <span class="status-badge status-diproses">
                                {{ $laporan->status_laporan }}
                            </span>
                        </td>

                        <td>
                            <a
                                href="{{ route('kabid.laporan.show', $laporan->id_laporan) }}"
                                class="detail-btn"
                            >
                                Detail
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="empty-state">
                            Belum ada laporan yang diteruskan untuk verifikasi.
                        </td>
                    </tr>

                @endforelse
            </tbody>

        </table>
    </div>

</div>
@endsection