<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi_Laporan_Kerusakan_Sarpras</title>
    <style>
        @page {
            margin: 25px 35px 40px 35px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1f2937;
            font-size: 9.5px;
            line-height: 1.3;
        }
        footer {
            position: fixed;
            bottom: -25px;
            left: 0px;
            right: 0px;
            height: 20px;
            font-size: 8pt;
            color: #6b7280;
            text-align: right;
            border-top: 1px solid #e5e7eb;
            padding-top: 4px;
        }
        .page-number:before {
            content: "Halaman " counter(page);
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .header-logo {
            width: 70px;
            text-align: center;
            vertical-align: middle;
        }
        .header-logo img {
            max-width: 65px;
            max-height: 65px;
        }
        .header-text {
            text-align: center;
            vertical-align: middle;
        }
        .header-text h3 {
            margin: 0;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header-text h2 {
            margin: 2px 0;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header-text p {
            margin: 0;
            font-size: 8.5px;
            color: #4b5563;
        }
        .header-divider {
            border-bottom: 2.5px double #111827;
            margin-bottom: 12px;
        }
        .doc-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
            color: #114F72;
        }
        .doc-meta {
            text-align: center;
            font-size: 9px;
            color: #4b5563;
            margin-bottom: 15px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th {
            background-color: #114F72;
            color: #ffffff;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            padding: 6px 8px;
            border: 1px solid #0d3f5c;
            text-align: left;
        }
        .data-table td {
            padding: 5px 8px;
            border: 1px solid #e5e7eb;
            font-size: 9px;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8.5px;
            font-weight: bold;
        }
        .badge-menunggu { background-color: #fef3c7; color: #92400e; }
        .badge-diproses { background-color: #dbeafe; color: #1e40af; }
        .badge-disetujui { background-color: #ccfbf1; color: #115e59; }
        .badge-selesai { background-color: #d1fae5; color: #065f46; }
        .badge-dikembalikan { background-color: #fee2e2; color: #991b1b; }

        .summary-box {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background-color: #f8fafc;
            padding: 8px 12px;
            margin-bottom: 20px;
        }
        .summary-title {
            font-weight: bold;
            font-size: 9.5px;
            margin-bottom: 4px;
            color: #114F72;
            text-transform: uppercase;
        }
        .summary-table {
            width: 100%;
            font-size: 9px;
        }
        .summary-table td {
            padding: 2px 5px;
        }

        .signature-section {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
            font-size: 9.5px;
        }
        .signature-space {
            height: 55px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <footer>
        <span class="page-number"></span>
    </footer>

    {{-- KOP SURAT --}}
    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @endif
            </td>
            <td class="header-text">
                <h3>PEMERINTAH KOTA PADANG</h3>
                <h2>DINAS PERDAGANGAN</h2>
                <p>Jl. Niaga No. 200, Kota Padang, Sumatera Barat 25118</p>
                <p>Telepon: (0751) 22456 | Email: perdagangan@padang.go.id | Website: perdagangan.padang.go.id</p>
            </td>
        </tr>
    </table>
    <div class="header-divider"></div>

    {{-- JUDUL DOKUMEN --}}
    <div class="doc-title">
        LAPORAN REKAPITULASI KERUSAKAN SARANA DAN PRASARANA PASAR
    </div>
    <div class="doc-meta">
        {{ $filterDescription }} | Total: {{ $summary['total'] }} Laporan | Dicetak pada: {{ $printDate }}
    </div>

    {{-- TABEL LAPORAN --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 11%;">Tanggal</th>
                <th style="width: 12%;">ID Laporan</th>
                <th style="width: 22%;">Pasar & Lokasi</th>
                <th style="width: 25%;">Fasilitas</th>
                <th style="width: 14%;">Kategori Kerusakan</th>
                <th style="width: 12%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporanList as $index => $l)
                @php
                    $badgeClass = match($l->status_laporan) {
                        'Menunggu' => 'badge-menunggu',
                        'Diproses' => 'badge-diproses',
                        'Disetujui' => 'badge-disetujui',
                        'Selesai' => 'badge-selesai',
                        'Dikembalikan' => 'badge-dikembalikan',
                        'Ditolak' => 'badge-dikembalikan',
                        default => '',
                    };
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($l->tanggal_lapor)->format('d/m/Y') }}</td>
                    <td><strong>{{ $l->id_laporan }}</strong></td>
                    <td>
                        <strong>{{ $l->lokasi->pasar->nama_pasar ?? '-' }}</strong><br>
                        <span style="color: #4b5563;">{{ $l->lokasi->nama_lokasi ?? '-' }}</span>
                    </td>
                    <td>
                        <strong>{{ $l->nama_fasilitas_display }}</strong>
                    </td>
                    <td>
                        {{ $l->kategori_laporan_display }}
                    </td>
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ $l->status_laporan }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px; color: #6b7280;">
                        Tidak ada data laporan kerusakan sesuai kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- RINGKASAN REKAPITULASI --}}
    <div class="summary-box">
        <div class="summary-title">Ringkasan Rekapitulasi Laporan:</div>
        <table class="summary-table">
            <tr>
                <td><strong>Total Laporan:</strong> {{ $summary['total'] }}</td>
                <td><strong>Menunggu:</strong> {{ $summary['menunggu'] }}</td>
                <td><strong>Diproses:</strong> {{ $summary['diproses'] }}</td>
                <td><strong>Disetujui:</strong> {{ $summary['disetujui'] }}</td>
                <td><strong>Selesai:</strong> {{ $summary['selesai'] }}</td>
                <td><strong>Dikembalikan/Ditolak:</strong> {{ $summary['dikembalikan'] }}</td>
            </tr>
        </table>
    </div>

    {{-- TANDA TANGAN --}}
    <div class="signature-section">
        <div class="signature-box">
            <p>Padang, {{ $printDate }}</p>
            <p>Kepala Dinas Perdagangan Kota Padang</p>
            <div class="signature-space"></div>
            <p class="signature-name">Sutan Perdagangan, S.E., M.Si.</p>
            <p>NIP. 19750812 200003 1 002</p>
        </div>
    </div>

</body>
</html>
