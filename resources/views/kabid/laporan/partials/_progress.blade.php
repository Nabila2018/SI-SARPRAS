@php
    $existingProgress = $laporan->progresPerbaikan ? $laporan->progresPerbaikan->sortBy('persentase_penyelesaian') : collect();
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4">
        <div>
            <h3 class="text-base font-bold text-gray-800">Progres Perbaikan Fasilitas</h3>
            <p class="text-xs text-gray-500 mt-0.5">Catatan perkembangan fisik perbaikan fasilitas secara bertahap (0%, 50%, 100%).</p>
        </div>
    </div>

    @if($existingProgress->isEmpty())
        <div class="rounded-xl bg-gray-50 border border-gray-100 p-8 text-center text-sm text-gray-500">
            Belum ada progres perbaikan yang dicatat untuk laporan ini.
        </div>
    @else
        <div class="relative border-l-2 border-gray-200 ml-4 space-y-8 my-4">
            @foreach($existingProgress as $progres)
                <div class="relative pl-6">
                    <div class="absolute -left-[9px] top-1 h-4 w-4 rounded-full border-2 border-white {{ $progres->persentase_penyelesaian == '100' ? 'bg-emerald-500' : ($progres->persentase_penyelesaian == '50' ? 'bg-blue-500' : 'bg-amber-500') }}"></div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-5 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold {{ $progres->persentase_penyelesaian == '100' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : ($progres->persentase_penyelesaian == '50' ? 'bg-blue-100 text-blue-800 border-blue-200' : 'bg-amber-100 text-amber-800 border-amber-200') }}">
                                Tahap {{ $progres->persentase_penyelesaian }}%
                            </span>
                            <span class="text-xs text-gray-500 font-medium">
                                {{ \Carbon\Carbon::parse($progres->tanggal_update)->translatedFormat('d F Y H:i') }} WIB
                            </span>
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
                                <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Foto Dokumen Progres ({{ $fotoCount }} Foto)</p>
                                <div class="flex flex-wrap items-center gap-3">
                                    <button type="button"
                                            onclick='openKabidMultiFotoModal(@json($fotoUrls), "Tahap {{ $progres->persentase_penyelesaian }}%")'
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

<!-- Modal Lightbox Galeri Multi Foto Kabid -->
<div id="kabidMultiFotoModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 px-4"
     onclick="if(event.target === this) closeKabidMultiFotoModal()">
    <button type="button" onclick="closeKabidMultiFotoModal()" class="absolute top-4 right-4 text-white/80 hover:text-white transition">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    
    <div class="flex flex-col items-center space-y-4 max-w-4xl w-full">
        <div class="text-white text-center">
            <h4 id="kabidMultiFotoTitle" class="text-base font-bold">Galeri Foto Progres</h4>
            <p id="kabidMultiFotoCounter" class="text-xs text-gray-300 mt-0.5">Foto 1 dari 1</p>
        </div>

        <div class="relative flex items-center justify-center w-full">
            <button type="button" id="kabidMultiFotoPrevBtn" onclick="prevKabidMultiFoto()" class="absolute left-2 text-white/80 hover:text-white p-2 rounded-full bg-black/40 transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <img id="kabidMultiFotoImg" src="" alt="Pratinjau Foto Progres" class="max-h-[70vh] max-w-full rounded-lg shadow-2xl">

            <button type="button" id="kabidMultiFotoNextBtn" onclick="nextKabidMultiFoto()" class="absolute right-2 text-white/80 hover:text-white p-2 rounded-full bg-black/40 transition">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        <div id="kabidMultiFotoThumbnails" class="flex items-center gap-2 overflow-x-auto p-1 max-w-full"></div>
    </div>
</div>

<script>
    let kabidActiveFotoList = [];
    let kabidActiveFotoIndex = 0;

    function openKabidMultiFotoModal(urls, title) {
        kabidActiveFotoList = urls;
        kabidActiveFotoIndex = 0;
        document.getElementById('kabidMultiFotoTitle').textContent = title || 'Galeri Foto Progres';
        updateKabidMultiFotoDisplay();

        const modal = document.getElementById('kabidMultiFotoModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeKabidMultiFotoModal() {
        const modal = document.getElementById('kabidMultiFotoModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function updateKabidMultiFotoDisplay() {
        if (!kabidActiveFotoList || kabidActiveFotoList.length === 0) return;

        document.getElementById('kabidMultiFotoImg').src = kabidActiveFotoList[kabidActiveFotoIndex];
        document.getElementById('kabidMultiFotoCounter').textContent = `Foto ${kabidActiveFotoIndex + 1} dari ${kabidActiveFotoList.length}`;

        const showNav = kabidActiveFotoList.length > 1;
        document.getElementById('kabidMultiFotoPrevBtn').style.display = showNav ? 'block' : 'none';
        document.getElementById('kabidMultiFotoNextBtn').style.display = showNav ? 'block' : 'none';

        const thumbContainer = document.getElementById('kabidMultiFotoThumbnails');
        thumbContainer.innerHTML = '';

        if (showNav) {
            kabidActiveFotoList.forEach((url, idx) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = `w-12 h-12 rounded-lg overflow-hidden border-2 transition ${idx === kabidActiveFotoIndex ? 'border-white opacity-100 scale-105' : 'border-transparent opacity-60 hover:opacity-100'}`;
                btn.onclick = () => {
                    kabidActiveFotoIndex = idx;
                    updateKabidMultiFotoDisplay();
                };
                btn.innerHTML = `<img src="${url}" class="w-full h-full object-cover" alt="Thumb ${idx+1}">`;
                thumbContainer.appendChild(btn);
            });
        }
    }

    function prevKabidMultiFoto() {
        if (kabidActiveFotoList.length <= 1) return;
        kabidActiveFotoIndex = (kabidActiveFotoIndex - 1 + kabidActiveFotoList.length) % kabidActiveFotoList.length;
        updateKabidMultiFotoDisplay();
    }

    function nextKabidMultiFoto() {
        if (kabidActiveFotoList.length <= 1) return;
        kabidActiveFotoIndex = (kabidActiveFotoIndex + 1) % kabidActiveFotoList.length;
        updateKabidMultiFotoDisplay();
    }
</script>
