@php
    $hasEvaluation = !is_null($laporan->kategori_kerusakan);
    $isDikembalikan = $laporan->status_laporan === 'Dikembalikan';
    $canEvaluate = in_array($laporan->status_laporan, ['Menunggu', 'Dikembalikan']);
    $canForward = $hasEvaluation && in_array($laporan->status_laporan, ['Menunggu', 'Dikembalikan']);
@endphp

<!-- Hasil Evaluasi Staff -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
    
    {{-- ALERT CATATAN REVISI EVALUASI DARI KABID (JIKA DIKEMBALIKAN) --}}
    @if($isDikembalikan && !empty($laporan->catatan_revisi_evaluasi))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 space-y-1.5">
            <div class="flex items-center gap-2 text-red-800 font-bold text-sm">
                <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Catatan Revisi Evaluasi (dari Kepala Bidang)</span>
            </div>
            <p class="text-xs text-red-700 leading-relaxed whitespace-pre-line">
                {{ $laporan->catatan_revisi_evaluasi }}
            </p>
        </div>
    @endif

    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
        <div>
            <h3 class="text-base font-bold text-gray-800">Hasil Evaluasi Staff</h3>
            <p class="text-xs text-gray-500 mt-0.5">Hasil pemeriksaan & analisis kerusakan fasilitas oleh Staff Sarpras.</p>
        </div>
        @if($isDikembalikan)
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold bg-red-100 text-red-700 border-red-200">
                Perlu Revisi (Dikembalikan)
            </span>
        @elseif($hasEvaluation)
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
            <p class="font-semibold">Laporan ini belum memiliki hasil evaluasi pemeriksaan.</p>
            <p class="text-xs text-amber-600 mt-1">Silakan klik tombol "Isi Evaluasi" di bawah untuk memasukkan hasil analisa kerusakan.</p>
        </div>
    @endif

    <!-- Tombol Aksi Evaluasi -->
    <div class="flex flex-wrap items-center justify-end gap-3 pt-4 border-t border-gray-100">
        <button type="button"
                onclick="openEvaluasiModal()"
                class="px-6 py-2.5 rounded-xl font-semibold shadow-sm transition text-sm {{ $canEvaluate ? 'bg-gradient-to-r from-[#114F72] to-[#16A394] text-white hover:opacity-90 cursor-pointer' : 'bg-gray-200 text-gray-500 cursor-not-allowed opacity-70' }}"
                {{ $canEvaluate ? '' : 'disabled' }}>
            {{ $hasEvaluation ? 'Edit Evaluasi' : 'Isi Evaluasi' }}
        </button>

        <button type="button"
                onclick="openForwardModal()"
                class="px-6 py-2.5 rounded-xl font-semibold shadow-sm transition text-sm {{ $canForward ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white hover:opacity-90 cursor-pointer' : 'bg-gray-200 text-gray-500 cursor-not-allowed opacity-70' }}"
                {{ $canForward ? '' : 'disabled' }}>
            {{ $isDikembalikan ? 'Kirim Ulang ke Kabid' : 'Teruskan ke Kabid' }}
        </button>
    </div>
</div>

<!-- Modal Input / Edit Evaluasi -->
<div id="evaluasiModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeEvaluasiModal()">
    <div class="w-full max-w-3xl rounded-2xl bg-white p-6 sm:p-8 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-5 border-b pb-3">
            <h3 class="text-lg font-bold text-gray-800">{{ $hasEvaluation ? 'Edit Evaluasi Laporan' : 'Isi Evaluasi Laporan' }}</h3>
            <button type="button" onclick="closeEvaluasiModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('staff.laporan.evaluasi.store', $laporan->id_laporan) }}?tab=evaluasi" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-600 mb-1.5">Kategori Kerusakan <span class="text-red-500">*</span></label>
                <select name="kategori_kerusakan" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] text-sm py-3 px-4">
                    <option value="">-- Pilih Kategori Kerusakan --</option>
                    <option value="Ringan" {{ old('kategori_kerusakan', $laporan->kategori_kerusakan) === 'Ringan' ? 'selected' : '' }}>Ringan</option>
                    <option value="Sedang" {{ old('kategori_kerusakan', $laporan->kategori_kerusakan) === 'Sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="Berat" {{ old('kategori_kerusakan', $laporan->kategori_kerusakan) === 'Berat' ? 'selected' : '' }}>Berat</option>
                </select>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-600 mb-1.5">Catatan Survey <span class="text-red-500">*</span></label>
                <textarea name="catatan_pemeriksaan" rows="4" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] text-sm p-4" placeholder="Tuliskan detail hasil survey teknis...">{{ old('catatan_pemeriksaan', $laporan->catatan_pemeriksaan) }}</textarea>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-600 mb-1.5">
                    Lampiran / Berkas Dokumen Evaluasi <span class="text-gray-400 font-normal text-xs uppercase">(Bisa Unggah Lebih Dari 1 Berkas / Foto)</span>
                </label>

                <input type="file" name="file_lampiran_evaluasi[]" id="file_lampiran_evaluasi" accept="image/*,.pdf,.doc,.docx" multiple class="hidden" onchange="handleEvaluasiFileSelection(this)">

                <div id="evaluasi-dropzone-box" class="border-2 border-dashed border-gray-200 rounded-2xl p-4 transition-all bg-gray-50/50 hover:border-[#114F72]">

                    <!-- Berkas Lama Saat Ini (Jika Ada) -->
                    @if(count($laporan->lampiran_evaluasi_list) > 0)
                        <div id="existing-lampiran-card" class="mb-3 p-3.5 bg-white border border-slate-200 rounded-2xl space-y-2 shadow-sm">
                            <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                                <span class="text-xs font-bold text-slate-700">Lampiran Ter-unggah saat ini ({{ count($laporan->lampiran_evaluasi_list) }} berkas)</span>
                            </div>
                            <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                                @foreach($laporan->lampiran_evaluasi_list as $idx => $fPath)
                                    @php
                                        $fExt = strtolower(pathinfo($fPath, PATHINFO_EXTENSION));
                                        $fIsImg = in_array($fExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                        $fUrl = asset('storage/' . $fPath);
                                        $fRaw = basename($fPath);
                                        if (preg_match('/^[a-f0-9]{32,40}\.' . $fExt . '$/i', $fRaw)) {
                                            $fName = $fIsImg ? 'Foto Lampiran Evaluasi.' . $fExt : 'Dokumen Lampiran Evaluasi.' . $fExt;
                                        } else {
                                            $fName = preg_replace('/^\d+_[a-f0-9]+_/', '', $fRaw);
                                            $fName = preg_replace('/^\d+_/', '', $fName);
                                        }
                                    @endphp
                                    <div id="existing-item-row-{{ $idx }}" class="flex items-center justify-between gap-2.5 p-2 bg-slate-50 border border-slate-200/80 rounded-xl hover:bg-slate-100/80 hover:border-slate-300 transition-all group relative overflow-hidden">
                                        <div id="existing-item-overlay-{{ $idx }}" class="hidden absolute inset-0 bg-rose-950/85 backdrop-blur-[1px] flex items-center justify-between px-3 text-white z-10">
                                            <span class="text-[11px] font-semibold flex items-center gap-1 text-rose-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Akan Dihapus
                                            </span>
                                            <button type="button" onclick="undoDeleteExistingItem({{ $idx }})" class="text-[10px] font-bold bg-white/20 hover:bg-white/30 text-white px-2 py-0.5 rounded-md transition cursor-pointer">
                                                Batal
                                            </button>
                                        </div>
                                        <input type="checkbox" name="hapus_lampiran_items[]" id="hapus_item_cb_{{ $idx }}" value="{{ $fPath }}" class="hidden">
                                        
                                        @if($fIsImg)
                                            <button type="button" onclick="openEvaluasiFotoModal('{{ $fUrl }}', '{{ e($fName) }}')" class="flex items-center gap-2.5 min-w-0 flex-1 text-left cursor-pointer outline-none">
                                                <i class="ph ph-image text-lg text-amber-600 flex-shrink-0 group-hover:scale-110 transition-transform"></i>
                                                <span class="font-medium text-xs text-slate-800 group-hover:text-[#114F72] truncate" title="{{ $fName }}">{{ $fName }}</span>
                                            </button>
                                        @else
                                            <a href="{{ $fUrl }}" target="_blank" class="flex items-center gap-2.5 min-w-0 flex-1 text-left outline-none">
                                                <i class="ph ph-file-pdf text-lg text-blue-600 flex-shrink-0 group-hover:scale-110 transition-transform"></i>
                                                <span class="font-medium text-xs text-slate-800 group-hover:text-[#114F72] truncate" title="{{ $fName }}">{{ $fName }}</span>
                                            </a>
                                        @endif

                                        <button type="button" onclick="markDeleteExistingItem({{ $idx }})" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors flex-shrink-0 cursor-pointer" title="Hapus berkas ini">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Prompt Dropzone (Saat Belum Pilih Berkas Baru) -->
                    <div id="evaluasi-dropzone-prompt" class="py-6 text-center cursor-pointer" onclick="document.getElementById('file_lampiran_evaluasi').click()">
                        <svg class="w-9 h-9 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm font-semibold text-gray-700 mb-0.5">Klik atau drag berkas baru ke sini</p>
                        <p class="text-xs text-gray-400">Dapat memilih sekaligus beberapa Foto (JPG, PNG) atau Dokumen (PDF, DOC) - Maks 5MB/file</p>
                    </div>

                    <!-- Preview Berkas Baru Terpilih -->
                    <div id="evaluasi-file-preview" class="hidden">
                        <div class="pb-2 mb-2 border-b border-gray-200">
                            <span id="evaluasi-preview-count" class="text-xs font-bold text-emerald-700"></span>
                        </div>
                        <div id="evaluasi-file-list" class="space-y-2 max-h-48 overflow-y-auto pr-1"></div>
                    </div>

                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeEvaluasiModal()" class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
                <button type="submit" class="rounded-xl bg-gradient-to-r from-[#114F72] to-[#16A394] px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:opacity-90 transition">Simpan Evaluasi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Teruskan ke Kabid -->
<div id="forwardModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeForwardModal()">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        <h3 class="text-lg font-bold text-gray-800">{{ $isDikembalikan ? 'Kirim Ulang Evaluasi ke Kabid' : 'Teruskan Laporan ke Kabid' }}</h3>
        <p class="mt-2 text-sm text-gray-600">
            {{ $isDikembalikan ? 'Apakah Anda yakin ingin mengirim ulang hasil evaluasi yang telah diperbaiki ini ke Kepala Bidang untuk diverifikasi kembali?' : 'Apakah Anda yakin ingin meneruskan laporan ini beserta hasil evaluasi ke Kepala Bidang untuk disetujui?' }}
        </p>

        <form action="{{ route('staff.laporan.forward', $laporan->id_laporan) }}?tab=evaluasi" method="POST" class="mt-6 flex justify-end gap-3">
            @csrf
            <button type="button" onclick="closeForwardModal()" class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
            <button type="submit" class="rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">
                {{ $isDikembalikan ? 'Ya, Kirim Ulang' : 'Ya, Teruskan' }}
            </button>
        </form>
    </div>
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

    function openEvaluasiModal() {
        const modal = document.getElementById('evaluasiModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeEvaluasiModal() {
        const modal = document.getElementById('evaluasiModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function openForwardModal() {
        const modal = document.getElementById('forwardModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeForwardModal() {
        const modal = document.getElementById('forwardModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function handleEvaluasiFileSelection(input) {
        const promptElem = document.getElementById('evaluasi-dropzone-prompt');
        const previewElem = document.getElementById('evaluasi-file-preview');
        const countElem = document.getElementById('evaluasi-preview-count');
        const fileListDiv = document.getElementById('evaluasi-file-list');

        if (!input.files || input.files.length === 0) {
            if (promptElem) promptElem.classList.remove('hidden');
            if (previewElem) previewElem.classList.add('hidden');
            return;
        }

        if (promptElem) promptElem.classList.add('hidden');
        if (previewElem) previewElem.classList.remove('hidden');

        if (countElem) {
            countElem.textContent = input.files.length + ' Berkas Baru Terpilih';
        }

        if (fileListDiv) {
            fileListDiv.innerHTML = '';
            Array.from(input.files).forEach((file, index) => {
                const isImg = file.type.startsWith('image/');
                const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
                
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between gap-3 text-xs text-gray-700 bg-white border border-gray-200 p-2.5 rounded-xl shadow-sm';
                item.innerHTML = `
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-lg ${isImg ? 'bg-amber-50 text-amber-600 border border-amber-100' : 'bg-blue-50 text-blue-600 border border-blue-100'} flex items-center justify-center flex-shrink-0">
                            ${isImg ? '<i class="ph ph-image text-base"></i>' : '<i class="ph ph-file-pdf text-base"></i>'}
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 truncate text-xs">${file.name}</p>
                            <p class="text-[10px] text-gray-400">${sizeMb} MB</p>
                        </div>
                    </div>
                `;
                fileListDiv.appendChild(item);
            });
        }
    }

    function markDeleteExistingItem(idx) {
        const cb = document.getElementById('hapus_item_cb_' + idx);
        const overlay = document.getElementById('existing-item-overlay-' + idx);
        if (cb) cb.checked = true;
        if (overlay) {
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
        }
    }

    function undoDeleteExistingItem(idx) {
        const cb = document.getElementById('hapus_item_cb_' + idx);
        const overlay = document.getElementById('existing-item-overlay-' + idx);
        if (cb) cb.checked = false;
        if (overlay) {
            overlay.classList.remove('flex');
            overlay.classList.add('hidden');
        }
    }

    // Drag & Drop event listener for evaluasi dropzone
    document.addEventListener('DOMContentLoaded', function() {
        const evalDropzoneBox = document.getElementById('evaluasi-dropzone-box');
        if (evalDropzoneBox) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(eventName) {
                evalDropzoneBox.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(function(eventName) {
                evalDropzoneBox.addEventListener(eventName, function() {
                    evalDropzoneBox.classList.add('border-[#114F72]', 'bg-[#114F72]/10');
                }, false);
            });

            ['dragleave', 'drop'].forEach(function(eventName) {
                evalDropzoneBox.addEventListener(eventName, function() {
                    evalDropzoneBox.classList.remove('border-[#114F72]', 'bg-[#114F72]/10');
                }, false);
            });

            evalDropzoneBox.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                if (dt && dt.files && dt.files.length > 0) {
                    const fileInput = document.getElementById('file_lampiran_evaluasi');
                    if (fileInput) {
                        fileInput.files = dt.files;
                        handleEvaluasiFileSelection(fileInput);
                    }
                }
            }, false);
        }
    });
</script>
