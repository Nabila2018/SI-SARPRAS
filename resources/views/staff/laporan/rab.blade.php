@extends('layouts.app')

@section('title', 'Penyusunan RAB - SI-SARPRAS')

@section('content')
<div class="max-w-xl mx-auto my-12 p-8 bg-white rounded-2xl shadow-sm border border-gray-200 text-center space-y-4">
    <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center mx-auto font-bold text-lg">
        !
    </div>
    <h2 class="text-lg font-bold text-gray-800">Penyusunan RAB Berpusat di Menu RAB</h2>
    <p class="text-xs text-gray-600 leading-relaxed">
        Rencana Anggaran Biaya (RAB) disusun dari menu utama RAB dengan menggabungkan 1 atau beberapa laporan yang disetujui Kabid.
    </p>
    <div class="pt-2">
        <a href="{{ route('staff.rab.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#114F72] text-white font-bold rounded-xl text-xs shadow hover:bg-[#114F72]/90 transition">
            Ke Halaman Menu RAB &rarr;
        </a>
    </div>
</div>
@endsection