@extends('layouts.app')

@section('title', 'Detail Laporan - SI-SARPRAS')
@section('breadcrumb', 'Detail Laporan')

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

        <p class="text-sm text-gray-500 mb-4">
            Status evaluasi:
            <span class="font-medium text-amber-600">
                Menunggu
            </span>
        </p>

        <button class="px-6 py-3 bg-gradient-to-r from-[#114F72] to-[#16A394] text-white font-semibold rounded-xl shadow-md opacity-50 cursor-not-allowed">
            Isi Evaluasi
        </button>

    </div>

</div>

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