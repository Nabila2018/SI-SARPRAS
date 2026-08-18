@extends('layouts.app')

@section('title', 'Buat Laporan - SI-SARPRAS')
@section('breadcrumb', 'Buat Laporan')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Formulir Pelaporan Kerusakan</h1>
        <p class="text-gray-500 mt-1">Lengkapi data di bawah untuk melaporkan kerusakan fasilitas pasar.</p>
    </div>

    <!-- Success/Error Message -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-emerald-700">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Card Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-[#115f8c]/5 to-[#16A394]/5">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#115f8c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Informasi Laporan
            </h2>
        </div>

        <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data" id="form-create-laporan" class="p-8 space-y-6">
            @csrf

                        <!-- Row 1: Pasar & Lokasi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Pasar -->
                <div>
                    <label for="id_pasar" class="block text-sm font-semibold text-gray-700 mb-2">
                        Pasar <span class="text-red-500">*</span>
                    </label>
                    
                    @if(auth()->user()->role->nama_role === 'Petugas UPTD')
                        <!-- UPTD: Pasar readonly (auto-fill) -->
                        <input type="hidden" name="id_pasar" value="{{ $pasarTerpilih }}">
                        <div class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-100 text-gray-700 font-medium">
                            {{ $pasar->first()->nama_pasar ?? '-' }}
                        </div>
                    @else
                        <!-- Staff/Kabid/Kadis: Dropdown normal -->
                        <select name="id_pasar" id="id_pasar" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all bg-white">
                            <option value="">-- Pilih Pasar --</option>
                            @foreach($pasar as $p)
                                <option value="{{ $p->id_pasar }}" {{ old('id_pasar') == $p->id_pasar ? 'selected' : '' }}>
                                    {{ $p->nama_pasar }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    
                    @error('id_pasar')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lokasi -->
                <div>
                    <label for="id_lokasi" class="block text-sm font-semibold text-gray-700 mb-2">
                        Lokasi <span class="text-red-500">*</span>
                    </label>
                    <input type="hidden" name="id_lokasi" id="id_lokasi" value="">
                    <div id="lokasi-wrapper" class="relative">
                        <input type="text" id="lokasi-search" placeholder="Cari atau pilih lokasi..." autocomplete="off" disabled
                            @if(auth()->user()->role->nama_role === 'Petugas UPTD')
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all bg-white"
                            @else
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all bg-gray-50"
                            @endif>
                        <div id="lokasi-options" class="hidden absolute z-20 mt-1 w-full max-h-56 overflow-auto rounded-xl border border-gray-200 bg-white shadow-lg"></div>
                    </div>
                    <div id="lokasi-readonly" class="hidden w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-100 text-gray-700 font-medium"></div>
                    @error('id_lokasi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Row 2: Fasilitas & Kategori -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Fasilitas -->
                <div>
                    <label for="fasilitas-search" class="block text-sm font-semibold text-gray-700 mb-2">
                        Fasilitas <span class="text-red-500">*</span>
                    </label>
                    <input type="hidden" name="id_fasilitas" id="id_fasilitas" value="{{ old('id_fasilitas') }}">
                    <div id="fasilitas-wrapper" class="relative">
                        <input type="text" id="fasilitas-search" placeholder="Cari atau pilih fasilitas..." autocomplete="off" disabled
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all bg-gray-50 disabled:bg-gray-50 disabled:text-gray-400 disabled:cursor-not-allowed">
                        <div id="fasilitas-options" class="hidden absolute z-20 mt-1 w-full max-h-56 overflow-auto rounded-xl border border-gray-200 bg-white shadow-lg"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Pilih hanya jika pilihan yang sesuai tidak tersedia.</p>
                    @error('id_fasilitas')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    <!-- Input Nama Ruang/Fasilitas Lainnya -->
                    <div id="wrapper_fasilitas_lainnya" class="hidden mt-3">
                        <label for="nama_fasilitas_lainnya" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Ruang/Fasilitas Lainnya <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_fasilitas_lainnya" id="nama_fasilitas_lainnya" maxlength="100"
                            value="{{ old('nama_fasilitas_lainnya') }}"
                            placeholder="Contoh: Gudang Alat Kebersihan"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all">
                        @error('nama_fasilitas_lainnya')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Kategori Laporan -->
                <div>
                    <label for="kategori-search" class="block text-sm font-semibold text-gray-700 mb-2">
                        Kategori Laporan <span class="text-red-500">*</span>
                    </label>
                    <input type="hidden" name="kategori_laporan" id="kategori_laporan" value="{{ old('kategori_laporan') }}">
                    <div id="kategori-wrapper" class="relative">
                        <input type="text" id="kategori-search" placeholder="Cari atau pilih kategori..." autocomplete="off"
                            value="{{ old('kategori_laporan') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all bg-white">
                        <div id="kategori-options" class="hidden absolute z-20 mt-1 w-full max-h-56 overflow-auto rounded-xl border border-gray-200 bg-white shadow-lg"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Pilih hanya jika pilihan yang sesuai tidak tersedia.</p>
                    @error('kategori_laporan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    <!-- Input Kategori Sarana Lainnya -->
                    <div id="wrapper_kategori_lainnya" class="hidden mt-3">
                        <label for="kategori_laporan_lainnya" class="block text-sm font-semibold text-gray-700 mb-2">
                            Kategori Sarana Lainnya <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="kategori_laporan_lainnya" id="kategori_laporan_lainnya" maxlength="100"
                            value="{{ old('kategori_laporan_lainnya') }}"
                            placeholder="Contoh: Sistem Keamanan dan CCTV"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all">
                        @error('kategori_laporan_lainnya')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Row 3: Item Kerusakan & Lokasi Spesifik -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Item Kerusakan -->
                <div>
                    <label for="item_kerusakan" class="block text-sm font-semibold text-gray-700 mb-2">
                        Item Kerusakan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="item_kerusakan" id="item_kerusakan" required
                        value="{{ old('item_kerusakan') }}"
                        placeholder="Contoh: Lampu, Atap, Pintu"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all">
                    @error('item_kerusakan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Detail Lokasi Spesifik -->
                <div>
                    <label for="lokasi_spesifik" class="block text-sm font-semibold text-gray-700 mb-2">
                         Detail Lokasi Spesifik
                    </label>

                    <input type="text" name="lokasi_spesifik" id="lokasi_spesifik"
                         value="{{ old('lokasi_spesifik') }}"
                         placeholder="Contoh: Di samping kios No. 12, dekat tangga"
                         class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all">

                    <p class="text-xs text-gray-400 mt-1">
                         Tambahkan keterangan untuk mempermudah menemukan titik kerusakan.
                    </p>

                    @error('lokasi_spesifik')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi Kerusakan -->
            <div>
                <label for="deskripsi_kerusakan" class="block text-sm font-semibold text-gray-700 mb-2">
                    Deskripsi Kerusakan <span class="text-red-500">*</span>
                </label>
                <textarea name="deskripsi_kerusakan" id="deskripsi_kerusakan" rows="4" required
                    placeholder="Jelaskan kondisi kerusakan secara detail..."
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all resize-none">{{ old('deskripsi_kerusakan') }}</textarea>
                @error('deskripsi_kerusakan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kondisi Diharapkan -->
            <div>
                <label for="kondisi_diharapkan" class="block text-sm font-semibold text-gray-700 mb-2">
                    Kondisi yang Diharapkan <span class="text-red-500">*</span>
                </label>
                <textarea name="kondisi_diharapkan" id="kondisi_diharapkan" rows="3" required
                    placeholder="Jelaskan kondisi ideal yang diharapkan setelah perbaikan..."
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all resize-none">{{ old('kondisi_diharapkan') }}</textarea>
                @error('kondisi_diharapkan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Foto Laporan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Foto Laporan <span class="text-red-500">*</span>
                </label>
                
                <input type="file" name="foto_laporan[]" id="foto_laporan" multiple accept="image/*" class="hidden"
                    onchange="handleFileSelection(this)">

                <div id="dropzone-box" class="border-2 border-dashed border-gray-200 rounded-2xl p-4 transition-all bg-gray-50/50 hover:border-[#115f8c]">
                    <!-- Prompt saat foto belum dipilih -->
                    <div id="dropzone-prompt" class="py-8 text-center cursor-pointer" onclick="document.getElementById('foto_laporan').click()">
                        <svg class="w-10 h-10 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-700 mb-1">Klik atau drag foto ke sini</p>
                        <p class="text-xs text-gray-400">Format: JPG, PNG (Maks. 2MB per foto)</p>
                    </div>

                    <!-- Grid Preview Foto INSIDE Dropzone Box -->
                    <div id="file-preview" class="hidden grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
                </div>
                
                @error('foto_laporan.*')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-4">
                <a href="{{ route('home') }}" class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium transition">
                    Batal
                </a>
                <button type="button" id="btn-trigger-review"
                    class="px-8 py-3 bg-gradient-to-r from-[#115f8c] to-[#16A394] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                    Kirim Laporan
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Modal Review Laporan -->
<div id="modal-review-laporan" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all">
        <!-- Header Modal -->
        <div class="bg-gradient-to-r from-[#115f8c] to-[#16A394] px-6 py-4 flex items-center justify-between text-white">
            <div class="flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-bold">Review Data Laporan</h3>
            </div>
            <button type="button" id="btn-close-modal-x" class="text-white/80 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Body Modal -->
        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto text-sm">
            <p class="text-xs text-gray-500 italic mb-2">Pastikan seluruh data dan foto laporan di bawah ini sudah benar sebelum dikirimkan.</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase">Pasar</span>
                    <span id="rev-pasar" class="font-semibold text-gray-800"></span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase">Lokasi</span>
                    <span id="rev-lokasi" class="font-semibold text-gray-800"></span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase">Fasilitas</span>
                    <span id="rev-fasilitas" class="font-semibold text-gray-800"></span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase">Kategori Laporan</span>
                    <span id="rev-kategori" class="font-semibold text-gray-800"></span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase">Item Kerusakan</span>
                    <span id="rev-item" class="font-semibold text-gray-800"></span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase">Lokasi Spesifik</span>
                    <span id="rev-lokasi-spesifik" class="font-semibold text-gray-800"></span>
                </div>
            </div>

            <div>
                <span class="block text-xs font-bold text-gray-400 uppercase mb-1">Deskripsi Kerusakan</span>
                <p id="rev-deskripsi" class="bg-gray-50 p-3 rounded-xl border border-gray-100 text-gray-700 whitespace-pre-line"></p>
            </div>

            <div>
                <span class="block text-xs font-bold text-gray-400 uppercase mb-1">Kondisi yang Diharapkan</span>
                <p id="rev-kondisi" class="bg-blue-50/50 p-3 rounded-xl border border-blue-100/50 text-gray-700 whitespace-pre-line"></p>
            </div>

            <div>
                <span class="block text-xs font-bold text-gray-400 uppercase mb-2">Foto Dokumentasi Laporan</span>
                <div id="rev-foto-preview" class="grid grid-cols-2 sm:grid-cols-3 gap-3"></div>
            </div>
        </div>

        <!-- Footer Modal -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
            <button type="button" id="btn-periksa-kembali"
                class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-100 transition">
                Periksa Kembali
            </button>
            <button type="button" id="btn-submit-laporan"
                class="px-6 py-2.5 bg-gradient-to-r from-[#115f8c] to-[#16A394] text-white font-semibold rounded-xl shadow hover:shadow-lg transition">
                Kirim Laporan
            </button>
        </div>
    </div>
</div>

@section('scripts')
<script>
    const pasarSelect    = document.getElementById('id_pasar');
    const lokasiInput    = document.getElementById('lokasi-search');
    const lokasiOptions  = document.getElementById('lokasi-options');
    const lokasiWrapper  = document.getElementById('lokasi-wrapper');
    const lokasiHidden   = document.querySelector('input[name="id_lokasi"]');
    const lokasiReadonly = document.getElementById('lokasi-readonly');

    const fasilitasInput    = document.getElementById('fasilitas-search');
    const fasilitasOptions  = document.getElementById('fasilitas-options');
    const fasilitasWrapper  = document.getElementById('fasilitas-wrapper');
    const fasilitasHidden   = document.getElementById('id_fasilitas');
    const fasilitasLainnyaWrapper = document.getElementById('wrapper_fasilitas_lainnya');
    const fasilitasLainnyaInput   = document.getElementById('nama_fasilitas_lainnya');

    const kategoriInput    = document.getElementById('kategori-search');
    const kategoriOptions  = document.getElementById('kategori-options');
    const kategoriWrapper  = document.getElementById('kategori-wrapper');
    const kategoriHidden   = document.getElementById('kategori_laporan');
    const kategoriLainnyaWrapper = document.getElementById('wrapper_kategori_lainnya');
    const kategoriLainnyaInput   = document.getElementById('kategori_laporan_lainnya');

    let lokasiData = [];
    let fasilitasData = [];
    const kategoriData = @json($kategoriLaporan ?? []);
    let fasilitasAbortController = null;

    @php
        $oldLokasiIdPhp     = old('id_lokasi')    ?? '';
        $oldFasilitasIdPhp  = old('id_fasilitas') ?? '';
        $oldKategoriValPhp  = old('kategori_laporan') ?? '';
    @endphp
    const oldLokasiId     = @json($oldLokasiIdPhp);
    const oldFasilitasId  = @json($oldFasilitasIdPhp);
    const oldKategoriVal  = @json($oldKategoriValPhp);

    function resetFasilitas() {
        if (fasilitasAbortController) {
            fasilitasAbortController.abort();
            fasilitasAbortController = null;
        }
        fasilitasData = [];
        fasilitasHidden.value = '';
        fasilitasInput.value = '';
        fasilitasInput.placeholder = '-- Pilih lokasi terlebih dahulu --';
        fasilitasInput.disabled = true;
        fasilitasInput.classList.add('bg-gray-50', 'cursor-not-allowed');
        fasilitasInput.classList.remove('bg-white');
        checkFasilitasLainnya('');
    }

    function loadFasilitas(lokasiId) {
        if (!lokasiId) {
            resetFasilitas();
            return;
        }

        if (fasilitasAbortController) {
            fasilitasAbortController.abort();
        }
        fasilitasAbortController = new AbortController();

        fasilitasInput.disabled = true;
        fasilitasInput.value = '';
        fasilitasInput.placeholder = 'Memuat fasilitas...';

        fetch('/api/fasilitas/' + lokasiId, { signal: fasilitasAbortController.signal })
            .then(function(response) {
                if (!response.ok) throw new Error('Response ' + response.status);
                return response.json();
            })
            .then(function(data) {
                if (!data || !data.length) {
                    fasilitasData = [];
                    fasilitasInput.placeholder = 'Tidak ada fasilitas tersedia';
                    fasilitasInput.disabled = true;
                    checkFasilitasLainnya('');
                    return;
                }

                fasilitasData = data.map(function(f) {
                    return { id: f.id_fasilitas, name: f.nama_fasilitas };
                });

                fasilitasInput.disabled = false;
                fasilitasInput.placeholder = 'Cari atau pilih fasilitas...';
                fasilitasInput.classList.remove('bg-gray-50', 'cursor-not-allowed');
                fasilitasInput.classList.add('bg-white');

                if (oldFasilitasId) {
                    const oldF = fasilitasData.find(function(f) { return f.id === oldFasilitasId; });
                    if (oldF) {
                        fasilitasInput.value  = oldF.name;
                        fasilitasHidden.value = oldF.id;
                        checkFasilitasLainnya(oldF.name);
                    }
                }
            })
            .catch(function(error) {
                if (error.name === 'AbortError') return;
                console.error('Error loading fasilitas:', error);
                fasilitasData = [];
                fasilitasInput.placeholder = 'Gagal memuat fasilitas';
                fasilitasInput.disabled = true;
                checkFasilitasLainnya('');
            });
    }

    function renderFasilitasOptions(query) {
        const searchQuery = (query || '').toLowerCase();
        const filtered = fasilitasData.filter(function(item) {
            return (item.name || '').toLowerCase().includes(searchQuery);
        });

        if (!filtered.length) {
            fasilitasOptions.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">Tidak ada fasilitas yang sesuai</div>';
            fasilitasOptions.classList.remove('hidden');
            return;
        }

        fasilitasOptions.innerHTML = '';
        filtered.forEach(function(item) {
            const option = document.createElement('button');
            option.type = 'button';
            option.className = 'block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 transition-colors';
            option.textContent = item.name;
            option.addEventListener('click', function() {
                fasilitasInput.value  = item.name;
                fasilitasHidden.value = item.id;
                fasilitasOptions.classList.add('hidden');
                checkFasilitasLainnya(item.name);
            });
            fasilitasOptions.appendChild(option);
        });

        fasilitasOptions.classList.remove('hidden');
    }

    function checkFasilitasLainnya(selectedName) {
        const isRuangLainnya = selectedName === 'Ruang Lainnya';

        if (isRuangLainnya) {
            fasilitasLainnyaWrapper.classList.remove('hidden');
            fasilitasLainnyaInput.required = true;
        } else {
            fasilitasLainnyaWrapper.classList.add('hidden');
            if (document.activeElement !== fasilitasLainnyaInput) {
                fasilitasLainnyaInput.value = '';
            }
            fasilitasLainnyaInput.required = false;
        }
    }

    function renderKategoriOptions(query) {
        const searchQuery = (query || '').toLowerCase();
        const filtered = kategoriData.filter(function(item) {
            return (item || '').toLowerCase().includes(searchQuery);
        });

        if (!filtered.length) {
            kategoriOptions.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">Tidak ada kategori yang sesuai</div>';
            kategoriOptions.classList.remove('hidden');
            return;
        }

        kategoriOptions.innerHTML = '';
        filtered.forEach(function(item) {
            const option = document.createElement('button');
            option.type = 'button';
            option.className = 'block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 transition-colors';
            option.textContent = item;
            option.addEventListener('click', function() {
                kategoriInput.value  = item;
                kategoriHidden.value = item;
                kategoriOptions.classList.add('hidden');
                checkKategoriLainnya(item);
            });
            kategoriOptions.appendChild(option);
        });

        kategoriOptions.classList.remove('hidden');
    }

    function checkKategoriLainnya(selectedVal) {
        if (selectedVal === 'Lainnya') {
            kategoriLainnyaWrapper.classList.remove('hidden');
            kategoriLainnyaInput.required = true;
        } else {
            kategoriLainnyaWrapper.classList.add('hidden');
            if (document.activeElement !== kategoriLainnyaInput) {
                kategoriLainnyaInput.value = '';
            }
            kategoriLainnyaInput.required = false;
        }
    }

    fasilitasInput.addEventListener('focus', function() {
        if (fasilitasData.length) renderFasilitasOptions(this.value);
    });

    fasilitasInput.addEventListener('input', function() {
        if (fasilitasData.length) renderFasilitasOptions(this.value);
    });

    kategoriInput.addEventListener('focus', function() {
        renderKategoriOptions(this.value);
    });

    kategoriInput.addEventListener('input', function() {
        renderKategoriOptions(this.value);
    });

    // -------------------------------------------------------
    // Fungsi Lokasi (dipertahankan dan diperluas)
    // -------------------------------------------------------

    function renderLokasiOptions(query) {
        const searchQuery = (query || '').toLowerCase();
        const filtered = lokasiData.filter(function(item) {
            return (item.name || '').toLowerCase().includes(searchQuery);
        });

        if (!filtered.length) {
            lokasiOptions.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">Tidak ada lokasi yang sesuai</div>';
            lokasiOptions.classList.remove('hidden');
            return;
        }

        lokasiOptions.innerHTML = '';
        filtered.forEach(function(item) {
            const option = document.createElement('button');
            option.type = 'button';
            option.className = 'block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 transition-colors';
            option.textContent = item.name;
            option.addEventListener('click', function() {
                lokasiInput.value  = item.name;
                lokasiHidden.value = item.id;
                lokasiOptions.classList.add('hidden');
                // Load fasilitas untuk lokasi yang baru dipilih
                loadFasilitas(item.id);
            });
            lokasiOptions.appendChild(option);
        });

        lokasiOptions.classList.remove('hidden');
    }

    function setLokasiInputEnabled(enabled) {
        lokasiInput.disabled = !enabled;
        if (!enabled) {
            lokasiInput.value  = '';
            lokasiOptions.innerHTML = '';
            lokasiOptions.classList.add('hidden');
        }
    }

    function showLokasiSelector(enabled) {
        if (enabled) {
            lokasiWrapper.classList.remove('hidden');
            lokasiReadonly.classList.add('hidden');
        } else {
            lokasiWrapper.classList.add('hidden');
            lokasiReadonly.classList.remove('hidden');
        }
    }

    function closeLokasiOptions() {
        lokasiOptions.classList.add('hidden');
    }

    // Fungsi load lokasi (dipertahankan, ditambah hook loadFasilitas)
    function loadLokasi(pasarId) {
        if (!pasarId) {
            lokasiData = [];
            lokasiHidden.value = '';
            lokasiReadonly.textContent = '';
            lokasiReadonly.classList.add('hidden');
            lokasiWrapper.classList.add('hidden');
            setLokasiInputEnabled(false);
            return;
        }

        fetch('/api/lokasi/' + pasarId)
            .then(function(response) { return response.json(); })
            .then(function(data) {
                const locations = Array.isArray(data) ? data : [];
                const hasChildLocations = locations.some(function(lokasi) {
                    return lokasi.id_induk !== null && lokasi.id_induk !== undefined && lokasi.id_induk !== '';
                });
                const rootLocations = locations.filter(function(lokasi) {
                    return lokasi.id_induk === null || lokasi.id_induk === undefined || lokasi.id_induk === '';
                });

                if (!locations.length) {
                    lokasiData = [];
                    lokasiHidden.value = '';
                    lokasiReadonly.textContent = '';
                    lokasiReadonly.classList.add('hidden');
                    lokasiWrapper.classList.add('hidden');
                    setLokasiInputEnabled(false);
                    return;
                }

                // Lokasi tunggal tanpa anak → readonly + auto-load fasilitas
                if (!hasChildLocations && rootLocations.length === 1) {
                    const lokasi = rootLocations[0];
                    const displayName = lokasi.nama_lengkap || lokasi.nama_lokasi_lengkap || lokasi.nama_lokasi;

                    lokasiData = [];
                    lokasiHidden.value = lokasi.id_lokasi;
                    lokasiReadonly.textContent = displayName;
                    showLokasiSelector(false);
                    lokasiInput.disabled = true;
                    // Auto-load fasilitas untuk lokasi tunggal yang sudah di-set otomatis
                    loadFasilitas(lokasi.id_lokasi);
                    return;
                }

                lokasiData = locations.map(function(lokasi) {
                    return {
                        id: lokasi.id_lokasi,
                        name: lokasi.nama_lengkap || lokasi.nama_lokasi_lengkap || lokasi.nama_lokasi,
                    };
                });

                lokasiHidden.value = '';
                showLokasiSelector(true);
                setLokasiInputEnabled(true);
                lokasiInput.value = '';
                renderLokasiOptions('');

                // Recovery setelah validation error: auto-pilih lokasi lama
                // agar fasilitas juga ikut dimuat ulang secara otomatis.
                if (oldLokasiId) {
                    const oldLokasi = lokasiData.find(function(l) { return l.id === oldLokasiId; });
                    if (oldLokasi) {
                        lokasiInput.value  = oldLokasi.name;
                        lokasiHidden.value = oldLokasi.id;
                        lokasiOptions.classList.add('hidden');
                        loadFasilitas(oldLokasi.id);
                    }
                }
            })
            .catch(function(error) {
                console.error('Error loading lokasi:', error);
                lokasiData = [];
                lokasiHidden.value = '';
                lokasiReadonly.textContent = '';
                lokasiReadonly.classList.add('hidden');
                lokasiWrapper.classList.add('hidden');
                setLokasiInputEnabled(false);
            });
    }

    // Event listener untuk perubahan pasar (jika dropdown)
    @if(auth()->user()->role->nama_role !== 'Petugas UPTD')
    pasarSelect.addEventListener('change', function() {
        loadLokasi(this.value);
    });
    @endif

    lokasiInput.addEventListener('focus', function() {
        if (lokasiData.length) {
            renderLokasiOptions(this.value);
        }
    });

    lokasiInput.addEventListener('input', function() {
        if (lokasiData.length) {
            renderLokasiOptions(this.value);
        }
    });

    document.addEventListener('click', function(event) {
        if (lokasiWrapper && !lokasiWrapper.contains(event.target)) {
            closeLokasiOptions();
        }
        if (fasilitasWrapper && !fasilitasWrapper.contains(event.target)) {
            fasilitasOptions.classList.add('hidden');
        }
        if (kategoriWrapper && !kategoriWrapper.contains(event.target)) {
            kategoriOptions.classList.add('hidden');
        }
    });

    // Auto-load lokasi saat halaman dimuat (untuk UPTD) dan recovery old value kategori
    document.addEventListener('DOMContentLoaded', function() {
        if (oldKategoriVal) {
            kategoriInput.value = oldKategoriVal;
            kategoriHidden.value = oldKategoriVal;
            checkKategoriLainnya(oldKategoriVal);
        }

        @if(auth()->user()->role->nama_role === 'Petugas UPTD' && $pasarTerpilih)
            loadLokasi(@json($pasarTerpilih));
        @endif
    });

    // Show and manage selected files with delete option
    let selectedFilesArray = [];

    function handleFileSelection(input) {
        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach(function(file) {
                const exists = selectedFilesArray.some(function(f) {
                    return f.name === file.name && f.size === file.size && f.lastModified === file.lastModified;
                });
                if (!exists) {
                    selectedFilesArray.push(file);
                }
            });
            updateFileInputAndPreview();
        }
    }

    function removeSelectedFile(index) {
        selectedFilesArray.splice(index, 1);
        updateFileInputAndPreview();
    }

    function updateFileInputAndPreview() {
        const input = document.getElementById('foto_laporan');
        const dt = new DataTransfer();
        selectedFilesArray.forEach(function(file) {
            dt.items.add(file);
        });
        input.files = dt.files;

        const promptElem = document.getElementById('dropzone-prompt');
        const previewElem = document.getElementById('file-preview');

        previewElem.innerHTML = '';

        if (selectedFilesArray.length === 0) {
            promptElem.classList.remove('hidden');
            previewElem.classList.add('hidden');
        } else {
            promptElem.classList.add('hidden');
            previewElem.classList.remove('hidden');

            selectedFilesArray.forEach(function(file, index) {
                const card = document.createElement('div');
                card.className = 'relative group rounded-xl overflow-hidden border border-gray-200 bg-white shadow-sm';

                const reader = new FileReader();
                reader.onload = function(e) {
                    card.innerHTML = `
                        <div class="relative h-28 w-full bg-gray-100">
                            <img src="${e.target.result}" alt="${file.name}" class="w-full h-full object-cover">
                            <button type="button" onclick="event.stopPropagation(); removeSelectedFile(${index})"
                                class="absolute top-1.5 right-1.5 w-6 h-6 bg-rose-600 hover:bg-rose-700 text-white rounded-full flex items-center justify-center shadow-md transition-transform transform hover:scale-110"
                                title="Hapus foto ini">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="p-2 text-xs">
                            <p class="font-semibold text-gray-800 truncate" title="${file.name}">${file.name}</p>
                            <p class="text-gray-400 mt-0.5">${(file.size / 1024).toFixed(1)} KB</p>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
                previewElem.appendChild(card);
            });

            // Card "+ Tambah Foto" inside dropzone grid
            const addTile = document.createElement('div');
            addTile.className = 'border-2 border-dashed border-gray-300 rounded-xl h-[152px] flex flex-col items-center justify-center text-gray-400 hover:border-[#115f8c] hover:text-[#115f8c] transition cursor-pointer bg-white/50 hover:bg-white';
            addTile.onclick = function(e) {
                e.stopPropagation();
                input.click();
            };
            addTile.innerHTML = `
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-xs font-semibold">Tambah Foto</span>
            `;
            previewElem.appendChild(addTile);
        }
    }

    // Drag & Drop event handling for dropzone
    const dropzoneBox = document.getElementById('dropzone-box');
    if (dropzoneBox) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(eventName) {
            dropzoneBox.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        ['dragenter', 'dragover'].forEach(function(eventName) {
            dropzoneBox.addEventListener(eventName, function() {
                dropzoneBox.classList.add('border-[#115f8c]', 'bg-[#115f8c]/10');
            }, false);
        });

        ['dragleave', 'drop'].forEach(function(eventName) {
            dropzoneBox.addEventListener(eventName, function() {
                dropzoneBox.classList.remove('border-[#115f8c]', 'bg-[#115f8c]/10');
            }, false);
        });

        dropzoneBox.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                handleFileSelection({ files: dt.files });
            }
        }, false);
    }

    // Modal Review Logic
    const formLaporan = document.getElementById('form-create-laporan');
    const modalReview = document.getElementById('modal-review-laporan');
    const btnTriggerReview = document.getElementById('btn-trigger-review');
    const btnPeriksaKembali = document.getElementById('btn-periksa-kembali');
    const btnCloseModalX = document.getElementById('btn-close-modal-x');
    const btnSubmitLaporan = document.getElementById('btn-submit-laporan');

    if (btnTriggerReview) {
        btnTriggerReview.addEventListener('click', function() {
            if (!formLaporan.checkValidity()) {
                formLaporan.reportValidity();
                return;
            }

            if (!lokasiHidden || !lokasiHidden.value) {
                alert('Silakan pilih lokasi terlebih dahulu.');
                return;
            }
            if (!fasilitasHidden || !fasilitasHidden.value) {
                alert('Silakan pilih fasilitas terlebih dahulu.');
                return;
            }
            if (!kategoriHidden || !kategoriHidden.value) {
                alert('Silakan pilih kategori laporan terlebih dahulu.');
                return;
            }

            const fileInput = document.getElementById('foto_laporan');
            if (!fileInput.files || fileInput.files.length === 0) {
                alert('Minimal 1 foto dokumentasi laporan harus diunggah.');
                return;
            }

            const pasarSelectElem = document.getElementById('id_pasar');
            let pasarText = '-';
            if (pasarSelectElem) {
                if (pasarSelectElem.tagName === 'SELECT' && pasarSelectElem.options && pasarSelectElem.options.length > 0) {
                    pasarText = pasarSelectElem.options[pasarSelectElem.selectedIndex]?.textContent || '-';
                } else if (pasarSelectElem.nextElementSibling) {
                    pasarText = pasarSelectElem.nextElementSibling.textContent || '-';
                }
            }
            document.getElementById('rev-pasar').textContent = pasarText.trim();
            document.getElementById('rev-lokasi').textContent = lokasiInput.value || '-';

            let fasilitasText = fasilitasInput.value;
            if (fasilitasText === 'Ruang Lainnya' && fasilitasLainnyaInput.value) {
                fasilitasText += ' (' + fasilitasLainnyaInput.value + ')';
            }
            document.getElementById('rev-fasilitas').textContent = fasilitasText || '-';

            let kategoriText = kategoriInput.value;
            if (kategoriText === 'Lainnya' && kategoriLainnyaInput.value) {
                kategoriText += ' (' + kategoriLainnyaInput.value + ')';
            }
            document.getElementById('rev-kategori').textContent = kategoriText || '-';

            document.getElementById('rev-item').textContent = document.getElementById('item_kerusakan').value || '-';
            document.getElementById('rev-lokasi-spesifik').textContent = document.getElementById('lokasi_spesifik').value || '-';
            document.getElementById('rev-deskripsi').textContent = document.getElementById('deskripsi_kerusakan').value || '-';
            document.getElementById('rev-kondisi').textContent = document.getElementById('kondisi_diharapkan').value || '-';

            const fotoContainer = document.getElementById('rev-foto-preview');
            fotoContainer.innerHTML = '';
            Array.from(fileInput.files).forEach(function(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-24 object-cover rounded-xl border border-gray-200 shadow-sm';
                    fotoContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            });

            modalReview.classList.remove('hidden');
        });
    }

    if (btnPeriksaKembali) {
        btnPeriksaKembali.addEventListener('click', function() {
            modalReview.classList.add('hidden');
        });
    }
    if (btnCloseModalX) {
        btnCloseModalX.addEventListener('click', function() {
            modalReview.classList.add('hidden');
        });
    }

    if (btnSubmitLaporan) {
        btnSubmitLaporan.addEventListener('click', function() {
            btnSubmitLaporan.disabled = true;
            btnSubmitLaporan.textContent = 'Mengirim...';
            formLaporan.submit();
        });
    }
</script>
@endsection
@endsection
