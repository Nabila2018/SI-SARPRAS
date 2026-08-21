@extends('layouts.app')

@section('title', 'Buat RAB Baru - SI-SARPRAS')

@section('breadcrumb')
    <a href="{{ route('staff.rab.index') }}" class="hover:text-[#114F72] transition">RAB</a>
    <svg class="w-4 h-4 mx-2 text-gray-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-gray-600">Buat RAB Baru</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Form Penyusunan RAB Baru</h1>
            <p class="text-xs text-gray-500 mt-1">Pilih lokasi pasar terlebih dahulu untuk menampilkan dan memilih laporan perbaikan.</p>
        </div>
        <a href="{{ route('staff.rab.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
            Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('staff.rab.store') }}" method="POST" id="formRab">
        @csrf

        <!-- STEP 1: PILIH PASAR & LAPORAN TERKAIT -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
            <div class="border-b pb-3">
                <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-[#114F72] text-white flex items-center justify-center text-xs">1</span>
                    Pilih Lokasi Pasar & Laporan Perbaikan
                </h2>
            </div>

            <!-- DROPDOWN PILIH PASAR -->
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1.5">
                    Pilih Lokasi Pasar <span class="text-red-500">*</span>
                </label>
                <select id="selectPasarFilter" onchange="filterLaporanByPasar(this.value)" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-3 bg-gray-50 font-semibold text-gray-800">
                    <option value="">-- Pilih Lokasi Pasar Terlebih Dahulu --</option>
                    @foreach($pasarList as $pasar)
                        <option value="{{ $pasar->id_pasar }}">{{ $pasar->nama_pasar }} ({{ $pasar->alamat ?? '' }})</option>
                    @endforeach
                </select>
            </div>

            <!-- NOTIFIKASI KOSONG JIKA BELUM PILIH PASAR -->
            <div id="emptyPasarNotice" class="p-6 bg-gray-50 border border-dashed border-gray-300 rounded-xl text-center text-xs text-gray-500 space-y-1">
                <svg class="w-8 h-8 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0H9m4 0h2m-6 4h6"/>
                </svg>
                <p class="font-bold text-gray-700">Daftar laporan masih kosong.</p>
                <p>Silakan pilih <span class="font-semibold text-[#114F72]">Lokasi Pasar</span> di atas untuk menampilkan daftar laporan perbaikan yang tersedia.</p>
            </div>

            @if($laporanEligible->isEmpty())
                <div id="noReportNotice" class="hidden p-6 bg-amber-50 border border-amber-200 rounded-xl text-center text-xs text-amber-800">
                    <p class="font-bold">Tidak ada laporan eligible yang tersedia.</p>
                    <p class="mt-1">Laporan yang dapat disusunkan RAB adalah laporan kategori Ringan/Sedang yang hasil evaluasinya telah disetujui Kabid.</p>
                </div>
            @endif

            <!-- CONTAINER DAFTAR LAPORAN TERFILTER -->
            <div id="laporanListContainer" class="hidden space-y-2 max-h-64 overflow-y-auto pr-1">
                @foreach($laporanEligible as $lap)
                    @php
                        $idPasar = $lap->lokasi->id_pasar ?? 'NO_PASAR';
                        $namaPasar = $lap->lokasi->pasar->nama_pasar ?? 'Pasar -';
                    @endphp
                    <label class="laporan-item flex items-start gap-3 p-3 bg-gray-50 hover:bg-gray-100/80 rounded-xl border border-gray-200 cursor-pointer transition hidden" data-id-pasar="{{ $idPasar }}">
                        <input type="checkbox" name="laporan_ids[]" value="{{ $lap->id_laporan }}" data-id-pasar="{{ $idPasar }}" class="laporan-checkbox mt-1 rounded border-gray-300 text-[#114F72] focus:ring-[#114F72]">
                        <div class="flex-1 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-800">{{ $lap->id_laporan }} - {{ $namaPasar }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $lap->kategori_kerusakan }}
                                </span>
                            </div>
                            <p class="text-gray-600 mt-1 font-medium">{{ $lap->nama_fasilitas_display }} - {{ $lap->item_kerusakan }}</p>
                            <p class="text-gray-400 text-[11px] mt-0.5">Lokasi Spesifik: {{ $lap->lokasi_spesifik ?? '-' }}</p>
                        </div>
                    </label>
                @endforeach
                <div id="noReportForPasar" class="hidden p-4 bg-amber-50 border border-amber-200 rounded-xl text-center text-xs text-amber-800 font-semibold">
                    Tidak ada laporan perbaikan yang eligible (Disetujui & belum masuk RAB) untuk pasar ini.
                </div>
            </div>
            @error('laporan_ids')
                <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <!-- STEP 2: DETAIL KEBUTUHAN RAB (SAB INTEGRATION) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4 mt-6">
            <div class="flex items-center justify-between border-b pb-3">
                <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-[#114F72] text-white flex items-center justify-center text-xs">2</span>
                    Rincian Kebutuhan RAB (Master SAB / Manual)
                </h2>
                <button type="button" onclick="addRow()" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-bold transition">
                    + Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="tableDetailRab">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                        <tr>
                            <th class="py-2.5 px-3 w-1/3">Pilih Kebutuhan SAB / Rincian</th>
                            <th class="py-2.5 px-3 w-28">Volume</th>
                            <th class="py-2.5 px-3 w-28">Satuan</th>
                            <th class="py-2.5 px-3 w-36">Harga Satuan (Rp)</th>
                            <th class="py-2.5 px-3 w-36 text-right">Subtotal (Rp)</th>
                            <th class="py-2.5 px-3 w-12 text-center">Hapus</th>
                        </tr>
                    </thead>
                    <tbody id="rabContainer" class="divide-y divide-gray-100">
                        <tr class="rab-row">
                            <td class="py-2.5 px-3">
                                <select onchange="onSabSelect(this)" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-xs outline-none focus:ring-1 focus:ring-[#114F72] mb-1">
                                    <option value="">-- Pilih dari Master SAB --</option>
                                    @foreach($sabList as $sab)
                                        <option value="{{ $sab->id_sab }}" data-nama="{{ $sab->nama_kebutuhan }}" data-satuan="{{ $sab->satuan }}" data-harga="{{ $sab->harga_standar }}">
                                            {{ $sab->nama_kebutuhan }} (Rp {{ number_format($sab->harga_standar, 0, ',', '.') }}/{{ $sab->satuan }})
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="id_sab[]" class="sab-id-input">
                                <input type="text" name="rincian_kebutuhan[]" placeholder="Rincian kebutuhan..." required class="rincian-input w-full p-2 bg-white border border-gray-200 rounded-lg text-xs outline-none focus:ring-1 focus:ring-[#114F72]">
                            </td>
                            <td class="py-2.5 px-3">
                                <input type="number" step="0.001" min="0.001" name="volume[]" oninput="calculateSubtotal(this)" required placeholder="0" class="volume-input w-full p-2 bg-white border border-gray-200 rounded-lg text-xs outline-none focus:ring-1 focus:ring-[#114F72]">
                            </td>
                            <td class="py-2.5 px-3">
                                <input type="text" name="satuan[]" required placeholder="sak/m2/kg..." class="satuan-input w-full p-2 bg-white border border-gray-200 rounded-lg text-xs outline-none focus:ring-1 focus:ring-[#114F72]">
                            </td>
                            <td class="py-2.5 px-3">
                                <input type="number" min="1" name="harga_satuan[]" oninput="calculateSubtotal(this)" required placeholder="0" class="harga-input w-full p-2 bg-white border border-gray-200 rounded-lg text-xs outline-none focus:ring-1 focus:ring-[#114F72]">
                            </td>
                            <td class="py-2.5 px-3 text-right font-bold text-gray-800 subtotal-display">
                                Rp 0
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                <button type="button" onclick="removeRow(this)" class="text-rose-500 hover:text-rose-700 font-bold">&times;</button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="border-t bg-gray-50/50">
                        <tr>
                            <td colspan="4" class="py-3 px-3 font-bold text-gray-700 text-right">Total Anggaran RAB:</td>
                            <td class="py-3 px-3 font-bold text-[#114F72] text-right text-sm" id="grandTotalDisplay">Rp 0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- ACTIONS -->
        <div class="flex items-center justify-end gap-3 mt-6">
            <button type="submit" name="action" value="draft" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold rounded-xl transition">
                Simpan sebagai Draft
            </button>
            <button type="submit" name="action" value="submit" class="px-5 py-2.5 bg-[#114F72] hover:bg-[#114F72]/90 text-white text-xs font-bold rounded-xl shadow-sm transition">
                Kirim RAB ke Kabid
            </button>
        </div>
    </form>
</div>

<script>
function filterLaporanByPasar(selectedPasarId) {
    const emptyNotice = document.getElementById('emptyPasarNotice');
    const container = document.getElementById('laporanListContainer');
    const noReportMsg = document.getElementById('noReportForPasar');
    const allItems = document.querySelectorAll('.laporan-item');
    const allCheckboxes = document.querySelectorAll('.laporan-checkbox');

    if (!selectedPasarId) {
        emptyNotice.classList.remove('hidden');
        container.classList.add('hidden');
        allCheckboxes.forEach(cb => {
            cb.checked = false;
        });
        return;
    }

    emptyNotice.classList.add('hidden');
    container.classList.remove('hidden');

    let visibleCount = 0;
    allItems.forEach(item => {
        const cb = item.querySelector('.laporan-checkbox');
        if (item.dataset.idPasar === selectedPasarId) {
            item.classList.remove('hidden');
            visibleCount++;
        } else {
            item.classList.add('hidden');
            cb.checked = false;
        }
    });

    if (visibleCount === 0) {
        noReportMsg.classList.remove('hidden');
    } else {
        noReportMsg.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('selectPasarFilter');
    if (select && select.value) {
        filterLaporanByPasar(select.value);
    }
});

function onSabSelect(selectEl) {
    const row = selectEl.closest('.rab-row');
    const selected = selectEl.options[selectEl.selectedIndex];

    if (selectEl.value) {
        row.querySelector('.sab-id-input').value = selectEl.value;
        row.querySelector('.rincian-input').value = selected.dataset.nama || '';
        row.querySelector('.satuan-input').value = selected.dataset.satuan || '';
        row.querySelector('.harga-input').value = selected.dataset.harga || '';
    } else {
        row.querySelector('.sab-id-input').value = '';
    }
    calculateSubtotal(selectEl);
}

function calculateSubtotal(el) {
    const row = el.closest('.rab-row');
    const vol = parseFloat(row.querySelector('.volume-input').value) || 0;
    const price = parseFloat(row.querySelector('.harga-input').value) || 0;
    const subtotal = vol * price;

    row.querySelector('.subtotal-display').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
    updateGrandTotal();
}

function updateGrandTotal() {
    let grand = 0;
    document.querySelectorAll('.rab-row').forEach(row => {
        const vol = parseFloat(row.querySelector('.volume-input').value) || 0;
        const price = parseFloat(row.querySelector('.harga-input').value) || 0;
        grand += (vol * price);
    });
    document.getElementById('grandTotalDisplay').innerText = 'Rp ' + grand.toLocaleString('id-ID');
}

function addRow() {
    const container = document.getElementById('rabContainer');
    const firstRow = container.querySelector('.rab-row');
    const newRow = firstRow.cloneNode(true);

    newRow.querySelector('select').selectedIndex = 0;
    newRow.querySelector('.sab-id-input').value = '';
    newRow.querySelector('.rincian-input').value = '';
    newRow.querySelector('.volume-input').value = '';
    newRow.querySelector('.satuan-input').value = '';
    newRow.querySelector('.harga-input').value = '';
    newRow.querySelector('.subtotal-display').innerText = 'Rp 0';

    container.appendChild(newRow);
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.rab-row');
    if (rows.length > 1) {
        btn.closest('.rab-row').remove();
        updateGrandTotal();
    } else {
        alert('Minimal harus ada 1 baris rincian kebutuhan RAB.');
    }
}
</script>
@endsection
