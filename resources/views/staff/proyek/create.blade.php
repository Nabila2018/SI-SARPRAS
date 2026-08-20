@extends('layouts.app')

@section('title', 'Buat Proyek Perbaikan Baru - SI-SARPRAS')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('staff.proyek.index') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-[#114F72] transition font-medium mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Proyek
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Buat Proyek Perbaikan Baru</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelompokkan beberapa laporan perbaikan yang evaluasinya telah disetujui dari pasar yang sama.</p>
        </div>
    </div>

    <!-- Error Validation Alert -->
    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-semibold space-y-1">
            <div class="flex items-center gap-2 text-rose-900 font-bold">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Gagal Membuat Proyek:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700 pl-7">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Create Proyek -->
    <form action="{{ route('staff.proyek.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 space-y-5">
            <h3 class="text-base font-bold text-gray-800 border-b border-gray-100 pb-3">Informasi Proyek</h3>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Nama Proyek <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama_proyek" value="{{ old('nama_proyek') }}" required placeholder="Contoh: Proyek Perbaikan Sarana Sanitasi & Atap Pasar Raya 2026" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#114F72] text-sm">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Deskripsi Proyek <span class="text-gray-400 font-normal text-xs uppercase">(Opsional)</span>
                </label>
                <textarea name="deskripsi_proyek" rows="3" placeholder="Tuliskan catatan lingkup perbaikan proyek..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#114F72] text-sm">{{ old('deskripsi_proyek') }}</textarea>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">
                    Pilih Pasar <span class="text-red-500">*</span>
                </label>
                <select id="select_pasar" name="id_pasar" required onchange="filterLaporanByPasar(this.value)" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#114F72] text-sm">
                    <option value="">-- Pilih Pasar --</option>
                    @foreach($pasarList as $pasar)
                        <option value="{{ $pasar->id_pasar }}" {{ old('id_pasar', $selectedPasarId) == $pasar->id_pasar ? 'selected' : '' }}>
                            {{ $pasar->nama_pasar }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Memilih pasar akan menampilkan daftar laporan berstatus "Disetujui" dari pasar tersebut.</p>
            </div>
        </div>

        <!-- Tabel Checkbox Laporan Eligible -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Pilih Laporan Terkait Proyek</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Centang laporan eligible (berstatus Disetujui dan belum masuk proyek).</p>
                </div>
                @if($selectedPasarId && $laporanEligible->isNotEmpty())
                    <label class="inline-flex items-center gap-2 text-xs font-semibold text-[#114F72] cursor-pointer">
                        <input type="checkbox" id="check_all_laporan" onchange="toggleCheckAll(this)" class="rounded text-[#114F72] focus:ring-[#114F72]">
                        Pilih Semua ({{ $laporanEligible->count() }})
                    </label>
                @endif
            </div>

            @if(!$selectedPasarId)
                <div class="p-8 text-center text-sm text-gray-500 bg-gray-50 rounded-xl border border-gray-100">
                    Silakan pilih pasar di atas terlebih dahulu untuk menampilkan daftar laporan yang dapat dimasukkan ke proyek.
                </div>
            @elseif($laporanEligible->isEmpty())
                <div class="p-8 text-center text-sm text-amber-800 bg-amber-50/60 rounded-xl border border-amber-200/60">
                    <p class="font-semibold">Tidak ada laporan eligible yang tersedia untuk pasar ini.</p>
                    <p class="text-xs text-amber-600 mt-1">Laporan harus berstatus "Disetujui" dan belum dimasukkan ke dalam proyek lain.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">Pilih</th>
                                <th class="px-4 py-3">ID Laporan</th>
                                <th class="px-4 py-3">Fasilitas / Item Kerusakan</th>
                                <th class="px-4 py-3">Lokasi Specific</th>
                                <th class="px-4 py-3">Pelapor</th>
                                <th class="px-4 py-3">Kategori Kerusakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($laporanEligible as $laporan)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-4 py-3.5 text-center">
                                        <input type="checkbox" name="id_laporan[]" value="{{ $laporan->id_laporan }}" class="laporan-checkbox rounded text-[#114F72] focus:ring-[#114F72]" {{ is_array(old('id_laporan')) && in_array($laporan->id_laporan, old('id_laporan')) ? 'checked' : '' }}>
                                    </td>
                                    <td class="px-4 py-3.5 font-bold text-[#114F72]">{{ $laporan->id_laporan }}</td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-semibold text-gray-800">{{ $laporan->nama_fasilitas_display }}</div>
                                        <div class="text-xs text-gray-500">{{ $laporan->item_kerusakan }}</div>
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-gray-600">
                                        {{ $laporan->lokasi->nama_lokasi ?? '-' }} ({{ $laporan->lokasi_spesifik }})
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-gray-600">
                                        {{ $laporan->pelapor->nama_lengkap ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @php
                                            $badgeClass = match($laporan->kategori_kerusakan) {
                                                'Ringan' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'Sedang' => 'bg-orange-100 text-orange-800 border-orange-200',
                                                'Berat' => 'bg-red-100 text-red-800 border-red-200',
                                                default => 'bg-gray-100 text-gray-700 border-gray-200',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                            {{ $laporan->kategori_kerusakan ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('staff.proyek.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 font-medium text-gray-600 hover:bg-gray-50 transition text-sm">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#114F72] to-[#16A394] text-white font-semibold shadow-md hover:opacity-90 transition text-sm" {{ !$selectedPasarId || $laporanEligible->isEmpty() ? 'disabled' : '' }}>
                Simpan Proyek Perbaikan
            </button>
        </div>
    </form>
</div>

<script>
    function filterLaporanByPasar(pasarId) {
        if (!pasarId) {
            window.location.href = "{{ route('staff.proyek.create') }}";
        } else {
            window.location.href = "{{ route('staff.proyek.create') }}?id_pasar=" + pasarId;
        }
    }

    function toggleCheckAll(source) {
        const checkboxes = document.querySelectorAll('.laporan-checkbox');
        checkboxes.forEach(cb => cb.checked = source.checked);
    }
</script>
@endsection
