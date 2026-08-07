@php
    $existingDetails = $laporan->detailRab;
    $hasExisting = $existingDetails && $existingDetails->count() > 0;

    $statusBadge = match($laporan->status_verifikasi_rab) {
        'Menunggu' => 'bg-amber-100 text-amber-700 border-amber-200',
        'Disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'Dikembalikan' => 'bg-red-100 text-red-700 border-red-200',
        default => 'bg-gray-100 text-gray-600 border-gray-200',
    };
    $statusText = $laporan->status_verifikasi_rab ?? 'Belum Dibuat';
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4">
        <div>
            <h3 class="text-base font-bold text-gray-800">Detail Verifikasi Rencana Anggaran Biaya</h3>
            <p class="text-xs text-gray-500 mt-0.5">Rincian estimasi biaya perbaikan yang diajukan oleh Staff Sarpras.</p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $statusBadge }}">
                Status Verifikasi: {{ $statusText }}
            </span>

            @if($hasExisting)
                <a href="{{ route('laporan.rab.pdf', $laporan->id_laporan) }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-gray-300 bg-white px-3.5 py-1.5 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 hover:text-[#114F72] transition">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download PDF
                </a>
            @endif
        </div>
    </div>

    @if($hasExisting)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">No</th>
                        <th class="text-left py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium w-2/5">Rincian Kebutuhan</th>
                        <th class="text-center py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">Volume</th>
                        <th class="text-center py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">Satuan</th>
                        <th class="text-right py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">Harga Satuan (Rp)</th>
                        <th class="text-right py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">Subtotal (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalKabidRab = 0; @endphp
                    @foreach($existingDetails as $index => $detail)
                        @php
                            $subtotal = $detail->volume * $detail->harga_satuan;
                            $totalKabidRab += $subtotal;
                        @endphp
                        <tr class="border-b border-gray-100">
                            <td class="py-3 px-2 text-gray-600">{{ $index + 1 }}</td>
                            <td class="py-3 px-2 font-medium text-gray-800">{{ $detail->rincian_kebutuhan }}</td>
                            <td class="py-3 px-2 text-center text-gray-700">{{ number_format($detail->volume, 2, ',', '.') }}</td>
                            <td class="py-3 px-2 text-center text-gray-700">{{ $detail->satuan }}</td>
                            <td class="py-3 px-2 text-right text-gray-700">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                            <td class="py-3 px-2 text-right font-bold text-gray-800">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200">
                        <td colspan="5" class="py-4 px-2 text-right font-bold text-gray-800">Total RAB</td>
                        <td class="py-4 px-2 text-right font-bold text-[#114F72] text-lg">Rp {{ number_format($totalKabidRab, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @else
        <div class="rounded-xl bg-gray-50 border border-gray-100 p-8 text-center text-sm text-gray-500">
            Belum ada Rencana Anggaran Biaya (RAB) yang dibuat untuk laporan ini.
        </div>
    @endif
</div>
