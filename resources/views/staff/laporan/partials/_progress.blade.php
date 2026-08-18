@php
    $existingProgress = $laporan->progresPerbaikan ? $laporan->progresPerbaikan->sortBy('persentase_penyelesaian') : collect();
    $existingStages = $existingProgress->pluck('persentase_penyelesaian')->toArray();
    $isRabApproved = $laporan->status_verifikasi_rab === 'Disetujui';

    $nextStage = null;
    if (!in_array('0', $existingStages)) {
        $nextStage = '0';
    } elseif (!in_array('50', $existingStages)) {
        $nextStage = '50';
    } elseif (!in_array('100', $existingStages)) {
        $nextStage = '100';
    }
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
    <!-- Header Card -->
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4">
        <div>
            <h3 class="text-base font-bold text-gray-800">Progres Perbaikan Fasilitas</h3>
            <p class="text-xs text-gray-500 mt-0.5">Catatan perkembangan fisik perbaikan fasilitas secara bertahap (0%, 50%, 100%).</p>
        </div>

        @if($isRabApproved && $nextStage !== null)
            <button type="button"
                    onclick="openProgresModal()"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#114F72] to-[#16A394] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:opacity-90 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Progres (Tahap {{ $nextStage }}%)
            </button>
        @endif
    </div>

    @if(!$isRabApproved)
        <div class="rounded-xl bg-amber-50/60 border border-amber-200/60 p-6 text-center text-sm text-amber-800">
            <p class="font-semibold">Progres perbaikan belum dapat ditambahkan.</p>
            <p class="text-xs text-amber-600 mt-1">Rencana Anggaran Biaya (RAB) harus disetujui oleh Kepala Bidang terlebih dahulu.</p>
        </div>
    @elseif($existingProgress->isEmpty())
        <div class="rounded-xl bg-gray-50 border border-gray-100 p-8 text-center text-sm text-gray-500">
            Belum ada progres perbaikan yang dicatat untuk laporan ini.
        </div>
    @else
        <!-- Progress Timeline -->
        <div class="relative border-l-2 border-gray-200 ml-4 space-y-8 my-4">
            @foreach($existingProgress as $progres)
                <div class="relative pl-6">
                    <!-- Dot Marker -->
                    <div class="absolute -left-[9px] top-1 h-4 w-4 rounded-full border-2 border-white {{ $progres->persentase_penyelesaian == '100' ? 'bg-emerald-500' : ($progres->persentase_penyelesaian == '50' ? 'bg-blue-500' : 'bg-amber-500') }}"></div>

                    <!-- Progress Card Content -->
                    <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-5 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold {{ $progres->persentase_penyelesaian == '100' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : ($progres->persentase_penyelesaian == '50' ? 'bg-blue-100 text-blue-800 border-blue-200' : 'bg-amber-100 text-amber-800 border-amber-200') }}">
                                    Tahap {{ $progres->persentase_penyelesaian }}%
                                </span>
                                <span class="text-xs text-gray-500 font-medium">
                                    {{ \Carbon\Carbon::parse($progres->tanggal_update)->translatedFormat('d F Y H:i') }} WIB
                                </span>
                            </div>

                            <button type="button"
                                    onclick='openEditProgresModal(@json($progres->load("fotoProgres")))'
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-[#114F72] bg-[#114F72]/5 hover:bg-[#114F72]/10 rounded-lg transition-colors border border-[#114F72]/20">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </button>
                        </div>

                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-1">Keterangan Perkembangan</p>
                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $progres->keterangan_perkembangan }}</p>
                        </div>

                        @if($progres->fotoProgres && $progres->fotoProgres->count() > 0)
                            @php
                                $fotoCount = $progres->fotoProgres->count();
                                $fotoUrls = $progres->fotoProgres->map(fn($f) => asset('storage/' . $f->file_foto))->values();
                            @endphp

                            <div>
                                <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Foto Dokumentasi ({{ $fotoCount }} Foto)</p>
                                <div class="flex flex-wrap items-center gap-3">
                                    <button type="button"
                                            onclick='openProgresMultiFotoModal(@json($fotoUrls), "Tahap {{ $progres->persentase_penyelesaian }}%")'
                                            class="group relative aspect-video w-48 overflow-hidden rounded-xl bg-gray-100 border border-gray-200 shadow-sm focus:outline-none">
                                        <img src="{{ $fotoUrls[0] }}" alt="Foto Utama" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                        
                                        @if($fotoCount > 1)
                                            <div class="absolute inset-0 bg-black/50 group-hover:bg-black/60 transition flex flex-col items-center justify-center text-white">
                                                <span class="text-sm font-bold">+{{ $fotoCount - 1 }} foto lainnya</span>
                                                <span class="text-[10px] text-white/80">Klik untuk lihat galeri</span>
                                            </div>
                                        @else
                                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold">
                                                Lihat Foto
                                            </div>
                                        @endif
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@if($isRabApproved && $nextStage !== null)
<!-- Modal Input Progres Baru -->
<div id="progresModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeProgresModal()">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4 border-b pb-3">
            <h3 class="text-lg font-bold text-gray-800">Input Progres Perbaikan Tahap {{ $nextStage }}%</h3>
            <button type="button" onclick="closeProgresModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('staff.laporan.progres.store', $laporan->id_laporan) }}?tab=progress" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-600 mb-1">Keterangan Perkembangan <span class="text-red-500">*</span></label>
                <textarea name="keterangan_perkembangan" rows="4" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] text-sm" placeholder="Jelaskan detail pekerjaan fisik yang sudah dilakukan pada tahap ini..."></textarea>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-600 mb-1">Unggah Foto Progres <span class="text-red-500">*</span></label>
                <input type="file"
                       id="progresFileInput"
                       name="foto_progres[]"
                       multiple
                       accept="image/jpeg,image/png,image/jpg"
                       onchange="handleProgresFileSelect(this)"
                       required
                       class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#114F72] hover:file:bg-blue-100">
                <ul class="text-xs text-gray-400 mt-1.5 space-y-0.5 list-disc list-inside">
                    <li>Minimal 1 foto, maksimal 5 foto.</li>
                    <li>Format berkas: JPG, JPEG, PNG.</li>
                    <li>Ukuran maksimal 4 MB per foto.</li>
                </ul>
                <div id="fileSelectSummary" class="mt-2 text-xs font-semibold text-[#114F72] hidden"></div>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-3 border-t">
                <button type="button" onclick="closeProgresModal()" class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
                <button type="submit" class="rounded-xl bg-gradient-to-r from-[#114F72] to-[#16A394] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">Simpan Progres</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Modal Edit Progres -->
<div id="editProgresModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeEditProgresModal()">
    <div class="w-full max-w-2xl rounded-2xl bg-white p-6 sm:p-8 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4 border-b pb-3">
            <h3 id="editProgresModalTitle" class="text-lg font-bold text-gray-800">Edit Progres Perbaikan</h3>
            <button type="button" onclick="closeEditProgresModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="editProgresForm" action="" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-600 mb-1.5">Keterangan Perkembangan <span class="text-red-500">*</span></label>
                <textarea name="keterangan_perkembangan" id="edit_keterangan_perkembangan" rows="4" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] text-sm p-4" placeholder="Jelaskan detail pekerjaan fisik yang sudah dilakukan pada tahap ini..."></textarea>
            </div>

            <!-- Pengelolaan Foto Progres Saat Ini -->
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-600 mb-1.5">Foto Dokumentasi Saat Ini</label>
                <div id="editProgresExistingPhotos" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-2"></div>
            </div>

            <!-- Tambah Foto Progres Baru -->
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-600 mb-1.5">Tambah Foto Progres Baru (Opsional)</label>
                <input type="file" name="foto_progres[]" id="editProgresFileInput" multiple accept="image/jpeg,image/png,image/jpg" class="hidden" onchange="handleEditProgresFileSelect(this)">
                <div id="edit-progres-dropzone-box" class="border-2 border-dashed border-gray-200 rounded-2xl p-4 transition-all bg-gray-50/50 hover:border-[#114F72]">
                    <div id="edit-progres-dropzone-prompt" class="py-5 text-center cursor-pointer" onclick="document.getElementById('editProgresFileInput').click()">
                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm font-semibold text-gray-700 mb-0.5">Klik atau drag foto baru ke sini</p>
                        <p class="text-xs text-gray-400">Format: JPG, PNG (Maks. 4MB per foto, Maks. 5 foto baru)</p>
                    </div>
                    <div id="edit-progres-file-preview" class="hidden grid grid-cols-2 sm:grid-cols-4 gap-3"></div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeEditProgresModal()" class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
                <button type="submit" class="rounded-xl bg-gradient-to-r from-[#114F72] to-[#16A394] px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:opacity-90 transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Lightbox Galeri Multi Foto -->
<div id="progresMultiFotoModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 px-4"
     onclick="if(event.target === this) closeProgresMultiFotoModal()">
    <button type="button" onclick="closeProgresMultiFotoModal()" class="absolute top-4 right-4 text-white/80 hover:text-white transition">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    
    <div class="flex flex-col items-center space-y-4 max-w-4xl w-full">
        <div class="text-white text-center">
            <h4 id="multiFotoTitle" class="text-base font-bold">Galeri Foto Progres</h4>
            <p id="multiFotoCounter" class="text-xs text-gray-300 mt-0.5">Foto 1 dari 1</p>
        </div>

        <div class="relative flex items-center justify-center w-full">
            <button type="button" id="multiFotoPrevBtn" onclick="prevMultiFoto()" class="absolute left-2 text-white/80 hover:text-white p-2 rounded-full bg-black/40 transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <img id="multiFotoImg" src="" alt="Pratinjau Foto Progres" class="max-h-[70vh] max-w-full rounded-lg shadow-2xl">

            <button type="button" id="multiFotoNextBtn" onclick="nextMultiFoto()" class="absolute right-2 text-white/80 hover:text-white p-2 rounded-full bg-black/40 transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        <div id="multiFotoThumbnails" class="flex items-center gap-2 overflow-x-auto p-1 max-w-full"></div>
    </div>
</div>

<script>
    let activeFotoList = [];
    let activeFotoIndex = 0;

    function handleProgresFileSelect(input) {
        const summaryDiv = document.getElementById('fileSelectSummary');
        if (!summaryDiv) return;

        const files = input.files;
        if (!files || files.length === 0) {
            summaryDiv.classList.add('hidden');
            summaryDiv.innerHTML = '';
            return;
        }

        if (files.length > 5) {
            alert('Maksimal 5 foto yang dapat dipilih sekaligus.');
            input.value = '';
            summaryDiv.classList.add('hidden');
            return;
        }

        const names = Array.from(files).map(f => f.name).join(', ');
        summaryDiv.classList.remove('hidden');
        summaryDiv.innerHTML = `Terpilih ${files.length} berkas: <span class="font-normal text-gray-600">${names}</span>`;
    }

    function openProgresModal() {
        const modal = document.getElementById('progresModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeProgresModal() {
        const modal = document.getElementById('progresModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function openProgresMultiFotoModal(urls, title) {
        activeFotoList = urls;
        activeFotoIndex = 0;
        document.getElementById('multiFotoTitle').textContent = title || 'Galeri Foto Progres';
        updateMultiFotoDisplay();

        const modal = document.getElementById('progresMultiFotoModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeProgresMultiFotoModal() {
        const modal = document.getElementById('progresMultiFotoModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function updateMultiFotoDisplay() {
        if (!activeFotoList || activeFotoList.length === 0) return;

        document.getElementById('multiFotoImg').src = activeFotoList[activeFotoIndex];
        document.getElementById('multiFotoCounter').textContent = `Foto ${activeFotoIndex + 1} dari ${activeFotoList.length}`;

        const showNav = activeFotoList.length > 1;
        document.getElementById('multiFotoPrevBtn').style.display = showNav ? 'block' : 'none';
        document.getElementById('multiFotoNextBtn').style.display = showNav ? 'block' : 'none';

        const thumbContainer = document.getElementById('multiFotoThumbnails');
        thumbContainer.innerHTML = '';

        if (showNav) {
            activeFotoList.forEach((url, idx) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = `w-12 h-12 rounded-lg overflow-hidden border-2 transition ${idx === activeFotoIndex ? 'border-white opacity-100 scale-105' : 'border-transparent opacity-60 hover:opacity-100'}`;
                btn.onclick = () => {
                    activeFotoIndex = idx;
                    updateMultiFotoDisplay();
                };
                btn.innerHTML = `<img src="${url}" class="w-full h-full object-cover" alt="Thumb ${idx+1}">`;
                thumbContainer.appendChild(btn);
            });
        }
    }

    function prevMultiFoto() {
        if (activeFotoList.length <= 1) return;
        activeFotoIndex = (activeFotoIndex - 1 + activeFotoList.length) % activeFotoList.length;
        updateMultiFotoDisplay();
    }

    function nextMultiFoto() {
        if (activeFotoList.length <= 1) return;
        activeFotoIndex = (activeFotoIndex + 1) % activeFotoList.length;
        updateMultiFotoDisplay();
    }

    // Modal Edit Progres Logic
    let editProgresSelectedFilesArray = [];

    function openEditProgresModal(progres) {
        const modal = document.getElementById('editProgresModal');
        const form = document.getElementById('editProgresForm');
        const title = document.getElementById('editProgresModalTitle');
        const ketInput = document.getElementById('edit_keterangan_perkembangan');
        const existingPhotosDiv = document.getElementById('editProgresExistingPhotos');

        if (!modal || !progres) return;

        title.textContent = `Edit Progres Perbaikan Tahap ${progres.persentase_penyelesaian}%`;
        ketInput.value = progres.keterangan_perkembangan;
        form.action = `/staff/laporan/${progres.id_laporan}/progres/${progres.id_progres}?tab=progress`;

        existingPhotosDiv.innerHTML = '';
        if (progres.foto_progres && progres.foto_progres.length > 0) {
            progres.foto_progres.forEach(function(foto) {
                const card = document.createElement('div');
                card.id = `edit-card-foto-${foto.id_foto_progres}`;
                card.className = 'relative group rounded-xl overflow-hidden border border-gray-200 bg-gray-50 transition-all shadow-sm';
                card.innerHTML = `
                    <input type="checkbox" name="hapus_foto[]" id="edit_hapus_foto_${foto.id_foto_progres}" value="${foto.id_foto_progres}" class="hidden" onchange="toggleEditDeleteFotoState('${foto.id_foto_progres}')">
                    <div class="relative h-24 w-full">
                        <img src="/storage/${foto.file_foto}" alt="Foto Progres" class="w-full h-full object-cover">
                        <div id="edit-overlay-foto-${foto.id_foto_progres}" class="hidden absolute inset-0 bg-rose-900/60 flex items-center justify-center backdrop-blur-[1px]">
                            <span class="text-white text-[10px] font-bold bg-rose-600 px-2 py-0.5 rounded-full shadow">Akan Dihapus</span>
                        </div>
                        <button type="button" onclick="toggleEditDeleteFoto('${foto.id_foto_progres}')" id="edit-btn-delete-foto-${foto.id_foto_progres}"
                            class="absolute top-1 right-1 w-5 h-5 bg-rose-600 hover:bg-rose-700 text-white rounded-full flex items-center justify-center shadow transition-transform transform hover:scale-110 z-10"
                            title="Hapus foto ini">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                `;
                existingPhotosDiv.appendChild(card);
            });
        }

        editProgresSelectedFilesArray = [];
        updateEditProgresFileInputAndPreview();

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeEditProgresModal() {
        const modal = document.getElementById('editProgresModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function toggleEditDeleteFoto(id) {
        const checkbox = document.getElementById('edit_hapus_foto_' + id);
        if (checkbox) {
            checkbox.checked = !checkbox.checked;
            toggleEditDeleteFotoState(id);
        }
    }

    function toggleEditDeleteFotoState(id) {
        const checkbox = document.getElementById('edit_hapus_foto_' + id);
        const card = document.getElementById('edit-card-foto-' + id);
        const overlay = document.getElementById('edit-overlay-foto-' + id);
        const btn = document.getElementById('edit-btn-delete-foto-' + id);

        if (checkbox && checkbox.checked) {
            card.classList.add('border-rose-500', 'ring-2', 'ring-rose-400');
            card.classList.remove('border-gray-200');
            overlay.classList.remove('hidden');
            btn.className = 'absolute top-1 right-1 w-5 h-5 bg-gray-800 hover:bg-gray-900 text-white rounded-full flex items-center justify-center shadow transition-transform transform hover:scale-110 z-10';
            btn.title = 'Batal Hapus';
            btn.innerHTML = `
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
            `;
        } else if (checkbox) {
            card.classList.remove('border-rose-500', 'ring-2', 'ring-rose-400');
            card.classList.add('border-gray-200');
            overlay.classList.add('hidden');
            btn.className = 'absolute top-1 right-1 w-5 h-5 bg-rose-600 hover:bg-rose-700 text-white rounded-full flex items-center justify-center shadow transition-transform transform hover:scale-110 z-10';
            btn.title = 'Hapus foto ini';
            btn.innerHTML = `
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            `;
        }
    }

    function handleEditProgresFileSelect(input) {
        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach(function(file) {
                const exists = editProgresSelectedFilesArray.some(function(f) {
                    return f.name === file.name && f.size === file.size;
                });
                if (!exists && editProgresSelectedFilesArray.length < 5) {
                    editProgresSelectedFilesArray.push(file);
                }
            });
            updateEditProgresFileInputAndPreview();
        }
    }

    function removeEditProgresSelectedFile(index) {
        editProgresSelectedFilesArray.splice(index, 1);
        updateEditProgresFileInputAndPreview();
    }

    function updateEditProgresFileInputAndPreview() {
        const input = document.getElementById('editProgresFileInput');
        if (!input) return;

        const dt = new DataTransfer();
        editProgresSelectedFilesArray.forEach(function(file) {
            dt.items.add(file);
        });
        input.files = dt.files;

        const promptElem = document.getElementById('edit-progres-dropzone-prompt');
        const previewElem = document.getElementById('edit-progres-file-preview');

        previewElem.innerHTML = '';

        if (editProgresSelectedFilesArray.length === 0) {
            promptElem.classList.remove('hidden');
            previewElem.classList.add('hidden');
        } else {
            promptElem.classList.add('hidden');
            previewElem.classList.remove('hidden');

            editProgresSelectedFilesArray.forEach(function(file, index) {
                const card = document.createElement('div');
                card.className = 'relative group rounded-xl overflow-hidden border border-gray-200 bg-white shadow-sm';

                const reader = new FileReader();
                reader.onload = function(e) {
                    card.innerHTML = `
                        <div class="relative h-24 w-full bg-gray-100">
                            <img src="${e.target.result}" alt="${file.name}" class="w-full h-full object-cover">
                            <button type="button" onclick="event.stopPropagation(); removeEditProgresSelectedFile(${index})"
                                class="absolute top-1 right-1 w-5 h-5 bg-rose-600 hover:bg-rose-700 text-white rounded-full flex items-center justify-center shadow transition-transform transform hover:scale-110"
                                title="Hapus foto ini">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-1.5 text-[11px]">
                            <p class="font-semibold text-gray-800 truncate" title="${file.name}">${file.name}</p>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
                previewElem.appendChild(card);
            });

            if (editProgresSelectedFilesArray.length < 5) {
                const addTile = document.createElement('div');
                addTile.className = 'border-2 border-dashed border-gray-300 rounded-xl h-[120px] flex flex-col items-center justify-center text-gray-400 hover:border-[#115f8c] hover:text-[#115f8c] transition cursor-pointer bg-white/50 hover:bg-white';
                addTile.onclick = function(e) {
                    e.stopPropagation();
                    input.click();
                };
                addTile.innerHTML = `
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="text-[10px] font-semibold">Tambah</span>
                `;
                previewElem.appendChild(addTile);
            }
        }
    }

    // Drag & Drop for edit progres dropzone
    document.addEventListener('DOMContentLoaded', function() {
        const editDropzoneBox = document.getElementById('edit-progres-dropzone-box');
        if (editDropzoneBox) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(eventName) {
                editDropzoneBox.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(function(eventName) {
                editDropzoneBox.addEventListener(eventName, function() {
                    editDropzoneBox.classList.add('border-[#114F72]', 'bg-[#114F72]/10');
                }, false);
            });

            ['dragleave', 'drop'].forEach(function(eventName) {
                editDropzoneBox.addEventListener(eventName, function() {
                    editDropzoneBox.classList.remove('border-[#114F72]', 'bg-[#114F72]/10');
                }, false);
            });

            editDropzoneBox.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                if (dt && dt.files && dt.files.length > 0) {
                    const fileInput = document.getElementById('editProgresFileInput');
                    if (fileInput) {
                        fileInput.files = dt.files;
                        handleEditProgresFileSelect(fileInput);
                    }
                }
            }, false);
        }
    });
</script>
