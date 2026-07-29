@extends('layouts.app')

@section('title', 'Detail Laporan - SI-SARPRAS')
@section('breadcrumb')
    <a href="{{ route('staff.laporan.index') }}" class="hover:text-[#114F72] transition">Daftar Laporan Masuk</a>
    <svg class="w-4 h-4 mx-2 text-gray-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <span class="text-gray-600">Detail Laporan</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto pb-12">

    <!-- Tombol Kembali -->
    <a href="{{ route('staff.laporan.index') }}"
       class="inline-flex items-center gap-2 text-gray-600 hover:text-[#114F72] mb-6 transition">
        <svg class="w-5 h-5"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>

        Kembali ke Daftar
    </a>


    <!-- Informasi Laporan -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mb-6">

        <h2 class="text-lg font-bold text-gray-800 mb-4">
            Informasi Laporan
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

            <!-- Pelapor -->
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider">
                    Pelapor
                </p>
                <p class="font-medium text-gray-800">
                    {{ $laporan->pelapor->nama_lengkap ?? '-' }}
                </p>
            </div>

            <!-- Tanggal Lapor -->
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider">
                    Tanggal Lapor
                </p>
                <p class="font-medium text-gray-800">
                    {{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d M Y') }}
                </p>
            </div>

            <!-- Pasar -->
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider">
                    Pasar
                </p>
                <p class="font-medium text-gray-800">
                    {{ optional($laporan->lokasi->pasar)->nama_pasar ?? '-' }}
                </p>
            </div>

            <!-- Lokasi Spesifik -->
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider">
                    Lokasi Spesifik
                </p>
                <p class="font-medium text-gray-800">
                    {{ $laporan->lokasi->nama_lokasi ?? '-' }}
                </p>
            </div>

            <!-- Fasilitas -->
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider">
                    Fasilitas
                </p>
                <p class="font-medium text-gray-800">
                    {{ $laporan->fasilitas->nama_fasilitas ?? '-' }}
                </p>
            </div>

            <!-- Kategori -->
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider">
                    Kategori
                </p>
                <p class="font-medium text-gray-800">
                    {{ $laporan->kategori_laporan }}
                </p>
            </div>

        </div>

        <!-- Deskripsi Kerusakan -->
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">
                Deskripsi Kerusakan
            </p>
            <p class="text-gray-800 text-sm leading-relaxed">
                {{ $laporan->deskripsi_kerusakan }}
            </p>
        </div>

        <!-- Kondisi Diharapkan -->
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">
                Kondisi Diharapkan
            </p>
            <p class="text-gray-800 text-sm leading-relaxed">
                {{ $laporan->kondisi_diharapkan }}
            </p>
        </div>

    </div>


    <!-- Foto Dokumentasi -->
    @if($laporan->fotoLaporan->count() > 0)
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mb-6">

            <h2 class="text-lg font-bold text-gray-800 mb-4">
                Foto Dokumentasi
            </h2>

        
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            @foreach($laporan->fotoLaporan as $index => $foto)
                <button type="button"
                        onclick="openFotoModal({{ $index }})"
                        class="block rounded-lg overflow-hidden border border-gray-200 hover:shadow-md transition cursor-pointer">

                <img src="{{ asset('storage/' . $foto->file_foto) }}"
                     alt="Foto"
                     class="w-full h-32 object-cover">
                </button>
            @endforeach
        </div>

        </div>
    @endif

    <!-- Evaluasi -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">

        <h2 class="text-lg font-bold text-gray-800 mb-2">
            Evaluasi
        </h2>

        @php
            $hasEvaluation = !empty($laporan->kategori_kerusakan) || !empty($laporan->catatan_pemeriksaan);
            $canEvaluate = $laporan->status_laporan === 'Menunggu';
            $canForward = $hasEvaluation && $laporan->status_laporan === 'Menunggu';
            $badgeClass = match ($laporan->kategori_kerusakan) {
                'Ringan' => 'bg-amber-100 text-amber-700 border-amber-200',
                'Sedang' => 'bg-orange-100 text-orange-700 border-orange-200',
                'Berat' => 'bg-red-100 text-red-700 border-red-200',
                default => 'bg-gray-100 text-gray-600 border-gray-200',
            };
        @endphp

        @if($hasEvaluation)
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4 mb-4 space-y-3">
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-500">Kategori Kerusakan</p>
                    <span class="mt-1 inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium {{ $badgeClass }}">
                        {{ $laporan->kategori_kerusakan }}
                    </span>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-500">Catatan Pemeriksaan</p>
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $laporan->catatan_pemeriksaan ?: '-' }}</p>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-wrap gap-3">
            <button type="button"
                    onclick="openEvaluasiModal()"
                    class="px-6 py-3 rounded-xl font-semibold shadow-md transition {{ $canEvaluate ? 'bg-gradient-to-r from-[#114F72] to-[#16A394] text-white hover:opacity-90' : 'bg-gradient-to-r from-gray-300 to-gray-400 text-white cursor-not-allowed opacity-70' }}"
                    {{ $canEvaluate ? '' : 'disabled' }}>
                {{ $hasEvaluation ? 'Edit Evaluasi' : 'Isi Evaluasi' }}
            </button>

            <button type="button"
            onclick="openForwardModal()"
            class="px-6 py-3 rounded-xl font-semibold shadow-md transition {{ $canForward ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white hover:opacity-90' : 'bg-gradient-to-r from-gray-300 to-gray-400 text-white cursor-not-allowed opacity-70' }}"
            {{ $canForward ? '' : 'disabled' }}>
        Teruskan ke Kabid
    </button>

             {{-- TOMBOL BUAT RAB --}}
            @php
                $canCreateRab = $laporan->status_laporan === 'Disetujui' && is_null($laporan->status_verifikasi_rab);
            @endphp

            <a href="{{ route('staff.laporan.rab.show', $laporan->id_laporan) }}"
            class="px-6 py-3 rounded-xl font-semibold shadow-md transition {{ $canCreateRab ? 'bg-gradient-to-r from-[#114F72] to-[#16A394] text-white hover:opacity-90' : 'bg-gradient-to-r from-gray-300 to-gray-400 text-white cursor-not-allowed opacity-70 pointer-events-none' }}">
            Buat RAB
            </a>
        </div>

            <form id="forwardForm" action="{{ route('staff.laporan.forward', $laporan->id_laporan) }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>

    </div>

    <!-- Progres Perbaikan Card Section -->
    @php
        $existingProgress = $laporan->progresPerbaikan->sortBy('persentase_penyelesaian');
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
        $isComplete = in_array('100', $existingStages);
    @endphp

    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100 flex-wrap gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Progres Perbaikan</h2>
                <p class="text-xs text-gray-500 mt-0.5">Riwayat perkembangan pengerjaan fisik sarana pasar</p>
            </div>

            @if($isComplete)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 border border-emerald-200 px-3.5 py-1.5 text-xs font-bold text-emerald-700">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Perbaikan Selesai (100%)
                </span>
            @elseif($isRabApproved && !is_null($nextStage))
                <button type="button"
                        onclick="openProgresModal()"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#114F72] to-[#16A394] px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:opacity-90 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Progres (Tahap {{ $nextStage }}%)
                </button>
            @endif
        </div>

        @if(!$isRabApproved)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>Progres perbaikan hanya dapat diinput setelah Rencana Anggaran Biaya (RAB) disetujui oleh Kepala Bidang.</p>
            </div>
        @elseif($existingProgress->count() === 0)
            <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center">
                <p class="text-sm text-gray-500">Belum ada progres perbaikan yang diinput.</p>
                @if(!is_null($nextStage))
                    <button type="button" onclick="openProgresModal()" class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-[#114F72] hover:underline">
                        + Input Progres Pertama (0%)
                    </button>
                @endif
            </div>
        @else
            <!-- Timeline Progres -->
            <div class="space-y-6">
                @foreach($existingProgress as $progres)
                    <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-5">
                        <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center justify-center rounded-lg bg-[#114F72] text-white px-3 py-1 text-sm font-bold shadow-sm">
                                    {{ $progres->persentase_penyelesaian }}%
                                </span>
                                <span class="text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    @if($progres->persentase_penyelesaian == '0')
                                        Tahap Persiapan / Mulai (0%)
                                    @elseif($progres->persentase_penyelesaian == '50')
                                        Tahap Pengerjaan (50%)
                                    @else
                                        Tahap Selesai / Finishing (100%)
                                    @endif
                                </span>
                            </div>
                            <span class="text-xs text-gray-500 font-medium">
                                {{ \Carbon\Carbon::parse($progres->tanggal_update)->format('d F Y, H:i') }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-700 leading-relaxed mb-4">
                            {{ $progres->keterangan_perkembangan }}
                        </p>

                        <!-- Galeri Foto Progres -->
                        @if($progres->fotoProgres && $progres->fotoProgres->count() > 0)
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Foto Dokumentasi Progres:</p>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @foreach($progres->fotoProgres as $foto)
                                        <button type="button"
                                                onclick="openProgresFotoModal('{{ asset('storage/' . $foto->file_foto) }}')"
                                                class="group aspect-square rounded-lg overflow-hidden border border-gray-200 hover:shadow-md transition">
                                            <img src="{{ asset('storage/' . $foto->file_foto) }}" alt="Foto Progres" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<div id="toastMessage" class="fixed bottom-5 right-5 z-[60] hidden items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 shadow-lg"></div>

<!-- Modal Evaluasi -->
<div id="evaluasiModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeEvaluasiModal()">

    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Evaluasi Laporan</h3>
                <p class="text-sm text-gray-500">Catat hasil pemeriksaan staf.</p>
            </div>
            <button type="button" onclick="closeEvaluasiModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('staff.laporan.evaluasi.store', $laporan->id_laporan) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="kategori_kerusakan" class="block text-sm font-medium text-gray-700 mb-1">Kategori Kerusakan</label>
                    <select id="kategori_kerusakan"
                            name="kategori_kerusakan"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20"
                            required>
                        <option value="" disabled {{ old('kategori_kerusakan', $laporan->kategori_kerusakan) ? '' : 'selected' }}>Pilih kategori</option>
                        @foreach(['Ringan','Sedang','Berat'] as $option)
                            <option value="{{ $option }}" {{ old('kategori_kerusakan', $laporan->kategori_kerusakan) === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="catatan_pemeriksaan" class="block text-sm font-medium text-gray-700 mb-1">Catatan Pemeriksaan</label>
                    <textarea id="catatan_pemeriksaan"
                              name="catatan_pemeriksaan"
                              rows="4"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20"
                              placeholder="Tuliskan catatan pemeriksaan...">{{ old('catatan_pemeriksaan', $laporan->catatan_pemeriksaan) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeEvaluasiModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
                <button type="submit" class="rounded-lg bg-gradient-to-r from-[#114F72] to-[#16A394] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">Simpan Evaluasi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Teruskan -->
<div id="forwardModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeForwardModal()">

    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        <h3 class="text-lg font-semibold text-gray-800">Teruskan ke Kabid</h3>
        <p class="mt-2 text-sm text-gray-600">Apakah Anda yakin ingin meneruskan laporan ini ke Kabid?</p>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" onclick="closeForwardModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
            <button type="button" onclick="document.getElementById('forwardForm').submit();" class="rounded-lg bg-gradient-to-r from-[#114F72] to-[#16A394] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">Ya, Teruskan</button>
        </div>
    </div>
</div>

@if($isRabApproved && !is_null($nextStage))
<!-- Modal Tambah Progres -->
<div id="progresModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeProgresModal()">

    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Tambah Progres Perbaikan</h3>
                <p class="text-xs text-gray-500">Input catatan dan foto perkembangan pengerjaan</p>
            </div>
            <button type="button" onclick="closeProgresModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('staff.laporan.progres.store', $laporan->id_laporan) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Stage Badge -->
            <div class="mb-4 rounded-xl bg-gradient-to-r from-[#114F72]/10 to-[#16A394]/10 border border-[#114F72]/20 p-4 flex items-center justify-between">
                <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Tahap Progres Otomatis</span>
                <span class="inline-flex items-center gap-1 text-base font-bold text-[#114F72]">
                    {{ $nextStage }}%
                    @if($nextStage == '0')
                        (Persiapan / Mulai)
                    @elseif($nextStage == '50')
                        (Pengerjaan)
                    @else
                        (Selesai)
                    @endif
                </span>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="keterangan_perkembangan" class="block text-sm font-medium text-gray-700 mb-1">
                        Catatan Perkembangan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="keterangan_perkembangan"
                              name="keterangan_perkembangan"
                              rows="4"
                              required
                              maxlength="2000"
                              class="w-full rounded-lg border border-gray-300 p-3 text-sm focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20"
                              placeholder="Tuliskan detail pekerjaan perbaikan yang telah dilaksanakan..."></textarea>
                </div>

                <div>
                    <label for="foto_progres" class="block text-sm font-medium text-gray-700 mb-1">
                        Foto Dokumentasi Progres <span class="text-red-500">*</span>
                    </label>
                    <input type="file"
                           id="foto_progres"
                           name="foto_progres[]"
                           multiple
                           accept="image/*"
                           required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#114F72]/10 file:text-[#114F72] hover:file:bg-[#114F72]/20">
                    <p class="text-xs text-gray-500 mt-1">Bisa memilih lebih dari 1 foto (Maks 4 MB per foto).</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeProgresModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Batal</button>
                <button type="submit" class="rounded-lg bg-gradient-to-r from-[#114F72] to-[#16A394] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">Simpan Progres</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Modal Preview Foto Progres -->
<div id="progresFotoModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 px-4"
     onclick="if(event.target === this) closeProgresFotoModal()">
    <button type="button"
            onclick="closeProgresFotoModal()"
            class="absolute top-4 right-4 text-white/80 hover:text-white transition">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <img id="progresFotoModalImg"
         src=""
         alt="Foto Progres Detail"
         class="max-h-[85vh] max-w-full rounded-lg shadow-2xl">
</div>

<script>
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

    function openProgresFotoModal(src) {
        document.getElementById('progresFotoModalImg').src = src;
        const modal = document.getElementById('progresFotoModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeProgresFotoModal() {
        const modal = document.getElementById('progresFotoModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
    function openEvaluasiModal() {
        document.getElementById('evaluasiModal').classList.remove('hidden');
        document.getElementById('evaluasiModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeEvaluasiModal() {
        const modal = document.getElementById('evaluasiModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function openForwardModal() {
        document.getElementById('forwardModal').classList.remove('hidden');
        document.getElementById('forwardModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeForwardModal() {
        const modal = document.getElementById('forwardModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function showToast(message) {
        const toast = document.getElementById('toastMessage');
        if (!toast) return;
        toast.textContent = message;
        toast.classList.remove('hidden');
        toast.classList.add('flex');
        setTimeout(() => {
            toast.classList.add('hidden');
            toast.classList.remove('flex');
        }, 3000);
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeEvaluasiModal();
            closeForwardModal();
        }
    });

    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function () {
            openEvaluasiModal();
        });
    @endif

    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function () {
            showToast(@json(session('success')));
        });
    @endif
</script>

@if($laporan->fotoLaporan->count() > 0)
<!-- Modal Foto Dokumentasi -->
<div id="fotoModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 px-4"
     onclick="if(event.target === this) closeFotoModal()">

    <!-- Tombol Close -->
    <button type="button"
            onclick="closeFotoModal()"
            class="absolute top-4 right-4 text-white/80 hover:text-white transition">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    <!-- Tombol Previous -->
    <button type="button"
            id="fotoPrevBtn"
            onclick="showPrevFoto()"
            class="absolute left-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white transition">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>

    <!-- Gambar -->
    <img id="fotoModalImg"
         src=""
         alt="Foto Dokumentasi"
         class="max-h-[85vh] max-w-full rounded-lg shadow-2xl">

    <!-- Tombol Next -->
    <button type="button"
            id="fotoNextBtn"
            onclick="showNextFoto()"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white transition">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </button>

    <!-- Counter -->
    <div id="fotoCounter"
         class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/80 text-sm"></div>
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

    document.addEventListener('keydown', function (e) {
        const modal = document.getElementById('fotoModal');
        if (modal.classList.contains('hidden')) return;

        if (e.key === 'Escape') closeFotoModal();
        if (e.key === 'ArrowLeft') showPrevFoto();
        if (e.key === 'ArrowRight') showNextFoto();
    });
</script>
@endif
@endsection