@extends('layouts.app')

@section('title', 'Beranda - SI-SARPRAS')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">
            Selamat Datang, {{ auth()->user()->nama_lengkap }}
        </h1>
        <p class="text-gray-500">
            Anda masuk sebagai <span class="font-semibold text-[#003366]">{{ auth()->user()->role->nama_role }}</span>
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1 -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#003366]">
            <p class="text-sm text-gray-500">Total Laporan</p>
            <p class="text-2xl font-bold text-[#003366] mt-1">0</p>
        </div>
        <!-- Card 2 -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#0066CC]">
            <p class="text-sm text-gray-500">Menunggu</p>
            <p class="text-2xl font-bold text-[#0066CC] mt-1">0</p>
        </div>
        <!-- Card 3 -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-[#00A86B]">
            <p class="text-sm text-gray-500">Diproses</p>
            <p class="text-2xl font-bold text-[#00A86B] mt-1">0</p>
        </div>
        <!-- Card 4 -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-gray-400">
            <p class="text-sm text-gray-500">Selesai</p>
            <p class="text-2xl font-bold text-gray-600 mt-1">0</p>
        </div>
    </div>
</div>
@endsection