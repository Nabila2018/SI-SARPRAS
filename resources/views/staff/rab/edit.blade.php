@extends('layouts.app')

@section('title', 'Edit RAB - SI-SARPRAS')

@section('breadcrumb')
    <a href="{{ route('staff.rab.index') }}" class="hover:text-[#114F72] transition">RAB</a>
    <svg class="w-4 h-4 mx-2 text-gray-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <a href="{{ route('staff.rab.show', $rab->id_rab) }}" class="hover:text-[#114F72] transition">Detail RAB {{ $rab->id_rab }}</a>
    <svg class="w-4 h-4 mx-2 text-gray-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-gray-600">Edit RAB</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">
    <!-- Header Page (Tanpa Tombol Kembali/Batal) -->
    <div>
        <h1 class="text-xl font-bold text-gray-800">Edit RAB {{ $rab->id_rab }}</h1>
        <p class="text-xs text-gray-500 mt-1">Perbarui rincian kebutuhan material atau komposisi laporan dari pasar yang sama pada RAB ini.</p>
    </div>

    @if($isLocked)
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 space-y-1">
            <p class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Komposisi Laporan Terkunci
            </p>
            <p>RAB ini telah <strong>pernah disetujui</strong> oleh Kepala Bidang. Sesuai alur bisnis, komposisi laporan dalam RAB dikunci. Anda hanya dapat menyesuaikan rincian kebutuhan material, volume, dan harga satuan.</p>
        </div>
    @endif



    <form action="{{ route('staff.rab.update', $rab->id_rab) }}" method="POST" id="formRab">
        @csrf
        @method('PUT')

        <!-- STEP 1: PILIH LAPORAN TERKAIT -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b pb-3 gap-2">
                <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-[#114F72] text-white flex items-center justify-center text-xs font-semibold">1</span>
                    Daftar Laporan Terkait
                </h2>
                @if(!$isLocked)
                    <span class="text-[11px] font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                        * 1 RAB khusus untuk laporan dari lokasi pasar yang sama
                    </span>
                @endif
            </div>

            @if($isLocked)
                <div class="space-y-2">
                    @foreach($rab->laporan as $lap)
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-xs flex items-center justify-between">
                            <div>
                                <span class="font-bold text-gray-800">{{ $lap->id_laporan }} - {{ $lap->lokasi->pasar->nama_pasar ?? '-' }}</span>
                                <p class="text-gray-600 mt-0.5">{{ $lap->nama_fasilitas_display }} - {{ $lap->item_kerusakan }}</p>
                            </div>
                            <input type="hidden" name="laporan_ids[]" value="{{ $lap->id_laporan }}">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $lap->kategori_kerusakan }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                @php
                    $selectedLaporanIds = $rab->laporan->pluck('id_laporan')->toArray();
                @endphp
                <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                    @foreach($laporanEligible as $lap)
                        @php
                            $idPasar = $lap->lokasi->id_pasar ?? 'NO_PASAR';
                            $namaPasar = $lap->lokasi->pasar->nama_pasar ?? 'Pasar -';
                            $isChecked = in_array($lap->id_laporan, $selectedLaporanIds);
                        @endphp
                        <label class="flex items-start gap-3 p-3 bg-gray-50 hover:bg-gray-100/80 rounded-xl border border-gray-200 cursor-pointer transition">
                            <input type="checkbox" name="laporan_ids[]" value="{{ $lap->id_laporan }}" {{ $isChecked ? 'checked' : '' }} class="laporan-checkbox mt-1 rounded border-gray-300 text-[#114F72] focus:ring-[#114F72]">
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
                </div>
                @error('laporan_ids')
                    <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
                @enderror
            @endif
        </div>

        <!-- STEP 2: RINCIAN KEBUTUHAN RAB (SEARCHABLE AUTOCOMPLETE SAB) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4 mt-6">
            <div class="flex items-center justify-between border-b pb-3">
                <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-[#114F72] text-white flex items-center justify-center text-xs font-semibold">2</span>
                    Rincian Kebutuhan RAB
                </h2>
                <button type="button" onclick="addRow()" class="px-3.5 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="tableDetailRab">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold border-b border-gray-100">
                        <tr>
                            <th class="py-2.5 px-3 w-12 text-center">No.</th>
                            <th class="py-2.5 px-3">Kebutuhan</th>
                            <th class="py-2.5 px-3 w-28 text-center">Volume</th>
                            <th class="py-2.5 px-3 w-24 text-center">Satuan</th>
                            <th class="py-2.5 px-3 w-36 text-right">Harga Satuan</th>
                            <th class="py-2.5 px-3 w-36 text-right">Subtotal</th>
                            <th class="py-2.5 px-3 w-10 text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="rabContainer" class="divide-y divide-gray-100">
                        @forelse($rab->detailRab as $index => $item)
                            @php $sub = $item->volume * $item->harga_satuan; @endphp
                            <tr class="rab-row">
                                <!-- Kolom No. (Otomatis & Read-Only) -->
                                <td class="py-2.5 px-3 text-center font-bold text-gray-500 text-xs row-number">
                                    {{ $index + 1 }}
                                </td>

                                <!-- Kolom Kebutuhan (Searchable Autocomplete) -->
                                <td class="py-2.5 px-3">
                                    <input type="hidden" name="id_sab[]" class="sab-id-input" value="{{ $item->id_sab }}">
                                    <input type="hidden" name="rincian_kebutuhan[]" class="rincian-input" value="{{ $item->rincian_kebutuhan }}" required>
                                    
                                    <div class="relative autocomplete-container">
                                        <input type="text"
                                               value="{{ $item->rincian_kebutuhan }}"
                                               placeholder="Ketik nama kebutuhan (misal: Semen)..."
                                               autocomplete="off"
                                               class="sab-autocomplete-input w-full p-2.5 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-800 outline-none focus:border-[#114F72] focus:ring-2 focus:ring-[#114F72]/20 transition"
                                               oninput="handleSabSearch(this)"
                                               onfocus="handleSabSearch(this)"
                                               onkeydown="handleSabKeydown(event, this)">
                                        
                                        <div class="sab-suggestions-dropdown hidden absolute left-0 right-0 top-full mt-1 z-30 max-h-52 overflow-y-auto bg-white border border-gray-200 rounded-xl shadow-xl p-1 space-y-0.5"></div>
                                    </div>
                                </td>

                                <!-- Kolom Volume -->
                                <td class="py-2.5 px-3">
                                    <input type="number"
                                           step="0.001"
                                           min="0.001"
                                           name="volume[]"
                                           value="{{ (float)$item->volume }}"
                                           oninput="calculateSubtotal(this)"
                                           required
                                           placeholder="0"
                                           class="volume-input w-full p-2.5 bg-white border border-gray-200 rounded-xl text-xs text-center font-bold text-gray-800 outline-none focus:border-[#114F72] focus:ring-2 focus:ring-[#114F72]/20 transition">
                                </td>

                                <!-- Kolom Satuan (Read-Only Info Badge) -->
                                <td class="py-2.5 px-3 text-center">
                                    <input type="hidden" name="satuan[]" class="satuan-input" value="{{ $item->satuan }}" required>
                                    <span class="satuan-display inline-block w-full text-center px-2 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-600">
                                        {{ $item->satuan }}
                                    </span>
                                </td>

                                <!-- Kolom Harga Satuan (Read-Only Info Badge) -->
                                <td class="py-2.5 px-3 text-right">
                                    <input type="hidden" name="harga_satuan[]" class="harga-input" value="{{ (int)$item->harga_satuan }}" required>
                                    <span class="harga-display inline-block w-full text-right px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700">
                                        Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                    </span>
                                </td>

                                <!-- Kolom Subtotal -->
                                <td class="py-2.5 px-3 text-right font-extrabold text-[#114F72] text-xs subtotal-display">
                                    Rp {{ number_format($sub, 0, ',', '.') }}
                                </td>

                                <!-- Kolom Action Hapus (Ikon Trash Kecil Tanpa Header) -->
                                <td class="py-2.5 px-3 text-center">
                                    <button type="button" onclick="removeRow(this)" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer" title="Hapus baris ini">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="rab-row">
                                <td class="py-2.5 px-3 text-center font-bold text-gray-500 text-xs row-number">
                                    1
                                </td>
                                <td class="py-2.5 px-3">
                                    <input type="hidden" name="id_sab[]" class="sab-id-input">
                                    <input type="hidden" name="rincian_kebutuhan[]" class="rincian-input" required>
                                    
                                    <div class="relative autocomplete-container">
                                        <input type="text"
                                               placeholder="Ketik nama kebutuhan (misal: Semen)..."
                                               autocomplete="off"
                                               class="sab-autocomplete-input w-full p-2.5 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-800 outline-none focus:border-[#114F72] focus:ring-2 focus:ring-[#114F72]/20 transition"
                                               oninput="handleSabSearch(this)"
                                               onfocus="handleSabSearch(this)"
                                               onkeydown="handleSabKeydown(event, this)">
                                        
                                        <div class="sab-suggestions-dropdown hidden absolute left-0 right-0 top-full mt-1 z-30 max-h-52 overflow-y-auto bg-white border border-gray-200 rounded-xl shadow-xl p-1 space-y-0.5"></div>
                                    </div>
                                </td>

                                <td class="py-2.5 px-3">
                                    <input type="number"
                                           step="0.001"
                                           min="0.001"
                                           name="volume[]"
                                           oninput="calculateSubtotal(this)"
                                           required
                                           placeholder="0"
                                           class="volume-input w-full p-2.5 bg-white border border-gray-200 rounded-xl text-xs text-center font-bold text-gray-800 outline-none focus:border-[#114F72] focus:ring-2 focus:ring-[#114F72]/20 transition">
                                </td>

                                <td class="py-2.5 px-3 text-center">
                                    <input type="hidden" name="satuan[]" class="satuan-input" required>
                                    <span class="satuan-display inline-block w-full text-center px-2 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-600">
                                        -
                                    </span>
                                </td>

                                <td class="py-2.5 px-3 text-right">
                                    <input type="hidden" name="harga_satuan[]" class="harga-input" required>
                                    <span class="harga-display inline-block w-full text-right px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700">
                                        Rp 0
                                    </span>
                                </td>

                                <td class="py-2.5 px-3 text-right font-extrabold text-[#114F72] text-xs subtotal-display">
                                    Rp 0
                                </td>

                                <td class="py-2.5 px-3 text-center">
                                    <button type="button" onclick="removeRow(this)" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer" title="Hapus baris ini">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t bg-gray-50/50">
                        <tr>
                            <td colspan="5" class="py-3.5 px-3 font-bold text-gray-700 text-right">Total Anggaran RAB:</td>
                            <td class="py-3.5 px-3 font-extrabold text-[#114F72] text-right text-sm" id="grandTotalDisplay">
                                Rp {{ number_format($rab->total_biaya, 0, ',', '.') }}
                            </td>
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
// Data Master SAB Aktif untuk Autocomplete
const rawSabData = @json($sabList);
const masterSabList = Array.isArray(rawSabData) ? rawSabData.map(item => ({
    id: item.id_sab,
    nama: item.nama_kebutuhan,
    satuan: item.satuan,
    harga: parseFloat(item.harga_standar) || 0,
    harga_formatted: 'Rp ' + Number(item.harga_standar || 0).toLocaleString('id-ID')
})) : [];

document.addEventListener('DOMContentLoaded', function() {
    updateRowNumbers();
});

function updateRowNumbers() {
    document.querySelectorAll('#rabContainer .rab-row').forEach((row, index) => {
        const numTd = row.querySelector('.row-number');
        if (numTd) {
            numTd.textContent = index + 1;
        }
    });
}

// AUTOCOMPLETE SAB LOGIC
function handleSabSearch(inputEl) {
    const container = inputEl.closest('.autocomplete-container');
    const dropdown = container.querySelector('.sab-suggestions-dropdown');
    const row = inputEl.closest('.rab-row');
    const q = inputEl.value.trim().toLowerCase();

    // Sync rincian_kebutuhan hidden input
    row.querySelector('.rincian-input').value = inputEl.value;

    if (!q) {
        dropdown.innerHTML = '';
        dropdown.classList.add('hidden');
        resetSabData(row);
        return;
    }

    const filtered = masterSabList.filter(item => item.nama.toLowerCase().includes(q));

    if (filtered.length === 0) {
        dropdown.innerHTML = '<div class="p-2.5 text-xs text-gray-400 text-center font-medium">Tidak ada hasil dari Master SAB</div>';
        dropdown.classList.remove('hidden');
        return;
    }

    let html = '';
    filtered.forEach((item, index) => {
        const regex = new RegExp('(' + escapeRegExp(q) + ')', 'gi');
        const highlightedName = item.nama.replace(regex, '<mark class="bg-amber-200 text-gray-900 rounded-sm px-0.5">$1</mark>');

        html += `
            <div class="sab-option-item cursor-pointer px-3 py-2 hover:bg-[#114F72]/10 hover:text-[#114F72] rounded-lg flex items-center justify-between transition text-xs ${index === 0 ? 'bg-gray-50' : ''}"
                 data-id="${item.id}"
                 data-nama="${item.nama}"
                 data-satuan="${item.satuan}"
                 data-harga="${item.harga}"
                 data-harga-formatted="${item.harga_formatted}"
                 onclick="selectSabOption(this)">
                <span class="font-semibold text-gray-800">${highlightedName}</span>
                <span class="text-[11px] font-bold text-[#114F72] bg-[#114F72]/10 px-2 py-0.5 rounded-full">${item.harga_formatted} / ${item.satuan}</span>
            </div>
        `;
    });

    dropdown.innerHTML = html;
    dropdown.classList.remove('hidden');
}

function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function selectSabOption(optEl) {
    const row = optEl.closest('.rab-row');
    const id = optEl.dataset.id;
    const nama = optEl.dataset.nama;
    const satuan = optEl.dataset.satuan;
    const harga = parseFloat(optEl.dataset.harga) || 0;
    const hargaFormatted = optEl.dataset.hargaFormatted;

    row.querySelector('.sab-id-input').value = id;
    row.querySelector('.rincian-input').value = nama;
    row.querySelector('.sab-autocomplete-input').value = nama;

    row.querySelector('.satuan-input').value = satuan;
    row.querySelector('.satuan-display').textContent = satuan;

    row.querySelector('.harga-input').value = harga;
    row.querySelector('.harga-display').textContent = hargaFormatted;

    const dropdown = row.querySelector('.sab-suggestions-dropdown');
    dropdown.classList.add('hidden');

    calculateSubtotal(row.querySelector('.volume-input'));

    const volInput = row.querySelector('.volume-input');
    if (volInput) {
        volInput.focus();
    }
}

function resetSabData(row) {
    row.querySelector('.sab-id-input').value = '';
    row.querySelector('.satuan-input').value = '';
    row.querySelector('.satuan-display').textContent = '-';
    row.querySelector('.harga-input').value = '';
    row.querySelector('.harga-display').textContent = 'Rp 0';
    calculateSubtotal(row.querySelector('.volume-input'));
}

function handleSabKeydown(e, inputEl) {
    const container = inputEl.closest('.autocomplete-container');
    const dropdown = container.querySelector('.sab-suggestions-dropdown');
    const options = dropdown.querySelectorAll('.sab-option-item');

    if (dropdown.classList.contains('hidden') || options.length === 0) return;

    let activeIdx = Array.from(options).findIndex(opt => opt.classList.contains('bg-[#114F72]/10'));

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (activeIdx >= 0) options[activeIdx].classList.remove('bg-[#114F72]/10');
        activeIdx = (activeIdx + 1) % options.length;
        options[activeIdx].classList.add('bg-[#114F72]/10');
        options[activeIdx].scrollIntoView({ block: 'nearest' });
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (activeIdx >= 0) options[activeIdx].classList.remove('bg-[#114F72]/10');
        activeIdx = (activeIdx - 1 + options.length) % options.length;
        options[activeIdx].classList.add('bg-[#114F72]/10');
        options[activeIdx].scrollIntoView({ block: 'nearest' });
    } else if (e.key === 'Enter') {
        if (activeIdx >= 0 && options[activeIdx]) {
            e.preventDefault();
            selectSabOption(options[activeIdx]);
        }
    } else if (e.key === 'Escape') {
        dropdown.classList.add('hidden');
    }
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.autocomplete-container')) {
        document.querySelectorAll('.sab-suggestions-dropdown').forEach(dd => dd.classList.add('hidden'));
    }
});

function calculateSubtotal(el) {
    if (!el) return;
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

    newRow.querySelector('.sab-id-input').value = '';
    newRow.querySelector('.rincian-input').value = '';
    newRow.querySelector('.sab-autocomplete-input').value = '';
    newRow.querySelector('.volume-input').value = '';
    newRow.querySelector('.satuan-input').value = '';
    newRow.querySelector('.satuan-display').textContent = '-';
    newRow.querySelector('.harga-input').value = '';
    newRow.querySelector('.harga-display').textContent = 'Rp 0';
    newRow.querySelector('.subtotal-display').innerText = 'Rp 0';

    const dropdown = newRow.querySelector('.sab-suggestions-dropdown');
    if (dropdown) {
        dropdown.innerHTML = '';
        dropdown.classList.add('hidden');
    }

    container.appendChild(newRow);
    updateRowNumbers();
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.rab-row');
    if (rows.length > 1) {
        btn.closest('.rab-row').remove();
        updateRowNumbers();
        updateGrandTotal();
    } else {
        alert('Minimal harus ada 1 baris rincian kebutuhan RAB.');
    }
}
</script>
@endsection
