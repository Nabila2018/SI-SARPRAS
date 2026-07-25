@extends('layouts.app')

@section('title', 'Pengelolaan Akun')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-start justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Pengelolaan Akun
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Kelola akun pengguna SI-SARPRAS
            </p>
        </div>

        <button type="button"
                onclick="openTambahAkunModal()"
                class="inline-flex items-center gap-2 rounded-lg
                    bg-gradient-to-r from-[#114F72] to-[#16A394]
                    px-5 py-2.5 text-sm font-semibold text-white
                    shadow-sm hover:opacity-90 transition">

            <svg class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4v16m8-8H4"/>
            </svg>

            Tambah Akun
        </button>

    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3
                    text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3
                    text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- SEARCH & FILTER --}}
    <form method="GET"
          action="{{ route('staff.akun.index') }}"
          class="flex justify-end items-center gap-3">

        {{-- SEARCH --}}
        <div class="relative w-72">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="m21 21-4.35-4.35m2.35-5.65a8 8 0 11-16 0 8 8 0 0116 0z"/>
            </svg>

            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari"
                   class="w-full rounded-full border border-gray-300 bg-white py-2.5 pl-11 pr-4 text-sm
                          focus:border-[#16A394] focus:ring-0 outline-none transition-colors">
        </div>

        {{-- FILTER ICON --}}
        <div class="relative w-11 h-11">

            <select name="role"
                    onchange="this.form.submit()"
                    class="absolute inset-0 z-10 w-full h-full opacity-0 cursor-pointer
                           outline-none focus:outline-none focus:ring-0">

                <option value="">Semua Role</option>

                @foreach($roles as $role)
                    <option value="{{ $role->id_role }}"
                        {{ request('role') == $role->id_role ? 'selected' : '' }}>
                        {{ $role->nama_role }}
                    </option>
                @endforeach

            </select>

            <div class="w-11 h-11 rounded-full border border-gray-300 bg-white
                        flex items-center justify-center pointer-events-none
                        {{ request('role') ? 'border-[#16A394] bg-[#16A394]/10' : '' }}">

                <svg class="w-5 h-5 {{ request('role') ? 'text-[#16A394]' : 'text-gray-500' }}"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>

            </div>
        </div>

    </form>

    {{-- DAFTAR AKUN --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-5 py-4 text-left">Nama</th>
                        <th class="px-5 py-4 text-left">Username</th>
                        <th class="px-5 py-4 text-left">Role</th>
                        <th class="px-5 py-4 text-left">Unit / Pasar</th>
                        <th class="px-5 py-4 text-left">Status</th>
                        <th class="px-5 py-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse($users as $user)

                        <tr class="hover:bg-gray-50">

                            {{-- NAMA --}}
                            <td class="px-5 py-4 font-medium text-gray-800">
                                {{ $user->nama_lengkap }}
                            </td>

                            {{-- USERNAME --}}
                            <td class="px-5 py-4 text-gray-600">
                                {{ $user->username }}
                            </td>

                            {{-- ROLE --}}
                            <td class="px-5 py-4">
                                {{ $user->role->nama_role ?? '-' }}
                            </td>

                            {{-- UNIT / PASAR --}}
                            <td class="px-5 py-4">
                                {{ $user->pasar->nama_pasar ?? '-' }}
                            </td>

                            {{-- STATUS --}}
                            <td class="px-5 py-4">

                                @if($user->status_akun === 'Aktif')

                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                                 bg-green-100 text-green-700">
                                        Aktif
                                    </span>

                                @else

                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                                 bg-gray-100 text-gray-600">
                                        Nonaktif
                                    </span>

                                @endif

                            </td>

                            {{-- AKSI --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center justify-center gap-2">
                                    
                                    {{-- EDIT --}}
                                    <button type="button"
                                        onclick="openEditModal(
                                            {{ $user->id_user }},
                                            @js($user->nama_lengkap),
                                            @js($user->email),
                                            @js($user->username),
                                            @js($user->role->nama_role ?? '-'),
                                            @js($user->pasar->nama_pasar ?? '-')
                                        )"
                                        title="Edit akun"
                                        class="flex h-9 w-9 items-center justify-center
                                            rounded-lg border border-gray-200
                                            text-gray-500
                                            hover:border-[#16A394]
                                            hover:bg-[#16A394]/5
                                            hover:text-[#16A394]
                                            transition">

                                        <svg class="w-4 h-4"
                                             fill="none"
                                             stroke="currentColor"
                                             viewBox="0 0 24 24">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>

                                        </svg>

                                    </button>

                    {{-- AKTIF / NONAKTIF --}}
                    @if($user->id_user === auth()->id())

                        {{-- AKUN SENDIRI --}}
                        <button type="button"
                                disabled
                                title="Tidak dapat mengubah status akun sendiri"
                                class="flex h-9 w-9 items-center justify-center
                                    rounded-lg border border-gray-200
                                    text-gray-300 cursor-not-allowed">

                            <svg class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 2v10m6.364-7.364a9 9 0 11-12.728 0"/>
                            </svg>

                        </button>

                   @else

                        <button type="button"
                                onclick="openStatusModal(
                                    {{ $user->id_user }},
                                    @js($user->nama_lengkap),
                                    @js($user->status_akun)
                                )"
                                title="{{ $user->status_akun === 'Aktif'
                                    ? 'Nonaktifkan akun'
                                    : 'Aktifkan akun' }}"
                                class="flex h-9 w-9 items-center justify-center
                                    rounded-lg border border-gray-200 transition
                                    {{ $user->status_akun === 'Aktif'
                                        ? 'text-red-500 hover:border-red-300 hover:bg-red-50'
                                        : 'text-green-600 hover:border-green-300 hover:bg-green-50' }}">

                            <svg class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 2v10m6.364-7.364a9 9 0 11-12.728 0"/>
                            </svg>

    </button>

@endif
                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6"
                                class="px-5 py-10 text-center text-gray-500">
                                Belum ada akun.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- MODAL TAMBAH AKUN --}}
                                    <div id="tambahAkunModal"
                                        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4"
                                        onclick="if(event.target === this) closeTambahAkunModal()">

                                        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl max-h-[90vh] overflow-y-auto"
                                            onclick="event.stopPropagation()">

                                            {{-- HEADER --}}
                                            <div class="mb-6">
                                                <h2 class="text-xl font-bold text-gray-800">
                                                    Tambah Akun
                                                </h2>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    Tambahkan akun pengguna baru SI-SARPRAS
                                                </p>
                                            </div>

                                            <form action="{{ route('staff.akun.store') }}"
                                                method="POST">

                                                @csrf

                                                <div class="space-y-4">

                                                    {{-- NAMA LENGKAP --}}
                                                    <div>
                                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                                            Nama Lengkap <span class="text-red-500">*</span>
                                                        </label>

                                                        <input type="text"
                                                            name="nama_lengkap"
                                                            required
                                                            maxlength="100"
                                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                                                                    focus:border-[#16A394] focus:ring-1 focus:ring-[#16A394]">
                                                    </div>

                                                    {{-- USERNAME --}}
                                                    <div>
                                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                                            Username <span class="text-red-500">*</span>
                                                        </label>

                                                        <input type="text"
                                                            name="username"
                                                            required
                                                            maxlength="50"
                                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                                                                    focus:border-[#16A394] focus:ring-1 focus:ring-[#16A394]">
                                                    </div>

                                                    {{-- EMAIL --}}
                                                    <div>
                                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                                            Email <span class="text-red-500">*</span>
                                                        </label>

                                                        <input type="email"
                                                            name="email"
                                                            required
                                                            maxlength="100"
                                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                                                                    focus:border-[#16A394] focus:ring-1 focus:ring-[#16A394]">
                                                    </div>

                                                    {{-- ROLE --}}
                                                    <div>
                                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                                            Role <span class="text-red-500">*</span>
                                                        </label>

                                                        <select name="id_role"
                                                                id="tambahRole"
                                                                required
                                                                class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                                                                    focus:border-[#16A394] focus:ring-1 focus:ring-[#16A394]">

                                                            <option value="" selected disabled>
                                                                Pilih Role
                                                            </option>

                                                            @foreach($roles as $role)
                                                                <option value="{{ $role->id_role }}">
                                                                    {{ $role->nama_role }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                    </div>

                                                    {{-- UNIT / PASAR --}}
                                                    <div id="tambahPasarContainer">
                                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                                            Unit / Pasar
                                                        </label>

                                                        <select name="id_pasar"
                                                                id="tambahPasar"
                                                                disabled
                                                                class="w-full rounded-lg border border-gray-300
                                                                    bg-gray-100 px-4 py-2.5 text-gray-500
                                                                    disabled:cursor-not-allowed">

                                                            <option value="" selected disabled>
                                                                Pilih Unit / Pasar
                                                            </option>

                                                            

                                                            @foreach($pasars as $pasar)
                                                                <option value="{{ $pasar->id_pasar }}">
                                                                    {{ $pasar->nama_pasar }}
                                                                </option>
                                                            @endforeach

                                                        </select>

                                                        <p id="pasarHint"
                                                        class="mt-1.5 text-xs text-gray-400">
                                                            Unit/Pasar hanya digunakan untuk Petugas UPTD.
                                                        </p>
                                                    </div>

                                                    {{-- PASSWORD --}}
                                                    <div>
                                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                                            Password Awal <span class="text-red-500">*</span>
                                                        </label>

                                                        <input type="password"
                                                            name="password"
                                                            required
                                                            minlength="8"
                                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                                                                    focus:border-[#16A394] focus:ring-1 focus:ring-[#16A394]">
                                                    </div>

                                                    {{-- KONFIRMASI PASSWORD --}}
                                                    <div>
                                                        <label class="mb-2 block text-sm font-medium text-gray-700">
                                                            Konfirmasi Password <span class="text-red-500">*</span>
                                                        </label>

                                                        <input type="password"
                                                            name="password_confirmation"
                                                            required
                                                            minlength="8"
                                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                                                                    focus:border-[#16A394] focus:ring-1 focus:ring-[#16A394]">
                                                    </div>

                                                </div>

                                                {{-- BUTTON --}}
                                                <div class="mt-6 flex justify-end gap-3">

                                                    <button type="button"
                                                            onclick="closeTambahAkunModal()"
                                                            class="rounded-lg border border-gray-300 px-5 py-2.5
                                                                text-sm font-medium text-gray-600
                                                                hover:bg-gray-50 transition">
                                                        Batal
                                                    </button>

                                                    <button type="submit"
                                                            class="rounded-lg bg-gradient-to-r from-[#114F72] to-[#16A394]
                                                                px-5 py-2.5 text-sm font-semibold text-white
                                                                shadow-sm hover:opacity-90 transition">
                                                        Simpan Akun
                                                    </button>

                                                </div>

                                            </form>
                                        </div>
                                    </div>

{{-- MODAL EDIT AKUN --}}
<div id="editAkunModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4"
     onclick="if(event.target === this) closeEditModal()">

    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl"
         onclick="event.stopPropagation()">

        {{-- HEADER MODAL --}}
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">
                Edit Akun
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Perbarui informasi akun pengguna
            </p>
        </div>

        <form id="editAkunForm" method="POST">

            @csrf
            @method('PATCH')

            <div class="space-y-4">

                {{-- NAMA LENGKAP --}}
                <div>
                    <label for="editNama"
                           class="mb-2 block text-sm font-medium text-gray-700">
                        Nama Lengkap
                    </label>

                    <input type="text"
                           id="editNama"
                           name="nama_lengkap"
                           required
                           maxlength="100"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                                  focus:border-[#16A394] focus:ring-1 focus:ring-[#16A394]">
                </div>

                {{-- EMAIL --}}
                <div>
                    <label for="editEmail"
                           class="mb-2 block text-sm font-medium text-gray-700">
                        Email
                    </label>

                    <input type="email"
                           id="editEmail"
                           name="email"
                           required
                           maxlength="100"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                                  focus:border-[#16A394] focus:ring-1 focus:ring-[#16A394]">
                </div>

                {{-- USERNAME --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Username
                    </label>

                    <input type="text"
                           id="editUsername"
                           readonly
                           class="w-full cursor-not-allowed rounded-lg border border-gray-200
                                  bg-gray-100 px-4 py-2.5 text-gray-500">
                </div>

                {{-- ROLE --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Role
                    </label>

                    <input type="text"
                           id="editRole"
                           readonly
                           class="w-full cursor-not-allowed rounded-lg border border-gray-200
                                  bg-gray-100 px-4 py-2.5 text-gray-500">
                </div>

                {{-- UNIT / PASAR --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Unit / Pasar
                    </label>

                    <input type="text"
                           id="editPasar"
                           readonly
                           class="w-full cursor-not-allowed rounded-lg border border-gray-200
                                  bg-gray-100 px-4 py-2.5 text-gray-500">
                </div>

            </div>

            {{-- BUTTON --}}
            <div class="mt-6 flex justify-end gap-3">

                <button type="button"
                        onclick="closeEditModal()"
                        class="rounded-lg border border-gray-300 px-5 py-2.5
                               text-sm font-medium text-gray-600
                               hover:bg-gray-50 transition">
                    Batal
                </button>

                <button type="submit"
                        class="rounded-lg bg-gradient-to-r from-[#114F72] to-[#16A394]
                               px-5 py-2.5 text-sm font-semibold text-white
                               shadow-sm hover:opacity-90 transition">
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>
</div>

{{-- MODAL KONFIRMASI STATUS AKUN --}}
<div id="statusAkunModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4"
     onclick="if(event.target === this) closeStatusModal()">

    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
         onclick="event.stopPropagation()">

        <h3 id="statusModalTitle"
            class="text-lg font-semibold text-gray-800">
        </h3>

        <p id="statusModalMessage"
           class="mt-2 text-sm leading-6 text-gray-600">
        </p>

        <form id="statusAkunForm"
              method="POST"
              class="mt-6">

            @csrf
            @method('PATCH')

            <div class="flex justify-end gap-3">

                <button type="button"
                        onclick="closeStatusModal()"
                        class="rounded-lg border border-gray-300 px-4 py-2
                               text-sm font-medium text-gray-600
                               hover:bg-gray-50 transition">
                    Batal
                </button>

                <button type="submit"
                        id="statusModalButton"
                        class="rounded-lg px-4 py-2 text-sm font-semibold
                               text-white shadow-sm hover:opacity-90 transition">
                </button>

            </div>

        </form>

    </div>
</div>

<script>

    // =====================================================
    // EDIT AKUN
    // =====================================================

    function openEditModal(id, nama, email, username, role, pasar) {
        const modal = document.getElementById('editAkunModal');
        const form = document.getElementById('editAkunForm');

        document.getElementById('editNama').value = nama;
        document.getElementById('editEmail').value = email ?? '';
        document.getElementById('editUsername').value = username;
        document.getElementById('editRole').value = role;
        document.getElementById('editPasar').value = pasar;

        form.action = `/staff/akun/${id}`;

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        const modal = document.getElementById('editAkunModal');

        modal.classList.add('hidden');
        modal.classList.remove('flex');

        document.body.style.overflow = '';
    }


    // =====================================================
    // TAMBAH AKUN
    // =====================================================

    function openTambahAkunModal() {
        const modal = document.getElementById('tambahAkunModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        document.body.style.overflow = 'hidden';
    }

    function closeTambahAkunModal() {
        const modal = document.getElementById('tambahAkunModal');

        modal.classList.add('hidden');
        modal.classList.remove('flex');

        document.body.style.overflow = '';
    }


    // =====================================================
    // ROLE → UNIT / PASAR
    // =====================================================

    const tambahRole = document.getElementById('tambahRole');
    const tambahPasar = document.getElementById('tambahPasar');
    const pasarHint = document.getElementById('pasarHint');

    tambahRole.addEventListener('change', function () {

        // id_role 1 = Petugas UPTD
        if (this.value === '1') {

            tambahPasar.disabled = false;
            tambahPasar.required = true;

            tambahPasar.classList.remove(
                'bg-gray-100',
                'text-gray-500',
                'cursor-not-allowed'
            );

            tambahPasar.classList.add(
                'bg-white',
                'text-gray-700'
            );

            pasarHint.textContent =
                'Pilih unit/pasar tempat Petugas UPTD bertugas.';

            pasarHint.classList.remove('text-gray-400');
            pasarHint.classList.add('text-[#16A394]');

        } else {

            tambahPasar.value = '';
            tambahPasar.disabled = true;
            tambahPasar.required = false;

            tambahPasar.classList.remove(
                'bg-white',
                'text-gray-700'
            );

            tambahPasar.classList.add(
                'bg-gray-100',
                'text-gray-500',
                'cursor-not-allowed'
            );

            pasarHint.textContent =
                'Unit/Pasar hanya digunakan untuk Petugas UPTD.';

            pasarHint.classList.remove('text-[#16A394]');
            pasarHint.classList.add('text-gray-400');
        }
    });


    // =====================================================
    // AKTIF / NONAKTIF AKUN
    // =====================================================

    function openStatusModal(id, nama, status) {

        const modal = document.getElementById('statusAkunModal');
        const form = document.getElementById('statusAkunForm');
        const title = document.getElementById('statusModalTitle');
        const message = document.getElementById('statusModalMessage');
        const button = document.getElementById('statusModalButton');

        form.action = `/staff/akun/${id}/status`;

        if (status === 'Aktif') {

            title.textContent = 'Nonaktifkan Akun';

            message.textContent =
                `Apakah Anda yakin ingin menonaktifkan akun ${nama}? Pengguna tidak akan dapat mengakses sistem.`;

            button.textContent = 'Ya, Nonaktifkan';

            button.className =
                'rounded-lg bg-red-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-600 transition';

        } else {

            title.textContent = 'Aktifkan Akun';

            message.textContent =
                `Apakah Anda yakin ingin mengaktifkan kembali akun ${nama}?`;

            button.textContent = 'Ya, Aktifkan';

            button.className =
                'rounded-lg bg-gradient-to-r from-[#114F72] to-[#16A394] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        document.body.style.overflow = 'hidden';
    }

    function closeStatusModal() {
        const modal = document.getElementById('statusAkunModal');

        modal.classList.add('hidden');
        modal.classList.remove('flex');

        document.body.style.overflow = '';
    }

</script>

@endsection