<!-- Deskripsi Kerusakan & Kondisi Diharapkan -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
    <div>
        <h3 class="text-xs uppercase tracking-wider font-semibold text-gray-400 mb-2">Deskripsi Kerusakan</h3>
        <p class="text-sm text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100 whitespace-pre-line">
            {{ $laporan->deskripsi_kerusakan ?: 'Tidak ada deskripsi kerusakan.' }}
        </p>
    </div>

    @if($laporan->kondisi_diharapkan)
        <div>
            <h3 class="text-xs uppercase tracking-wider font-semibold text-gray-400 mb-2">Kondisi yang Diharapkan</h3>
            <p class="text-sm text-gray-700 leading-relaxed bg-blue-50/50 p-4 rounded-xl border border-blue-100/50 whitespace-pre-line">
                {{ $laporan->kondisi_diharapkan }}
            </p>
        </div>
    @endif
</div>

<!-- Foto Dokumentasi Laporan -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-base font-bold text-gray-800 mb-4">Foto Dokumentasi Laporan</h3>

    @if($laporan->fotoLaporan && $laporan->fotoLaporan->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            @foreach($laporan->fotoLaporan as $index => $foto)
                <button type="button"
                        onclick="openFotoModal({{ $index }})"
                        class="group relative aspect-video overflow-hidden rounded-xl bg-gray-100 border border-gray-200 focus:outline-none">
                    <img src="{{ asset('storage/' . $foto->file_foto) }}"
                         alt="Foto Dokumentasi"
                         class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold">
                        Lihat Foto
                    </div>
                </button>
            @endforeach
        </div>
    @else
        <div class="rounded-xl bg-gray-50 border border-gray-100 p-6 text-sm text-gray-500 text-center">
            Tidak ada foto dokumentasi yang diunggah pelapor.
        </div>
    @endif
</div>

@if($laporan->fotoLaporan && $laporan->fotoLaporan->count() > 0)
<!-- Modal Lightbox Foto -->
<div id="fotoModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 px-4"
     onclick="if(event.target === this) closeFotoModal()">
    <button type="button"
            onclick="closeFotoModal()"
            class="absolute top-4 right-4 text-white/80 hover:text-white transition">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <button type="button"
            id="fotoPrevBtn"
            onclick="showPrevFoto()"
            class="absolute left-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white transition">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
    <img id="fotoModalImg"
         src=""
         alt="Foto Dokumentasi"
         class="max-h-[85vh] max-w-full rounded-lg shadow-2xl">
    <button type="button"
            id="fotoNextBtn"
            onclick="showNextFoto()"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white transition">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
    <div id="fotoCounter" class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/80 text-sm"></div>
</div>

<script>
    const fotoList = @json($laporan->fotoLaporan->map(fn($f) => asset('storage/' . $f->file_foto))->values());
    let fotoIndex = 0;

    function updateFotoModal() {
        document.getElementById('fotoModalImg').src = fotoList[fotoIndex];
        document.getElementById('fotoCounter').textContent = (fotoIndex + 1) + ' / ' + fotoList.length;

        const showNav = fotoList.length > 1;
        document.getElementById('fotoPrevBtn').style.display = showNav ? 'block' : 'none';
        document.getElementById('fotoNextBtn').style.display = showNav ? 'block' : 'none';
    }

    function openFotoModal(index) {
        fotoIndex = index;
        updateFotoModal();
        const modal = document.getElementById('fotoModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeFotoModal() {
        const modal = document.getElementById('fotoModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function showPrevFoto() {
        fotoIndex = (fotoIndex - 1 + fotoList.length) % fotoList.length;
        updateFotoModal();
    }

    function showNextFoto() {
        fotoIndex = (fotoIndex + 1) % fotoList.length;
        updateFotoModal();
    }
</script>
@endif
