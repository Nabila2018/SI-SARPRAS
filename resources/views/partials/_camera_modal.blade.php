<!-- Modal Live Camera Component SI-SARPRAS (Vanilla Web API getUserMedia) -->
<div id="siSarprasCameraModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4 overflow-y-auto"
     onclick="if(event.target === this) closeSiSarprasCamera()">
    <div class="relative w-full max-w-lg bg-slate-900 rounded-2xl shadow-2xl overflow-hidden flex flex-col border border-slate-800" onclick="event.stopPropagation()">
        
        <!-- Header Modal Kamera -->
        <div class="bg-gradient-to-r from-[#114F72] to-[#16A394] px-5 py-3.5 flex items-center justify-between text-white shadow-md">
            <div class="flex items-center gap-2.5">
                <div class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </div>
                <h3 class="text-sm font-bold tracking-wide">Ambil Foto SI-SARPRAS</h3>
                <span id="cameraFacingBadge" class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full font-medium ml-1">Kamera Belakang</span>
            </div>
            <button type="button" onclick="closeSiSarprasCamera()" class="text-white/80 hover:text-white transition p-1 rounded-lg hover:bg-white/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Viewport Camera Stream & Preview Overlay -->
        <div class="relative bg-black flex items-center justify-center min-h-[320px] sm:min-h-[380px] overflow-hidden">
            <!-- Video Stream Live -->
            <video id="siSarprasCameraVideo" autoplay playsinline muted class="w-full h-full object-cover min-h-[320px] sm:min-h-[380px] max-h-[65vh]"></video>

            <!-- Image Preview Overlay (Tampil Saat Foto Di-Capture) -->
            <img id="siSarprasCameraCapturedPreview" src="" alt="Pratinjau Hasil Capture" class="hidden absolute inset-0 w-full h-full object-cover z-25">

            <!-- Grid Overlay Guide (Saat Live Stream) -->
            <div id="cameraGridOverlay" class="absolute inset-0 border border-white/10 pointer-events-none grid grid-cols-3 grid-rows-3">
                <div class="border-r border-b border-white/10"></div>
                <div class="border-r border-b border-white/10"></div>
                <div class="border-b border-white/10"></div>
                <div class="border-r border-b border-white/10"></div>
                <div class="border-r border-b border-white/10"></div>
                <div class="border-b border-white/10"></div>
                <div class="border-r border-white/10"></div>
                <div class="border-r border-white/10"></div>
                <div></div>
            </div>

            <!-- Shutter Flash Animation -->
            <div id="cameraFlashOverlay" class="absolute inset-0 bg-white opacity-0 pointer-events-none transition-opacity duration-100 z-30"></div>

            <!-- Toast / Notification Capture Success -->
            <div id="cameraToastCaptured" class="hidden absolute top-4 bg-emerald-600/90 text-white text-xs font-semibold px-4 py-2 rounded-full shadow-lg backdrop-blur-sm z-40 transition-all transform -translate-y-2">
                Foto berhasil ditambahkan!
            </div>

            <!-- Error State Container (Permission / Device Failure) -->
            <div id="cameraErrorBox" class="hidden absolute inset-0 bg-slate-950/95 flex flex-col items-center justify-center p-6 text-center text-white z-50">
                <div class="w-14 h-14 bg-rose-500/10 rounded-full flex items-center justify-center mb-3 border border-rose-500/20">
                    <svg class="w-7 h-7 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-rose-300 mb-1">Akses Kamera Bermasalah</h4>
                <p id="cameraErrorMessage" class="text-xs text-slate-300 mb-5 leading-relaxed max-w-xs"></p>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="retrySiSarprasCamera()" class="px-4 py-2 bg-[#16A394] hover:bg-[#114F72] text-white text-xs font-semibold rounded-xl transition shadow">
                        Coba Lagi
                    </button>
                    <button type="button" onclick="closeSiSarprasCamera()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium rounded-xl transition border border-slate-700">
                        Gunakan Upload File
                    </button>
                </div>
            </div>
        </div>

        <!-- Strip Thumbnails Foto Terambil Dalam Sesi Kamera -->
        <div id="cameraThumbnailsContainer" class="hidden px-4 py-2.5 bg-slate-950 border-t border-slate-800">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-semibold text-slate-300">Foto Terambil Sesi Ini:</span>
                <span id="cameraThumbnailsCount" class="text-[10px] text-emerald-400 font-bold bg-emerald-950/80 px-2 py-0.5 rounded-full border border-emerald-800/60">0 Foto</span>
            </div>
            <div id="cameraThumbnailsStrip" class="flex items-center gap-2 overflow-x-auto pb-1"></div>
        </div>

        <!-- Shutter & Controls Toolbar (Default State: Live Capture) -->
        <div id="cameraLiveControls" class="p-4 bg-slate-900 border-t border-slate-800 flex flex-col items-center gap-3">
            <div class="flex items-center justify-between w-full max-w-xs px-2">
                <!-- Toggle Facing Mode (Ganti Kamera) -->
                <button type="button" id="btnSwitchCamera" onclick="switchSiSarprasCamera()"
                        class="p-3 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-full transition shadow-md hover:scale-105 active:scale-95 border border-slate-700"
                        title="Ganti Kamera (Depan / Belakang)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>

                <!-- Shutter Capture Button -->
                <button type="button" id="btnCapturePhoto" onclick="captureSiSarprasPhoto()"
                        class="w-16 h-16 bg-white hover:bg-gray-100 border-4 border-[#16A394] rounded-full flex items-center justify-center shadow-xl transition-all transform active:scale-90 hover:scale-105 group"
                        title="Ambil Foto">
                    <div class="w-11 h-11 bg-gradient-to-r from-[#114F72] to-[#16A394] rounded-full group-hover:opacity-90 transition-opacity"></div>
                </button>

                <!-- Done & Close Button -->
                <button type="button" onclick="closeSiSarprasCamera()"
                        class="p-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-full transition shadow-md hover:scale-105 active:scale-95"
                        title="Selesai Mengambil Foto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </div>
            
            <p class="text-[11px] text-slate-400 text-center font-medium">
                Klik tombol bulat di tengah untuk mengcapture foto secara live.
            </p>
        </div>

        <!-- Controls Toolbar (Preview State: Ambil Ulang vs Gunakan Foto) -->
        <div id="cameraPreviewControls" class="hidden p-4 bg-slate-900 border-t border-slate-800 flex items-center justify-center gap-3">
            <button type="button" onclick="retakeCurrentPhoto()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl border border-slate-700 transition shadow">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Ambil Ulang
            </button>
            <button type="button" onclick="acceptCurrentPhoto()"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-[#114F72] to-[#16A394] hover:opacity-95 text-white text-xs font-semibold rounded-xl transition shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Gunakan Foto Ini
            </button>
        </div>

    </div>
</div>

<script>
    let siSarprasCameraStream = null;
    let cameraFacingMode = 'environment';
    let onCameraPhotoCapturedCallback = null;
    let pendingCapturedFile = null;
    let cameraSessionFiles = [];

    /**
     * Membuka Modal Kamera Live
     */
    function openSiSarprasCamera(onCapturedCallback) {
        onCameraPhotoCapturedCallback = onCapturedCallback;
        cameraSessionFiles = [];
        pendingCapturedFile = null;
        updateSessionThumbnailsStrip();

        const modal = document.getElementById('siSarprasCameraModal');
        if (!modal) return;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        showLiveModeUI();
        startSiSarprasCamera();
    }

    /**
     * Menutup Kamera Live
     */
    function closeSiSarprasCamera() {
        stopSiSarprasCameraStream();

        const modal = document.getElementById('siSarprasCameraModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        document.body.style.overflow = '';
    }

    function stopSiSarprasCameraStream() {
        if (siSarprasCameraStream) {
            siSarprasCameraStream.getTracks().forEach(track => track.stop());
            siSarprasCameraStream = null;
        }
        const video = document.getElementById('siSarprasCameraVideo');
        if (video) {
            video.srcObject = null;
        }
    }

    async function startSiSarprasCamera() {
        stopSiSarprasCameraStream();

        const video = document.getElementById('siSarprasCameraVideo');
        const errorBox = document.getElementById('cameraErrorBox');
        const badge = document.getElementById('cameraFacingBadge');

        errorBox.classList.add('hidden');
        if (badge) {
            badge.textContent = cameraFacingMode === 'environment' ? 'Kamera Belakang' : 'Kamera Depan';
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showCameraError("Perangkat atau browser Anda tidak mendukung akses kamera secara langsung (getUserMedia).");
            return;
        }

        const constraints = {
            video: {
                facingMode: { ideal: cameraFacingMode },
                width: { ideal: 1920 },
                height: { ideal: 1080 }
            },
            audio: false
        };

        try {
            siSarprasCameraStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = siSarprasCameraStream;
            await video.play();
        } catch (err) {
            console.error("Camera access error:", err);
            let message = "Gagal menghubungkan ke kamera.";
            if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                message = "Izin kamera ditolak. Silakan izinkan akses kamera pada pengaturan perizinan browser Anda.";
            } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                message = "Tidak ada perangkat kamera yang ditemukan di perangkat Anda.";
            } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
                message = "Kamera sedang dipakai oleh aplikasi lain. Tutup aplikasi tersebut terlebih dahulu.";
            } else if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                message = "Akses kamera membutuhkan protokol HTTPS atau localhost.";
            }
            showCameraError(message);
        }
    }

    function showCameraError(message) {
        const errorBox = document.getElementById('cameraErrorBox');
        const errorMsg = document.getElementById('cameraErrorMessage');
        if (errorMsg) errorMsg.textContent = message;
        if (errorBox) errorBox.classList.remove('hidden');
    }

    function retrySiSarprasCamera() {
        startSiSarprasCamera();
    }

    function switchSiSarprasCamera() {
        cameraFacingMode = (cameraFacingMode === 'environment') ? 'user' : 'environment';
        startSiSarprasCamera();
    }

    /**
     * Snapshot frame video kamera live -> Preview Mode
     */
    function captureSiSarprasPhoto() {
        const video = document.getElementById('siSarprasCameraVideo');
        if (!video || !siSarprasCameraStream) {
            alert('Stream kamera belum siap.');
            return;
        }

        const width = video.videoWidth || 1280;
        const height = video.videoHeight || 720;

        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;

        const ctx = canvas.getContext('2d');
        if (cameraFacingMode === 'user') {
            ctx.translate(width, 0);
            ctx.scale(-1, 1);
        }
        ctx.drawImage(video, 0, 0, width, height);

        // Flash effect
        const flash = document.getElementById('cameraFlashOverlay');
        if (flash) {
            flash.style.opacity = '0.9';
            setTimeout(() => { flash.style.opacity = '0'; }, 150);
        }

        canvas.toBlob(function(blob) {
            if (!blob) return;

            const filename = `kamera_${Date.now()}.jpg`;
            pendingCapturedFile = new File([blob], filename, {
                type: 'image/jpeg',
                lastModified: Date.now()
            });

            // Tampilkan foto hasil capture pada <img id="siSarprasCameraCapturedPreview">
            const previewImg = document.getElementById('siSarprasCameraCapturedPreview');
            if (previewImg) {
                previewImg.src = canvas.toDataURL('image/jpeg', 0.90);
                previewImg.classList.remove('hidden');
            }

            showPreviewModeUI();
        }, 'image/jpeg', 0.90);
    }

    /**
     * Pengguna memilih "Ambil Ulang" -> Buang pending file, kembali ke live stream
     */
    function retakeCurrentPhoto() {
        pendingCapturedFile = null;
        showLiveModeUI();
    }

    /**
     * Pengguna memilih "Gunakan Foto Ini" -> Konfirmasi file ke callback form & strip thumbnail
     */
    function acceptCurrentPhoto() {
        if (!pendingCapturedFile) return;

        // Tambah ke array sesi
        cameraSessionFiles.push(pendingCapturedFile);

        // Panggil callback pembawa file di halaman form host
        if (typeof onCameraPhotoCapturedCallback === 'function') {
            onCameraPhotoCapturedCallback(pendingCapturedFile);
        }

        updateSessionThumbnailsStrip();

        // Toast sukses
        const toast = document.getElementById('cameraToastCaptured');
        if (toast) {
            toast.classList.remove('hidden');
            toast.classList.remove('-translate-y-2');
            setTimeout(() => {
                toast.classList.add('hidden');
                toast.classList.add('-translate-y-2');
            }, 2000);
        }

        pendingCapturedFile = null;
        showLiveModeUI();
    }

    function showLiveModeUI() {
        const previewImg = document.getElementById('siSarprasCameraCapturedPreview');
        const gridOverlay = document.getElementById('cameraGridOverlay');
        const liveControls = document.getElementById('cameraLiveControls');
        const previewControls = document.getElementById('cameraPreviewControls');

        if (previewImg) previewImg.classList.add('hidden');
        if (gridOverlay) gridOverlay.classList.remove('hidden');
        if (liveControls) liveControls.classList.remove('hidden');
        if (previewControls) previewControls.classList.add('hidden');
    }

    function showPreviewModeUI() {
        const gridOverlay = document.getElementById('cameraGridOverlay');
        const liveControls = document.getElementById('cameraLiveControls');
        const previewControls = document.getElementById('cameraPreviewControls');

        if (gridOverlay) gridOverlay.classList.add('hidden');
        if (liveControls) liveControls.classList.add('hidden');
        if (previewControls) previewControls.classList.remove('hidden');
    }

    function updateSessionThumbnailsStrip() {
        const container = document.getElementById('cameraThumbnailsContainer');
        const strip = document.getElementById('cameraThumbnailsStrip');
        const countSpan = document.getElementById('cameraThumbnailsCount');

        if (!container || !strip || !countSpan) return;

        if (cameraSessionFiles.length === 0) {
            container.classList.add('hidden');
            strip.innerHTML = '';
            countSpan.textContent = '0 Foto';
            return;
        }

        container.classList.remove('hidden');
        countSpan.textContent = `${cameraSessionFiles.length} Foto Terambil`;
        strip.innerHTML = '';

        cameraSessionFiles.forEach((file, idx) => {
            const thumb = document.createElement('div');
            thumb.className = 'relative flex-shrink-0 w-12 h-12 rounded-lg overflow-hidden border border-slate-700 bg-slate-900 group';
            
            const reader = new FileReader();
            reader.onload = function(e) {
                thumb.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover" alt="Foto ${idx+1}">
                    <span class="absolute bottom-0 inset-x-0 bg-black/60 text-[9px] text-white text-center font-semibold">#${idx+1}</span>
                `;
            };
            reader.readAsDataURL(file);
            strip.appendChild(thumb);
        });
    }
</script>
