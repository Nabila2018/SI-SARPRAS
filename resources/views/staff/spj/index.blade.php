@extends('layouts.app')

@section('title', 'Dokumen SPJ - SI-SARPRAS')

@section('breadcrumb')
    <span class="text-gray-600">Dokumen SPJ</span>
@endsection

@section('content')
<div class="pb-12">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            Dokumen SPJ
        </h1>

        <p class="mt-2 text-sm text-gray-500">
            Kelola dokumen Surat Pertanggungjawaban (SPJ) yang digunakan sebagai administrasi kegiatan perbaikan sarana dan prasarana pasar.
        </p>
    </div>



    <!-- Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">

        <!-- Card Header -->
        <div class="bg-gradient-to-r from-[#114F72]/5 to-[#16A394]/5 border-b border-gray-200 px-6 py-5">

            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">

                <div class="flex items-center gap-3">

                    <div class="w-11 h-11 rounded-xl bg-[#114F72]/10 flex items-center justify-center">

                        <svg class="w-6 h-6 text-[#114F72]"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>

                        </svg>

                    </div>

                    <div>

                        <h2 class="text-lg font-bold text-gray-800">
                            Daftar Dokumen SPJ
                        </h2>

                        <p class="text-sm text-gray-500">
                            Daftar seluruh dokumen SPJ yang telah dibuat.
                        </p>

                    </div>

                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3">

                    <!-- Button (Staff Only) - KIRI -->
                    @if(auth()->user()->role->nama_role === 'Staff Sarana dan Prasarana')
                        <a href="{{ route('staff.spj.create') }}"
                           class="h-10 inline-flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-[#114F72] to-[#16A394] px-4 text-xs font-semibold text-white hover:opacity-90 transition flex-shrink-0 leading-none">

                            <svg class="w-4 h-4"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 4v16m8-8H4"/>

                            </svg>

                            <span>Tambah Dokumen SPJ</span>

                        </a>
                    @endif

                    <!-- Search - KANAN -->
                    <form method="GET" class="m-0 p-0 flex items-center h-10 w-full sm:w-auto">

                        <div class="relative h-10 w-full sm:w-64 flex items-center">

                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Cari nama pekerjaan..."
                                class="h-10 w-full rounded-full border border-gray-300 pl-9 pr-4 text-xs text-gray-700 leading-none focus:border-[#114F72] focus:outline-none focus:ring-2 focus:ring-[#114F72]/20">

                            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">

                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="m21 21-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/>

                            </svg>

                        </div>

                    </form>

                </div>

            </div>

        </div>

        <!-- Table -->
        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            No
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            ID RAB & Lokasi
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Nama Pekerjaan
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Periode
                        </th>

                        <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">

                    @forelse($spjList as $index => $spj)

                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $spjList->firstItem() + $index }}
                            </td>

                            <td class="px-6 py-4">
                                @if($spj->rab)
                                    <a href="{{ route('staff.rab.show', $spj->rab->id_rab) }}" class="font-bold text-[#114F72] text-xs hover:underline">
                                        {{ $spj->rab->id_rab }}
                                    </a>
                                    <div class="text-xs text-gray-500">
                                        {{ $spj->rab->nama_pasar }}
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">

                                <p class="font-semibold text-gray-800">
                                    {{ $spj->nama_pekerjaan }}
                                </p>

                                @if($spj->keterangan)
                                    <p class="mt-1 text-xs text-gray-500 line-clamp-1">
                                        {{ $spj->keterangan }}
                                    </p>
                                @endif

                            </td>

                            <td class="px-6 py-4 text-sm text-gray-700">

                                {{ \Carbon\Carbon::parse($spj->periode_mulai)->format('d M Y') }}

                                <span class="mx-1 text-gray-400">—</span>

                                {{ \Carbon\Carbon::parse($spj->periode_selesai)->format('d M Y') }}

                            </td>

                            <td class="px-6 py-4">

                                <div class="flex justify-center gap-2">

                                    <!-- Detail -->
                                    <a href="{{ route('staff.spj.show',$spj->id_spj) }}"
                                       class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-sky-100 text-sky-600 hover:bg-sky-200 transition"
                                       title="Detail">

                                        <svg class="w-5 h-5"
                                             fill="none"
                                             stroke="currentColor"
                                             viewBox="0 0 24 24">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5
                                                     c4.478 0 8.268 2.943 9.542 7
                                                     -1.274 4.057-5.064 7-9.542 7
                                                     -4.477 0-8.268-2.943-9.542-7z"/>

                                        </svg>

                                    </a>

                                    @if(auth()->user()->role->nama_role === 'Staff Sarana dan Prasarana')
                                        <!-- Edit -->
                                        <a href="{{ route('staff.spj.edit',$spj->id_spj) }}"
                                           class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-amber-100 text-amber-600 hover:bg-amber-200 transition"
                                           title="Edit">

                                            <svg class="w-5 h-5"
                                                 fill="none"
                                                 stroke="currentColor"
                                                 viewBox="0 0 24 24">

                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M11 5h2m-7 7l8-8
                                                         a2.828 2.828 0 114 4l-8 8
                                                         H6v-4z"/>

                                            </svg>

                                        </a>

                                        <!-- Delete -->
                                        <button
                                            type="button"
                                            onclick="openDeleteModal(@json($spj->id_spj))"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition"
                                            title="Hapus">

                                            <svg class="w-5 h-5"
                                                 fill="none"
                                                 stroke="currentColor"
                                                 viewBox="0 0 24 24">

                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M19 7L5 7
                                                         M10 11v6
                                                         M14 11v6
                                                         M6 7l1 12
                                                         a2 2 0 002 2h6
                                                         a2 2 0 002-2l1-12
                                                         M9 7V4
                                                         a1 1 0 011-1h4
                                                         a1 1 0 011 1v3"/>

                                            </svg>

                                        </button>
                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" class="px-6 py-16 text-center">

                                <div class="flex flex-col items-center">

                                    <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mb-4">

                                        <svg class="w-10 h-10 text-gray-400"
                                             fill="none"
                                             stroke="currentColor"
                                             viewBox="0 0 24 24">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>

                                        </svg>

                                    </div>

                                    <h3 class="text-lg font-semibold text-gray-700">
                                        Belum Ada Dokumen SPJ
                                    </h3>

                                    <p class="mt-2 text-sm text-gray-500">
                                        Dokumen SPJ yang ditambahkan akan muncul di sini.
                                    </p>

                                    @if(auth()->user()->role->nama_role === 'Staff Sarana dan Prasarana')
                                        <a href="{{ route('staff.spj.create') }}"
                                           class="mt-6 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#114F72] to-[#16A394] px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:opacity-90 transition">

                                            <svg class="w-5 h-5"
                                                 fill="none"
                                                 stroke="currentColor"
                                                 viewBox="0 0 24 24">

                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M12 4v16m8-8H4"/>

                                            </svg>

                                            Tambah Dokumen SPJ

                                        </a>
                                    @endif

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if($spjList->hasPages())

            <div class="border-t border-gray-200 px-6 py-4">

                {{ $spjList->links() }}

            </div>

        @endif

    </div>

</div>


<!-- Modal Hapus -->
<div id="deleteModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4">

    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">

        <div class="flex items-center gap-3 mb-4">

            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">

                <svg class="w-6 h-6 text-red-600"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>

                </svg>

            </div>

            <div>

                <h3 class="font-bold text-gray-800">
                    Hapus Dokumen SPJ
                </h3>

                <p class="text-sm text-gray-500">
                    Dokumen yang dihapus tidak dapat dikembalikan.
                </p>

            </div>

        </div>

        <p class="text-sm text-gray-600 mb-6">

            Apakah Anda yakin ingin menghapus dokumen SPJ ini?

        </p>

        <div class="flex justify-end gap-3">

            <button type="button"
                    onclick="closeDeleteModal()"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">

                Batal

            </button>

            <form id="deleteForm" method="POST">

                @csrf
                @method('DELETE')

                <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">

                    Hapus

                </button>

            </form>

        </div>

    </div>

</div>


<script>

function openDeleteModal(id){

    document.getElementById('deleteForm').action =
        "{{ url('staff/spj') }}/" + id;

    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');

    document.body.style.overflow='hidden';

}

function closeDeleteModal(){

    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');

    document.body.style.overflow='';

}

document.addEventListener('keydown',function(e){

    if(e.key==='Escape'){

        closeDeleteModal();

    }

});

</script>

@endsection
```
