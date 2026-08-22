@php
    $hasEvaluation = !is_null($laporan->kategori_kerusakan);
    $canVerify = in_array($laporan->status_laporan, ['Diproses', 'Menunggu']) && $hasEvaluation;
@endphp

<!-- Hasil Evaluasi Staff -->
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
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Catatan Survey</p>
                <p class="text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100 whitespace-pre-line">
                    {{ $laporan->catatan_pemeriksaan ?: 'Tidak ada catatan survey.' }}
                </p>
            </div>
            @if(count($laporan->lampiran_evaluasi_list) > 0)
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Berkas Lampiran Evaluasi</p>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach($laporan->lampiran_evaluasi_list as $index => $filePath)
                            @php
                                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                $fileUrl = asset('storage/' . $filePath);
                                $rawName = basename($filePath);
                                
                                if (preg_match('/^[a-f0-9]{32,40}\.' . $ext . '$/i', $rawName)) {
                                    $displayName = $isImage ? 'Foto Lampiran Evaluasi.' . $ext : 'Dokumen Lampiran Evaluasi.' . $ext;
                                } else {
                                    $displayName = preg_replace('/^\d+_[a-f0-9]+_/', '', $rawName);
                                    $displayName = preg_replace('/^\d+_/', '', $displayName);
                                }
                            @endphp

                            @if($isImage)
                                <button type="button"
                                        onclick="openEvaluasiFotoModal('{{ $fileUrl }}', '{{ e($displayName) }}')"
                                        class="inline-flex items-center gap-2 px-3.5 py-2 bg-gray-50 border border-gray-200 text-[#114F72] font-semibold text-xs rounded-xl hover:bg-gray-100 transition shadow-sm cursor-pointer max-w-xs truncate"
                                        title="{{ $displayName }}">
                                    <i class="ph ph-image text-base text-[#114F72] flex-shrink-0"></i>
                                    <span class="truncate">{{ $displayName }}</span>
                                </button>
                            @else
                                <a href="{{ $fileUrl }}" target="_blank"
                                   class="inline-flex items-center gap-2 px-3.5 py-2 bg-gray-50 border border-gray-200 text-[#114F72] font-semibold text-xs rounded-xl hover:bg-gray-100 transition shadow-sm max-w-xs truncate"
                                   title="{{ $displayName }}">
                                    <i class="ph ph-file-pdf text-base text-[#114F72] flex-shrink-0"></i>
                                    <span class="truncate">{{ $displayName }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
            @if($laporan->evaluator)
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Evaluator Staff</p>
                    <p class="text-gray-700 font-medium">Dievaluasi oleh: {{ $laporan->evaluator->nama_lengkap }}</p>
                </div>
            @endif
            @if($laporan->tanggal_evaluasi)
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tanggal Evaluasi</p>
                    <p class="text-gray-600">{{ \Carbon\Carbon::parse($laporan->tanggal_evaluasi)->translatedFormat('d F Y H:i') }} WIB</p>
                </div>
            @endif
        </div>
    @else
        <div class="rounded-xl bg-amber-50/60 border border-amber-200/60 p-6 text-center text-sm text-amber-800">
            <p class="font-semibold">Belum terdapat hasil evaluasi dari Staff Sarana dan Prasarana.</p>
        </div>
    @endif
</div>

<!-- Modal Lightbox Preview Foto Evaluasi (In-Page Preview) -->
<div id="evaluasiFotoModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" onclick="closeEvaluasiFotoModal()">
    <div class="relative max-w-[90vw] max-h-[90vh] flex flex-col items-center justify-center" onclick="event.stopPropagation()">
        <button type="button" onclick="closeEvaluasiFotoModal()" class="absolute -top-12 right-0 text-white hover:text-gray-300 p-2 text-sm font-bold flex items-center gap-1 bg-white/10 rounded-lg backdrop-blur-md cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Tutup
        </button>
        <img id="evaluasiFotoModalImg" src="" alt="Preview Lampiran Evaluasi" class="max-h-[80vh] max-w-[90vw] object-contain rounded-xl shadow-2xl border border-white/20">
        <p id="evaluasiFotoModalTitle" class="mt-3 text-xs text-white/80 font-medium text-center"></p>
    </div>
</div>

<!-- Keputusan Kepala Bidang -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-base font-bold text-gray-800 mb-1">Keputusan / Verifikasi Laporan</h3>
    <p class="text-xs text-gray-500 mb-4">Berikan verifikasi persetujuan atau pengembalian laporan berdasarkan evaluasi Staff.</p>

    @if($canVerify)
        <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
            <button type="button"
                    onclick="openKembalikanModal()"
                    class="px-6 py-2.5 rounded-xl font-semibold text-white shadow-sm bg-gradient-to-r from-amber-500 to-red-500 hover:opacity-90 transition text-sm">
                Kembalikan
            </button>

            <button type="button"
                    onclick="openSetujuiModal()"
                    class="px-6 py-2.5 rounded-xl font-semibold text-white shadow-sm bg-gradient-to-r from-[#114F72] to-[#16A394] hover:opacity-90 transition text-sm">
                Setujui Laporan
            </button>
        </div>
    @else
        <div class="rounded-xl bg-gray-50 border border-gray-100 p-4 text-sm text-gray-600">
            <p><strong>Status Verifikasi:</strong> {{ $laporan->status_laporan }}</p>
            @if($laporan->catatan_revisi_laporan)
                <p class="text-xs text-red-600 mt-1"><strong>Catatan Revisi:</strong> {{ $laporan->catatan_revisi_laporan }}</p>
            @endif
        </div>
    @endif
</div>

<!-- Modal Setujui Laporan -->
<div id="setujuiModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeSetujuiModal()">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        <h3 class="text-lg font-bold text-gray-800">Setujui Laporan</h3>
        <p class="mt-2 text-sm text-gray-600">Apakah Anda yakin ingin menyetujui laporan ini? Laporan yang disetujui akan dapat dilanjutkan ke tahap pembuatan RAB.</p>

        <form action="{{ route('kabid.laporan.setujui', $laporan->id_laporan) }}?tab=evaluasi" method="POST" class="mt-6 flex justify-end gap-3">
            @csrf
            <button type="button" onclick="closeSetujuiModal()" class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
            <button type="submit" class="rounded-xl bg-gradient-to-r from-[#114F72] to-[#16A394] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">Ya, Setujui</button>
        </form>
    </div>
</div>

<!-- Modal Kembalikan Laporan -->
<div id="kembalikanModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeKembalikanModal()">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4 border-b pb-3">
            <h3 class="text-lg font-bold text-gray-800">Kembalikan Laporan ke Staff</h3>
            <button type="button" onclick="closeKembalikanModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('kabid.laporan.kembalikan', $laporan->id_laporan) }}?tab=evaluasi" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-600 mb-1">Catatan Revisi <span class="text-red-500">*</span></label>
                <textarea name="catatan_revisi_evaluasi" rows="4" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm" placeholder="Berikan catatan perbaikan/alasan pengembalian..."></textarea>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-3 border-t">
                <button type="button" onclick="closeKembalikanModal()" class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
                <button type="submit" class="rounded-xl bg-gradient-to-r from-amber-500 to-red-500 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">Kembalikan Laporan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openSetujuiModal() {
        const modal = document.getElementById('setujuiModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeSetujuiModal() {
        const modal = document.getElementById('setujuiModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function openKembalikanModal() {
        const modal = document.getElementById('kembalikanModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeKembalikanModal() {
        const modal = document.getElementById('kembalikanModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
</script>
