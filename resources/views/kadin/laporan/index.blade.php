@extends('layouts.app')

@section('title', 'Daftar Laporan (Monitoring) - SI-SARPRAS')
@section('breadcrumb', 'Daftar Laporan')

@section('content')
<div class="space-y-6">

    {{-- HEADER TITLE & ACTION BUTTON --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Daftar Laporan</h1>
            <p class="text-xs font-medium text-gray-500 mt-1">
                Memantau seluruh laporan kerusakan sarana dan prasarana dari 9 pasar (Read-Only)
            </p>
        </div>

        <div class="flex items-center gap-2">
            {{-- TOMBOL CETAK LAPORAN (OPEN MODAL) --}}
            <button type="button" 
                    onclick="openModalCetak()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#114F72] hover:bg-[#0d3f5c] text-white text-xs font-semibold rounded-xl shadow-sm transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>

    {{-- DATA TABLE LAPORAN WITH INLINE SEARCH & FILTER POPOVER IN HEADER --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        
        {{-- CARD HEADER --}}
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h2 class="text-base font-bold text-gray-800">
                    Laporan Masuk
                </h2>
            </div>

            {{-- SEARCH & FILTER POPOVER FORM --}}
            <form method="GET" action="{{ route('kadin.laporan.index') }}" class="flex items-center gap-2">
                
                {{-- SEARCH PILL --}}
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari"
                           onkeydown="if(event.key === 'Enter'){ this.form.submit(); }"
                           class="w-[180px] sm:w-[220px] rounded-full border border-gray-300 pl-9 pr-3 py-1.5 text-xs text-gray-700 focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20 bg-white">
                </div>

                {{-- FILTER BUTTON & POPOVER DROPDOWN --}}
                <div class="relative">
                    <button type="button"
                            id="filterToggle"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 hover:text-[#114F72] transition-colors relative"
                            aria-label="Filter">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V20a1 1 0 01-1.447.894l-2-1A1 1 0 0110 19v-5.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        @if(request()->filled('pasar') || request()->filled('status'))
                            <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                        @endif
                    </button>

                    <div id="filterPopover" class="absolute right-0 top-10 z-20 hidden w-56 rounded-xl border border-gray-200 bg-white p-4 shadow-xl space-y-3">
                        <div>
                            <label for="pasar" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                Pasar
                            </label>
                            <select id="pasar"
                                    name="pasar"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-xs text-gray-700 focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20"
                                    onchange="this.form.submit()">
                                <option value="">Semua Pasar</option>
                                @foreach($pasarList as $pasar)
                                    <option value="{{ $pasar->id_pasar }}" {{ request('pasar') == $pasar->id_pasar ? 'selected' : '' }}>
                                        {{ $pasar->nama_pasar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="status" class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                Status
                            </label>
                            <select id="status"
                                    name="status"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-xs text-gray-700 focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20"
                                    onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                @foreach($statusList as $statusItem)
                                    <option value="{{ $statusItem }}" {{ request('status') == $statusItem ? 'selected' : '' }}>
                                        {{ $statusItem }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if(request()->hasAny(['search', 'pasar', 'status']))
                            <div class="pt-2 border-t border-gray-100 flex justify-end">
                                <a href="{{ route('kadin.laporan.index') }}" class="text-xs text-red-600 hover:text-red-700 font-semibold">
                                    Reset Filter
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

            </form>
        </div>

        @if($laporanList->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                No
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Pasar & Lokasi
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Fasilitas
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Kategori Kerusakan
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Tanggal   
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($laporanList as $index => $l)
                            @php
                                $statusColors = [
                                    'Menunggu' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'Diproses' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'Disetujui' => 'bg-teal-100 text-teal-700 border-teal-200',
                                    'Selesai' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'Dikembalikan' => 'bg-red-100 text-red-700 border-red-200',
                                    'Ditolak' => 'bg-gray-100 text-gray-700 border-gray-300',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">

                                <!-- No -->
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $laporanList->firstItem() + $index }}
                                </td>

                                <!-- Pasar & Lokasi -->
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $l->lokasi->pasar->nama_pasar ?? '-' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $l->lokasi->nama_lokasi ?? '-' }}
                                    </p>
                                </td>

                                <!-- Fasilitas -->
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                    {{ $l->nama_fasilitas_display }}
                                </td>

                                <!-- Kategori Kerusakan -->
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $l->kategori_laporan_display }}
                                </td>

                                <!-- Tanggal -->
                                <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($l->tanggal_lapor)->format('d M Y') }}
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium border {{ $statusColors[$l->status_laporan] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                        {{ $l->status_laporan }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <a href="{{ route('laporan.show', $l->id_laporan) }}" 
                                       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-[#114F72] bg-[#114F72]/5 hover:bg-[#114F72]/10 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Detail
                                    </a>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <span class="text-sm text-gray-500">
                    Menampilkan {{ $laporanList->firstItem() ?? 0 }}–{{ $laporanList->lastItem() ?? 0 }} dari {{ $laporanList->total() }} laporan
                </span>
                <div>
                    {{ $laporanList->links() }}
                </div>
            </div>
        @else
            <div class="py-12 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-semibold text-gray-700">Tidak ada laporan ditemukan.</p>
                <p class="text-xs text-gray-400 mt-1">Coba sesuaikan kata kunci pencarian, pilihan pasar, atau status.</p>
            </div>
        @endif
    </div>

</div>

{{-- MODAL CETAK LAPORAN --}}
<div id="modalCetakLaporan" 
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 transition-opacity duration-200">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden border border-gray-100 transform transition-all">
        
        {{-- MODAL HEADER --}}
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#114F72]/10 flex items-center justify-center text-[#114F72]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Cetak Laporan Kerusakan</h3>
                    <p class="text-[11px] text-gray-500">Pilih cakupan data yang ingin dicetak ke PDF</p>
                </div>
            </div>
            <button type="button" 
                    onclick="closeModalCetak()" 
                    class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- FORM CETAK --}}
        <form id="formCetakLaporan" action="{{ route('kadin.laporan.print') }}" method="GET" target="_blank" onsubmit="setTimeout(closeModalCetak, 500)">
            
            {{-- HIDDEN INPUTS FOR CURRENT ACTIVE FILTER --}}
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="pasar" value="{{ request('pasar') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">

            <div class="p-6 space-y-4">
                
                {{-- PILIHAN TIPE CETAK --}}
                <div class="space-y-3">
                    
                    {{-- OPSI 1: HASIL FILTER SAAT INI --}}
                    <label class="flex items-start gap-3 p-3.5 rounded-xl border border-gray-200 hover:border-[#114F72] hover:bg-[#114F72]/5 transition-all cursor-pointer">
                        <input type="radio" 
                               name="tipe_cetak" 
                               value="filter" 
                               checked 
                               onchange="togglePeriodeContainer()" 
                               class="mt-0.5 text-[#114F72] focus:ring-[#114F72]">
                        <div>
                            <span class="text-xs font-bold text-gray-900 block">Hasil Filter Saat Ini</span>
                            <span class="text-[11px] text-gray-500 mt-0.5 block">
                                Mencetak laporan sesuai pencarian, pasar, dan status yang aktif di halaman.
                            </span>
                        </div>
                    </label>

                    {{-- OPSI 2: SEMUA LAPORAN --}}
                    <label class="flex items-start gap-3 p-3.5 rounded-xl border border-gray-200 hover:border-[#114F72] hover:bg-[#114F72]/5 transition-all cursor-pointer">
                        <input type="radio" 
                               name="tipe_cetak" 
                               value="semua" 
                               onchange="togglePeriodeContainer()" 
                               class="mt-0.5 text-[#114F72] focus:ring-[#114F72]">
                        <div>
                            <span class="text-xs font-bold text-gray-900 block">Semua Laporan</span>
                            <span class="text-[11px] text-gray-500 mt-0.5 block">
                                Mencetak seluruh data laporan dari 9 pasar tanpa batasan kriteria.
                            </span>
                        </div>
                    </label>

                    {{-- OPSI 3: BERDASARKAN PERIODE --}}
                    <label class="flex items-start gap-3 p-3.5 rounded-xl border border-gray-200 hover:border-[#114F72] hover:bg-[#114F72]/5 transition-all cursor-pointer">
                        <input type="radio" 
                               name="tipe_cetak" 
                               value="periode" 
                               onchange="togglePeriodeContainer()" 
                               class="mt-0.5 text-[#114F72] focus:ring-[#114F72]">
                        <div class="w-full">
                            <span class="text-xs font-bold text-gray-900 block">Berdasarkan Periode</span>
                            <span class="text-[11px] text-gray-500 mt-0.5 block">
                                Mencetak laporan berdasarkan bulan dan tahun tertentu.
                            </span>
                        </div>
                    </label>

                </div>

                {{-- CONTAINER PERIODE (BULAN & TAHUN) --}}
                <div id="containerPeriode" class="hidden p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Bulan</label>
                            <select name="bulan" onchange="updateCetakCount()" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs text-gray-800 focus:ring-2 focus:ring-[#114F72]">
                                <option value="">Semua Bulan (Tahunan)</option>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tahun</label>
                            @php
                                $currentYear = (int) date('Y');
                            @endphp
                            <select name="tahun" onchange="updateCetakCount()" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs text-gray-800 focus:ring-2 focus:ring-[#114F72]">
                                @for($y = $currentYear; $y >= $currentYear - 3; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                {{-- DYNAMIC DATA COUNTER BOX --}}
                <div class="p-3.5 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="textJumlahData" class="text-xs font-bold text-blue-900">
                            Memuat jumlah data...
                        </span>
                    </div>
                </div>

            </div>

            {{-- MODAL FOOTER --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-2.5">
                <button type="button" 
                        onclick="closeModalCetak()" 
                        class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-[#114F72] to-[#16A394] hover:opacity-90 text-white text-xs font-semibold rounded-xl shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>Pratinjau Cetak</span>
                </button>
            </div>

        </form>
    </div>
</div>

{{-- MODAL & POPOVER SCRIPTS --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('filterToggle');
        const popover = document.getElementById('filterPopover');

        if (toggle && popover) {
            toggle.addEventListener('click', function (event) {
                event.stopPropagation();
                popover.classList.toggle('hidden');
            });

            document.addEventListener('click', function () {
                popover.classList.add('hidden');
            });

            popover.addEventListener('click', function (event) {
                event.stopPropagation();
            });
        }
    });

    function openModalCetak() {
        const modal = document.getElementById('modalCetakLaporan');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        updateCetakCount();
    }

    function closeModalCetak() {
        const modal = document.getElementById('modalCetakLaporan');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function togglePeriodeContainer() {
        const tipe = document.querySelector('input[name="tipe_cetak"]:checked').value;
        const container = document.getElementById('containerPeriode');
        if (tipe === 'periode') {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
        updateCetakCount();
    }

    function updateCetakCount() {
        const form = document.getElementById('formCetakLaporan');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        fetch(`/kadin/laporan/count?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('textJumlahData').textContent = `${data.count} Data Laporan akan dicetak.`;
                }
            })
            .catch(() => {
                document.getElementById('textJumlahData').textContent = `Memuat jumlah data...`;
            });
    }
</script>

@endsection
