<!-- Modal Lightbox Pratinjau Foto (SI-SARPRAS Global) -->
<div id="siSarprasPhotoLightbox"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/85 backdrop-blur-md p-4 transition-all duration-200"
     onclick="if(event.target === this) closeSiSarprasPhotoLightbox()">
    <div class="relative max-w-4xl max-h-[90vh] w-full bg-slate-900 rounded-2xl shadow-2xl overflow-hidden flex flex-col border border-slate-800" onclick="event.stopPropagation()">
        <!-- Header Lightbox -->
        <div class="bg-slate-900/90 px-5 py-3.5 flex items-center justify-between border-b border-slate-800 text-white">
            <div class="flex items-center gap-2 overflow-hidden">
                <svg class="w-5 h-5 text-[#16A394] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 id="siSarprasLightboxTitle" class="text-sm font-semibold truncate text-slate-200">Pratinjau Foto</h3>
            </div>
            <button type="button" onclick="closeSiSarprasPhotoLightbox()" class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <!-- Body Lightbox Image -->
        <div class="relative bg-black flex items-center justify-center p-2 min-h-[300px] max-h-[75vh] overflow-auto">
            <img id="siSarprasLightboxImage" src="" alt="Pratinjau Foto" class="max-w-full max-h-[72vh] object-contain rounded-lg shadow-xl">
        </div>
        <!-- Footer Lightbox -->
        <div class="px-5 py-3 bg-slate-900 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
            <span>Klik di mana saja di luar gambar untuk menutup</span>
            <button type="button" onclick="closeSiSarprasPhotoLightbox()" class="px-4 py-1.5 bg-[#16A394] hover:bg-[#114F72] text-white font-semibold rounded-lg transition shadow">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function openSiSarprasPhotoLightbox(imgSrc, titleText) {
        const modal = document.getElementById('siSarprasPhotoLightbox');
        const img = document.getElementById('siSarprasLightboxImage');
        const title = document.getElementById('siSarprasLightboxTitle');

        if (!modal || !img) return;

        img.src = imgSrc;
        if (title) title.textContent = titleText || 'Pratinjau Foto';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeSiSarprasPhotoLightbox() {
        const modal = document.getElementById('siSarprasPhotoLightbox');
        if (!modal) return;

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSiSarprasPhotoLightbox();
        }
    });
</script>
