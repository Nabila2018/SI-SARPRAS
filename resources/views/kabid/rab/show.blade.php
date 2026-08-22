@extends('layouts.app')

@section('title', 'Detail Verifikasi RAB - Kabid SI-SARPRAS')

@section('breadcrumb')
    <a href="{{ route('kabid.rab.index') }}" class="hover:text-[#114F72] transition">Verifikasi RAB</a>
    <svg class="w-4 h-4 mx-2 text-gray-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-gray-600">Detail RAB {{ $rab->id_rab }}</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-800">Detail Verifikasi RAB {{ $rab->id_rab }}</h1>
                @php
                    $badge = match($rab->status_verifikasi_rab) {
                        'Disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'Menunggu' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'Dikembalikan' => 'bg-rose-50 text-rose-700 border-rose-200',
                        default => 'bg-gray-50 text-gray-600 border-gray-200',
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-extrabold border {{ $badge }}">
                    {{ $rab->status_verifikasi_rab }}
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Lokasi Pasar: <span class="font-semibold text-gray-700">{{ $rab->nama_pasar }}</span></p>
        </div>

        <a href="{{ route('kabid.rab.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
            Kembali
        </a>
    </div>

    <!-- Banner Info RAB Overview -->
    <div class="bg-gradient-to-r from-[#114F72] to-[#16A394] rounded-2xl p-6 text-white shadow-lg grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <p class="text-xs text-white/70 font-semibold uppercase tracking-wider">Lokasi Pasar</p>
            <p class="text-lg font-bold mt-0.5">{{ $rab->nama_pasar }}</p>
        </div>
        <div>
            <p class="text-xs text-white/70 font-semibold uppercase tracking-wider">Jumlah Laporan Tergabung</p>
            <p class="text-lg font-bold mt-0.5">{{ $rab->laporan->count() }} Laporan Kerusakan</p>
        </div>
        <div>
            <p class="text-xs text-white/70 font-semibold uppercase tracking-wider">Total Anggaran RAB</p>
            <p class="text-xl font-extrabold text-amber-300 mt-0.5">Rp {{ number_format($rab->total_biaya, 0, ',', '.') }}</p>
        </div>
    </div>



    <!-- SECTION 1: DAFTAR LAPORAN DALAM RAB -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
        <h2 class="text-sm font-bold text-gray-800 border-b pb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
            </svg>
            Daftar Laporan yang Digabung dalam RAB Ini
        </h2>
        <div class="divide-y divide-gray-100">
            @forelse($rab->laporan as $lap)
                <div class="py-3 flex flex-col md:flex-row md:items-center justify-between gap-2 text-xs hover:bg-gray-50/60 p-2 rounded-xl transition">
                    <div>
                        <a href="{{ route('laporan.show', $lap->id_laporan) }}" class="font-bold text-[#114F72] hover:underline flex items-center gap-1.5">
                            <span>{{ $lap->id_laporan }}</span>
                            <span class="text-gray-400 font-normal">•</span>
                            <span class="text-gray-800">{{ $lap->nama_fasilitas_display }}</span>
                        </a>
                        <p class="text-gray-600 mt-0.5 font-medium">{{ $lap->item_kerusakan }} ({{ $lap->lokasi_spesifik ?? '-' }})</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $lap->kategori_kerusakan }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Progress: {{ $lap->latest_progress_percentage }}%
                        </span>
                    </div>
                </div>
            @empty
                <p class="py-4 text-xs text-gray-400 text-center">Belum ada laporan yang dikaitkan.</p>
            @endforelse
        </div>
    </div>

    <!-- SECTION 2: RINCIAN KEBUTUHAN RAB -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
        <h2 class="text-sm font-bold text-gray-800 border-b pb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Rincian Kebutuhan & Biaya (Detail RAB)
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="py-3 px-4 w-12">No</th>
                        <th class="py-3 px-4">Rincian Kebutuhan</th>
                        <th class="py-3 px-4 text-center">Volume</th>
                        <th class="py-3 px-4 text-center">Satuan</th>
                        <th class="py-3 px-4 text-right">Harga Satuan</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rab->detailRab as $index => $item)
                        @php $sub = $item->volume * $item->harga_satuan; @endphp
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3 px-4 font-semibold text-gray-500">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-gray-800">
                                {{ $item->rincian_kebutuhan }}
                                @if($item->id_sab)
                                    <span class="ml-1 text-[10px] px-2 py-0.5 rounded font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">SAB</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-gray-700">{{ (float)$item->volume }}</td>
                            <td class="py-3 px-4 text-center font-semibold text-gray-700">{{ $item->satuan }}</td>
                            <td class="py-3 px-4 text-right font-medium text-gray-700">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-extrabold text-gray-800">Rp {{ number_format($sub, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">Belum ada rincian kebutuhan yang diinput.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t-2 border-gray-200 bg-gray-50/80 font-bold">
                    <tr>
                        <td colspan="5" class="py-4 px-4 text-right text-gray-700 text-xs uppercase tracking-wider">Total Anggaran RAB:</td>
                        <td class="py-4 px-4 text-right text-[#114F72] text-base font-extrabold">Rp {{ number_format($rab->total_biaya, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- VERIFIKASI ACTIONS FOR KABID -->
    @if($rab->status_verifikasi_rab === 'Menunggu')
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
            <h2 class="text-sm font-bold text-gray-800 border-b pb-3">Keputusan Verifikasi Kabid</h2>
            <div class="flex flex-wrap items-center gap-4">
                <form action="{{ route('kabid.rab.setujui', $rab->id_rab) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui RAB ini?')">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition transform hover:-translate-y-0.5">
                        Setujui RAB Ini
                    </button>
                </form>

                <button type="button" onclick="document.getElementById('modalRevisi').classList.remove('hidden')" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-md transition transform hover:-translate-y-0.5">
                    Kembalikan dengan Catatan Revisi
                </button>
            </div>
        </div>

        <!-- MODAL REVISI -->
        <div id="modalRevisi" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-gray-100">
                <h3 class="text-base font-bold text-gray-800 border-b pb-2">Kembalikan RAB untuk Revisi</h3>
                <form action="{{ route('kabid.rab.kembalikan', $rab->id_rab) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Catatan Revisi <span class="text-rose-500">*</span></label>
                        <textarea name="catatan_revisi_rab" rows="4" required placeholder="Tuliskan alasan pengembalian RAB dan petunjuk perbaikan..." class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#114F72]/20 focus:border-[#114F72]"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('modalRevisi').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-md transition">
                            Kirim Catatan Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
