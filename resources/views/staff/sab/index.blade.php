@extends('layouts.app')

@section('title', 'Master SAB (Standar Analisa Biaya) - SI-SARPRAS')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Master Standar Analisa Biaya (SAB)</h1>
            <p class="text-xs text-gray-500 mt-1">Daftar patokan harga standar bahan, alat, dan pekerjaan perbaikan sarana prasarana.</p>
        </div>

        <button type="button" onclick="document.getElementById('modalTambahSab').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#114F72] to-[#16A394] text-white font-bold rounded-xl text-sm shadow hover:opacity-95 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Master SAB
        </button>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-semibold space-y-1">
            <div class="flex items-center gap-2 text-rose-900 font-bold">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Terjadi Kesalahan Input:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700 pl-7">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filter & Search Card -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-200">
        <form method="GET" action="{{ route('staff.sab.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID, nama kebutuhan, atau satuan..." class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-2.5">
            </div>

            <div>
                <select name="status" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-2.5">
                    <option value="">-- Semua Status --</option>
                    <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Nonaktif" {{ request('status') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 bg-[#114F72] text-white font-bold rounded-xl text-xs shadow hover:bg-[#114F72]/90 transition">
                    Cari / Filter
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('staff.sab.index') }}" class="px-4 py-2.5 border border-gray-300 text-gray-600 rounded-xl text-xs font-semibold hover:bg-gray-50 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Master SAB -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-700 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">ID SAB</th>
                        <th class="px-6 py-4">Nama Kebutuhan</th>
                        <th class="px-6 py-4">Satuan</th>
                        <th class="px-6 py-4 text-right">Harga Standar (Rp)</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sabList as $sab)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-bold text-[#114F72] text-xs">{{ $sab->id_sab }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-800 text-xs">{{ $sab->nama_kebutuhan }}</td>
                            <td class="px-6 py-4 text-xs text-gray-600">{{ $sab->satuan }}</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-800 text-xs">
                                Rp {{ number_format($sab->harga_standar, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($sab->status_aktif === 'Aktif')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Edit Modal Button -->
                                    <button type="button" onclick="openEditModal('{{ $sab->id_sab }}', '{{ addslashes($sab->nama_kebutuhan) }}', '{{ addslashes($sab->satuan) }}', {{ $sab->harga_standar }}, '{{ $sab->status_aktif }}')" class="p-1.5 text-[#114F72] hover:bg-[#114F72]/10 rounded-lg transition" title="Edit SAB">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <!-- Toggle Status Button -->
                                    <form action="{{ route('staff.sab.toggle-status', $sab->id_sab) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mengubah status SAB ini?')" class="px-2.5 py-1 rounded-lg text-xs font-bold border transition {{ $sab->status_aktif === 'Aktif' ? 'border-rose-200 text-rose-600 hover:bg-rose-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }}">
                                            {{ $sab->status_aktif === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada data Master SAB.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sabList->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $sabList->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah SAB -->
<div id="modalTambahSab" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base font-bold text-gray-800">Tambah Master SAB Baru</h3>
            <button type="button" onclick="document.getElementById('modalTambahSab').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('staff.sab.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">Nama Kebutuhan <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kebutuhan" required placeholder="Misal: Cat Tembok 20kg" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-2.5">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                <input type="text" name="satuan" required placeholder="Misal: Sak / Dus / Pail / M3" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-2.5">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">Harga Standar (Rp) <span class="text-red-500">*</span></label>
                <input type="number" step="1" name="harga_standar" required placeholder="0" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-2.5">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">Status Awal</label>
                <select name="status_aktif" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-2.5">
                    <option value="Aktif" selected>Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalTambahSab').classList.add('hidden')" class="px-4 py-2 border border-gray-300 text-gray-600 rounded-xl text-xs font-semibold hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-5 py-2 bg-[#114F72] text-white rounded-xl text-xs font-bold shadow hover:bg-[#114F72]/90">Simpan SAB</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit SAB -->
<div id="modalEditSab" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base font-bold text-gray-800">Edit Master SAB: <span id="editSabIdText"></span></h3>
            <button type="button" onclick="document.getElementById('modalEditSab').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="formEditSab" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">Nama Kebutuhan <span class="text-red-500">*</span></label>
                <input type="text" id="editNamaKebutuhan" name="nama_kebutuhan" required class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-2.5">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                <input type="text" id="editSatuan" name="satuan" required class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-2.5">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">Harga Standar (Rp) <span class="text-red-500">*</span></label>
                <input type="number" step="1" id="editHargaStandar" name="harga_standar" required class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-2.5">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider font-semibold text-gray-700 mb-1">Status Aktif</label>
                <select id="editStatusAktif" name="status_aktif" class="w-full text-xs rounded-xl border-gray-300 shadow-sm focus:border-[#114F72] focus:ring-[#114F72] p-2.5">
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalEditSab').classList.add('hidden')" class="px-4 py-2 border border-gray-300 text-gray-600 rounded-xl text-xs font-semibold hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-5 py-2 bg-[#114F72] text-white rounded-xl text-xs font-bold shadow hover:bg-[#114F72]/90">Update SAB</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, nama, satuan, harga, status) {
        document.getElementById('editSabIdText').textContent = id;
        document.getElementById('editNamaKebutuhan').value = nama;
        document.getElementById('editSatuan').value = satuan;
        document.getElementById('editHargaStandar').value = harga;
        document.getElementById('editStatusAktif').value = status;

        const form = document.getElementById('formEditSab');
        form.action = `/staff/sab/${id}`;

        document.getElementById('modalEditSab').classList.remove('hidden');
    }
</script>
@endsection
