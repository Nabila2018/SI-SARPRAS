@extends('layouts.app')

@section('title', 'Daftar Laporan Masuk - SI-SARPRAS')
@section('breadcrumb', 'Daftar Laporan Masuk')

@section('content')
<div class="max-w-6xl mx-auto pb-12">

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Laporan Masuk</h1>
        <p class="text-gray-500 mt-1">
            Laporan kerusakan yang dikirimkan oleh Petugas UPTD.
        </p>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>

            <p class="text-sm text-emerald-700">
                {{ session('success') }}
            </p>
        </div>
    @endif

    <!-- Tabel -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">

        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-[#114F72]"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>

                <h2 class="text-lg font-semibold text-gray-800">
                    Laporan Masuk
                </h2>
            </div>

            <form method="GET" action="{{ route('staff.laporan.index') }}" class="flex items-center gap-2">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari"
                           onkeydown="if(event.key === 'Enter'){ this.form.submit(); }"
                           class="w-[220px] rounded-full border border-gray-300 pl-9 pr-3 py-2 text-sm text-gray-700 focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20">
                </div>

                <div class="relative">
                    <button type="button"
                            id="filterToggle"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 hover:text-[#114F72] transition-colors"
                            aria-label="Filter">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V20a1 1 0 01-1.447.894l-2-1A1 1 0 0110 19v-5.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                    </button>

                    <div id="filterPopover" class="absolute right-0 top-11 z-10 hidden w-52 rounded-lg border border-gray-200 bg-white p-3 shadow-lg">
                        <div class="space-y-3">
                            <div>
                                <label for="pasar" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Pasar
                                </label>
                                <select id="pasar"
                                        name="pasar"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20"
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
                                <label for="status" class="mb-1 block text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Status
                                </label>
                                <select id="status"
                                        name="status"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20"
                                        onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    @foreach($statusList as $statusOption)
                                        <option value="{{ $statusOption }}" {{ request('status') == $statusOption ? 'selected' : '' }}>
                                            {{ $statusOption }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const toggle = document.getElementById('filterToggle');
                const popover = document.getElementById('filterPopover');

                if (!toggle || !popover) return;

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
            });
        </script>

        @if($laporan->count() > 0)

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left">

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
                        @foreach($laporan as $index => $l)
                            <tr class="hover:bg-gray-50/50 transition-colors">

                                <!-- No -->
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $laporan->firstItem() + $index }}
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
                                    @php
                                        $statusColors = [
                                            'Menunggu' => 'bg-amber-100 text-amber-700 border-amber-200',
                                            'Diproses' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            'Selesai' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                            'Dikembalikan' => 'bg-red-100 text-red-700 border-red-200',
                                            'Ditolak' => 'bg-gray-100 text-gray-700 border-gray-300',
                                        ];
                                    @endphp

                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium border {{ $statusColors[$l->status_laporan] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                        {{ $l->status_laporan }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('laporan.show', $l->id_laporan) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-[#114F72] bg-[#114F72]/5 hover:bg-[#114F72]/10 rounded-lg transition-colors">

                                        <svg class="w-4 h-4"
                                             fill="none"
                                             stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>

                                        Detail
                                    </a>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            <!-- Pagination -->
            @php
                $paginator = $laporan
                    ->onEachSide(1)
                    ->appends(request()->query());
            @endphp

            <div class="flex flex-col gap-4 px-6 py-4 border-t border-gray-100 sm:flex-row sm:items-center sm:justify-between">

                <div class="text-sm text-gray-500">
                    Menampilkan
                    {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }}
                    dari {{ $paginator->total() }} laporan
                </div>

                @if($paginator->hasPages())
                    <nav class="flex flex-wrap items-center justify-end gap-2"
                         aria-label="Pagination">

                        <!-- Previous -->
                        @if($paginator->onFirstPage())
                            <span class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                                <svg class="w-4 h-4"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M15 19l-7-7 7-7"/>
                                </svg>
                                Sebelumnya
                            </span>
                        @else
                            <a href="{{ $paginator->previousPageUrl() }}"
                               class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-[#114F72] bg-white border border-gray-200 rounded-lg hover:bg-[#114F72]/5 hover:border-[#114F72]/20 transition-colors">
                                <svg class="w-4 h-4"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M15 19l-7-7 7-7"/>
                                </svg>
                                Sebelumnya
                            </a>
                        @endif

                        @php
                            $window = 2;
                            $start = max(1, $paginator->currentPage() - $window);
                            $end = min(
                                $paginator->lastPage(),
                                $paginator->currentPage() + $window
                            );
                        @endphp

                        <!-- First Page -->
                        @if($start > 1)
                            <a href="{{ $paginator->url(1) }}"
                               class="inline-flex items-center justify-center min-w-10 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                1
                            </a>

                            @if($start > 2)
                                <span class="px-2 text-sm text-gray-400">
                                    ...
                                </span>
                            @endif
                        @endif

                        <!-- Page Numbers -->
                        @for($page = $start; $page <= $end; $page++)
                            @if($page == $paginator->currentPage())
                                <span class="inline-flex items-center justify-center min-w-10 px-3 py-2 text-sm font-semibold text-white bg-gradient-to-r from-[#114F72] to-[#16A394] border border-transparent rounded-lg shadow-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $paginator->url($page) }}"
                                   class="inline-flex items-center justify-center min-w-10 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor

                        <!-- Last Page -->
                        @if($end < $paginator->lastPage())

                            @if($end < $paginator->lastPage() - 1)
                                <span class="px-2 text-sm text-gray-400">
                                    ...
                                </span>
                            @endif

                            <a href="{{ $paginator->url($paginator->lastPage()) }}"
                               class="inline-flex items-center justify-center min-w-10 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                {{ $paginator->lastPage() }}
                            </a>
                        @endif

                        <!-- Next -->
                        @if($paginator->hasMorePages())
                            <a href="{{ $paginator->nextPageUrl() }}"
                               class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-[#114F72] bg-white border border-gray-200 rounded-lg hover:bg-[#114F72]/5 hover:border-[#114F72]/20 transition-colors">
                                Berikutnya

                                <svg class="w-4 h-4"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <span class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                                Berikutnya

                                <svg class="w-4 h-4"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        @endif

                    </nav>
                @endif
            </div>

        @else

            <!-- Empty State -->
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>

                <h3 class="text-lg font-medium text-gray-800 mb-1">
                    Belum Ada Laporan Masuk
                </h3>

                <p class="text-sm text-gray-500">
                    Tidak ada laporan yang perlu ditinjau saat ini.
                </p>
            </div>

        @endif

    </div>
</div>
@endsection