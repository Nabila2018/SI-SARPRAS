@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">
            Profil Saya
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Informasi akun dan profil pengguna
        </p>
    </div>

    {{-- NOTIFIKASI SUKSES --}}
`@if(session('success'))
    <div class="flex items-center gap-3 rounded-lg
                border border-green-200 bg-green-50
                px-4 py-3 text-sm text-green-700">

        <svg class="h-5 w-5 shrink-0"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>

        <span>
            {{ session('success') }}
        </span>

    </div>
@endif


    {{-- PROFIL --}}
    <div class="rounded-xl border border-gray-100 bg-white shadow-sm">

        {{-- HEADER CARD --}}
<div class="flex items-center justify-between gap-4 border-b border-gray-100 p-6">

    {{-- PROFIL USER --}}
    <div class="flex items-center gap-4">

        {{-- AVATAR --}}
        <div class="flex h-14 w-14 items-center justify-center
                    rounded-full bg-[#16A394]/10
                    text-xl font-bold text-[#16A394]">

            {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}

        </div>

        <div>
            <h2 class="text-lg font-semibold text-gray-800">
                {{ $user->nama_lengkap }}
            </h2>

            <p class="text-sm text-gray-500">
                {{ $user->role->nama_role ?? '-' }}
            </p>
        </div>

    </div>


    {{-- BUTTON EDIT PROFIL --}}
    <button type="button"
            onclick="openEditProfilModal()"
            class="inline-flex items-center gap-2 rounded-lg
                   bg-gradient-to-r from-[#114F72] to-[#16A394]
                   px-4 py-2.5 text-sm font-semibold text-white
                   shadow-sm hover:opacity-90 transition">

        <svg class="h-4 w-4"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>

        Edit Profil
    </button>

</div>

        


        {{-- INFORMASI --}}
        <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

            {{-- NAMA --}}
            <div>
                <p class="text-sm text-gray-500">
                    Nama Lengkap
                </p>

                <p class="mt-1 font-medium text-gray-800">
                    {{ $user->nama_lengkap }}
                </p>
            </div>


            {{-- EMAIL --}}
            <div>
                <p class="text-sm text-gray-500">
                    Email
                </p>

                <p class="mt-1 font-medium text-gray-800">
                    {{ $user->email }}
                </p>
            </div>


            {{-- ROLE --}}
            <div>
                <p class="text-sm text-gray-500">
                    Role
                </p>

                <p class="mt-1 font-medium text-gray-800">
                    {{ $user->role->nama_role ?? '-' }}
                </p>
            </div>


            {{-- UNIT / PASAR --}}
            @if($user->pasar)

                <div>
                    <p class="text-sm text-gray-500">
                        Unit / Pasar
                    </p>

                    <p class="mt-1 font-medium text-gray-800">
                        {{ $user->pasar->nama_pasar }}
                    </p>
                </div>

            @endif


            {{-- STATUS --}}
            <div>
                <p class="text-sm text-gray-500">
                    Status Akun
                </p>

                <div class="mt-1">

                    @if($user->status_akun === 'Aktif')

                        <span class="inline-flex rounded-full bg-green-100
                                     px-3 py-1 text-xs font-semibold text-green-700">
                            Aktif
                        </span>

                    @else

                        <span class="inline-flex rounded-full bg-gray-100
                                     px-3 py-1 text-xs font-semibold text-gray-600">
                            Nonaktif
                        </span>

                    @endif

                </div>
            </div>

        </div>

    </div>
{{-- ================================================= --}}
{{-- KEAMANAN AKUN --}}
{{-- ================================================= --}}
<div>
    <h2 class="mb-3 text-lg font-semibold text-gray-800">
        Keamanan Akun
    </h2>

    <div class="flex items-center justify-between gap-4
                rounded-xl border border-gray-100
                bg-white p-6 shadow-sm">

        <div>
            <p class="font-medium text-gray-800">
                Password
            </p>

            <p class="mt-1 text-sm text-gray-500">
                Perbarui password secara berkala untuk menjaga keamanan akun Anda.
            </p>
        </div>

        <button type="button"
                onclick="openPasswordModal()"
                class="shrink-0 rounded-lg border border-[#16A394]
                       px-4 py-2.5 text-sm font-semibold text-[#16A394]
                       transition hover:bg-[#16A394]/5">
            Ganti Password
    </div>
</div>

{{-- MODAL EDIT PROFIL --}}
<div id="editProfilModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4"
     onclick="if(event.target === this) closeEditProfilModal()">

    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl"
         onclick="event.stopPropagation()">

        {{-- HEADER --}}
        <div>
            <h2 class="text-xl font-bold text-gray-800">
                Edit Profil
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Perbarui informasi profil Anda
            </p>
        </div>


        {{-- FORM --}}
        <form action="{{ route('profil.update') }}"
              method="POST"
              class="mt-6 space-y-4">

            @csrf
            @method('PATCH')


            {{-- NAMA LENGKAP --}}
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Nama Lengkap
                </label>

                <input type="text"
                       name="nama_lengkap"
                       value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                       required
                       maxlength="100"
                       class="w-full rounded-lg border border-gray-300
                              px-4 py-2.5 text-sm outline-none
                              focus:border-[#16A394] focus:ring-0">
            </div>

            {{-- EMAIL --}}
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">
                    Email
                </label>

                <input type="email"
                       name="email"
                       value="{{ old('email', $user->email) }}"
                       required
                       maxlength="100"
                       class="w-full rounded-lg border border-gray-300
                              px-4 py-2.5 text-sm outline-none
                              focus:border-[#16A394] focus:ring-0">

                @error('email')
                    <p class="mt-1 text-sm text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- BUTTON --}}
            <div class="flex justify-end gap-3 pt-3">

                <button type="button"
                        onclick="closeEditProfilModal()"
                        class="rounded-lg border border-gray-300
                               px-4 py-2.5 text-sm font-medium text-gray-600
                               transition hover:bg-gray-50">
                    Batal
                </button>

                <button type="submit"
                        class="rounded-lg bg-gradient-to-r
                               from-[#114F72] to-[#16A394]
                               px-4 py-2.5 text-sm font-semibold text-white
                               shadow-sm transition hover:opacity-90">
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>
</div>

{{-- ===================================================== --}}
{{-- MODAL GANTI PASSWORD --}}
{{-- ===================================================== --}}
<div id="passwordModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
     onclick="if(event.target === this) closePasswordModal()">

    <div class="w-full max-w-lg rounded-2xl bg-white p-7 shadow-2xl"
         onclick="event.stopPropagation()">

        {{-- HEADER --}}
        <div>
            <h2 class="text-xl font-bold text-gray-800">
                Ganti Password
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Gunakan password baru untuk menjaga keamanan akun Anda.
            </p>
        </div>


        {{-- FORM --}}
        <form action="{{ route('profil.password.update') }}"
              method="POST"
              class="mt-6 space-y-5">

            @csrf
            @method('PATCH')


            {{-- PASSWORD SAAT INI --}}
            <div>
                <label for="current_password"
                       class="mb-2 block text-sm font-medium text-gray-700">
                    Password Saat Ini
                </label>

                <div class="relative">
                    <input type="password"
                           id="current_password"
                           name="current_password"
                           required
                           autocomplete="current-password"
                           class="w-full rounded-lg border border-gray-300
                                  pl-4 pr-10 py-3 text-sm text-gray-800
                                  outline-none transition
                                  focus:border-[#16A394] focus:ring-0">
                    <button type="button" 
                            onclick="toggleProfilPassword('current_password', this)"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                            title="Tampilkan / Sembunyikan Kata Sandi">
                        <svg class="h-5 w-5 icon-eye hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg class="h-5 w-5 icon-eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.046 10.046 0 013.122-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-6.182-3.155a3 3 0 004.243-4.243M3 3l18 18"/>
                        </svg>
                    </button>
                </div>

                @error('current_password')
                    <p class="mt-1 text-sm text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- PASSWORD BARU --}}
            <div>
                <label for="password"
                       class="mb-2 block text-sm font-medium text-gray-700">
                    Password Baru
                </label>

                <div class="relative">
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           minlength="8"
                           autocomplete="new-password"
                           class="w-full rounded-lg border border-gray-300
                                  pl-4 pr-10 py-3 text-sm text-gray-800
                                  outline-none transition
                                  focus:border-[#16A394] focus:ring-0">
                    <button type="button" 
                            onclick="toggleProfilPassword('password', this)"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                            title="Tampilkan / Sembunyikan Kata Sandi">
                        <svg class="h-5 w-5 icon-eye hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg class="h-5 w-5 icon-eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.046 10.046 0 013.122-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-6.182-3.155a3 3 0 004.243-4.243M3 3l18 18"/>
                        </svg>
                    </button>
                </div>

                @error('password')
                    <p class="mt-1 text-sm text-red-500">
                        {{ $message }}
                    </p>
                @enderror

                <p class="mt-1 text-xs text-gray-400">
                    Minimal 8 karakter.
                </p>
            </div>


            {{-- KONFIRMASI PASSWORD --}}
            <div>
                <label for="password_confirmation"
                       class="mb-2 block text-sm font-medium text-gray-700">
                    Konfirmasi Password Baru
                </label>

                <div class="relative">
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           required
                           minlength="8"
                           autocomplete="new-password"
                           class="w-full rounded-lg border border-gray-300
                                  pl-4 pr-10 py-3 text-sm text-gray-800
                                  outline-none transition
                                  focus:border-[#16A394] focus:ring-0">
                    <button type="button" 
                            onclick="toggleProfilPassword('password_confirmation', this)"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
                            title="Tampilkan / Sembunyikan Kata Sandi">
                        <svg class="h-5 w-5 icon-eye hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg class="h-5 w-5 icon-eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.046 10.046 0 013.122-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-6.182-3.155a3 3 0 004.243-4.243M3 3l18 18"/>
                        </svg>
                    </button>
                </div>
            </div>


            {{-- BUTTON --}}
            <div class="flex items-center justify-end gap-3 pt-3">

                <button type="button"
                        onclick="closePasswordModal()"
                        class="rounded-lg border border-gray-300
                               px-5 py-2.5 text-sm font-medium text-gray-600
                               transition hover:bg-gray-50">
                    Batal
                </button>

                <button type="submit"
                        class="rounded-lg bg-gradient-to-r
                               from-[#114F72] to-[#16A394]
                               px-5 py-2.5 text-sm font-semibold text-white
                               shadow-sm transition hover:opacity-90">
                    Simpan Password
                </button>

            </div>

        </form>

    </div>

</div>

<script>
    function toggleProfilPassword(fieldId, btn) {
        const input = document.getElementById(fieldId);
        if (!input) return;
        const isNowText = input.type === 'password';
        input.type = isNowText ? 'text' : 'password';
        
        const eyeIcon = btn.querySelector('.icon-eye');
        const eyeOffIcon = btn.querySelector('.icon-eye-off');
        if (eyeIcon && eyeOffIcon) {
            eyeIcon.classList.toggle('hidden', !isNowText);
            eyeOffIcon.classList.toggle('hidden', isNowText);
        }
    }

    function openEditProfilModal() {
        const modal = document.getElementById('editProfilModal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        document.body.style.overflow = 'hidden';
    }

    function closeEditProfilModal() {
        const modal = document.getElementById('editProfilModal');

        modal.classList.add('hidden');
        modal.classList.remove('flex');

        document.body.style.overflow = '';
    }

    function openPasswordModal() {
    const modal = document.getElementById('passwordModal');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    document.body.style.overflow = 'hidden';
    }


    function closePasswordModal() {
    const modal = document.getElementById('passwordModal');

    modal.classList.add('hidden');
    modal.classList.remove('flex');

    document.body.style.overflow = '';

    // Kosongkan field ketika modal ditutup
    document.getElementById('current_password').value = '';
    document.getElementById('password').value = '';
    document.getElementById('password_confirmation').value = '';
    }

    @if($errors->has('current_password') || $errors->has('password'))
    document.addEventListener('DOMContentLoaded', function () {
        openPasswordModal();
    });
    @endif

</script>
@endsection