@extends('layouts.app')

@section('title', 'Master Data - SI-SARPRAS')

@section('breadcrumb')
    <span class="text-gray-600">Master Data</span>
@endsection

@section('content')
<div class="space-y-6 pb-12">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pengelolaan Master Data</h1>
            <p class="text-xs text-gray-500 mt-1">Kelola data acuan pasar, lokasi, fasilitas, kategori laporan, dan standar harga SAB.</p>
        </div>
    </div>



    <!-- TAB NAVIGATION BAR -->
    <div class="bg-white rounded-2xl p-2 shadow-sm border border-gray-100 flex overflow-x-auto gap-2">
        <a href="{{ route('staff.master.index', ['tab' => 'pasar']) }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $activeTab === 'pasar' ? 'bg-[#114F72] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
            <span>Pasar</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $activeTab === 'pasar' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">{{ $pasarList->count() }}</span>
        </a>

        <a href="{{ route('staff.master.index', ['tab' => 'lokasi']) }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $activeTab === 'lokasi' ? 'bg-[#114F72] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
            <span>Lokasi</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $activeTab === 'lokasi' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">{{ $lokasiList->count() }}</span>
        </a>

        <a href="{{ route('staff.master.index', ['tab' => 'fasilitas']) }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $activeTab === 'fasilitas' ? 'bg-[#114F72] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
            <span>Fasilitas</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $activeTab === 'fasilitas' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">{{ $fasilitasList->count() }}</span>
        </a>

        <a href="{{ route('staff.master.index', ['tab' => 'kategori']) }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $activeTab === 'kategori' ? 'bg-[#114F72] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
            <span>Kategori Laporan</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $activeTab === 'kategori' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">{{ $kategoriList->count() }}</span>
        </a>

        <a href="{{ route('staff.master.index', ['tab' => 'sab']) }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $activeTab === 'sab' ? 'bg-[#114F72] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
            <span>Master SAB</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $activeTab === 'sab' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">{{ $sabList->count() }}</span>
        </a>
    </div>

    <!-- TAB CONTENT AREA -->
    @if($activeTab === 'pasar')
        <!-- TAB 1: PASAR -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Master Data Pasar</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Daftar lokasi pasar utama di Kota Padang.</p>
                </div>
                <button type="button" onclick="openModalPasar()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#114F72] hover:bg-[#114F72]/90 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Pasar Baru
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-bold tracking-wider">
                        <tr>
                            <th class="py-3 px-4">ID Pasar</th>
                            <th class="py-3 px-4">Nama Pasar</th>
                            <th class="py-3 px-4">Alamat</th>
                            <th class="py-3 px-4 text-center">Jumlah Lokasi</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pasarList as $p)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-3.5 px-4 font-bold text-[#114F72]">{{ $p->id_pasar }}</td>
                                <td class="py-3.5 px-4 font-extrabold text-gray-800">{{ $p->nama_pasar }}</td>
                                <td class="py-3.5 px-4 text-gray-600">{{ $p->alamat ?? '-' }}</td>
                                <td class="py-3.5 px-4 text-center font-bold text-gray-700">
                                    <span class="px-2.5 py-1 bg-gray-100 rounded-lg text-gray-800 text-[11px]">{{ $p->lokasi_count }} Area/Blok</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold border {{ $p->status_aktif === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-300' }}">
                                        {{ $p->status_aktif ?? 'Aktif' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button" onclick="editPasar('{{ $p->id_pasar }}', '{{ addslashes($p->nama_pasar) }}', '{{ addslashes($p->alamat ?? '') }}')" class="px-2.5 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 rounded-lg font-bold transition">
                                            Edit
                                        </button>
                                        <form action="{{ route('staff.master.pasar.toggle-status', $p->id_pasar) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1.5 {{ $p->status_aktif === 'Aktif' ? 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }} border rounded-lg font-bold transition">
                                                {{ $p->status_aktif === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-8 text-center text-gray-400">Belum ada data pasar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($activeTab === 'lokasi')
        <!-- TAB 2: LOKASI -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Master Data Lokasi & Hierarki</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola area, blok, kios, dan tempat usaha per pasar dengan hirarki Induk - Sub Lokasi.</p>
                </div>
                <button type="button" onclick="openModalLokasi()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#114F72] hover:bg-[#114F72]/90 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Lokasi Baru
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-bold tracking-wider">
                        <tr>
                            <th class="py-3 px-4">ID Lokasi</th>
                            <th class="py-3 px-4">Nama Lokasi / Hierarki</th>
                            <th class="py-3 px-4">Pasar</th>
                            <th class="py-3 px-4 text-center">Tipe</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($lokasiList as $l)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-3.5 px-4 font-bold text-[#114F72]">{{ $l->id_lokasi }}</td>
                                <td class="py-3.5 px-4 font-bold text-gray-800">
                                    @if($l->induk)
                                        <span class="text-gray-400 font-normal">{{ $l->induk->nama_lokasi }} &rarr; </span>
                                    @endif
                                    <span>{{ $l->nama_lokasi }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-gray-700">{{ $l->pasar->nama_pasar ?? '-' }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded text-[11px] font-bold">
                                        {{ $l->tipe_lokasi }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold border {{ $l->status_aktif === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-300' }}">
                                        {{ $l->status_aktif ?? 'Aktif' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button" onclick="editLokasi('{{ $l->id_lokasi }}', '{{ $l->id_pasar }}', '{{ $l->id_induk }}', '{{ addslashes($l->nama_lokasi) }}', '{{ $l->tipe_lokasi }}', '{{ $l->tahun_mulai_dibangun }}', '{{ $l->tahun_selesai_dibangun }}', '{{ $l->luas_tanah }}', '{{ $l->luas_bangunan }}', '{{ addslashes($l->keterangan ?? '') }}')" class="px-2.5 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 rounded-lg font-bold transition">
                                            Edit
                                        </button>
                                        <form action="{{ route('staff.master.lokasi.toggle-status', $l->id_lokasi) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1.5 {{ $l->status_aktif === 'Aktif' ? 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }} border rounded-lg font-bold transition">
                                                {{ $l->status_aktif === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-8 text-center text-gray-400">Belum ada data lokasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($activeTab === 'fasilitas')
        <!-- TAB 3: FASILITAS -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Master Data Fasilitas & Pemetaan Lokasi</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola jenis sarana/fasilitas dan pemetannya ke lokasi-lokasi pasar (`lokasi_fasilitas`).</p>
                </div>
                <button type="button" onclick="openModalFasilitas()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#114F72] hover:bg-[#114F72]/90 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Fasilitas Baru
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-bold tracking-wider">
                        <tr>
                            <th class="py-3 px-4">ID Fasilitas</th>
                            <th class="py-3 px-4">Nama Fasilitas</th>
                            <th class="py-3 px-4">Terhubung di Lokasi</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($fasilitasList as $f)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-3.5 px-4 font-bold text-[#114F72]">{{ $f->id_fasilitas }}</td>
                                <td class="py-3.5 px-4 font-bold text-gray-800">
                                    {{ $f->nama_fasilitas }}
                                    @if(in_array(strtolower($f->nama_fasilitas), ['ruang lainnya', 'lainnya']))
                                        <span class="ml-1 px-1.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded text-[10px] font-bold">Fallback</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-gray-600">
                                    @if($f->lokasiFasilitas->isNotEmpty())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($f->lokasiFasilitas->take(4) as $lf)
                                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-[10px] font-semibold">
                                                    {{ $lf->lokasi->nama_lokasi ?? '-' }} ({{ $lf->lokasi->pasar->nama_pasar ?? '-' }})
                                                </span>
                                            @endforeach
                                            @if($f->lokasiFasilitas->count() > 4)
                                                <span class="px-2 py-0.5 bg-sky-50 text-sky-700 rounded text-[10px] font-bold">+{{ $f->lokasiFasilitas->count() - 4 }} lagi</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Semua Lokasi / Belum dipetakan</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold border {{ $f->status_aktif === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-300' }}">
                                        {{ $f->status_aktif ?? 'Aktif' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        @php
                                            $mappedLokasiIds = json_encode($f->lokasiFasilitas->pluck('id_lokasi')->toArray());
                                        @endphp
                                        <button type="button" onclick="editFasilitas('{{ $f->id_fasilitas }}', '{{ addslashes($f->nama_fasilitas) }}', {{ $mappedLokasiIds }})" class="px-2.5 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 rounded-lg font-bold transition">
                                            Edit
                                        </button>
                                        @if(!in_array(strtolower(trim($f->nama_fasilitas)), ['ruang lainnya', 'lainnya']))
                                            <form action="{{ route('staff.master.fasilitas.toggle-status', $f->id_fasilitas) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-2.5 py-1.5 {{ $f->status_aktif === 'Aktif' ? 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }} border rounded-lg font-bold transition">
                                                    {{ $f->status_aktif === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-8 text-center text-gray-400">Belum ada data fasilitas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($activeTab === 'kategori')
        <!-- TAB 4: KATEGORI LAPORAN -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Master Kategori Laporan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Daftar kategori kerusakan sarana yang dapat dipilih pelapor UPTD.</p>
                </div>
                <button type="button" onclick="openModalKategori()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#114F72] hover:bg-[#114F72]/90 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Kategori Baru
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-bold tracking-wider">
                        <tr>
                            <th class="py-3 px-4">ID Kategori</th>
                            <th class="py-3 px-4">Nama Kategori</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($kategoriList as $k)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-3.5 px-4 font-bold text-[#114F72]">{{ $k->id_kategori }}</td>
                                <td class="py-3.5 px-4 font-bold text-gray-800">
                                    {{ $k->nama_kategori }}
                                    @if(strtolower(trim($k->nama_kategori)) === 'lainnya')
                                        <span class="ml-1 px-1.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded text-[10px] font-bold">Fallback</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold border {{ $k->status_aktif === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-300' }}">
                                        {{ $k->status_aktif ?? 'Aktif' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button" onclick="editKategori('{{ $k->id_kategori }}', '{{ addslashes($k->nama_kategori) }}')" class="px-2.5 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 rounded-lg font-bold transition">
                                            Edit
                                        </button>
                                        @if(strtolower(trim($k->nama_kategori)) !== 'lainnya')
                                            <form action="{{ route('staff.master.kategori.toggle-status', $k->id_kategori) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-2.5 py-1.5 {{ $k->status_aktif === 'Aktif' ? 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }} border rounded-lg font-bold transition">
                                                    {{ $k->status_aktif === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-gray-400">Belum ada data kategori.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($activeTab === 'sab')
        <!-- TAB 5: MASTER SAB -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Master Standar Harga Satuan (SAB)</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Daftar acuan standar kebutuhan material, bahan, dan harga perbaikan untuk penyusunan RAB.</p>
                </div>
                <button type="button" onclick="openModalSab()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#114F72] hover:bg-[#114F72]/90 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Item SAB Baru
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-bold tracking-wider">
                        <tr>
                            <th class="py-3 px-4">ID SAB</th>
                            <th class="py-3 px-4">Nama Kebutuhan</th>
                            <th class="py-3 px-4 text-center">Satuan</th>
                            <th class="py-3 px-4 text-right">Harga Standar</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($sabList as $s)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-3.5 px-4 font-bold text-[#114F72]">{{ $s->id_sab }}</td>
                                <td class="py-3.5 px-4 font-bold text-gray-800">{{ $s->nama_kebutuhan }}</td>
                                <td class="py-3.5 px-4 text-center font-semibold text-gray-700">{{ $s->satuan }}</td>
                                <td class="py-3.5 px-4 text-right font-extrabold text-gray-800">Rp {{ number_format($s->harga_standar, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold border {{ $s->status_aktif === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-300' }}">
                                        {{ $s->status_aktif ?? 'Aktif' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button" onclick="editSab('{{ $s->id_sab }}', '{{ addslashes($s->nama_kebutuhan) }}', '{{ addslashes($s->satuan) }}', '{{ $s->harga_standar }}')" class="px-2.5 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 rounded-lg font-bold transition">
                                            Edit
                                        </button>
                                        <form action="{{ route('staff.master.sab.toggle-status', $s->id_sab) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1.5 {{ $s->status_aktif === 'Aktif' ? 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }} border rounded-lg font-bold transition">
                                                {{ $s->status_aktif === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-8 text-center text-gray-400">Belum ada data SAB.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<!-- ==================== MODALS ==================== -->

<!-- MODAL PASAR -->
<div id="modalPasar" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-gray-100">
        <h3 id="modalPasarTitle" class="text-base font-bold text-gray-800 border-b pb-2">Tambah Pasar Baru</h3>
        <form id="formPasar" method="POST" action="{{ route('staff.master.pasar.store') }}" class="space-y-4">
            @csrf
            <div id="methodPasar"></div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Pasar <span class="text-rose-500">*</span></label>
                <input type="text" id="inputNamaPasar" name="nama_pasar" required placeholder="Contoh: Pasar Raya" class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#114F72]/20 focus:border-[#114F72]">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Alamat Pasar</label>
                <textarea id="inputAlamatPasar" name="alamat" rows="3" placeholder="Alamat lokasi pasar..." class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#114F72]/20 focus:border-[#114F72]"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModalPasar()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-[#114F72] hover:bg-[#114F72]/90 text-white text-xs font-bold rounded-xl shadow-md transition">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL LOKASI -->
<div id="modalLokasi" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <h3 id="modalLokasiTitle" class="text-base font-bold text-gray-800 border-b pb-2">Tambah Lokasi Baru</h3>
        <form id="formLokasi" method="POST" action="{{ route('staff.master.lokasi.store') }}" class="space-y-3 text-xs">
            @csrf
            <div id="methodLokasi"></div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Pasar <span class="text-rose-500">*</span></label>
                <select id="inputPasarLokasi" name="id_pasar" required class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-[#114F72]/20">
                    <option value="">-- Pilih Pasar --</option>
                    @foreach($pasarList as $p)
                        <option value="{{ $p->id_pasar }}">{{ $p->nama_pasar }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Lokasi Induk (Opsional untuk Hierarki)</label>
                <select id="inputIndukLokasi" name="id_induk" class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-[#114F72]/20">
                    <option value="">-- Tidak Ada (Sebagai Induk Utama) --</option>
                    @foreach($lokasiList as $l)
                        <option value="{{ $l->id_lokasi }}">{{ $l->pasar->nama_pasar ?? '-' }} &rarr; {{ $l->nama_lokasi }} ({{ $l->tipe_lokasi }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama Lokasi <span class="text-rose-500">*</span></label>
                    <input type="text" id="inputNamaLokasi" name="nama_lokasi" required placeholder="Contoh: Blok A" class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-[#114F72]/20">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Tipe Lokasi <span class="text-rose-500">*</span></label>
                    <input type="text" id="inputTipeLokasi" name="tipe_lokasi" required placeholder="Contoh: Area / Blok / Kios" class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-[#114F72]/20">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Tahun Mulai Dibangun</label>
                    <input type="number" id="inputTahunMulai" name="tahun_mulai_dibangun" placeholder="Contoh: 2018" class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Tahun Selesai Dibangun</label>
                    <input type="number" id="inputTahunSelesai" name="tahun_selesai_dibangun" placeholder="Contoh: 2019" class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Luas Tanah (m²)</label>
                    <input type="number" step="0.01" id="inputLuasTanah" name="luas_tanah" placeholder="0" class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Luas Bangunan (m²)</label>
                    <input type="number" step="0.01" id="inputLuasBangunan" name="luas_bangunan" placeholder="0" class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none">
                </div>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Keterangan</label>
                <textarea id="inputKeteranganLokasi" name="keterangan" rows="2" placeholder="Keterangan tambahan..." class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModalLokasi()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-[#114F72] hover:bg-[#114F72]/90 text-white font-bold rounded-xl shadow-md transition">Simpan Lokasi</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL FASILITAS -->
<div id="modalFasilitas" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <h3 id="modalFasilitasTitle" class="text-base font-bold text-gray-800 border-b pb-2">Tambah Fasilitas Baru</h3>
        <form id="formFasilitas" method="POST" action="{{ route('staff.master.fasilitas.store') }}" class="space-y-4 text-xs">
            @csrf
            <div id="methodFasilitas"></div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Nama Fasilitas / Sarana <span class="text-rose-500">*</span></label>
                <input type="text" id="inputNamaFasilitas" name="nama_fasilitas" required placeholder="Contoh: Toilet Umum / Kran Air" class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-[#114F72]/20">
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Hubungkan ke Lokasi Pasar (Mapping `lokasi_fasilitas`)</label>
                <p class="text-[11px] text-gray-500 mb-2">Pilih lokasi di mana fasilitas ini berada agar muncul pada form UPTD.</p>
                <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-xl p-3 bg-gray-50/50 space-y-1.5">
                    @foreach($lokasiList as $lok)
                        <label class="flex items-center gap-2 cursor-pointer text-gray-700 hover:text-gray-900">
                            <input type="checkbox" name="lokasi_ids[]" value="{{ $lok->id_lokasi }}" class="checkboxLokasiFasilitas rounded border-gray-300 text-[#114F72] focus:ring-[#114F72]">
                            <span>{{ $lok->pasar->nama_pasar ?? '-' }} &rarr; {{ $lok->nama_lokasi }} ({{ $lok->tipe_lokasi }})</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModalFasilitas()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-[#114F72] hover:bg-[#114F72]/90 text-white font-bold rounded-xl shadow-md transition">Simpan Fasilitas</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL KATEGORI -->
<div id="modalKategori" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-gray-100">
        <h3 id="modalKategoriTitle" class="text-base font-bold text-gray-800 border-b pb-2">Tambah Kategori Baru</h3>
        <form id="formKategori" method="POST" action="{{ route('staff.master.kategori.store') }}" class="space-y-4 text-xs">
            @csrf
            <div id="methodKategori"></div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                <input type="text" id="inputNamaKategori" name="nama_kategori" required placeholder="Contoh: Sanitasi & Air" class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-[#114F72]/20">
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModalKategori()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-[#114F72] hover:bg-[#114F72]/90 text-white font-bold rounded-xl shadow-md transition">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL SAB -->
<div id="modalSab" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-gray-100">
        <h3 id="modalSabTitle" class="text-base font-bold text-gray-800 border-b pb-2">Tambah Item SAB Baru</h3>
        <form id="formSab" method="POST" action="{{ route('staff.master.sab.store') }}" class="space-y-3 text-xs">
            @csrf
            <div id="methodSab"></div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Nama Kebutuhan / Material <span class="text-rose-500">*</span></label>
                <input type="text" id="inputNamaSab" name="nama_kebutuhan" required placeholder="Contoh: Semen Portland 40kg" class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-[#114F72]/20">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Satuan <span class="text-rose-500">*</span></label>
                    <input type="text" id="inputSatuanSab" name="satuan" required placeholder="Contoh: Sak / M3 / Pcs" class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Harga Standar (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" id="inputHargaSab" name="harga_standar" required placeholder="75000" class="w-full p-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModalSab()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-[#114F72] hover:bg-[#114F72]/90 text-white font-bold rounded-xl shadow-md transition">Simpan Item SAB</button>
            </div>
        </form>
    </div>
</div>

<script>
    // PASAR
    function openModalPasar() {
        document.getElementById('modalPasarTitle').innerText = 'Tambah Pasar Baru';
        document.getElementById('formPasar').action = "{{ route('staff.master.pasar.store') }}";
        document.getElementById('methodPasar').innerHTML = '';
        document.getElementById('inputNamaPasar').value = '';
        document.getElementById('inputAlamatPasar').value = '';
        document.getElementById('modalPasar').classList.remove('hidden');
    }
    function editPasar(id, nama, alamat) {
        document.getElementById('modalPasarTitle').innerText = 'Edit Data Pasar (' + id + ')';
        document.getElementById('formPasar').action = "/staff/master/pasar/" + id;
        document.getElementById('methodPasar').innerHTML = '@method("PUT")';
        document.getElementById('inputNamaPasar').value = nama;
        document.getElementById('inputAlamatPasar').value = alamat;
        document.getElementById('modalPasar').classList.remove('hidden');
    }
    function closeModalPasar() { document.getElementById('modalPasar').classList.add('hidden'); }

    // LOKASI
    function openModalLokasi() {
        document.getElementById('modalLokasiTitle').innerText = 'Tambah Lokasi Baru';
        document.getElementById('formLokasi').action = "{{ route('staff.master.lokasi.store') }}";
        document.getElementById('methodLokasi').innerHTML = '';
        document.getElementById('inputPasarLokasi').value = '';
        document.getElementById('inputIndukLokasi').value = '';
        document.getElementById('inputNamaLokasi').value = '';
        document.getElementById('inputTipeLokasi').value = '';
        document.getElementById('inputTahunMulai').value = '';
        document.getElementById('inputTahunSelesai').value = '';
        document.getElementById('inputLuasTanah').value = '';
        document.getElementById('inputLuasBangunan').value = '';
        document.getElementById('inputKeteranganLokasi').value = '';
        document.getElementById('modalLokasi').classList.remove('hidden');
    }
    function editLokasi(id, idPasar, idInduk, nama, tipe, tMulai, tSelesai, lTanah, lBangunan, ket) {
        document.getElementById('modalLokasiTitle').innerText = 'Edit Lokasi (' + id + ')';
        document.getElementById('formLokasi').action = "/staff/master/lokasi/" + id;
        document.getElementById('methodLokasi').innerHTML = '@method("PUT")';
        document.getElementById('inputPasarLokasi').value = idPasar;
        document.getElementById('inputIndukLokasi').value = idInduk;
        document.getElementById('inputNamaLokasi').value = nama;
        document.getElementById('inputTipeLokasi').value = tipe;
        document.getElementById('inputTahunMulai').value = tMulai;
        document.getElementById('inputTahunSelesai').value = tSelesai;
        document.getElementById('inputLuasTanah').value = lTanah;
        document.getElementById('inputLuasBangunan').value = lBangunan;
        document.getElementById('inputKeteranganLokasi').value = ket;
        document.getElementById('modalLokasi').classList.remove('hidden');
    }
    function closeModalLokasi() { document.getElementById('modalLokasi').classList.add('hidden'); }

    // FASILITAS
    function openModalFasilitas() {
        document.getElementById('modalFasilitasTitle').innerText = 'Tambah Fasilitas Baru';
        document.getElementById('formFasilitas').action = "{{ route('staff.master.fasilitas.store') }}";
        document.getElementById('methodFasilitas').innerHTML = '';
        document.getElementById('inputNamaFasilitas').value = '';
        document.querySelectorAll('.checkboxLokasiFasilitas').forEach(cb => cb.checked = false);
        document.getElementById('modalFasilitas').classList.remove('hidden');
    }
    function editFasilitas(id, nama, mappedLokasiIds) {
        document.getElementById('modalFasilitasTitle').innerText = 'Edit Fasilitas (' + id + ')';
        document.getElementById('formFasilitas').action = "/staff/master/fasilitas/" + id;
        document.getElementById('methodFasilitas').innerHTML = '@method("PUT")';
        document.getElementById('inputNamaFasilitas').value = nama;
        document.querySelectorAll('.checkboxLokasiFasilitas').forEach(cb => {
            cb.checked = mappedLokasiIds.includes(cb.value);
        });
        document.getElementById('modalFasilitas').classList.remove('hidden');
    }
    function closeModalFasilitas() { document.getElementById('modalFasilitas').classList.add('hidden'); }

    // KATEGORI
    function openModalKategori() {
        document.getElementById('modalKategoriTitle').innerText = 'Tambah Kategori Baru';
        document.getElementById('formKategori').action = "{{ route('staff.master.kategori.store') }}";
        document.getElementById('methodKategori').innerHTML = '';
        document.getElementById('inputNamaKategori').value = '';
        document.getElementById('modalKategori').classList.remove('hidden');
    }
    function editKategori(id, nama) {
        document.getElementById('modalKategoriTitle').innerText = 'Edit Kategori (' + id + ')';
        document.getElementById('formKategori').action = "/staff/master/kategori/" + id;
        document.getElementById('methodKategori').innerHTML = '@method("PUT")';
        document.getElementById('inputNamaKategori').value = nama;
        document.getElementById('modalKategori').classList.remove('hidden');
    }
    function closeModalKategori() { document.getElementById('modalKategori').classList.add('hidden'); }

    // SAB
    function openModalSab() {
        document.getElementById('modalSabTitle').innerText = 'Tambah Item SAB Baru';
        document.getElementById('formSab').action = "{{ route('staff.master.sab.store') }}";
        document.getElementById('methodSab').innerHTML = '';
        document.getElementById('inputNamaSab').value = '';
        document.getElementById('inputSatuanSab').value = '';
        document.getElementById('inputHargaSab').value = '';
        document.getElementById('modalSab').classList.remove('hidden');
    }
    function editSab(id, nama, satuan, harga) {
        document.getElementById('modalSabTitle').innerText = 'Edit Item SAB (' + id + ')';
        document.getElementById('formSab').action = "/staff/master/sab/" + id;
        document.getElementById('methodSab').innerHTML = '@method("PUT")';
        document.getElementById('inputNamaSab').value = nama;
        document.getElementById('inputSatuanSab').value = satuan;
        document.getElementById('inputHargaSab').value = harga;
        document.getElementById('modalSab').classList.remove('hidden');
    }
    function closeModalSab() { document.getElementById('modalSab').classList.add('hidden'); }
</script>
@endsection
