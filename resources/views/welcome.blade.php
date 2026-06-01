<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Resident Help') }} - Solusi Hunian Nyaman</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased font-sans text-gray-900 bg-gray-50">

    <nav x-data="{ open: false }" class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center gap-2">
                        <img src="{{ asset('logo/logo.jpg') }}" alt="Resident Help Logo"
                            class="w-16 h-16 rounded-full object-cover">
                        <span class="font-bold text-xl tracking-tight text-gray-800">Resident Help</span>
                    </div>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="text-sm font-semibold text-gray-900 hover:text-purple-600 transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-sm font-semibold text-gray-900 hover:text-purple-600 transition">Masuk</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-full hover:bg-purple-700 transition shadow-md">
                                    Daftar Akun
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>

                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden bg-white border-t">
            <div class="pt-2 pb-3 space-y-1 px-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-purple-700 bg-purple-50">Dashboard</a>
                @else
                    <a href="{{ route('login') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Masuk</a>
                    <a href="{{ route('register') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-purple-700 hover:text-purple-900 hover:bg-purple-50">Daftar
                        Akun</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="relative bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <svg class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-white transform translate-x-1/2"
                    fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                    <polygon points="50,0 100,0 50,100 0,100" />
                </svg>

                <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                            <span class="block xl:inline">Solusi Hunian</span>
                            <span class="block text-purple-600 xl:inline">Nyaman & Terawat</span>
                        </h1>
                        <p
                            class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            Laporkan kerusakan rumah Anda dengan mudah. Kami menghubungkan Anda dengan teknisi
                            profesional untuk perbaikan cepat, transparan, dan bergaransi.
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow">
                                @auth
                                    {{-- LOGIKA: Cek Role User --}}
                                    @if (Auth::user()->role === 'admin')
                                        {{-- Jika ADMIN, arahkan ke Dashboard (Hide tombol Lapor) --}}
                                        <a href="{{ route('dashboard') }}"
                                            class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 md:py-4 md:text-lg">
                                            <i class="fas fa-tachometer-alt mr-2"></i> Ke Dashboard Admin
                                        </a>
                                    @else
                                        {{-- Jika WARGA/NASABAH, tampilkan tombol Lapor --}}
                                        <a href="{{ route('complaints.create') }}"
                                            class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 md:py-4 md:text-lg">
                                            <i class="fas fa-exclamation-circle mr-2"></i> Lapor Kerusakan
                                        </a>
                                    @endif
                                @else
                                    {{-- Jika TAMU (Belum Login) --}}
                                    <a href="{{ route('register') }}"
                                        class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 md:py-4 md:text-lg">
                                        Mulai Sekarang
                                    </a>
                                @endauth
                            </div>

                            {{-- Tombol Secondary --}}
                            <div class="mt-3 sm:mt-0 sm:ml-3">
                                <a href="#features"
                                    class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 md:py-4 md:text-lg">
                                    Pelajari Lebih Lanjut
                                </a>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
            <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full"
                src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80"
                alt="Rumah Nyaman">
        </div>
    </div>

    <div id="features" class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-purple-600 font-semibold tracking-wide uppercase">Layanan Kami</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Mengapa Memilih Resident Help?
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                    Kami memastikan setiap keluhan warga ditangani dengan standar profesionalisme tinggi.
                </p>
            </div>

            <div class="mt-10">
                <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-10">
                    <div class="relative">
                        <dt>
                            <div
                                class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-purple-500 text-white">
                                <i class="fas fa-bolt fa-lg"></i>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Respon Cepat (SLA)</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Kami berkomitmen menangani keluhan darurat sesegera mungkin dengan standar waktu layanan
                            terukur.
                        </dd>
                    </div>

                    <div class="relative">
                        <dt>
                            <div
                                class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-purple-500 text-white">
                                <i class="fas fa-users-cog fa-lg"></i>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Teknisi Ahli</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Bekerja sama dengan mitra teknisi berpengalaman di bidang listrik, air, bangunan, dan atap.
                        </dd>
                    </div>

                    <div class="relative">
                        <dt>
                            <div
                                class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-purple-500 text-white">
                                <i class="fas fa-shield-alt fa-lg"></i>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-gray-900">Garansi Perbaikan</p>
                        </dt>
                        <dd class="mt-2 ml-16 text-base text-gray-500">
                            Setiap unit rumah dilindungi masa garansi. Pantau status garansi rumah Anda secara realtime.
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-12">Cara Kerja Sistem</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
                <div class="flex flex-col items-center">
                    <div
                        class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 mb-4">
                        <span class="text-2xl font-bold">1</span>
                    </div>
                    <h3 class="font-bold text-lg">Login / Daftar</h3>
                    <p class="text-gray-500 text-sm mt-2">Masuk menggunakan akun Warga atau Nasabah.</p>
                </div>
                <div class="flex flex-col items-center">
                    <div
                        class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 mb-4">
                        <span class="text-2xl font-bold">2</span>
                    </div>
                    <h3 class="font-bold text-lg">Lapor Keluhan</h3>
                    <p class="text-gray-500 text-sm mt-2">Isi form keluhan, pilih unit, dan upload foto bukti.</p>
                </div>
                <div class="flex flex-col items-center">
                    <div
                        class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 mb-4">
                        <span class="text-2xl font-bold">3</span>
                    </div>
                    <h3 class="font-bold text-lg">Proses Perbaikan</h3>
                    <p class="text-gray-500 text-sm mt-2">Admin menugaskan teknisi untuk datang ke lokasi.</p>
                </div>
                <div class="flex flex-col items-center">
                    <div
                        class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 mb-4">
                        <span class="text-2xl font-bold">4</span>
                    </div>
                    <h3 class="font-bold text-lg">Selesai & Rating</h3>
                    <p class="text-gray-500 text-sm mt-2">Konfirmasi selesai dan beri penilaian layanan.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-purple-700">
        <div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                <span class="block">Siap menikmati hunian tanpa cemas?</span>
                <span class="block">Bergabunglah dengan Resident Help hari ini.</span>
            </h2>
            <p class="mt-4 text-lg leading-6 text-purple-200">
                Layanan manajemen komplain perumahan terpadu untuk kenyamanan keluarga Anda.
            </p>
            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                    class="mt-8 w-full inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-purple-600 bg-white hover:bg-purple-50 sm:w-auto">
                    Daftar Sekarang
                </a>
            @endif
        </div>
    </div>

    <footer class="bg-gray-800">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex justify-center md:justify-start space-x-6 md:order-2">
                    <a href="#" class="text-gray-400 hover:text-gray-300">
                        <span class="sr-only">Facebook</span>
                        <i class="fab fa-facebook fa-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-300">
                        <span class="sr-only">Instagram</span>
                        <i class="fab fa-instagram fa-lg"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-300">
                        <span class="sr-only">Twitter</span>
                        <i class="fab fa-twitter fa-lg"></i>
                    </a>
                </div>
                <div class="mt-8 md:mt-0 md:order-1">
                    <p class="text-center text-base text-gray-400">
                        &copy; {{ date('Y') }} Resident Help. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>
