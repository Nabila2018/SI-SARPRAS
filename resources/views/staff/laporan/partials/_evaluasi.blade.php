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
            @if($laporan->file_lampiran_evaluasi)
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Berkas Lampiran Evaluasi</p>
                    <a href="{{ asset('storage/' . $laporan->file_lampiran_evaluasi) }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-50 border border-gray-200 text-[#114F72] font-semibold text-xs rounded-xl hover:bg-gray-100 transition shadow-sm">
                        <svg class="w-4 h-4 text-[#114F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        Lihat / Unduh Lampiran Evaluasi
                    </a>
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
                    Lampiran / Berkas Dokumen Evaluasi <span class="text-gray-400 font-normal text-xs uppercase">(Opsional)</span>
                </label>

                <input type="file" name="file_lampiran_evaluasi" id="file_lampiran_evaluasi" accept="image/*,.pdf,.doc,.docx" class="hidden" onchange="handleEvaluasiFileSelection(this)">
                <input type="checkbox" name="hapus_lampiran_evaluasi" id="hapus_lampiran_evaluasi" value="1" class="hidden" onchange="toggleDeleteExistingLampiranState()">

                <div id="evaluasi-dropzone-box" class="border-2 border-dashed border-gray-200 rounded-2xl p-4 transition-all bg-gray-50/50 hover:border-[#114F72]">

                    <!-- Berkas Lama Saat Ini (Jika Ada) -->
                    @if($laporan->file_lampiran_evaluasi)
                        <div id="existing-lampiran-card" class="mb-3 p-3.5 bg-white border border-gray-200 rounded-xl flex items-center justify-between gap-3 shadow-sm relative overflow-hidden">
                            <div id="existing-lampiran-overlay" class="hidden absolute inset-0 bg-rose-900/70 flex items-center justify-center backdrop-blur-[1px] z-10">
                                <span class="text-white text-xs font-bold bg-rose-600 px-3 py-1 rounded-full shadow">Akan Dihapus</span>
                            </div>
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-lg bg-[#114F72]/10 flex items-center justify-center text-[#114F72] flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 text-xs">
                                    <p class="font-semibold text-gray-800 truncate">Berkas Lampiran Ter-unggah</p>
                                    <a href="{{ asset('storage/' . $laporan->file_lampiran_evaluasi) }}" target="_blank" class="text-[#114F72] hover:underline font-medium">
                                        Buka Berkas Berjalan
                                    </a>
                                </div>
                            </div>
                            <button type="button" onclick="toggleDeleteExistingLampiran()" id="btn-delete-existing-lampiran"
                                class="p-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-full flex items-center justify-center shadow transition-transform transform hover:scale-110 flex-shrink-0 z-20"
                                title="Hapus lampiran ini">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    <!-- Prompt Dropzone (Saat Belum Pilih Berkas Baru) -->
                    <div id="evaluasi-dropzone-prompt" class="py-6 text-center cursor-pointer" onclick="document.getElementById('file_lampiran_evaluasi').click()">
                        <svg class="w-9 h-9 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm font-semibold text-gray-700 mb-0.5">Klik atau drag berkas baru ke sini</p>
                        <p class="text-xs text-gray-400">Format: Foto (JPG, PNG) atau Dokumen (PDF, DOC, DOCX) - Maks. 5MB</p>
                    </div>

                    <!-- Preview Berkas Baru Terpilih -->
                    <div id="evaluasi-file-preview" class="hidden">
                        <div class="flex items-center justify-between gap-3 text-sm text-gray-700 bg-white border border-gray-200 p-3.5 rounded-xl shadow-sm">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 flex-shrink-0 border border-emerald-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p id="evaluasi-filename" class="font-semibold text-gray-800 truncate text-xs sm:text-sm"></p>
                                    <p id="evaluasi-filesize" class="text-xs text-gray-400 mt-0.5"></p>
                                </div>
                            </div>
                            <button type="button" onclick="removeEvaluasiSelectedFile()" class="p-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-full flex items-center justify-center shadow transition-transform transform hover:scale-110 flex-shrink-0" title="Hapus berkas ini">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
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

<script>
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
        const filenameElem = document.getElementById('evaluasi-filename');
        const filesizeElem = document.getElementById('evaluasi-filesize');

        if (input.files && input.files.length > 0) {
            const file = input.files[0];
            filenameElem.textContent = file.name;
            filesizeElem.textContent = (file.size / 1024).toFixed(1) + ' KB';
            promptElem.classList.add('hidden');
            previewElem.classList.remove('hidden');
        }
    }

    function removeEvaluasiSelectedFile() {
        const input = document.getElementById('file_lampiran_evaluasi');
        if (input) {
            input.value = '';
        }
        const promptElem = document.getElementById('evaluasi-dropzone-prompt');
        const previewElem = document.getElementById('evaluasi-file-preview');
        if (promptElem && previewElem) {
            previewElem.classList.add('hidden');
            promptElem.classList.remove('hidden');
        }
    }

    function toggleDeleteExistingLampiran() {
        const checkbox = document.getElementById('hapus_lampiran_evaluasi');
        if (checkbox) {
            checkbox.checked = !checkbox.checked;
            toggleDeleteExistingLampiranState();
        }
    }

    function toggleDeleteExistingLampiranState() {
        const checkbox = document.getElementById('hapus_lampiran_evaluasi');
        const overlay = document.getElementById('existing-lampiran-overlay');
        const btn = document.getElementById('btn-delete-existing-lampiran');

        if (checkbox && checkbox.checked) {
            overlay.classList.remove('hidden');
            btn.className = 'p-1.5 bg-gray-800 hover:bg-gray-900 text-white rounded-full flex items-center justify-center shadow transition-transform transform hover:scale-110 flex-shrink-0 z-20';
            btn.title = 'Batal Hapus';
            btn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
            `;
        } else if (checkbox) {
            overlay.classList.add('hidden');
            btn.className = 'p-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-full flex items-center justify-center shadow transition-transform transform hover:scale-110 flex-shrink-0 z-20';
            btn.title = 'Hapus lampiran ini';
            btn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            `;
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
