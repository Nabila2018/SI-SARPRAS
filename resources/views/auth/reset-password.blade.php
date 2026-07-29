<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi - SI-SARPRAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .gradient-bg {
            background: linear-gradient(135deg, #114F72 0%, #087EAF 50%, #16A394 100%);
        }
        .gradient-btn {
            background: linear-gradient(135deg, #087EAF 0%, #0D929F 75%, #16A394 100%);
        }
        .gradient-btn:hover {
            background: linear-gradient(135deg, #087EAF 0%, #0D929F 50%, #16A394 100%);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        {{-- Header Branding --}}
        <div class="gradient-bg px-8 py-10 text-center text-white relative">
            <div class="flex justify-center items-center gap-3 mb-4">
                <img src="https://upload.wikimedia.org/wikipedia/commons/a/a7/Logo_Kota_Padang.png" 
                     alt="Logo Kota Padang" 
                     class="h-14 w-auto drop-shadow-md">
            </div>
            <h1 class="text-2xl font-bold tracking-tight">SI-SARPRAS</h1>
            <p class="text-blue-200 text-xs mt-1 uppercase tracking-wider font-medium">Dinas Perdagangan Kota Padang</p>
        </div>

        {{-- Form Container --}}
        <div class="p-8">
            <div class="text-center mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Reset Kata Sandi</h2>
                <p class="text-gray-500 text-sm">Buat kata sandi baru untuk akun Anda.</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-red-800">Terjadi Kesalahan</p>
                        <p class="text-sm text-red-600 mt-1">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" 
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:outline-none cursor-not-allowed"
                            value="{{ old('email', $email) }}" required readonly>
                    </div>
                </div>

                <!-- Password Baru -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Kata Sandi Baru
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" 
                            class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003366] focus:border-[#003366] transition-all"
                            placeholder="Masukkan kata sandi baru (min. 8 karakter)" required autofocus minlength="8">
                        <button type="button" 
                            onclick="togglePasswordVisibility('password', this)"
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

                <!-- Konfirmasi Password Baru -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Konfirmasi Kata Sandi Baru
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                            class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003366] focus:border-[#003366] transition-all"
                            placeholder="Ulangi kata sandi baru" required minlength="8">
                        <button type="button" 
                            onclick="togglePasswordVisibility('password_confirmation', this)"
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

                <!-- Submit Button -->
                <button type="submit" 
                    class="w-full gradient-btn text-white font-semibold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                    Perbarui Kata Sandi
                </button>
            </form>

            <script>
                function togglePasswordVisibility(fieldId, btn) {
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
            </script>

            <!-- Footer -->
            <div class="mt-8 text-center text-xs text-gray-400">
                <p>© 2026 Dinas Perdagangan Kota Padang</p>
            </div>
        </div>
    </div>

</body>
</html>
