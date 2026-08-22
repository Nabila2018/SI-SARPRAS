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
        <div class="rounded-xl bg-gray-50 border border-gray-100 p-8 text-center text-sm text-gray-500">
            Belum terdapat hasil evaluasi untuk laporan ini.
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

<script>
    function openEvaluasiFotoModal(url, title = '') {
        const modal = document.getElementById('evaluasiFotoModal');
        const img = document.getElementById('evaluasiFotoModalImg');
        const titleEl = document.getElementById('evaluasiFotoModalTitle');
        if (!modal || !img) return;
        img.src = url;
        if (titleEl) titleEl.innerText = title;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeEvaluasiFotoModal() {
        const modal = document.getElementById('evaluasiFotoModal');
        if (!modal) return;
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
</script>
