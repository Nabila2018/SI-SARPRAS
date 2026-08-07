<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>RAB_{{ $laporan->id_laporan }}</title>
    <style>
        @page {
            margin: 20px 30px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1f2937;
            font-size: 11px;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header-logo {
            width: 75px;
            text-align: center;
            vertical-align: middle;
        }
        .header-logo img {
            max-width: 70px;
            max-height: 70px;
        }
        .header-text {
            text-align: center;
            vertical-align: middle;
        }
        .header-text h3 {
            margin: 0;
            font-size: 13px;
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
            font-size: 9px;
            color: #4b5563;
        }
        .header-divider {
            border-bottom: 3px double #111827;
            margin-bottom: 15px;
        }
        .document-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            color: #114F72;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }
        .meta-table td {
            padding: 6px 10px;
            font-size: 10.5px;
            vertical-align: top;
        }
        .meta-label {
            width: 18%;
            font-weight: bold;
            color: #374151;
        }
        .meta-colon {
            width: 2%;
            text-align: center;
            font-weight: bold;
        }
        .meta-value {
            width: 30%;
            color: #111827;
        }
        .rab-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .rab-table th {
            background-color: #114F72;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9.5px;
            padding: 8px 6px;
            border: 1px solid #114F72;
            text-align: left;
        }
        .rab-table td {
            padding: 7px 6px;
            border: 1px solid #d1d5db;
            font-size: 10px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .total-row {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .total-row td {
            border-top: 2px solid #114F72;
            font-size: 11px;
        }
        .footer-note {
            margin-top: 30px;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            font-size: 8.5px;
            color: #6b7280;
            text-align: justify;
        }
    </style>
</head>
<body>

    <!-- Header Instansi -->
    <table class="header-table">
        <tr>
            @if(!empty($logoBase64))
                <td class="header-logo">
                    <img src="{{ $logoBase64 }}" alt="Logo Padang">
                </td>
            @endif
            <td class="header-text">
                <h3>Pemerintah Kota Padang</h3>
                <h2>Dinas Perdagangan</h2>
                <p>Jalan Khatib Sulaiman No. 1, Kota Padang, Sumatera Barat</p>
            </td>
        </tr>
    </table>

    <div class="header-divider"></div>

    <!-- Judul Dokumen -->
    <div class="document-title">
        Rencana Anggaran Biaya (RAB)
    </div>

    <!-- Informasi Laporan -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">ID Laporan</td>
            <td class="meta-colon">:</td>
            <td class="meta-value"><strong>{{ $laporan->id_laporan }}</strong></td>
            
            <td class="meta-label">Nama Pasar</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ $laporan->lokasi->pasar->nama_pasar ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal Lapor</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->translatedFormat('d F Y') }}</td>
            
            <td class="meta-label">Lokasi</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ $laporan->lokasi->nama_lokasi ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Pelapor</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ $laporan->pelapor->nama_lengkap ?? '-' }}</td>
            
            <td class="meta-label">Fasilitas</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ $laporan->fasilitas->nama_fasilitas ?? '-' }}</td>
        </tr>
    </table>

    <!-- Tabel Rincian RAB -->
    <table class="rab-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 40%;">Rincian Kebutuhan</th>
                <th class="text-center" style="width: 12%;">Volume</th>
                <th class="text-center" style="width: 13%;">Satuan</th>
                <th class="text-right" style="width: 15%;">Harga Satuan</th>
                <th class="text-right" style="width: 15%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $totalRab = 0; @endphp
            @foreach($laporan->detailRab as $index => $detail)
                @php
                    $subtotal = $detail->volume * $detail->harga_satuan;
                    $totalRab += $subtotal;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $detail->rincian_kebutuhan }}</td>
                    <td class="text-center">{{ number_format($detail->volume, 2, ',', '.') }}</td>
                    <td class="text-center">{{ $detail->satuan }}</td>
                    <td class="text-right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL RAB:</td>
                <td class="text-right font-bold" style="color: #114F72;">Rp {{ number_format($totalRab, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Catatan & Footer -->
    <div class="footer-note">
        <p style="margin: 0 0 2px 0;">* Dokumen Rencana Anggaran Biaya (RAB) ini diterbitkan secara otomatis oleh Sistem Informasi SI-SARPRAS Dinas Perdagangan Kota Padang pada {{ now()->translatedFormat('d F Y H:i') }} WIB.</p>
        <p style="margin: 0;">* Dokumen sah tanpa tanda tangan basah apabila telah diverifikasi dan disetujui dalam sistem.</p>
    </div>

</body>
</html>
