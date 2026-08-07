@php
    $buktiList = $laporan->buktiPembelian ? $laporan->buktiPembelian->sortByDesc('tanggal_bukti') : collect();
    $hasBukti = $buktiList->isNotEmpty();
    $totalNominal = $buktiList->sum('nominal');
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4">
        <div>
            <h3 class="text-base font-bold text-gray-800">Dokumen Bukti Pembelian</h3>
            <p class="text-xs text-gray-500 mt-0.5">Daftar kuitansi, nota, dan bukti transaksi pembayaran perbaikan fasilitas.</p>
        </div>
    </div>

    @if(!$hasBukti)
        <div class="rounded-xl bg-gray-50 border border-gray-100 p-8 text-center text-sm text-gray-500">
            Belum ada bukti pembelian yang diunggah.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">No</th>
                        <th class="text-left py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">Tanggal Bukti</th>
                        <th class="text-left py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">Nama Berkas</th>
                        <th class="text-right py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">Nominal (Rp)</th>
                        <th class="text-center py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($buktiList as $index => $bukti)
                        <tr class="border-b border-gray-100">
                            <td class="py-3 px-2 text-gray-600">{{ $index + 1 }}</td>
                            <td class="py-3 px-2 font-medium text-gray-700">
                                {{ \Carbon\Carbon::parse($bukti->tanggal_bukti)->translatedFormat('d F Y') }}
                            </td>
                            <td class="py-3 px-2 text-gray-800 font-medium">
                                {{ basename($bukti->file_bukti) }}
                            </td>
                            <td class="py-3 px-2 text-right font-bold text-gray-800">
                                Rp {{ number_format($bukti->nominal, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button"
                                            onclick="openGeneralPreviewBuktiModal('{{ asset('storage/' . $bukti->file_bukti) }}', '{{ basename($bukti->file_bukti) }}')"
                                            class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                            title="Lihat / Pratinjau Berkas">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>

                                    <a href="{{ route('laporan.bukti.download', [$laporan->id_laporan, $bukti->id_bukti]) }}"
                                       class="p-1.5 text-[#114F72] hover:bg-blue-50 rounded-lg transition"
                                       title="Unduh Berkas">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200">
                        <td colspan="3" class="py-4 px-2 text-right font-bold text-gray-800">Total Transaksi</td>
                        <td class="py-4 px-2 text-right font-bold text-[#114F72] text-lg">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>

<!-- Modal Pratinjau Bukti Pembelian -->
<div id="generalPreviewBuktiModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 px-4"
     onclick="if(event.target === this) closeGeneralPreviewBuktiModal()">
    <div class="w-full max-w-4xl rounded-2xl bg-white p-6 shadow-2xl space-y-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b pb-3">
            <div>
                <h3 class="text-base font-bold text-gray-800">Pratinjau Berkas Bukti Pembelian</h3>
                <p id="generalPreviewBuktiSubtitle" class="text-xs text-gray-500"></p>
            </div>
            <button type="button" onclick="closeGeneralPreviewBuktiModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div id="generalPreviewBuktiBody" class="flex items-center justify-center min-h-[50vh] max-h-[75vh] overflow-auto">
            <!-- Rendered by JS -->
        </div>
    </div>
</div>

<script>
    function openGeneralPreviewBuktiModal(url, filename) {
        document.getElementById('generalPreviewBuktiSubtitle').textContent = filename || '';
        const body = document.getElementById('generalPreviewBuktiBody');
        const ext = (filename || '').split('.').pop().toLowerCase();

        if (ext === 'pdf') {
            body.innerHTML = `<iframe src="${url}" class="w-full h-[70vh] rounded-xl border border-gray-200"></iframe>`;
        } else {
            body.innerHTML = `<img src="${url}" alt="Bukti Pembelian" class="max-h-[70vh] max-w-full rounded-xl shadow-md object-contain">`;
        }

        const modal = document.getElementById('generalPreviewBuktiModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeGeneralPreviewBuktiModal() {
        const modal = document.getElementById('generalPreviewBuktiModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
</script>
