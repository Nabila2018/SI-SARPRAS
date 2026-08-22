@extends('layouts.app')

@section('title', 'Detail RAB - SI-SARPRAS')

@section('breadcrumb')
    <a href="{{ route('staff.rab.index') }}" class="hover:text-[#114F72] transition">RAB</a>
    <svg class="w-4 h-4 mx-2 text-gray-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-gray-600">Detail RAB {{ $rab->id_rab }}</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">
    <!-- Header Page (Header Kanan: Tombol Unduh PDF) -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-800">Detail RAB {{ $rab->id_rab }}</h1>
            @php
                $badge = match($rab->status_verifikasi_rab) {
                    'Disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'Menunggu' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'Dikembalikan' => 'bg-rose-50 text-rose-700 border-rose-200',
                    default => 'bg-blue-50 text-blue-700 border-blue-200',
                };
            @endphp
            <span class="px-3 py-1 rounded-full text-xs font-extrabold border {{ $badge }}">
                {{ $rab->status_verifikasi_rab }}
            </span>
        </div>

        <!-- Tombol Unduh PDF di Header Kanan -->
        <div>
            <a href="{{ route('staff.rab.pdf', $rab->id_rab) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 text-xs font-bold rounded-xl transition shadow-sm">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Unduh PDF
            </a>
        </div>
    </div>



    <!-- Banner Info RAB Overview -->
    <div class="bg-gradient-to-r from-[#114F72] to-[#16A394] rounded-2xl p-6 text-white shadow-lg grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <p class="text-xs text-white/70 font-semibold uppercase tracking-wider">PASAR</p>
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

    <!-- Catatan Revisi jika dikembalikan -->
    @if($rab->status_verifikasi_rab === 'Dikembalikan' && $rab->catatan_revisi_rab)
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs space-y-1 text-rose-800 shadow-sm">
            <p class="font-bold flex items-center gap-1.5 text-rose-900">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Catatan Revisi dari Kabid:
            </p>
            <p class="pl-5 leading-relaxed">{{ $rab->catatan_revisi_rab }}</p>
        </div>
    @endif

    <!-- SECTION 1: LAPORAN TERKAIT -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
        <h2 class="text-sm font-bold text-gray-800 border-b pb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
            </svg>
            Laporan Terkait
        </h2>
        <div class="divide-y divide-gray-100">
            @forelse($rab->laporan as $lap)
                <div class="py-3 flex flex-col md:flex-row md:items-center justify-between gap-3 hover:bg-gray-50/60 p-2.5 rounded-xl transition">
                    <div>
                        <div class="font-bold text-xs text-gray-800 flex items-center gap-1.5">
                            <span class="text-[#114F72]">{{ $lap->id_laporan }}</span>
                            <span class="text-gray-400 font-normal">•</span>
                            <span>{{ $lap->nama_fasilitas_display }}</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-0.5 font-medium">{{ $lap->item_kerusakan }} ({{ $lap->lokasi_spesifik ?? '-' }})</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Progress: {{ $lap->latest_progress_percentage }}%
                        </span>
                        <a href="{{ route('laporan.show', $lap->id_laporan) }}" class="px-3 py-1.5 bg-[#114F72]/5 hover:bg-[#114F72]/10 text-[#114F72] text-xs font-bold rounded-xl transition inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span>Detail</span>
                        </a>
                    </div>
                </div>
            @empty
                <p class="py-4 text-xs text-gray-400 text-center">Belum ada laporan yang dikaitkan.</p>
            @endforelse
        </div>
    </div>

    <!-- SECTION 2: RINCIAN KEBUTUHAN DAN BIAYA -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-4">
        <h2 class="text-sm font-bold text-gray-800 border-b pb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Rincian Kebutuhan dan Biaya
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
                        <td colspan="5" class="py-4 px-4 text-right text-gray-700 text-xs font-bold uppercase tracking-wider">Total Anggaran RAB:</td>
                        <td class="py-4 px-4 text-right text-[#114F72] text-base font-extrabold">Rp {{ number_format($rab->total_biaya, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- WORKFLOW ACTIONS (Hanya Tampil Jika Aksi Tersedia) -->
    @if(in_array($rab->status_verifikasi_rab, ['Draft', 'Dikembalikan', 'Disetujui']))
        <div class="flex items-center justify-end gap-3 pt-2">
            @if($rab->status_verifikasi_rab === 'Draft')
                <a href="{{ route('staff.rab.edit', $rab->id_rab) }}" class="px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-xs font-bold rounded-xl transition">
                    Edit RAB
                </a>
                <button type="button" onclick="openKirimRabModal()" class="px-5 py-2.5 bg-gradient-to-r from-[#114F72] to-[#16A394] hover:opacity-90 text-white text-xs font-bold rounded-xl shadow-md transition">
                    Kirim ke Kabid
                </button>
            @elseif($rab->status_verifikasi_rab === 'Dikembalikan')
                <a href="{{ route('staff.rab.edit', $rab->id_rab) }}" class="px-5 py-2.5 bg-gradient-to-r from-[#114F72] to-[#16A394] hover:opacity-90 text-white text-xs font-bold rounded-xl shadow-md transition">
                    Edit / Perbaiki RAB
                </a>
            @elseif($rab->status_verifikasi_rab === 'Disetujui')
                <a href="{{ route('staff.rab.edit', $rab->id_rab) }}" class="px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-xs font-bold rounded-xl transition">
                    Edit / Revisi RAB
                </a>
            @endif
        </div>
    @endif
</div>

<!-- MODAL KIRIM KE KABID -->
@if($rab->status_verifikasi_rab === 'Draft')
<div id="kirimRabModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeKirimRabModal()">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl space-y-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base font-bold text-gray-800">Kirim RAB ke Kepala Bidang</h3>
            <button type="button" onclick="closeKirimRabModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <p class="text-xs text-gray-600 leading-relaxed">
            Apakah Anda yakin ingin mengirim RAB <strong class="text-gray-800">{{ $rab->id_rab }}</strong> ke Kepala Bidang untuk diverifikasi? Status RAB akan diperbarui menjadi <em>Menunggu Verifikasi</em>.
        </p>

        <form action="{{ route('staff.rab.submit', $rab->id_rab) }}" method="POST" class="flex items-center justify-end gap-3 pt-3 border-t">
            @csrf
            <button type="button" onclick="closeKirimRabModal()" class="rounded-xl border border-gray-300 px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
                Batal
            </button>
            <button type="submit" class="rounded-xl bg-gradient-to-r from-[#114F72] to-[#16A394] px-5 py-2 text-xs font-semibold text-white shadow-md hover:opacity-90 transition">
                Ya, Kirim RAB
            </button>
        </form>
    </div>
</div>

<script>
    function openKirimRabModal() {
        const modal = document.getElementById('kirimRabModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeKirimRabModal() {
        const modal = document.getElementById('kirimRabModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
</script>
@endif
@endsection
