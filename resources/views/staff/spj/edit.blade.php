@extends('layouts.app')

@section('title', 'Edit Dokumen SPJ - SI-SARPRAS')

@section('breadcrumb')
    <a href="{{ route('staff.spj.index') }}" class="text-gray-600 hover:text-[#114F72]">Dokumen SPJ</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-600">Edit Dokumen SPJ</span>
@endsection

@section('content')
<div class="pb-12 space-y-6 max-w-4xl mx-auto">

    <!-- Header Page -->
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Edit Dokumen SPJ: {{ $spj->id_spj }}</h1>
        <p class="mt-1 text-sm text-gray-500">
            Perbarui informasi atau file dokumen Surat Pertanggungjawaban (SPJ).
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

    <form action="{{ route('staff.spj.update', $spj->id_spj) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
            <h2 class="text-lg font-bold text-gray-800 border-b pb-3">Form Edit SPJ</h2>

            <!-- RAB Terikat (Read-Only) -->
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-500 mb-1">
                    RAB Terikat (Tidak Dapat Diubah)
                </label>
                <input type="text" value="{{ $spj->id_rab }} (Pasar: {{ $spj->rab->nama_pasar ?? '-' }})" disabled class="w-full text-sm rounded-xl border-gray-200 bg-gray-100 text-gray-700 p-3 cursor-not-allowed font-semibold">
            </div>

            <!-- Nama Pekerjaan -->
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-2">
                    Nama Pekerjaan / Judul SPJ <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama_pekerjaan" value="{{ old('nama_pekerjaan', $spj->nama_pekerjaan) }}" required class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-3">
            </div>

            <!-- Periode Mulai & Selesai -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-2">
                        Periode Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="periode_mulai" value="{{ old('periode_mulai', \Carbon\Carbon::parse($spj->periode_mulai)->format('Y-m-d')) }}" required class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-3">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-2">
                        Periode Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="periode_selesai" value="{{ old('periode_selesai', \Carbon\Carbon::parse($spj->periode_selesai)->format('Y-m-d')) }}" required class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-3">
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-2">
                    Keterangan Tambahan
                </label>
                <textarea name="keterangan" rows="3" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-3">{{ old('keterangan', $spj->keterangan) }}</textarea>
            </div>

            <!-- Upload File PDF SPJ Baru -->
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-2">
                    Ganti File PDF SPJ (Opsional)
                </label>
                @if($spj->file_spj)
                    <div class="mb-2 text-xs text-gray-600 flex items-center gap-2">
                        <span>File saat ini:</span>
                        <a href="{{ asset('storage/' . $spj->file_spj) }}" target="_blank" class="font-bold text-[#114F72] hover:underline">{{ basename($spj->file_spj) }}</a>
                    </div>
                @endif
                <input type="file" name="file_spj" accept=".pdf" class="w-full text-sm rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-2 bg-gray-50">
                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah file PDF.</p>
            </div>
        </div>

        <!-- Form Action -->
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('staff.spj.index') }}" class="px-5 py-2.5 border border-gray-300 text-gray-600 rounded-xl text-sm font-semibold hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-[#114F72] text-white font-bold rounded-xl text-sm shadow hover:bg-[#114F72]/90 transition">
                Update Dokumen SPJ
            </button>
        </div>
    </form>
</div>
@endsection
