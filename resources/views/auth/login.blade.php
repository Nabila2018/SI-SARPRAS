<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SI-SARPRAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .gradient-btn {
            background: linear-gradient(135deg, #087EAF 0%, #0D929F 75%, #16A394 100%);
        }
        .gradient-btn:hover {
            background: linear-gradient(135deg, #087EAF 0%, #0D929F 50%, #16A394 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        /* Custom checkbox navy */
        .checkbox-navy {
            accent-color: #087EAF;
        }
        .checkbox-navy:checked {
            background-color: #087EAF;
            border-color: #087EAF;
        }
    </style>
</head>
<body class="min-h-screen flex">

    <!-- KIRI: Background + Overlay Navy -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-gray-900">
        <img src="{{ asset('images/pasar indo.jpg') }}" 
             alt="Pasar Tradisional" 
             class="absolute inset-0 w-full h-full object-cover opacity-70">
        
        <div class="absolute inset-0 bg-gradient-to-br from-[#114F72]/85 via-[#0D929F]/75 to-[#16A394]/70"></div>

        <div class="relative z-10 flex flex-col justify-between p-12 text-white w-full">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold tracking-wider uppercase text-emerald-300">Dinas Perdagangan</h2>
                    <p class="text-xs text-blue-100">Pemerintah Kota Padang</p>
                </div>
            </div>

            <div class="mt-auto mb-auto">
                <h1 class="text-4xl font-bold leading-tight mb-4">
                    Sistem Informasi Pelaporan<br>
                    Sarana dan Prasarana Pasar<br>
                    <span class="text-emerald-300">(SI-SARPRAS)</span>
                </h1>
                <p class="text-blue-100 text-lg leading-relaxed max-w-md">
                    Platform terintegrasi untuk memantau, melaporkan, dan mengelola kondisi sarana dan prasarana pasar di Kota Padang.
                </p>
            </div>

            <div class="mt-8">
                <p class="text-xs text-blue-200 mb-3 uppercase tracking-wider">Data Kota Padang • Per Juli 2025</p>
                <div class="grid grid-cols-3 gap-4">
                    <div class="glass-card rounded-lg p-4">
                        <p class="text-xs text-blue-200 mb-1">Laporan Masuk</p>
                        <p class="text-3xl font-bold text-white">248</p>
                    </div>
                    <div class="glass-card rounded-lg p-4">
                        <p class="text-xs text-blue-200 mb-1">Sedang Diproses</p>
                        <p class="text-3xl font-bold text-emerald-300">67</p>
                    </div>
                    <div class="glass-card rounded-lg p-4">
                        <p class="text-xs text-blue-200 mb-1">Selesai</p>
                        <p class="text-3xl font-bold text-white">163</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-xs text-blue-300">
                <p>Foto: Pasar Rakyat Indonesia - Unsplash</p>
            </div>
        </div>
    </div>

    <!-- KANAN: Form Login -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-50 p-8">
        <div class="w-full max-w-md">
            
            <div class="lg:hidden text-center mb-8">
                <h1 class="text-2xl font-bold text-[#003366]">SI-SARPRAS</h1>
                <p class="text-sm text-gray-500">Sistem Informasi Pelaporan Kerusakan</p>
            </div>

            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang Kembali</h2>
                <p class="text-gray-500 text-sm">Silakan masuk untuk melanjutkan</p>
            </div>
            @if(session('status'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-800">Berhasil</p>
                        <p class="text-sm text-green-700 mt-1">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
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

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

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
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003366] focus:border-[#003366] transition-all"
                            placeholder="Masukkan email Anda"
                            value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" 
                            class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003366] focus:border-[#003366] transition-all"
                            placeholder="Masukkan kata sandi" required>
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

                <!-- Remember Me & Lupa Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" 
                            class="h-4 w-4 checkbox-navy rounded border-gray-300 text-[#003366] focus:ring-[#003366]">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>

                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-[#003366] hover:underline">
                        Lupa Kata Sandi?
                    </a>
                </div>v>

                <!-- Submit Button -->
                <button type="submit" 
                    class="w-full gradient-btn text-white font-semibold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                    Masuk
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-8 text-center text-xs text-gray-400">
                <p>© 2026 Dinas Perdagangan Kota Padang</p>
            </div>
        </div>
    </div>

</body>
</html>