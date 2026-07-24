@extends('layouts.app')

@section('title', 'Detail Laporan - SI-SARPRAS')
@section('breadcrumb', 'Detail Laporan')

@section('content')

<div class="max-w-4xl mx-auto pb-12">

    {{-- KEMBALI --}}
    <a href="{{ route('kabid.laporan.index') }}"
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


    {{-- INFORMASI LAPORAN --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mb-6">

        <div class="flex flex-wrap items-start justify-between gap-3 mb-5">

            <div>
                <h2 class="text-lg font-bold text-gray-800">
                    Informasi Laporan
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Detail laporan kerusakan yang diteruskan untuk verifikasi.
                </p>
            </div>

            <span class="inline-flex items-center rounded-full border
                         border-orange-200 bg-orange-50
                         px-3 py-1 text-xs font-semibold text-orange-700">
                {{ $laporan->status_laporan }}
            </span>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

            {{-- Pelapor --}}
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider">
                    Pelapor
                </p>

                <p class="font-medium text-gray-800 mt-1">
                    {{ $laporan->pelapor?->nama_lengkap ?? '-' }}
                </p>
            </div>


            {{-- Tanggal --}}
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider">
                    Tanggal Lapor
                </p>

                <p class="font-medium text-gray-800 mt-1">
                    {{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d M Y') }}
                </p>
            </div>


            {{-- Pasar --}}
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider">
                    Pasar
                </p>

                <p class="font-medium text-gray-800 mt-1">
                    {{ $laporan->lokasi?->pasar?->nama_pasar ?? '-' }}
                </p>
            </div>


            {{-- Lokasi --}}
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider">
                    Lokasi
                </p>

                <p class="font-medium text-gray-800 mt-1">
                    {{ $laporan->lokasi?->nama_lokasi ?? '-' }}
                </p>
            </div>


            {{-- Lokasi Spesifik --}}
            @if($laporan->lokasi_spesifik)
                <div>
                    <p class="text-gray-500 text-xs uppercase tracking-wider">
                        Lokasi Spesifik
                    </p>

                    <p class="font-medium text-gray-800 mt-1">
                        {{ $laporan->lokasi_spesifik }}
                    </p>
                </div>
            @endif


            {{-- Fasilitas --}}
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider">
                    Fasilitas
                </p>

                <p class="font-medium text-gray-800 mt-1">
                    {{ $laporan->fasilitas?->nama_fasilitas ?? '-' }}
                </p>
            </div>


            {{-- Kategori --}}
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider">
                    Kategori Laporan
                </p>

                <p class="font-medium text-gray-800 mt-1">
                    {{ $laporan->kategori_laporan ?? '-' }}
                </p>
            </div>


            {{-- Item Kerusakan --}}
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wider">
                    Item Kerusakan
                </p>

                <p class="font-medium text-gray-800 mt-1">
                    {{ $laporan->item_kerusakan ?? '-' }}
                </p>
            </div>

        </div>


        {{-- Deskripsi --}}
        <div class="mt-5 pt-4 border-t border-gray-100">

            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">
                Deskripsi Kerusakan
            </p>

            <p class="text-gray-800 text-sm leading-relaxed">
                {{ $laporan->deskripsi_kerusakan ?: '-' }}
            </p>

        </div>


        {{-- Kondisi Diharapkan --}}
        <div class="mt-4 pt-4 border-t border-gray-100">

            <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">
                Kondisi Diharapkan
            </p>

            <p class="text-gray-800 text-sm leading-relaxed">
                {{ $laporan->kondisi_diharapkan ?: '-' }}
            </p>

        </div>

    </div>


    {{-- FOTO DOKUMENTASI --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mb-6">

        <h2 class="text-lg font-bold text-gray-800 mb-4">
            Foto Dokumentasi
        </h2>

        @if($laporan->fotoLaporan->count() > 0)

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">

                @foreach($laporan->fotoLaporan as $index => $foto)

                    <button type="button"
                            onclick="openFotoModal({{ $index }})"
                            class="block rounded-lg overflow-hidden
                                   border border-gray-200 hover:shadow-md
                                   transition cursor-pointer">

                        <img src="{{ asset('storage/' . $foto->file_foto) }}"
                             alt="Foto Dokumentasi"
                             class="w-full h-32 object-cover">

                    </button>

                @endforeach

            </div>

        @else

            <div class="rounded-lg bg-gray-50 border border-gray-100
                        px-4 py-5 text-sm text-gray-500 text-center">
                Tidak ada foto dokumentasi.
            </div>

        @endif
    

    </div>


    {{-- HASIL EVALUASI STAFF --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">

        <div class="mb-5">

            <h2 class="text-lg font-bold text-gray-800">
                Hasil Evaluasi Staff
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Hasil pemeriksaan Staff Sarana dan Prasarana.
            </p>

        </div>


        @php
            $badgeClass = match ($laporan->kategori_kerusakan) {
                'Ringan' => 'bg-amber-100 text-amber-700 border-amber-200',
                'Sedang' => 'bg-orange-100 text-orange-700 border-orange-200',
                'Berat' => 'bg-red-100 text-red-700 border-red-200',
                default => 'bg-gray-100 text-gray-600 border-gray-200',
            };
        @endphp


        @if($laporan->kategori_kerusakan || $laporan->catatan_pemeriksaan)

            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4 space-y-4">

                {{-- Kategori Kerusakan --}}
                <div>

                    <p class="text-xs uppercase tracking-wider text-gray-500 mb-1">
                        Kategori Kerusakan
                    </p>

                    @if($laporan->kategori_kerusakan)

                        <span class="inline-flex items-center rounded-full border
                                     px-2.5 py-1 text-xs font-medium
                                     {{ $badgeClass }}">

                            {{ $laporan->kategori_kerusakan }}

                        </span>

                    @else
                        <p class="text-sm text-gray-700">-</p>
                    @endif

                </div>


                {{-- Catatan --}}
                <div>

                    <p class="text-xs uppercase tracking-wider text-gray-500 mb-1">
                        Catatan Pemeriksaan
                    </p>

                    <p class="text-sm text-gray-700 leading-relaxed">
                        {{ $laporan->catatan_pemeriksaan ?: '-' }}
                    </p>

                </div>

            </div>

        @else

            <div class="rounded-lg bg-gray-50 border border-gray-100
                        px-4 py-5 text-sm text-gray-500 text-center">
                Belum terdapat hasil evaluasi Staff.
            </div>

        @endif

    </div>
{{-- KEPUTUSAN KEPALA BIDANG --}}
<div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mt-6">

    <h2 class="text-lg font-bold text-gray-800 mb-2">
        Verifikasi Laporan
    </h2>

    <p class="text-sm text-gray-500 mb-5">
        Berikan keputusan terhadap laporan berdasarkan hasil evaluasi Staff.
    </p>

    <div class="flex justify-end gap-3">

    <button type="button"
            onclick="openKembalikanModal()"
            class="px-6 py-3 rounded-xl font-semibold text-white shadow-md
                   bg-gradient-to-r from-[#F59E0B] to-[#EF4444]
                   hover:opacity-90 transition">
        Kembalikan
    </button>

    <button type="button"
            onclick="openSetujuiModal()"
            class="px-6 py-3 rounded-xl font-semibold text-white shadow-md
                   bg-gradient-to-r from-[#114F72] to-[#16A394]
                   hover:opacity-90 transition">
        Setujui Laporan
    </button>

</div>


    {{-- MODAL KONFIRMASI SETUJUI --}}
<div id="setujuiModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeSetujuiModal()">

    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
         onclick="event.stopPropagation()">

        <h3 class="text-lg font-semibold text-gray-800">
            Setujui Laporan
        </h3>

        <p class="mt-2 text-sm text-gray-600">
            Apakah Anda yakin ingin menyetujui laporan ini?
        </p>

        <div class="mt-6 flex justify-end gap-3">

            <button type="button"
                    onclick="closeSetujuiModal()"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                Batal
            </button>

            <form action="{{ route('kabid.laporan.setujui', $laporan->id_laporan) }}"
                  method="POST">
                @csrf

                <button type="submit"
                        class="rounded-lg bg-gradient-to-r from-[#114F72] to-[#16A394] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition">
                    Ya, Setujui
                </button>
            </form>

        </div>
</div>
</div>
    {{-- MODAL KEMBALIKAN LAPORAN --}}
<div id="kembalikanModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4"
     onclick="if(event.target === this) closeKembalikanModal()">

    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
         onclick="event.stopPropagation()">

        <h3 class="text-lg font-semibold text-gray-800">
            Kembalikan Laporan
        </h3>

        <p class="mt-2 text-sm text-gray-600">
            Tuliskan alasan atau catatan pengembalian laporan.
        </p>

        <form action="{{ route('kabid.laporan.kembalikan', $laporan->id_laporan) }}"
              method="POST"
              class="mt-5">

            @csrf

            <label for="catatan_revisi_evaluasi"
                   class="block text-sm font-medium text-gray-700 mb-2">
                Catatan Pengembalian
                <span class="text-red-500">*</span>
            </label>

            <textarea
                id="catatan_revisi_evaluasi"
                name="catatan_revisi_evaluasi"
                rows="4"
                required
                maxlength="1000"
                placeholder="Tuliskan alasan laporan dikembalikan..."
                class="w-full rounded-xl border border-gray-300 px-4 py-3
                       text-sm focus:border-[#16A394] focus:ring-[#16A394]
                       resize-none">{{ old('catatan_revisi_evaluasi') }}</textarea>

            <div class="mt-6 flex justify-end gap-3">

                <button type="button"
                        onclick="closeKembalikanModal()"
                        class="rounded-lg border border-gray-300 px-4 py-2
                               text-sm font-medium text-gray-600
                               hover:bg-gray-50 transition">
                    Batal
                </button>

                <button type="submit"
                        class="rounded-lg bg-gradient-to-r from-[#F59E0B] to-[#EF4444]
                               px-4 py-2 text-sm font-semibold text-white
                               shadow-sm hover:opacity-90 transition">
                    Ya, Kembalikan
                </button>

            </div>

        </form>

    </div>
</div>

<script>
    function openSetujuiModal() {
        const modal = document.getElementById('setujuiModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeSetujuiModal() {
        const modal = document.getElementById('setujuiModal');

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function openKembalikanModal() {
        const modal = document.getElementById('kembalikanModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeKembalikanModal() {
        const modal = document.getElementById('kembalikanModal');

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
</script>
</div>
</div>


{{-- ============================== --}}
{{-- MODAL FOTO --}}
{{-- ============================== --}}

@if($laporan->fotoLaporan->count() > 0)

<div id="fotoModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 px-4"
     onclick="if(event.target === this) closeFotoModal()">


    {{-- Close --}}
    <button type="button"
            onclick="closeFotoModal()"
            class="absolute top-4 right-4 text-white/80 hover:text-white transition">

        <svg class="w-8 h-8"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">

            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"/>

        </svg>

    </button>


    {{-- Previous --}}
    <button type="button"
            id="fotoPrevBtn"
            onclick="showPrevFoto()"
            class="absolute left-4 top-1/2 -translate-y-1/2
                   text-white/80 hover:text-white transition">

        <svg class="w-10 h-10"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">

            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 19l-7-7 7-7"/>

        </svg>

    </button>


    {{-- Foto --}}
    <img id="fotoModalImg"
         src=""
         alt="Foto Dokumentasi"
         class="max-h-[85vh] max-w-full rounded-lg shadow-2xl">


    {{-- Next --}}
    <button type="button"
            id="fotoNextBtn"
            onclick="showNextFoto()"
            class="absolute right-4 top-1/2 -translate-y-1/2
                   text-white/80 hover:text-white transition">

        <svg class="w-10 h-10"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">

            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"/>

        </svg>

    </button>


    {{-- Counter --}}
    <div id="fotoCounter"
         class="absolute bottom-4 left-1/2 -translate-x-1/2
                text-white/80 text-sm">
    </div>

</div>


<script>
    const fotoList = @json(
        $laporan->fotoLaporan
            ->map(fn($foto) => asset('storage/' . $foto->file_foto))
            ->values()
    );

    let fotoIndex = 0;


    function updateFotoModal() {

        document.getElementById('fotoModalImg').src =
            fotoList[fotoIndex];

        document.getElementById('fotoCounter').textContent =
            (fotoIndex + 1) + ' / ' + fotoList.length;


        const showNavigation = fotoList.length > 1;

        document.getElementById('fotoPrevBtn').style.display =
            showNavigation ? 'block' : 'none';

        document.getElementById('fotoNextBtn').style.display =
            showNavigation ? 'block' : 'none';
    }


    function openFotoModal(index) {

        fotoIndex = index;

        updateFotoModal();

        const modal =
            document.getElementById('fotoModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        document.body.style.overflow = 'hidden';
    }


    function closeFotoModal() {

        const modal =
            document.getElementById('fotoModal');

        modal.classList.add('hidden');
        modal.classList.remove('flex');

        document.body.style.overflow = '';
    }


    function showPrevFoto() {

        fotoIndex =
            (fotoIndex - 1 + fotoList.length)
            % fotoList.length;

        updateFotoModal();
    }


    function showNextFoto() {

        fotoIndex =
            (fotoIndex + 1)
            % fotoList.length;

        updateFotoModal();
    }


    document.addEventListener('keydown', function (event) {

        const modal =
            document.getElementById('fotoModal');

        if (modal.classList.contains('hidden')) {
            return;
        }

        if (event.key === 'Escape') {
            closeFotoModal();
        }

        if (event.key === 'ArrowLeft') {
            showPrevFoto();
        }

        if (event.key === 'ArrowRight') {
            showNextFoto();
        }
    });
</script>

@endif

@endsection