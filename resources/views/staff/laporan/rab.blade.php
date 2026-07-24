@extends('layouts.app')

@section('title', 'RAB - SI-SARPRAS')
@section('breadcrumb')
    <a href="{{ route('staff.laporan.index') }}" class="hover:text-[#114F72] transition">Daftar Laporan Masuk</a>
    <svg class="w-4 h-4 mx-2 text-gray-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <a href="{{ route('laporan.show', $laporan->id_laporan) }}" class="hover:text-[#114F72] transition">Detail Laporan</a>
    <svg class="w-4 h-4 mx-2 text-gray-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-gray-600">Rencana Anggaran Biaya</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto pb-12">

    <!-- Tombol Kembali -->
    <a href="{{ route('staff.laporan.index') }}"
       class="inline-flex items-center gap-2 text-gray-600 hover:text-[#114F72] mb-6 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar
    </a>

    
    <!-- Alert Messages -->
    @if(session('success'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Status RAB -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800">Status RAB</h2>
            @php
                $statusBadge = match($laporan->status_verifikasi_rab) {
                    'Menunggu' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'Disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                    'Dikembalikan' => 'bg-red-100 text-red-700 border-red-200',
                    default => 'bg-gray-100 text-gray-600 border-gray-200',
                };
                $statusText = $laporan->status_verifikasi_rab ?? 'Belum Dibuat';
            @endphp
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium {{ $statusBadge }}">
                {{ $statusText }}
            </span>
        </div>

        @if($laporan->status_verifikasi_rab === 'Dikembalikan' && $laporan->catatan_revisi_rab)
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 mb-4">
                <p class="text-xs uppercase tracking-wider text-red-600 font-medium mb-1">Catatan Revisi dari Kabid</p>
                <p class="text-sm text-red-700">{{ $laporan->catatan_revisi_rab }}</p>
            </div>
        @endif
    </div>

    <!-- Form RAB -->
    @php
        $isEditable = is_null($laporan->status_verifikasi_rab) || $laporan->status_verifikasi_rab === 'Dikembalikan';
        $existingDetails = $laporan->detailRab;
        $hasExisting = $existingDetails->count() > 0;
    @endphp

    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Detail Rencana Anggaran Biaya</h2>

        <form id="rabForm" action="{{ route('staff.laporan.rab.store', $laporan->id_laporan) }}" method="POST">
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
                                            placeholder="Contoh: Cat tembok" {{ $isEditable ? '' : 'readonly' }} required>
                                    </td>
                                    <td class="py-2 px-2">
                                        <input type="number" name="volume[]" value="{{ $detail->volume }}" step="0.001"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20 volume-input"
                                            placeholder="0" {{ $isEditable ? '' : 'readonly' }} required>
                                    </td>
                                    <td class="py-2 px-2">
                                        <input type="text" name="satuan[]" value="{{ $detail->satuan }}"
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20"
                                            placeholder="kg, m², unit" {{ $isEditable ? '' : 'readonly' }} required>
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
                                        placeholder="Contoh: Cat tembok" required>
                                </td>
                                <td class="py-2 px-2">
                                    <input type="number" name="volume[]" step="0.001"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20 volume-input"
                                        placeholder="0" required>
                                </td>
                                <td class="py-2 px-2">
                                    <input type="text" name="satuan[]"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20"
                                        placeholder="kg, m², unit" required>
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

                        @if($isEditable)
                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="button" onclick="addRow()" class="inline-flex items-center gap-2 rounded-lg border border-[#114F72] px-4 py-2 text-sm font-medium text-[#114F72] hover:bg-[#114F72]/5 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Baris
                    </button>

                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-[#114F72] to-[#16A394] px-6 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Simpan RAB
                    </button>

                    @if($hasExisting)
                        <button type="button" onclick="openForwardRabModal()"
                            class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-[#114F72] to-[#16A394] px-6 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                            </svg>
                            Teruskan ke Kabid
                        </button>
                    @endif
                </div>
            @endif
        </form>

        @if($isEditable && $hasExisting)
            <form id="forwardForm" action="{{ route('staff.laporan.rab.forward', $laporan->id_laporan) }}" method="POST" class="hidden">
                @csrf
            </form>
        @endif
    </div>
</div>

<!-- Modal Konfirmasi Teruskan -->
<div id="forwardRabModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeForwardRabModal()">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        <h3 class="text-lg font-semibold text-gray-800">Teruskan RAB ke Kabid</h3>
        <p class="mt-2 text-sm text-gray-600">Apakah Anda yakin ingin meneruskan RAB ini ke Kabid untuk diverifikasi?</p>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button" onclick="closeForwardRabModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
            <button type="button" onclick="document.getElementById('forwardForm').submit();" class="rounded-lg bg-gradient-to-r from-[#114F72] to-[#16A394] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">Ya, Teruskan</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // ===== FILTER TOGGLE =====
    (function() {
        const filterToggle = document.getElementById('filterToggle');
        const filterPopover = document.getElementById('filterPopover');

        if (filterToggle && filterPopover) {
            filterToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                filterPopover.classList.toggle('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!filterPopover.contains(e.target) && e.target !== filterToggle) {
                    filterPopover.classList.add('hidden');
                }
            });
        }
    })();

    // ===== RAB CALCULATION =====
    function formatRupiah(angka) {
        return 'Rp ' + angka.toLocaleString('id-ID');
    }

    function calculateSubtotal(row) {
        const volume = parseFloat(row.querySelector('.volume-input').value) || 0;
        const harga = parseFloat(row.querySelector('.harga-input').value) || 0;
        const subtotal = volume * harga;
        row.querySelector('.subtotal-cell').textContent = formatRupiah(subtotal);
        return subtotal;
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.rab-row').forEach(row => {
            total += calculateSubtotal(row);
        });
        document.getElementById('totalRab').textContent = formatRupiah(total);
    }

    function addRow() {
        const tbody = document.getElementById('rabBody');
        const rowCount = tbody.querySelectorAll('.rab-row').length;
        const newRow = document.createElement('tr');
        newRow.className = 'rab-row border-b border-gray-100';
        newRow.setAttribute('data-index', rowCount);

        newRow.innerHTML = `
            <td class="py-3 px-2 text-gray-600 row-number">${rowCount + 1}</td>
            <td class="py-2 px-2">
                <input type="text" name="rincian_kebutuhan[]"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20"
                    placeholder="Contoh: Cat tembok" required>
            </td>
            <td class="py-2 px-2">
                <input type="number" name="volume[]" step="0.001"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20 volume-input"
                    placeholder="0" required>
            </td>
            <td class="py-2 px-2">
                <input type="text" name="satuan[]"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20"
                    placeholder="kg, m², unit" required>
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
        `;

        tbody.appendChild(newRow);
        attachListeners(newRow);
        renumberRows();
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.rab-row');
        if (rows.length <= 1) {
            alert('Minimal harus ada 1 baris RAB.');
            return;
        }
        btn.closest('.rab-row').remove();
        renumberRows();
        calculateTotal();
    }

    function renumberRows() {
        document.querySelectorAll('.rab-row').forEach((row, index) => {
            row.setAttribute('data-index', index);
            row.querySelector('.row-number').textContent = index + 1;
        });
    }

    function attachListeners(row) {
        const volumeInput = row.querySelector('.volume-input');
        const hargaInput = row.querySelector('.harga-input');

        if (volumeInput) {
            volumeInput.addEventListener('input', calculateTotal);
        }
        if (hargaInput) {
            hargaInput.addEventListener('input', calculateTotal);
        }
    }

    function openForwardRabModal() {
        document.getElementById('forwardRabModal').classList.remove('hidden');
        document.getElementById('forwardRabModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeForwardRabModal() {
        const modal = document.getElementById('forwardRabModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    // Init
    document.querySelectorAll('.rab-row').forEach(row => attachListeners(row));
    calculateTotal();

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeForwardRabModal();
        }
    });
</script>
@endsection


<