@php
    $hasEvaluation = !is_null($laporan->kategori_kerusakan);
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
        <div>
            <h3 class="text-base font-bold text-gray-800">Hasil Evaluasi Staff</h3>
            <p class="text-xs text-gray-500 mt-0.5">Hasil pemeriksaan & analisis kerusakan fasilitas oleh Staff Sarpras.</p>
        </div>
        @if($hasEvaluation)
            @php
                $evalBadge = match($laporan->kategori_kerusakan) {
                    'Ringan' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'Sedang' => 'bg-orange-100 text-orange-700 border-orange-200',
                    'Berat' => 'bg-red-100 text-red-700 border-red-200',
                    default => 'bg-gray-100 text-gray-600 border-gray-200',
                };
            @endphp
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $evalBadge }}">
                Kategori: {{ $laporan->kategori_kerusakan }}
            </span>
        @else
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-600 border-gray-200">
                Belum Dievaluasi
            </span>
        @endif
    </div>

    @if($hasEvaluation)
        <div class="space-y-4 text-sm">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Catatan Pemeriksaan</p>
                <p class="text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100 whitespace-pre-line">
                    {{ $laporan->catatan_pemeriksaan ?: 'Tidak ada catatan pemeriksaan.' }}
                </p>
            </div>
            @if($laporan->tanggal_evaluasi)
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tanggal Evaluasi</p>
                    <p class="text-gray-600">{{ \Carbon\Carbon::parse($laporan->tanggal_evaluasi)->translatedFormat('d F Y H:i') }} WIB</p>
                </div>
            @endif
        </div>
    @else
        <div class="rounded-xl bg-gray-50 border border-gray-100 p-8 text-center text-sm text-gray-500">
            Belum terdapat hasil evaluasi untuk laporan ini.
        </div>
    @endif
</div>
