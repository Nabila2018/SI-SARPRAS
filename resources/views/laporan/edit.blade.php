@extends('layouts.app')

@section('title', 'Edit Laporan #' . $laporan->id_laporan . ' - SI-SARPRAS')
@section('breadcrumb', 'Edit Laporan')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Laporan Kerusakan</h1>
            <p class="text-gray-500 mt-1">Ubah rincian data laporan kerusakan #{{ $laporan->id_laporan }}.</p>
        </div>
        <a href="{{ route('laporan.show', $laporan->id_laporan) }}" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition">
            Kembali ke Detail
        </a>
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

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <h3 class="text-sm font-bold text-red-800 mb-1">Terjadi kesalahan validasi:</h3>
            <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Card Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-[#115f8c]/5 to-[#16A394]/5">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#115f8c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Perbarui Informasi Laporan
            </h2>
        </div>

        <form action="{{ route('laporan.update', $laporan->id_laporan) }}" method="POST" enctype="multipart/form-data" id="form-edit-laporan" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Row 1: Pasar & Lokasi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Pasar -->
                <div>
                    <label for="id_pasar" class="block text-sm font-semibold text-gray-700 mb-2">
                        Pasar <span class="text-red-500">*</span>
                    </label>
                    
                    @if(auth()->user()->role->nama_role === 'Petugas UPTD')
                        <input type="hidden" name="id_pasar" id="id_pasar" value="{{ $laporan->lokasi->id_pasar ?? $pasarTerpilih }}">
                        <div class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-100 text-gray-700 font-medium">
                            {{ $laporan->lokasi->pasar->nama_pasar ?? '-' }}
                        </div>
                    @else
                        <select name="id_pasar" id="id_pasar" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all bg-white">
                            @foreach($pasar as $p)
                                <option value="{{ $p->id_pasar }}" {{ old('id_pasar', $laporan->lokasi->id_pasar ?? '') == $p->id_pasar ? 'selected' : '' }}>
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
                    <label for="lokasi-search" class="block text-sm font-semibold text-gray-700 mb-2">
                        Lokasi <span class="text-red-500">*</span>
                    </label>
                    <input type="hidden" name="id_lokasi" id="id_lokasi" value="{{ old('id_lokasi', $laporan->id_lokasi) }}">
                    <div id="lokasi-wrapper" class="relative">
                        <input type="text" id="lokasi-search" placeholder="Cari atau pilih lokasi..." autocomplete="off"
                            value="{{ old('nama_lokasi_search', $laporan->lokasi->nama_lokasi ?? '') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all bg-white">
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
                    <input type="hidden" name="id_fasilitas" id="id_fasilitas" value="{{ old('id_fasilitas', $laporan->id_fasilitas) }}">
                    <div id="fasilitas-wrapper" class="relative">
                        <input type="text" id="fasilitas-search" placeholder="Cari atau pilih fasilitas..." autocomplete="off"
                            value="{{ old('nama_fasilitas_search', $laporan->fasilitas->nama_fasilitas ?? '') }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all bg-white">
                        <div id="fasilitas-options" class="hidden absolute z-20 mt-1 w-full max-h-56 overflow-auto rounded-xl border border-gray-200 bg-white shadow-lg"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Pilih hanya jika pilihan yang sesuai tidak tersedia.</p>
                    @error('id_fasilitas')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    <!-- Input Nama Ruang/Fasilitas Lainnya -->
                    <div id="wrapper_fasilitas_lainnya" class="{{ ($laporan->fasilitas->nama_fasilitas ?? '') === 'Ruang Lainnya' || old('nama_fasilitas_lainnya') ? '' : 'hidden' }} mt-3">
                        <label for="nama_fasilitas_lainnya" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Ruang/Fasilitas Lainnya <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_fasilitas_lainnya" id="nama_fasilitas_lainnya" maxlength="100"
                            value="{{ old('nama_fasilitas_lainnya', $laporan->nama_fasilitas_lainnya) }}"
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
                    <input type="hidden" name="kategori_laporan" id="kategori_laporan" value="{{ old('kategori_laporan', $laporan->kategori_laporan) }}">
                    <div id="kategori-wrapper" class="relative">
                        <input type="text" id="kategori-search" placeholder="Cari atau pilih kategori..." autocomplete="off"
                            value="{{ old('kategori_laporan', $laporan->kategori_laporan) }}"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all bg-white">
                        <div id="kategori-options" class="hidden absolute z-20 mt-1 w-full max-h-56 overflow-auto rounded-xl border border-gray-200 bg-white shadow-lg"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Pilih hanya jika pilihan yang sesuai tidak tersedia.</p>
                    @error('kategori_laporan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    <!-- Input Kategori Sarana Lainnya -->
                    <div id="wrapper_kategori_lainnya" class="{{ old('kategori_laporan', $laporan->kategori_laporan) === 'Lainnya' || old('kategori_laporan_lainnya') ? '' : 'hidden' }} mt-3">
                        <label for="kategori_laporan_lainnya" class="block text-sm font-semibold text-gray-700 mb-2">
                            Kategori Sarana Lainnya <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="kategori_laporan_lainnya" id="kategori_laporan_lainnya" maxlength="100"
                            value="{{ old('kategori_laporan_lainnya', $laporan->kategori_laporan_lainnya) }}"
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
                        value="{{ old('item_kerusakan', $laporan->item_kerusakan) }}"
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
                         value="{{ old('lokasi_spesifik', $laporan->lokasi_spesifik) }}"
                         placeholder="Contoh: Depan Toko Maju Jaya, Dekat Tangga Barat"
                         class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all">

                    <p class="text-xs text-gray-500 mt-1">
                        Opsional. Tambahkan rincian patokan lokasi untuk memudahkan petugas menemukan objek kerusakan.
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
                    placeholder="Jelaskan detail kerusakan yang terjadi..."
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all">{{ old('deskripsi_kerusakan', $laporan->deskripsi_kerusakan) }}</textarea>
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
                    placeholder="Jelaskan kondisi atau perbaikan yang diharapkan..."
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all">{{ old('kondisi_diharapkan', $laporan->kondisi_diharapkan) }}</textarea>
                @error('kondisi_diharapkan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pengelolaan Foto Laporan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Foto Dokumentasi Laporan Saat Ini
                </label>

                <!-- Foto Lama -->
                @if($laporan->fotoLaporan && $laporan->fotoLaporan->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                        @foreach($laporan->fotoLaporan as $foto)
                            <div id="card-foto-{{ $foto->id_foto }}" class="relative group rounded-xl overflow-hidden border border-gray-200 bg-gray-50 transition-all shadow-sm">
                                <input type="checkbox" name="hapus_foto[]" id="hapus_foto_{{ $foto->id_foto }}" value="{{ $foto->id_foto }}" class="hidden" onchange="toggleDeleteFotoState('{{ $foto->id_foto }}')">
                                
                                <div class="relative h-28 w-full cursor-pointer" onclick="openSiSarprasPhotoLightbox('{{ asset('storage/' . $foto->file_foto) }}', 'Foto Laporan #{{ $foto->id_foto }}')">
                                    <img id="img-foto-{{ $foto->id_foto }}" src="{{ asset('storage/' . $foto->file_foto) }}" alt="Foto Laporan" class="w-full h-full object-cover transition-all hover:scale-105 duration-300">
                                    <div id="overlay-foto-{{ $foto->id_foto }}" class="hidden absolute inset-0 bg-rose-900/60 flex items-center justify-center backdrop-blur-[1px]">
                                        <span class="text-white text-xs font-bold bg-rose-600 px-2.5 py-1 rounded-full shadow">Akan Dihapus</span>
                                    </div>
                                    <button type="button" onclick="event.stopPropagation(); toggleDeleteFoto('{{ $foto->id_foto }}')"
                                        id="btn-delete-foto-{{ $foto->id_foto }}"
                                        class="absolute top-1.5 right-1.5 w-6 h-6 bg-rose-600 hover:bg-rose-700 text-white rounded-full flex items-center justify-center shadow-md transition-transform transform hover:scale-110 z-10"
                                        title="Hapus foto ini">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Tambah Foto Baru -->
                <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Tambah Foto Baru (Opsional)
                    </label>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openLaporanEditCamera()"
                            title="Ambil Foto Kamera"
                            class="inline-flex items-center justify-center p-2 bg-gradient-to-r from-[#115f8c] to-[#16A394] text-white rounded-xl shadow-sm hover:opacity-90 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <input type="file" name="foto_laporan[]" id="foto_laporan" multiple accept="image/*" class="hidden"
                    onchange="handleFileSelection(this)">

                <div id="dropzone-box" class="border-2 border-dashed border-gray-200 rounded-2xl p-4 transition-all bg-gray-50/50 hover:border-[#115f8c]">
                    <!-- Prompt saat belum memilih foto baru -->
                    <div id="dropzone-prompt" class="py-8 text-center cursor-pointer" onclick="document.getElementById('foto_laporan').click()">
                        <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-700 mb-1">Klik atau drag foto baru ke sini</p>
                        <p class="text-xs text-gray-400">Format: JPG, PNG (Maks. 2MB per foto)</p>
                    </div>

                    <!-- Grid Preview Foto INSIDE Dropzone Box -->
                    <div id="file-preview" class="hidden grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
                </div>
                
                @error('foto_laporan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                @error('foto_laporan.*')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-4">
                <a href="{{ route('laporan.show', $laporan->id_laporan) }}" class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium transition">
                    Batal
                </a>
                <button type="button" id="btn-trigger-review"
                    class="px-8 py-3 bg-gradient-to-r from-[#115f8c] to-[#16A394] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Modal Review Laporan Edit -->
<div id="modal-review-laporan" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all">
        <!-- Header Modal -->
        <div class="bg-gradient-to-r from-[#115f8c] to-[#16A394] px-6 py-4 flex items-center justify-between text-white">
            <div class="flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-bold">Review Perubahan Laporan</h3>
            </div>
            <button type="button" id="btn-close-modal-x" class="text-white/80 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Body Modal -->
        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto text-sm">
            <p class="text-xs text-gray-500 italic mb-2">Pastikan perubahan data dan foto laporan di bawah ini sudah benar sebelum disimpan.</p>
            
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
                <span class="block text-xs font-bold text-gray-400 uppercase mb-2">Foto Baru Yang Akan Ditambahkan</span>
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
                Simpan Perubahan
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

    const initialLokasiId = @json($laporan->id_lokasi);
    const initialFasilitasId = @json($laporan->id_fasilitas);

    function setLokasiInputEnabled(enabled) {
        lokasiInput.disabled = !enabled;
        if (enabled) {
            lokasiInput.classList.remove('bg-gray-50', 'cursor-not-allowed');
            lokasiInput.classList.add('bg-white');
        } else {
            lokasiInput.classList.add('bg-gray-50', 'cursor-not-allowed');
            lokasiInput.classList.remove('bg-white');
        }
    }

    function showLokasiSelector(showInput) {
        if (showInput) {
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

    function loadFasilitas(lokasiId, preselectId) {
        if (!lokasiId) {
            resetFasilitas();
            return;
        }

        if (fasilitasAbortController) {
            fasilitasAbortController.abort();
        }
        fasilitasAbortController = new AbortController();

        fasilitasInput.disabled = true;
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

                const targetId = preselectId || fasilitasHidden.value || initialFasilitasId;
                if (targetId) {
                    const foundF = fasilitasData.find(function(f) { return f.id === targetId; });
                    if (foundF) {
                        fasilitasInput.value  = foundF.name;
                        fasilitasHidden.value = foundF.id;
                        checkFasilitasLainnya(foundF.name);
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
                loadFasilitas(item.id, null);
            });
            lokasiOptions.appendChild(option);
        });

        lokasiOptions.classList.remove('hidden');
    }

    function loadLokasi(pasarId) {
        if (!pasarId) {
            lokasiData = [];
            lokasiHidden.value = '';
            lokasiReadonly.textContent = '';
            lokasiReadonly.classList.add('hidden');
            lokasiWrapper.classList.add('hidden');
            setLokasiInputEnabled(false);
            resetFasilitas();
            return;
        }

        setLokasiInputEnabled(false);
        lokasiInput.value = 'Memuat lokasi...';
        closeLokasiOptions();

        fetch('/api/lokasi/' + pasarId)
            .then(function(res) { return res.json(); })
            .then(function(locations) {
                const rootLocations = locations.filter(function(l) { return !l.id_induk; });
                const hasChildLocations = locations.some(function(l) { return !!l.id_induk; });

                if (!locations.length) {
                    lokasiData = [];
                    lokasiHidden.value = '';
                    lokasiReadonly.textContent = 'Tidak ada lokasi tersedia untuk pasar ini';
                    showLokasiSelector(false);
                    resetFasilitas();
                    return;
                }

                if (!hasChildLocations && rootLocations.length === 1) {
                    const lokasi = rootLocations[0];
                    const displayName = lokasi.nama_lengkap || lokasi.nama_lokasi_lengkap || lokasi.nama_lokasi;

                    lokasiData = [];
                    lokasiHidden.value = lokasi.id_lokasi;
                    lokasiReadonly.textContent = displayName;
                    showLokasiSelector(false);
                    lokasiInput.disabled = true;
                    loadFasilitas(lokasi.id_lokasi, initialFasilitasId);
                    return;
                }

                lokasiData = locations.map(function(lokasi) {
                    return {
                        id: lokasi.id_lokasi,
                        name: lokasi.nama_lengkap || lokasi.nama_lokasi_lengkap || lokasi.nama_lokasi,
                    };
                });

                showLokasiSelector(true);
                setLokasiInputEnabled(true);

                const currentLokasiId = lokasiHidden.value || initialLokasiId;
                if (currentLokasiId) {
                    const foundL = lokasiData.find(function(l) { return l.id === currentLokasiId; });
                    if (foundL) {
                        lokasiInput.value  = foundL.name;
                        lokasiHidden.value = foundL.id;
                        loadFasilitas(foundL.id, initialFasilitasId);
                    }
                } else {
                    lokasiInput.value = '';
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
                resetFasilitas();
            });
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

    lokasiInput.addEventListener('focus', function() {
        if (lokasiData.length) renderLokasiOptions(this.value);
    });

    lokasiInput.addEventListener('input', function() {
        if (lokasiData.length) renderLokasiOptions(this.value);
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

    function toggleDeleteFoto(id) {
        const checkbox = document.getElementById('hapus_foto_' + id);
        if (checkbox) {
            checkbox.checked = !checkbox.checked;
            toggleDeleteFotoState(id);
        }
    }

    function toggleDeleteFotoState(id) {
        const checkbox = document.getElementById('hapus_foto_' + id);
        const card = document.getElementById('card-foto-' + id);
        const overlay = document.getElementById('overlay-foto-' + id);
        const btn = document.getElementById('btn-delete-foto-' + id);

        if (checkbox && checkbox.checked) {
            card.classList.add('border-rose-500', 'ring-2', 'ring-rose-400');
            card.classList.remove('border-gray-200');
            overlay.classList.remove('hidden');
            btn.className = 'absolute top-1.5 right-1.5 w-6 h-6 bg-gray-800 hover:bg-gray-900 text-white rounded-full flex items-center justify-center shadow-md transition-transform transform hover:scale-110 z-10';
            btn.title = 'Batal Hapus';
            btn.innerHTML = `
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
            `;
        } else if (checkbox) {
            card.classList.remove('border-rose-500', 'ring-2', 'ring-rose-400');
            card.classList.add('border-gray-200');
            overlay.classList.add('hidden');
            btn.className = 'absolute top-1.5 right-1.5 w-6 h-6 bg-rose-600 hover:bg-rose-700 text-white rounded-full flex items-center justify-center shadow-md transition-transform transform hover:scale-110 z-10';
            btn.title = 'Hapus foto ini';
            btn.innerHTML = `
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            `;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const initialPasarId = document.getElementById('id_pasar')?.value;
        if (initialPasarId) {
            loadLokasi(initialPasarId);
        }

        if (kategoriHidden.value) {
            kategoriInput.value = kategoriHidden.value;
            checkKategoriLainnya(kategoriHidden.value);
        }
    });

    // Show and manage selected new files with delete option
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
                card.className = 'relative group rounded-xl overflow-hidden border border-gray-200 bg-white shadow-sm cursor-pointer hover:border-[#16A394] transition-all';

                const reader = new FileReader();
                reader.onload = function(e) {
                    card.onclick = function() {
                        openSiSarprasPhotoLightbox(e.target.result, file.name);
                    };
                    card.innerHTML = `
                        <div class="relative h-28 w-full bg-gray-100">
                            <img src="${e.target.result}" alt="${file.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <button type="button" onclick="event.stopPropagation(); removeSelectedFile(${index})"
                                class="absolute top-1.5 right-1.5 w-6 h-6 bg-rose-600 hover:bg-rose-700 text-white rounded-full flex items-center justify-center shadow-md transition-transform transform hover:scale-110 z-10"
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

    // Modal Review Logic for Edit
    const formEdit = document.getElementById('form-edit-laporan');
    const modalReview = document.getElementById('modal-review-laporan');
    const btnTriggerReview = document.getElementById('btn-trigger-review');
    const btnPeriksaKembali = document.getElementById('btn-periksa-kembali');
    const btnCloseModalX = document.getElementById('btn-close-modal-x');
    const btnSubmitLaporan = document.getElementById('btn-submit-laporan');

    if (btnTriggerReview) {
        btnTriggerReview.addEventListener('click', function() {
            if (!formEdit.checkValidity()) {
                formEdit.reportValidity();
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

            const fileInput = document.getElementById('foto_laporan');
            const fotoContainer = document.getElementById('rev-foto-preview');
            fotoContainer.innerHTML = '';

            if (fileInput.files && fileInput.files.length > 0) {
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
            } else {
                fotoContainer.innerHTML = '<span class="text-xs text-gray-400 italic col-span-3">Tidak ada foto baru ditambahkan.</span>';
            }

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

    function openLaporanEditCamera() {
        openSiSarprasCamera(function(capturedFile) {
            if (capturedFile.size > 2 * 1024 * 1024) {
                alert(`Ukuran foto kamera (${(capturedFile.size / (1024*1024)).toFixed(1)}MB) melebihi batas maksimal 2MB.`);
                return;
            }
            const exists = selectedFilesArray.some(function(f) {
                return f.name === capturedFile.name && f.size === capturedFile.size;
            });
            if (!exists) {
                selectedFilesArray.push(capturedFile);
                updateFileInputAndPreview();
            }
        });
    }

    if (btnSubmitLaporan) {
        btnSubmitLaporan.addEventListener('click', function() {
            btnSubmitLaporan.disabled = true;
            btnSubmitLaporan.textContent = 'Menyimpan...';
            formEdit.submit();
        });
    }
</script>

@include('partials._camera_modal')
@endsection
@endsection
