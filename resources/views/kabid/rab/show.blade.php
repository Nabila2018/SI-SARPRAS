@extends('layouts.app')

@section('title', 'Detail RAB - Kepala Bidang')

@section('breadcrumb')
    <a href="{{ route('kabid.rab.index') }}" class="hover:text-[#114F72] transition">Daftar RAB</a>
    <svg class="w-4 h-4 mx-2 text-gray-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-gray-600">Detail RAB</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto pb-12">

    <!-- Tombol Kembali -->
    <a href="{{ route('kabid.rab.index') }}"
       class="inline-flex items-center gap-2 text-gray-600 hover:text-[#114F72] mb-6 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar RAB
    </a>

    <!-- Alert Notifications -->
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

    <!-- Status & Informasi Laporan Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Detail Verifikasi RAB</h1>
                <p class="text-xs text-gray-500 mt-1">ID Laporan: #{{ $laporan->id_laporan }}</p>
            </div>

            @php
                $statusBadge = match($laporan->status_verifikasi_rab) {
                    'Menunggu' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'Disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                    'Dikembalikan' => 'bg-red-100 text-red-700 border-red-200',
                    default => 'bg-gray-100 text-gray-600 border-gray-200',
                };
            @endphp
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $statusBadge }}">
                Status Verifikasi: {{ $laporan->status_verifikasi_rab }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Pasar & Lokasi</p>
                <p class="font-semibold text-gray-800">{{ $laporan->lokasi?->pasar?->nama_pasar ?? '-' }}</p>
                <p class="text-gray-600 text-xs mt-0.5">{{ $laporan->lokasi_spesifik ?? '-' }}</p>
            </div>

            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Fasilitas & Kerusakan</p>
                <p class="font-semibold text-gray-800">{{ $laporan->fasilitas?->nama_fasilitas ?? '-' }}</p>
                <p class="text-gray-600 text-xs mt-0.5">{{ $laporan->item_kerusakan }} (Kategori: {{ $laporan->kategori_kerusakan ?? '-' }})</p>
            </div>

            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tanggal Diteruskan Staff</p>
                <p class="font-medium text-gray-800">
                    {{ $laporan->tanggal_input_rab ? \Carbon\Carbon::parse($laporan->tanggal_input_rab)->format('d F Y H:i') : '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tanggal Verifikasi Kabid</p>
                <p class="font-medium text-gray-800">
                    {{ $laporan->tanggal_verifikasi_rab ? \Carbon\Carbon::parse($laporan->tanggal_verifikasi_rab)->format('d F Y H:i') : '-' }}
                </p>
            </div>
        </div>

        @if($laporan->catatan_revisi_rab)
            <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-red-600 mb-1">Catatan Revisi RAB</p>
                <p class="text-sm text-red-700">{{ $laporan->catatan_revisi_rab }}</p>
            </div>
        @endif
    </div>

    <!-- Tabel Rincian RAB Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Rincian Anggaran Biaya</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">No</th>
                        <th class="text-left py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">Rincian Kebutuhan</th>
                        <th class="text-right py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">Volume</th>
                        <th class="text-left py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">Satuan</th>
                        <th class="text-right py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">Harga Satuan (Rp)</th>
                        <th class="text-right py-3 px-2 text-gray-500 text-xs uppercase tracking-wider font-medium">Subtotal (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalRab = 0; @endphp
                    @forelse($laporan->detailRab as $index => $item)
                        @php
                            $subtotal = $item->volume * $item->harga_satuan;
                            $totalRab += $subtotal;
                        @endphp
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-2 text-gray-600">{{ $index + 1 }}</td>
                            <td class="py-3 px-2 font-medium text-gray-800">{{ $item->rincian_kebutuhan }}</td>
                            <td class="py-3 px-2 text-right text-gray-700">{{ number_format($item->volume, 2, ',', '.') }}</td>
                            <td class="py-3 px-2 text-gray-700">{{ $item->satuan }}</td>
                            <td class="py-3 px-2 text-right text-gray-700">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="py-3 px-2 text-right font-medium text-gray-800">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">Tidak ada detail rincian RAB.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200 bg-gray-50/50">
                        <td colspan="5" class="py-4 px-2 text-right font-bold text-gray-800 text-base">Total RAB:</td>
                        <td class="py-4 px-2 text-right font-bold text-[#114F72] text-lg">Rp {{ number_format($totalRab, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Tombol Verifikasi Action (Hanya jika status 'Menunggu') -->
    @if($laporan->status_verifikasi_rab === 'Menunggu')
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-gray-800 text-base">Verifikasi RAB Ini</h3>
                <p class="text-xs text-gray-500 mt-0.5">Silakan periksa rincian biaya di atas sebelum menyetujui atau mengembalikan.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Kembalikan Button -->
                <button type="button"
                        onclick="openKembalikanRabModal()"
                        class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-red-50 px-5 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Kembalikan untuk Revisi
                </button>

                <!-- Setujui Button -->
                <button type="button"
                        onclick="openSetujuiRabModal()"
                        class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-[#114F72] to-[#16A394] px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:opacity-90 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Setujui RAB
                </button>
            </div>
        </div>
    @endif

</div>

<!-- Modal Setujui RAB -->
<div id="setujuiRabModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeSetujuiRabModal()">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800">Setujui RAB</h3>
                <p class="text-xs text-gray-500">Konfirmasi persetujuan Rencana Anggaran Biaya</p>
            </div>
        </div>

        <p class="text-sm text-gray-600 mb-6">
            Apakah Anda yakin ingin menyetujui Rencana Anggaran Biaya (RAB) sebesar <strong class="text-[#114F72]">Rp {{ number_format($totalRab ?? 0, 0, ',', '.') }}</strong> ini?
        </p>

        <form action="{{ route('kabid.rab.setujui', $laporan->id_laporan) }}" method="POST">
            @csrf
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeSetujuiRabModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
                <button type="submit" class="rounded-lg bg-gradient-to-r from-[#114F72] to-[#16A394] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">Ya, Setujui RAB</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kembalikan RAB -->
<div id="kembalikanRabModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeKembalikanRabModal()">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800">Kembalikan RAB untuk Revisi</h3>
                <p class="text-xs text-gray-500">Berikan alasan dan catatan revisi untuk Staff</p>
            </div>
        </div>

        <form action="{{ route('kabid.rab.kembalikan', $laporan->id_laporan) }}" method="POST">
            @csrf
            <div class="mb-6">
                <label for="catatan_revisi_rab" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan Revisi RAB <span class="text-red-500">*</span>
                </label>
                <textarea id="catatan_revisi_rab"
                          name="catatan_revisi_rab"
                          rows="4"
                          required
                          maxlength="1000"
                          placeholder="Contoh: Harga satuan material terlalu tinggi, mohon disesuaikan dengan standar SSH..."
                          class="w-full rounded-lg border border-gray-300 p-3 text-sm focus:border-[#114F72] focus:outline-none focus:ring-1 focus:ring-[#114F72]"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeKembalikanRabModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
                <button type="submit" class="rounded-lg bg-red-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition">Kembalikan RAB</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openSetujuiRabModal() {
        const modal = document.getElementById('setujuiRabModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeSetujuiRabModal() {
        const modal = document.getElementById('setujuiRabModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function openKembalikanRabModal() {
        const modal = document.getElementById('kembalikanRabModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeKembalikanRabModal() {
        const modal = document.getElementById('kembalikanRabModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>
@endsection
