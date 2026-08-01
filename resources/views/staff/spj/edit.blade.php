@extends('layouts.app')

@section('title', 'Edit Dokumen SPJ - SI-SARPRAS')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-gray-600 hover:text-[#114F72] transition">Dashboard</a>
    <span class="mx-2 text-gray-400">/</span>
    <a href="{{ route('staff.spj.index') }}" class="text-gray-600 hover:text-[#114F72] transition">Dokumen SPJ</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-600">Edit Dokumen SPJ</span>
@endsection

@section('content')

<div class="max-w-5xl mx-auto pb-12">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            Edit Dokumen SPJ
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            Perbarui informasi dokumen dan daftar laporan perbaikan yang dihubungkan ke SPJ ini.
        </p>
    </div>

    @if(session('error'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <form
        action="{{ route('staff.spj.update', $spj->id_spj) }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')

        {{-- ========================= --}}
        {{-- CARD 1: INFORMASI SPJ --}}
        {{-- ========================= --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6">

            <div class="bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 border-b border-gray-200 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-[#114F72]/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-7 7l8-8a2.828 2.828 0 114 4l-8 8H6v-4z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">
                            Informasi SPJ
                        </h2>
                        <p class="text-sm text-gray-500">
                            Perbarui rincian identitas dan periode kegiatan dokumen.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6">

                {{-- Nama Pekerjaan --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Pekerjaan
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        name="nama_pekerjaan"
                        value="{{ old('nama_pekerjaan', $spj->nama_pekerjaan) }}"
                        placeholder="Masukkan nama pekerjaan"
                        class="w-full max-w-xl rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 shadow-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20">

                    @error('nama_pekerjaan')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Periode --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-w-xl">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Periode Mulai
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="date"
                            name="periode_mulai"
                            value="{{ old('periode_mulai', \Carbon\Carbon::parse($spj->periode_mulai)->format('Y-m-d')) }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 shadow-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20">

                        @error('periode_mulai')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Periode Selesai
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="date"
                            name="periode_selesai"
                            value="{{ old('periode_selesai', \Carbon\Carbon::parse($spj->periode_selesai)->format('Y-m-d')) }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 shadow-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20">

                        @error('periode_selesai')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Keterangan --}}
                <div class="mt-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Keterangan
                    </label>

                    <textarea
                        rows="4"
                        name="keterangan"
                        placeholder="Masukkan keterangan (opsional)"
                        class="w-full max-w-xl rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 shadow-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20">{{ old('keterangan', $spj->keterangan) }}</textarea>

                    @error('keterangan')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

        </div>

        {{-- ========================= --}}
        {{-- CARD 2: DOKUMEN SPJ --}}
        {{-- ========================= --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6">

            <div class="bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 border-b border-gray-200 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-[#114F72]/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4a1 1 0 011-1h8l4 4v13a2 2 0 01-2 2H9a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">
                            Dokumen SPJ
                        </h2>
                        <p class="text-sm text-gray-500">
                            Ganti berkas SPJ PDF jika ingin memperbarui file dokumen.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6">

                {{-- Berkas Saat Ini --}}
                @if($spj->file_spj)
                    <div class="mb-5 p-4 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-between max-w-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V7.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 1H7a2 2 0 00-2 2v16a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-xs text-gray-500 font-medium">Berkas Saat Ini:</p>
                                <p class="text-sm font-semibold text-gray-800 truncate" title="{{ basename($spj->file_spj) }}">
                                    {{ basename($spj->file_spj) }}
                                </p>
                            </div>
                        </div>

                        <a href="{{ asset('storage/' . $spj->file_spj) }}"
                           target="_blank"
                           class="inline-flex items-center gap-1 text-xs font-semibold text-[#114F72] hover:underline flex-shrink-0">
                            <span>Lihat Berkas</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                @endif

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Unggah File Baru (Opsional)
                </label>

                <input
                    type="file"
                    name="file_spj"
                    accept=".pdf"
                    class="block w-full max-w-xl rounded-lg border border-gray-300 text-sm text-gray-700 p-1
                           file:mr-4
                           file:rounded-md
                           file:border-0
                           file:bg-[#114F72]
                           file:px-4
                           file:py-2
                           file:text-white
                           file:font-medium
                           hover:file:bg-[#0d3f5c]">

                <p class="mt-2 text-sm text-gray-500">
                    Biarkan kosong jika tidak ingin mengganti file SPJ. Format PDF (Maks. 5 MB).
                </p>

                @error('file_spj')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>

        {{-- ========================= --}}
        {{-- CARD 3: DAFTAR LAPORAN --}}
        {{-- ========================= --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6">

            <div class="bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 border-b border-gray-200 px-6 py-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-[#114F72]/10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">
                                Pilih Laporan Perbaikan
                            </h2>
                            <p class="text-sm text-gray-500">
                                Centang laporan yang ingin dihubungkan ke dokumen SPJ ini.
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        <span id="selectedCount" class="text-sm font-semibold text-[#114F72] bg-[#114F72]/10 px-3 py-1.5 rounded-full">
                            0 laporan dipilih
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6">

                {{-- Toolbar Search + Filter --}}
                <div class="flex flex-wrap items-center justify-between gap-3 mb-5">

                    {{-- Search --}}
                    <div class="relative w-full sm:w-80">
                        <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>

                        <input
                            type="text"
                            id="searchLaporan"
                            placeholder="Cari nomor laporan, pasar, atau fasilitas..."
                            class="w-full rounded-full border border-gray-300 pl-9 pr-4 py-2 text-sm text-gray-700 focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20">
                    </div>

                    {{-- Filter Pasar --}}
                    <div class="relative">
                        <button type="button"
                                id="filterTogglePasar"
                                onclick="toggleFilterPasar()"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 hover:text-[#114F72] transition-colors"
                                aria-label="Filter Pasar"
                                title="Filter berdasarkan pasar">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V20a1 1 0 01-1.447.894l-2-1A1 1 0 0110 19v-5.586L3.293 6.707A1 1 0 013 6V4z"/>
                            </svg>
                        </button>

                        <div id="filterPopoverPasar" class="absolute right-0 top-11 z-10 hidden w-56 rounded-lg border border-gray-200 bg-white p-3 shadow-lg">
                            <div>
                                <label for="filterPasar" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Filter Pasar
                                </label>
                                <select id="filterPasar"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20">
                                    <option value="">Semua Pasar</option>
                                    @foreach($laporanList->pluck('lokasi.pasar.nama_pasar')->filter()->unique()->sort() as $pasar)
                                        <option value="{{ strtolower($pasar) }}">
                                            {{ $pasar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Tabel --}}
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-center" style="width: 50px;">
                                    <input
                                        type="checkbox"
                                        id="selectAll"
                                        class="rounded border-gray-300 text-[#114F72] focus:ring-[#114F72]">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Nomor Laporan
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Pasar
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Fasilitas
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Tanggal Lapor
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            @php
                                $oldLaporan = old('laporan', $spj->laporan->pluck('id_laporan')->toArray());
                            @endphp

                            @forelse($laporanList as $laporan)
                                <tr
                                    class="hover:bg-gray-50 laporan-row"
                                    data-pasar="{{ strtolower($laporan->lokasi->pasar->nama_pasar ?? '') }}"
                                    data-search="{{ strtolower(
                                        ($laporan->nomor_laporan ?? '') . ' ' .
                                        ($laporan->lokasi->pasar->nama_pasar ?? '') . ' ' .
                                        ($laporan->fasilitas->nama_fasilitas ?? '')
                                    ) }}">

                                    {{-- Checkbox --}}
                                    <td class="px-4 py-3.5 text-center">
                                        <input
                                            type="checkbox"
                                            name="laporan[]"
                                            value="{{ $laporan->id_laporan }}"
                                            class="laporan-checkbox rounded border-gray-300 text-[#114F72] focus:ring-[#114F72]"
                                            {{ in_array($laporan->id_laporan, $oldLaporan) ? 'checked' : '' }}>
                                    </td>

                                    {{-- Nomor Laporan --}}
                                    <td class="px-4 py-3.5 text-sm font-semibold text-gray-800">
                                        {{ $laporan->nomor_laporan }}
                                    </td>

                                    {{-- Pasar --}}
                                    <td class="px-4 py-3.5 text-sm text-gray-700">
                                        {{ $laporan->lokasi->pasar->nama_pasar ?? '-' }}
                                    </td>

                                    {{-- Fasilitas --}}
                                    <td class="px-4 py-3.5 text-sm text-gray-700">
                                        {{ $laporan->fasilitas->nama_fasilitas ?? '-' }}
                                    </td>

                                    {{-- Tanggal Lapor --}}
                                    <td class="px-4 py-3.5 text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->translatedFormat('d M Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                                            </svg>
                                            <h3 class="text-base font-semibold text-gray-700">
                                                Tidak Ada Laporan
                                            </h3>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Belum ada laporan berstatus 'Selesai' yang tersedia.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            @error('laporan')
                                <tr>
                                    <td colspan="5">
                                        <p class="px-4 py-3 text-sm text-red-600">
                                            {{ $message }}
                                        </p>
                                    </td>
                                </tr>
                            @enderror
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

        {{-- Action Buttons --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('staff.spj.index') }}"
               class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-100 transition">
                Batal
            </a>

            <button
                type="submit"
                class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#114F72] to-[#16A394] text-white font-semibold shadow-md hover:opacity-90 transition">
                Perbarui Dokumen SPJ
            </button>
        </div>

    </form>

</div>

<script>
function toggleFilterPasar() {
    const popover = document.getElementById('filterPopoverPasar');
    if (popover) {
        popover.classList.toggle('hidden');
    }
}

document.addEventListener('click', function(e) {
    const popover = document.getElementById('filterPopoverPasar');
    const toggle = document.getElementById('filterTogglePasar');
    if (popover && toggle && !popover.contains(e.target) && !toggle.contains(e.target)) {
        popover.classList.add('hidden');
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchLaporan');
    const filterPasar = document.getElementById('filterPasar');
    const selectAll = document.getElementById('selectAll');

    const rows = document.querySelectorAll('.laporan-row');
    const checkboxes = document.querySelectorAll('.laporan-checkbox');
    const counter = document.getElementById('selectedCount');

    // ==========================
    // Counter Laporan Dipilih
    // ==========================
    function updateCounter() {
        let total = document.querySelectorAll('.laporan-checkbox:checked').length;
        if (total === 0) {
            counter.innerHTML = 'Belum ada laporan dipilih';
        } else if (total === 1) {
            counter.innerHTML = '1 laporan dipilih';
        } else {
            counter.innerHTML = total + ' laporan dipilih';
        }
    }

    // ==========================
    // Search + Filter
    // ==========================
    function filterRows() {
        const keyword = searchInput.value.toLowerCase();
        const pasar = filterPasar.value;

        rows.forEach(function(row){
            const searchText = row.dataset.search;
            const pasarText = row.dataset.pasar;

            const matchKeyword = searchText.includes(keyword);
            const matchPasar = pasar === '' || pasarText === pasar;

            row.style.display = (matchKeyword && matchPasar) ? '' : 'none';
        });

        updateSelectAll();
    }

    // ==========================
    // Update Select All
    // ==========================
    function updateSelectAll(){
        let visibleCheckboxes = [];

        rows.forEach(function(row){
            if(row.style.display !== 'none'){
                visibleCheckboxes.push(row.querySelector('.laporan-checkbox'));
            }
        });

        if(visibleCheckboxes.length === 0){
            selectAll.checked = false;
            selectAll.indeterminate = false;
            return;
        }

        const checked = visibleCheckboxes.filter(cb => cb.checked).length;
        selectAll.checked = checked === visibleCheckboxes.length;
        selectAll.indeterminate = checked > 0 && checked < visibleCheckboxes.length;
    }

    // ==========================
    // Pilih Semua
    // ==========================
    selectAll.addEventListener('change', function(){
        rows.forEach(function(row){
            if(row.style.display === 'none') return;
            row.querySelector('.laporan-checkbox').checked = selectAll.checked;
        });

        updateCounter();
    });

    // ==========================
    // Event Checkbox
    // ==========================
    checkboxes.forEach(function(box){
        box.addEventListener('change', function(){
            updateCounter();
            updateSelectAll();
        });
    });

    // ==========================
    // Event Search & Filter
    // ==========================
    if (searchInput) searchInput.addEventListener('keyup', filterRows);
    if (filterPasar) filterPasar.addEventListener('change', filterRows);

    // ==========================
    // Initial State
    // ==========================
    updateCounter();
    updateSelectAll();
});
</script>
@endsection
