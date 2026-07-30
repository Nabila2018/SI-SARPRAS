@extends('layouts.app')

@section('title', 'Tambah Dokumen SPJ - SI-SARPRAS')

@section('breadcrumb')
    <a href="{{ route('staff.spj.index') }}"
       class="text-gray-600 hover:text-[#114F72]">
        Dokumen SPJ
    </a>

    <span class="mx-2 text-gray-400">/</span>

    <span class="text-gray-600">
        Tambah Dokumen SPJ
    </span>
@endsection

@section('content')

<div class="pb-12">

    {{-- Header --}}
    <div class="mb-6">

        <h1 class="text-3xl font-bold text-gray-800">
            Tambah Dokumen SPJ
        </h1>

        <p class="mt-2 text-sm text-gray-500">
            Buat dokumen Surat Pertanggungjawaban (SPJ).
        </p>

    </div>

    @if(session('error'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <form
        action="{{ route('staff.spj.store') }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf

        {{-- ========================= --}}
        {{-- INFORMASI SPJ --}}
        {{-- ========================= --}}

        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6">

            <div class="bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 border-b border-gray-200 px-6 py-5">

                <div class="flex items-center gap-3">

                    <div class="w-11 h-11 rounded-xl bg-[#114F72]/10 flex items-center justify-center">

                        <svg class="w-6 h-6 text-[#114F72]"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>

                        </svg>

                    </div>

                    <div>

                        <h2 class="text-lg font-bold text-gray-800">
                            Informasi SPJ
                        </h2>

                        <p class="text-sm text-gray-500">
                            Isi informasi dokumen SPJ.
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
                        value="{{ old('nama_pekerjaan') }}"
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
                            value="{{ old('periode_mulai') }}"
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
                            value="{{ old('periode_selesai') }}"
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
                        class="w-full max-w-xl rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 shadow-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20">{{ old('keterangan') }}</textarea>

                    @error('keterangan')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

        </div>
                {{-- ========================= --}}
        {{-- DOKUMEN SPJ --}}
        {{-- ========================= --}}

        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6">

            <div class="bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 border-b border-gray-200 px-6 py-5">

                <div class="flex items-center gap-3">

                    <div class="w-11 h-11 rounded-xl bg-[#114F72]/10 flex items-center justify-center">

                        <svg class="w-6 h-6 text-[#114F72]"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M7 16V4a1 1 0 011-1h8l4 4v13a2 2 0 01-2 2H9a2 2 0 01-2-2z"/>

                        </svg>

                    </div>

                    <div>

                        <h2 class="text-lg font-bold text-gray-800">
                            Dokumen SPJ
                        </h2>

                        <p class="text-sm text-gray-500">
                            Unggah dokumen SPJ dalam format PDF.
                        </p>

                    </div>

                </div>

            </div>

            <div class="p-6">

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    File SPJ
                    <span class="text-red-500">*</span>
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
                    Format PDF dengan ukuran maksimal 5 MB.
                </p>

                @error('file_spj')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>


        {{-- ========================= --}}
        {{-- DAFTAR LAPORAN --}}
        {{-- ========================= --}}

        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6">

            <div class="bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 border-b border-gray-200 px-6 py-5">

                <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">

                    <div>

                        <h2 class="text-lg font-bold text-gray-800">
                            Daftar Laporan
                        </h2>

                        <p class="text-sm text-gray-500">
                            Pilih laporan yang akan dimasukkan ke dalam dokumen SPJ.
                        </p>

                    </div>

                    <span id="selectedCount"
                          class="text-sm font-semibold text-[#114F72]">

                        Belum ada laporan dipilih

                    </span>

                </div>

            </div>

            <div class="p-6">

                {{-- Toolbar --}}
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

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-4 py-3 text-center">

                                    <input
                                        type="checkbox"
                                        id="selectAll"
                                        class="rounded border-gray-300 text-[#114F72] focus:ring-[#114F72]">

                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">
                                    Nomor
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">
                                    Pasar
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">
                                    Fasilitas
                                </th>

                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">
                                    Tanggal Lapor
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">

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
                                <td class="px-4 py-4 text-center">

                                    <input
                                        type="checkbox"
                                        name="laporan[]"
                                        value="{{ $laporan->id_laporan }}"
                                        class="laporan-checkbox rounded border-gray-300 text-[#114F72] focus:ring-[#114F72]"
                                        {{ in_array($laporan->id_laporan, old('laporan', [])) ? 'checked' : '' }}>

                                </td>

                                {{-- Nomor Laporan --}}
                                <td class="px-4 py-4">

                                    <span class="font-medium text-gray-800">

                                        {{ $laporan->nomor_laporan }}

                                    </span>

                                </td>

                                {{-- Pasar --}}
                                <td class="px-4 py-4">

                                    {{ $laporan->lokasi->pasar->nama_pasar ?? '-' }}

                                </td>

                                {{-- Fasilitas --}}
                                <td class="px-4 py-4">

                                    {{ $laporan->fasilitas->nama_fasilitas ?? '-' }}

                                </td>

                                {{-- Tanggal --}}
                                <td class="px-4 py-4">

                                    {{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d M Y') }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="py-12 text-center">

                                    <div class="flex flex-col items-center">

                                        <svg class="w-12 h-12 text-gray-300 mb-3"
                                             fill="none"
                                             stroke="currentColor"
                                             viewBox="0 0 24 24">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>

                                        </svg>

                                        <h3 class="text-lg font-semibold text-gray-700">

                                            Tidak Ada Laporan

                                        </h3>

                                        <p class="text-sm text-gray-500 mt-2">

                                            Belum ada laporan yang dapat dipilih.

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

        {{-- Action Button --}}
        <div class="flex justify-end gap-3">

            <a href="{{ route('staff.spj.index') }}"
               class="px-5 py-2.5 rounded-xl border border-gray-300
                      text-gray-700 font-medium hover:bg-gray-100 transition">

                Batal

            </a>

            <button
                type="submit"
                class="px-6 py-2.5 rounded-xl
                       bg-gradient-to-r from-[#114F72] to-[#16A394]
                       text-white font-semibold shadow-md
                       hover:opacity-90 transition">

                Simpan Dokumen SPJ

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

            row.style.display = (matchKeyword && matchPasar)
                ? ''
                : 'none';

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

                visibleCheckboxes.push(
                    row.querySelector('.laporan-checkbox')
                );

            }

        });

        if(visibleCheckboxes.length === 0){

            selectAll.checked = false;
            selectAll.indeterminate = false;
            return;

        }

        const checked = visibleCheckboxes.filter(cb => cb.checked).length;

        selectAll.checked = checked === visibleCheckboxes.length;

        selectAll.indeterminate =
            checked > 0 &&
            checked < visibleCheckboxes.length;

    }

    // ==========================
    // Pilih Semua
    // ==========================

    selectAll.addEventListener('change', function(){

        rows.forEach(function(row){

            if(row.style.display === 'none') return;

            row.querySelector('.laporan-checkbox').checked =
                selectAll.checked;

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
    // Event Search
    // ==========================

    searchInput.addEventListener('keyup', filterRows);

    // ==========================
    // Event Filter Pasar
    // ==========================

    filterPasar.addEventListener('change', filterRows);

    // ==========================
    // Initial
    // ==========================

    updateCounter();
    updateSelectAll();

});

</script>

@endsection