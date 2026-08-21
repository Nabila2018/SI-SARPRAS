@php
    $rab = $laporan->rab;
    $statusRab = $rab ? $rab->status_verifikasi_rab : 'Belum Dibuat';

    $statusBadge = match($statusRab) {
        'Menunggu' => 'bg-amber-50 text-amber-700 border-amber-200',
        'Disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'Dikembalikan' => 'bg-rose-50 text-rose-700 border-rose-200',
        'Draft' => 'bg-blue-50 text-blue-700 border-blue-200',
        default => 'bg-gray-50 text-gray-600 border-gray-200',
    };
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
    <!-- Header Kartu -->
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4">
        <div>
            <h3 class="text-base font-bold text-gray-800">Informasi Rencana Anggaran Biaya (RAB)</h3>
            <p class="text-xs text-gray-500 mt-0.5">Ringkasan status penyusunan RAB untuk perbaikan laporan ini.</p>
        </div>

        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold {{ $statusBadge }}">
            Status RAB: {{ $statusRab }}
        </span>
    </div>

    @if(!$rab)
        <!-- KONDISI 1: LAPORAN BELUM MASUK RAB -->
        <div class="rounded-xl bg-amber-50/60 border border-amber-200/60 p-6 text-center text-xs text-amber-800 space-y-3">
            <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center mx-auto text-amber-600 font-bold text-base">
                !
            </div>
            <div>
                <p class="font-bold text-sm">Laporan Belum Terikat ke RAB</p>
                <p class="text-amber-700 mt-1 max-w-md mx-auto">
                    Laporan ini belum dimasukkan ke dalam Rencana Anggaran Biaya. Penyusunan dan pengelolaan RAB dilakukan secara terpusat melalui menu **Rencana Anggaran Biaya (RAB)**.
                </p>
            </div>
            @if(auth()->user()->role->nama_role === 'Staff Sarana dan Prasarana' && in_array($laporan->status_laporan, ['Disetujui', 'Diproses']) && in_array($laporan->kategori_kerusakan, ['Ringan', 'Sedang']))
                <div class="pt-2">
                    <a href="{{ route('staff.rab.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#114F72] text-white text-xs font-bold rounded-xl shadow-sm hover:bg-[#114F72]/90 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat RAB Baru di Menu RAB
                    </a>
                </div>
            @endif
        </div>
    @else
        <!-- KONDISI 2: LAPORAN SUDAH TERIKAT RAB (SUMMARY INFO) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <p class="text-gray-400 font-semibold uppercase tracking-wider mb-1">ID / Kode RAB</p>
                <p class="text-sm font-bold text-[#114F72]">{{ $rab->id_rab }}</p>
            </div>

            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <p class="text-gray-400 font-semibold uppercase tracking-wider mb-1">Total Anggaran RAB</p>
                <p class="text-sm font-bold text-gray-800">Rp {{ number_format($rab->total_biaya, 0, ',', '.') }}</p>
            </div>

            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <p class="text-gray-400 font-semibold uppercase tracking-wider mb-1">Jumlah Laporan Tergabung</p>
                <p class="text-sm font-bold text-gray-800">{{ $rab->laporan->count() }} Laporan</p>
            </div>
        </div>

        @if($rab->status_verifikasi_rab === 'Dikembalikan' && $rab->catatan_revisi_rab)
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs space-y-1">
                <p class="uppercase font-bold text-rose-800">Catatan Revisi Kabid:</p>
                <p class="text-rose-900 leading-relaxed">{{ $rab->catatan_revisi_rab }}</p>
            </div>
        @endif

        <div class="pt-2 flex items-center justify-between border-t border-gray-100">
            <p class="text-xs text-gray-500">
                Detail rincian kebutuhan material, volume, dan harga dapat dilihat pada menu utama RAB.
            </p>
            @if(auth()->user()->role->nama_role === 'Staff Sarana dan Prasarana')
                <a href="{{ route('staff.rab.show', $rab->id_rab) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#114F72] hover:bg-[#114F72]/90 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    Lihat Detail RAB di Menu RAB &rarr;
                </a>
            @else
                <a href="{{ route('kabid.rab.show', $rab->id_rab) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#114F72] hover:bg-[#114F72]/90 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    Verifikasi RAB di Menu RAB &rarr;
                </a>
            @endif
        </div>
    @endif
</div>
