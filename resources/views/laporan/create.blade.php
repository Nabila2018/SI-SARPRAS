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

        <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
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
                        Lokasi Spesifik <span class="text-red-500">*</span>
                    </label>
                    <select name="id_lokasi" id="id_lokasi" required
                        @if(auth()->user()->role->nama_role === 'Petugas UPTD')
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all bg-white"
                        @else
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all bg-gray-50"
                            disabled
                        @endif
                        >
                        <option value="">-- Pilih Lokasi --</option>
                    </select>
                    @error('id_lokasi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Row 2: Fasilitas & Kategori -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Fasilitas -->
                <div>
                    <label for="id_fasilitas" class="block text-sm font-semibold text-gray-700 mb-2">
                        Fasilitas <span class="text-red-500">*</span>
                    </label>
                    <select name="id_fasilitas" id="id_fasilitas" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all bg-white">
                        <option value="">-- Pilih Fasilitas --</option>
                        @foreach($fasilitas as $f)
                            <option value="{{ $f->id_fasilitas }}" {{ old('id_fasilitas') == $f->id_fasilitas ? 'selected' : '' }}>
                                {{ $f->nama_fasilitas }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_fasilitas')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori Laporan -->
                <div>
                    <label for="kategori_laporan" class="block text-sm font-semibold text-gray-700 mb-2">
                        Kategori Laporan <span class="text-red-500">*</span>
                    </label>
                    <select name="kategori_laporan" id="kategori_laporan" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all bg-white">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoriLaporan as $k)
                            <option value="{{ $k }}" {{ old('kategori_laporan') == $k ? 'selected' : '' }}>
                                {{ $k }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_laporan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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

                <!-- Lokasi Spesifik -->
                <div>
                    <label for="lokasi_spesifik" class="block text-sm font-semibold text-gray-700 mb-2">
                        Detail Lokasi
                    </label>
                    <input type="text" name="lokasi_spesifik" id="lokasi_spesifik"
                        value="{{ old('lokasi_spesifik') }}"
                        placeholder="Contoh: Dekat pintu masuk utama"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#115f8c] focus:border-[#115f8c] transition-all">
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
                
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-[#115f8c] transition-colors cursor-pointer" 
                     onclick="document.getElementById('foto_laporan').click()">
                    
                    <svg class="w-10 h-10 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    
                    <p class="text-sm text-gray-600 mb-1">Klik atau drag foto ke sini</p>
                    <p class="text-xs text-gray-400">Format: JPG, PNG (Maks. 2MB per foto)</p>
                    
                    <input type="file" name="foto_laporan[]" id="foto_laporan" multiple accept="image/*" class="hidden"
                        onchange="showFileNames(this)">
                </div>
                
                <!-- Preview file names -->
                <div id="file-preview" class="mt-3 space-y-2"></div>
                
                @error('foto_laporan.*')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-4">
                <a href="{{ route('home') }}" class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium transition">
                    Batal
                </a>
                <button type="submit" 
                    class="px-8 py-3 bg-gradient-to-r from-[#115f8c] to-[#16A394] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                    Kirim Laporan
                </button>
            </div>

        </form>
    </div>
</div>

@section('scripts')
<script>
    const pasarSelect = document.getElementById('id_pasar');
    const lokasiSelect = document.getElementById('id_lokasi');
    
    // Fungsi load lokasi
    function loadLokasi(pasarId) {
        if (!pasarId) {
            lokasiSelect.innerHTML = '<option value="">-- Pilih Lokasi --</option>';
            lokasiSelect.disabled = true;
            return;
        }

        fetch(`/api/lokasi/${pasarId}`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">-- Pilih Lokasi --</option>';
                
                // Format: "Parent > Child > Grandchild" untuk hierarki jelas
                data.forEach(function(lokasi) {
                    let displayName = lokasi.nama_lengkap || lokasi.nama_lokasi;
                    options += `<option value="${lokasi.id_lokasi}">${displayName}</option>`;
                });
                
                lokasiSelect.innerHTML = options;
                lokasiSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                lokasiSelect.innerHTML = '<option value="">Gagal memuat lokasi</option>';
            });
    }

    // Event listener untuk perubahan pasar (jika dropdown)
    @if(auth()->user()->role->nama_role !== 'Petugas UPTD')
    pasarSelect.addEventListener('change', function() {
        loadLokasi(this.value);
    });
    @endif

    // Auto-load lokasi saat halaman dimuat (untuk UPTD)
    document.addEventListener('DOMContentLoaded', function() {
        @if(auth()->user()->role->nama_role === 'Petugas UPTD' && $pasarTerpilih)
            loadLokasi({{ $pasarTerpilih }});
        @endif
    });

    // Show selected file names
    function showFileNames(input) {
        const preview = document.getElementById('file-preview');
        preview.innerHTML = '';
        
        if (input.files && input.files.length > 0) {
            for (let i = 0; i < input.files.length; i++) {
                const file = input.files[i];
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2 text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded-lg';
                div.innerHTML = `
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    ${file.name} (${(file.size / 1024).toFixed(1)} KB)
                `;
                preview.appendChild(div);
            }
        }
    }
</script>
@endsection
@endsection