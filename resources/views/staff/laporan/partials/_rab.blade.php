@php
    $isEditable = is_null($laporan->status_verifikasi_rab) || $laporan->status_verifikasi_rab === 'Dikembalikan';
    $existingDetails = $laporan->detailRab;
    $hasExisting = $existingDetails && $existingDetails->count() > 0;

    $statusText = match($laporan->status_verifikasi_rab) {
        'Menunggu' => 'Menunggu',
        'Disetujui' => 'Disetujui',
        'Dikembalikan' => 'Dikembalikan',
        default => ($hasExisting ? 'Belum Diteruskan' : 'Belum Dibuat'),
    };

    $statusBadge = match($laporan->status_verifikasi_rab) {
        'Menunggu' => 'bg-amber-100 text-amber-700 border-amber-200',
        'Disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'Dikembalikan' => 'bg-red-100 text-red-700 border-red-200',
        default => 'bg-gray-100 text-gray-600 border-gray-200',
    };
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
    <!-- Header Kartu & Status -->
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4">
        <div>
            <h3 class="text-base font-bold text-gray-800">Detail Rencana Anggaran Biaya (RAB)</h3>
            <p class="text-xs text-gray-500 mt-0.5">Rincian estimasi kebutuhan bahan, alat, dan biaya perbaikan fasilitas.</p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $statusBadge }}">
                {{ $statusText }}
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

    <!-- Banner Catatan Revisi Kabid -->
    @if($laporan->status_verifikasi_rab === 'Dikembalikan' && $laporan->catatan_revisi_rab)
        <div class="rounded-xl border border-red-200 bg-red-50 p-4">
            <p class="text-xs uppercase tracking-wider text-red-600 font-semibold mb-1">Catatan Revisi RAB dari Kabid</p>
            <p class="text-sm text-red-700">{{ $laporan->catatan_revisi_rab }}</p>
        </div>
    @endif

    <!-- Form & Tabel RAB -->
    <form id="rabForm" action="{{ route('staff.laporan.rab.store', $laporan->id_laporan) }}?tab=rab" method="POST">
        @csrf

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="rabTable">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">No</th>
                        <th class="text-left py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium w-2/5">Rincian Kebutuhan</th>
                        <th class="text-left py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">Volume</th>
                        <th class="text-left py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">Satuan</th>
                        <th class="text-left py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">Harga Satuan (Rp)</th>
                        <th class="text-right py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">Subtotal (Rp)</th>
                        @if($isEditable)
                            <th class="text-center py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium w-16">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="rabBody">
                    @if($hasExisting)
                        @foreach($existingDetails as $index => $detail)
                            <tr class="rab-row border-b border-gray-100" data-index="{{ $index }}">
                                <td class="py-3 px-2 text-gray-600 row-number">{{ $index + 1 }}</td>
                                <td class="py-2 px-2">
                                    <input type="text" name="rincian_kebutuhan[]" value="{{ $detail->rincian_kebutuhan }}"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20"
                                        placeholder="Contoh: Semen Padang 50kg" {{ $isEditable ? '' : 'readonly' }} required>
                                </td>
                                <td class="py-2 px-2">
                                    <input type="number" name="volume[]" value="{{ $detail->volume }}" step="0.001"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20 volume-input"
                                        placeholder="0" {{ $isEditable ? '' : 'readonly' }} required>
                                </td>
                                <td class="py-2 px-2">
                                    <input type="text" name="satuan[]" value="{{ $detail->satuan }}"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20"
                                        placeholder="Sak, galon, unit" {{ $isEditable ? '' : 'readonly' }} required>
                                </td>
                                <td class="py-2 px-2">
                                    <input type="number" name="harga_satuan[]" value="{{ $detail->harga_satuan }}" step="1"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20 harga-input"
                                        placeholder="0" {{ $isEditable ? '' : 'readonly' }} required>
                                </td>
                                <td class="py-3 px-2 text-right font-medium text-gray-800 subtotal-cell">Rp 0</td>
                                @if($isEditable)
                                    <td class="py-2 px-2 text-center">
                                        <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700 transition p-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <!-- Baris default kosong -->
                        <tr class="rab-row border-b border-gray-100" data-index="0">
                            <td class="py-3 px-2 text-gray-600 row-number">1</td>
                            <td class="py-2 px-2">
                                <input type="text" name="rincian_kebutuhan[]"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20"
                                    placeholder="Contoh: Semen Padang 50kg" required>
                            </td>
                            <td class="py-2 px-2">
                                <input type="number" name="volume[]" step="0.001"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20 volume-input"
                                    placeholder="0" required>
                            </td>
                            <td class="py-2 px-2">
                                <input type="text" name="satuan[]"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20"
                                    placeholder="Sak, galon, unit" required>
                            </td>
                            <td class="py-2 px-2">
                                <input type="number" name="harga_satuan[]" step="1"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20 harga-input"
                                    placeholder="0" required>
                            </td>
                            <td class="py-3 px-2 text-right font-medium text-gray-800 subtotal-cell">Rp 0</td>
                            <td class="py-2 px-2 text-center">
                                <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700 transition p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200">
                        <td colspan="5" class="py-4 px-2 text-right font-bold text-gray-800">Total RAB</td>
                        <td class="py-4 px-2 text-right font-bold text-[#114F72] text-lg" id="totalRab">Rp 0</td>
                        @if($isEditable)
                            <td></td>
                        @endif
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-gray-100">
            <div class="flex flex-wrap items-center gap-3">
                @if($isEditable)
                    <button type="button" onclick="addRow()" class="inline-flex items-center gap-2 rounded-xl border border-[#114F72] px-4 py-2 text-sm font-medium text-[#114F72] hover:bg-[#114F72]/5 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Baris
                    </button>

                    <button type="button" onclick="openForwardRabModal()"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#114F72] to-[#16A394] px-6 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                        </svg>
                        Teruskan ke Kabid
                    </button>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Modal Konfirmasi Teruskan RAB -->
<div id="forwardRabModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeForwardRabModal()">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        <h3 class="text-lg font-bold text-gray-800">Teruskan RAB ke Kabid</h3>
        <p class="mt-2 text-sm text-gray-600">Apakah Anda yakin ingin meneruskan RAB ini ke Kabid untuk diverifikasi?</p>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button" onclick="closeForwardRabModal()" class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
            <button type="button" onclick="document.getElementById('rabForm').submit();" class="rounded-xl bg-gradient-to-r from-[#114F72] to-[#16A394] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">Ya, Teruskan</button>
        </div>
    </div>
</div>

<script>
    const isEditable = @json($isEditable);

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    }

    function calculateRow(row) {
        const volumeInput = row.querySelector('.volume-input');
        const hargaInput = row.querySelector('.harga-input');
        const subtotalCell = row.querySelector('.subtotal-cell');

        const volume = parseFloat(volumeInput.value) || 0;
        const harga = parseFloat(hargaInput.value) || 0;
        const subtotal = volume * harga;

        subtotalCell.textContent = formatRupiah(subtotal);
        return subtotal;
    }

    function calculateTotal() {
        const rows = document.querySelectorAll('.rab-row');
        let total = 0;
        rows.forEach(row => {
            total += calculateRow(row);
        });
        document.getElementById('totalRab').textContent = formatRupiah(total);
    }

    function updateRowNumbers() {
        const rows = document.querySelectorAll('.rab-row');
        rows.forEach((row, index) => {
            row.querySelector('.row-number').textContent = index + 1;
        });
    }

    function addRow() {
        if (!isEditable) return;
        const body = document.getElementById('rabBody');
        const index = body.children.length;

        const tr = document.createElement('tr');
        tr.className = 'rab-row border-b border-gray-100';
        tr.setAttribute('data-index', index);
        tr.innerHTML = `
            <td class="py-3 px-2 text-gray-600 row-number">${index + 1}</td>
            <td class="py-2 px-2">
                <input type="text" name="rincian_kebutuhan[]" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20" placeholder="Contoh: Semen Padang 50kg" required>
            </td>
            <td class="py-2 px-2">
                <input type="number" name="volume[]" step="0.001" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20 volume-input" placeholder="0" required>
            </td>
            <td class="py-2 px-2">
                <input type="text" name="satuan[]" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20" placeholder="Sak, galon, unit" required>
            </td>
            <td class="py-2 px-2">
                <input type="number" name="harga_satuan[]" step="1" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20 harga-input" placeholder="0" required>
            </td>
            <td class="py-3 px-2 text-right font-medium text-gray-800 subtotal-cell">Rp 0</td>
            <td class="py-2 px-2 text-center">
                <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700 transition p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </td>
        `;
        body.appendChild(tr);
        attachEventListeners(tr);
        updateRowNumbers();
        calculateTotal();
    }

    function removeRow(button) {
        if (!isEditable) return;
        const rows = document.querySelectorAll('.rab-row');
        if (rows.length <= 1) {
            alert('Minimal harus ada 1 baris detail RAB.');
            return;
        }
        const row = button.closest('tr');
        row.remove();
        updateRowNumbers();
        calculateTotal();
    }

    function attachEventListeners(row) {
        const volumeInput = row.querySelector('.volume-input');
        const hargaInput = row.querySelector('.harga-input');

        if (volumeInput) volumeInput.addEventListener('input', calculateTotal);
        if (hargaInput) hargaInput.addEventListener('input', calculateTotal);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.rab-row').forEach(row => attachEventListeners(row));
        calculateTotal();
    });

    function openForwardRabModal() {
        const modal = document.getElementById('forwardRabModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeForwardRabModal() {
        const modal = document.getElementById('forwardRabModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
</script>
