@php
    $existingDetails = $laporan->detailRab;
    $hasExisting = $existingDetails && $existingDetails->count() > 0;
    $canVerifyRab = $laporan->status_verifikasi_rab === 'Menunggu';

    $statusBadge = match($laporan->status_verifikasi_rab) {
        'Menunggu' => 'bg-amber-100 text-amber-700 border-amber-200',
        'Disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'Dikembalikan' => 'bg-red-100 text-red-700 border-red-200',
        default => 'bg-gray-100 text-gray-600 border-gray-200',
    };
    $statusText = match($laporan->status_verifikasi_rab) {
        'Menunggu' => 'Menunggu Verifikasi',
        'Disetujui' => 'Disetujui',
        'Dikembalikan' => 'Dikembalikan',
        default => ($hasExisting ? 'Draft' : 'Belum Dibuat'),
    };
@endphp

<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4">
            <div>
                <h3 class="text-base font-bold text-gray-800">Detail Rencana Anggaran Biaya (RAB)</h3>
                <p class="text-xs text-gray-500 mt-0.5">Rincian estimasi kebutuhan bahan, alat, dan biaya perbaikan fasilitas.</p>
            </div>

            <div class="flex items-center gap-3">
                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $statusBadge }}">
                    Status: {{ $statusText }}
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

    <!-- Keputusan / Verifikasi RAB oleh Kepala Bidang -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
        <div class="border-b border-gray-100 pb-3">
            <h3 class="text-base font-bold text-gray-800">Keputusan Verifikasi RAB</h3>
            <p class="text-xs text-gray-500 mt-0.5">Berikan verifikasi persetujuan atau pengembalian revisi RAB yang diajukan oleh Staff.</p>
        </div>

        @if($canVerifyRab)
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button"
                        onclick="openKembalikanRabModal()"
                        class="px-6 py-2.5 rounded-xl font-semibold text-white shadow-sm bg-gradient-to-r from-amber-500 to-red-500 hover:opacity-90 transition text-xs">
                    Kembalikan RAB untuk Revisi
                </button>

                <button type="button"
                        onclick="openSetujuiRabModal()"
                        class="px-6 py-2.5 rounded-xl font-semibold text-white shadow-sm bg-gradient-to-r from-[#114F72] to-[#16A394] hover:opacity-90 transition text-xs">
                    Setujui RAB
                </button>
            </div>
        @else
            <div class="rounded-xl bg-gray-50 border border-gray-100 p-4 text-xs text-gray-600">
                <p><strong>Status Verifikasi RAB:</strong> {{ $statusText }}</p>
                @if($laporan->status_verifikasi_rab === 'Dikembalikan' && $laporan->catatan_revisi_rab)
                    <p class="text-xs text-red-600 mt-1"><strong>Catatan Revisi:</strong> {{ $laporan->catatan_revisi_rab }}</p>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Modal Setujui RAB -->
<div id="setujuiRabModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeSetujuiRabModal()">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        <h3 class="text-lg font-bold text-gray-800">Setujui RAB</h3>
        <p class="mt-2 text-sm text-gray-600">Apakah Anda yakin ingin menyetujui Rencana Anggaran Biaya (RAB) ini? RAB yang disetujui akan menjadi dasar pelaksanaan pekerjaan perbaikan.</p>

        <form action="{{ route('kabid.rab.setujui', $laporan->id_laporan) }}" method="POST" class="mt-6 flex justify-end gap-3">
            @csrf
            <button type="button" onclick="closeSetujuiRabModal()" class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
            <button type="submit" class="rounded-xl bg-gradient-to-r from-[#114F72] to-[#16A394] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">Ya, Setujui RAB</button>
        </form>
    </div>
</div>

<!-- Modal Kembalikan RAB -->
<div id="kembalikanRabModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeKembalikanRabModal()">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4 border-b pb-3">
            <h3 class="text-lg font-bold text-gray-800">Kembalikan RAB ke Staff</h3>
            <button type="button" onclick="closeKembalikanRabModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('kabid.rab.kembalikan', $laporan->id_laporan) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-600 mb-1">Catatan Revisi RAB <span class="text-red-500">*</span></label>
                <textarea name="catatan_revisi_rab" rows="4" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm" placeholder="Berikan catatan revisi atau alasan pengembalian RAB..."></textarea>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-3 border-t">
                <button type="button" onclick="closeKembalikanRabModal()" class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
                <button type="submit" class="rounded-xl bg-gradient-to-r from-amber-500 to-red-500 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">Kembalikan RAB</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openSetujuiRabModal() {
        const modal = document.getElementById('setujuiRabModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeSetujuiRabModal() {
        const modal = document.getElementById('setujuiRabModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function openKembalikanRabModal() {
        const modal = document.getElementById('kembalikanRabModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeKembalikanRabModal() {
        const modal = document.getElementById('kembalikanRabModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
</script>
