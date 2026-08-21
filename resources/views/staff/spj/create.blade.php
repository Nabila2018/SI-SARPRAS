@extends('layouts.app')

@section('title', 'Tambah Dokumen SPJ - SI-SARPRAS')

@section('breadcrumb')
    <a href="{{ route('staff.spj.index') }}" class="text-gray-600 hover:text-[#114F72]">Dokumen SPJ</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-600">Tambah Dokumen SPJ</span>
@endsection

@section('content')
<div class="pb-12 space-y-6">

    <!-- Header Page -->
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Tambah Dokumen SPJ</h1>
        <p class="mt-1 text-sm text-gray-500">
            Buat Surat Pertanggungjawaban (SPJ) berbasis RAB yang seluruh laporannya telah 100% selesai dikerjakan.
        </p>
    </div>

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 font-semibold">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 space-y-1">
            <p class="font-bold">Terjadi Kesalahan Input:</p>
            <ul class="list-disc list-inside text-xs pl-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('staff.spj.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Card Form SPJ & Pilih RAB -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
            <h2 class="text-lg font-bold text-gray-800 border-b pb-3">Informasi SPJ & RAB Terkait</h2>

            <!-- Dropdown Pilih RAB -->
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-2">
                    Pilih RAB (Seluruh Laporan 100% Selesai) <span class="text-red-500">*</span>
                </label>
                <select name="id_rab" id="selectRab" required onchange="handleRabChange(this)" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-3">
                    <option value="">-- Pilih RAB Disetujui --</option>
                    @foreach($rabList as $rab)
                        <option value="{{ $rab->id_rab }}" {{ old('id_rab') === $rab->id_rab ? 'selected' : '' }}>
                            {{ $rab->id_rab }} - {{ $rab->nama_pasar }} (Rp {{ number_format($rab->total_biaya, 0, ',', '.') }} | {{ $rab->laporan->count() }} Laporan Selesai)
                        </option>
                    @endforeach
                </select>
                @if($rabList->isEmpty())
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">
                        * Belum ada RAB yang eligible (berstatus Disetujui, seluruh laporan 100% selesai & belum ber-SPJ).
                    </p>
                @endif
            </div>

            <!-- Nama Pekerjaan -->
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-2">
                    Nama Pekerjaan / Judul SPJ <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama_pekerjaan" value="{{ old('nama_pekerjaan') }}" required placeholder="Contoh: Pemeliharaan Bangunan Pasar Induk Tahun 2026" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-3">
            </div>

            <!-- Periode Mulai & Selesai -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-2">
                        Periode Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="periode_mulai" value="{{ old('periode_mulai') }}" required class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-3">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-2">
                        Periode Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="periode_selesai" value="{{ old('periode_selesai') }}" required class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-3">
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-2">
                    Keterangan Tambahan
                </label>
                <textarea name="keterangan" rows="3" placeholder="Catatan atau keterangan mengenai pertanggungjawaban fisik/keuangan..." class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-3">{{ old('keterangan') }}</textarea>
            </div>

            <!-- Upload File PDF SPJ -->
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-2">
                    Upload Dokumen PDF SPJ <span class="text-red-500">*</span>
                </label>
                <input type="file" name="file_spj" accept=".pdf" required class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-2 bg-gray-50">
                <p class="text-xs text-gray-500 mt-1">Format wajib PDF, ukuran maksimal 5 MB.</p>
            </div>
        </div>

        <!-- Form Action -->
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('staff.spj.index') }}" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold rounded-xl transition">
                Batal
            </a>
            <button type="submit" class="px-5 py-2.5 bg-[#114F72] hover:bg-[#114F72]/90 text-white text-xs font-bold rounded-xl shadow-sm transition">
                Simpan Dokumen SPJ
            </button>
        </div>
    </form>
</div>
@endsection