<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>RAB_{{ $rab->id_rab }}</title>
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
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #114F72;
            margin: 12px 0 6px 0;
            text-transform: uppercase;
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

    <!-- Informasi Meta RAB -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">ID RAB</td>
            <td class="meta-colon">:</td>
            <td class="meta-value"><strong>{{ $rab->id_rab }}</strong></td>
            
            <td class="meta-label">Lokasi Pasar</td>
            <td class="meta-colon">:</td>
            <td class="meta-value"><strong>{{ $rab->nama_pasar }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Status RAB</td>
            <td class="meta-colon">:</td>
            <td class="meta-value"><strong>{{ $rab->status_verifikasi_rab }}</strong></td>
            
            <td class="meta-label">Tanggal Dibuat</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ \Carbon\Carbon::parse($rab->created_at)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <!-- Daftar Laporan Kerusakan yang Dicakup -->
    <div class="section-title">Daftar Laporan Kerusakan Terkait ({{ $rab->laporan->count() }} Laporan)</div>
    <table class="rab-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 20%;">ID Laporan</th>
                <th style="width: 25%;">Fasilitas</th>
                <th style="width: 30%;">Item Kerusakan</th>
                <th class="text-center" style="width: 20%;">Kategori Kerusakan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rab->laporan as $idx => $lap)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold">{{ $lap->id_laporan }}</td>
                    <td>{{ $lap->nama_fasilitas_display }}</td>
                    <td>{{ $lap->item_kerusakan }} ({{ $lap->lokasi_spesifik ?? '-' }})</td>
                    <td class="text-center font-bold">{{ $lap->kategori_kerusakan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tabel Rincian RAB -->
    <div class="section-title">Rincian Kebutuhan & Biaya RAB</div>
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
            @foreach($rab->detailRab as $index => $detail)
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

    <!-- Footer Note -->
    <div class="footer-note">
        <p style="margin: 0 0 2px 0;">* Dokumen Rencana Anggaran Biaya (RAB) ini diterbitkan secara otomatis oleh Sistem Informasi SI-SARPRAS Dinas Perdagangan Kota Padang pada {{ now()->translatedFormat('d F Y H:i') }} WIB.</p>
        <p style="margin: 0;">* Dokumen sah tanpa tanda tangan basah apabila telah diverifikasi dan disetujui dalam sistem.</p>
    </div>

</body>
</html>
