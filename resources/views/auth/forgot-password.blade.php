<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - SI-SARPRAS</title>
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
                <h2 class="text-xl font-bold text-gray-800 mb-2">Lupa Kata Sandi?</h2>
                <p class="text-gray-500 text-sm">Masukkan email terdaftar Anda untuk menerima tautan pemulihan kata sandi.</p>
            </div>

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-800">Petunjuk Dikirim</p>
                        <p class="text-sm text-green-700 mt-1">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

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

            <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Terdaftar
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" 
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003366] focus:border-[#003366] transition-all"
                            placeholder="nama@email.com"
                            value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                    class="w-full gradient-btn text-white font-semibold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                    Kirim Tautan Reset Kata Sandi
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-medium text-[#003366] hover:underline gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Halaman Masuk
                </a>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-xs text-gray-400">
                <p>© 2026 Dinas Perdagangan Kota Padang</p>
            </div>
        </div>
    </div>

</body>
</html>
