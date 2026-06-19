<x-app-layout>
    <x-slot name="header">
        {{ __('Pusat Laporan & Cetak') }}
    </x-slot>

    {{-- Filter Perbulan --}}
    <div class="mb-6 p-4 bg-white rounded-lg shadow-md border border-gray-200">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                <select name="month" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-purple-500 focus:ring-purple-500">
                    <option value="">Semua Bulan</option>
                    @foreach (['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $nama)
                        <option value="{{ $i + 1 }}" {{ request('month') == $i + 1 ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
                <select name="year" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-purple-500 focus:ring-purple-500">
                    <option value="">Semua Tahun</option>
                    @foreach (range(date('Y'), date('Y') - 5) as $thn)
                        <option value="{{ $thn }}" {{ request('year') == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300">
                <i class="fas fa-times mr-1"></i> Reset
            </a>
        </form>
    </div>

    <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-3">

        <div class="p-6 bg-white rounded-lg shadow-md border-l-4 border-purple-500">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-purple-100 rounded-full text-purple-500 mr-4">
                    <i class="fas fa-file-alt fa-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">1. Rekapitulasi Keluhan</h4>
                    <p class="text-xs text-gray-500">Daftar semua laporan masuk</p>
                </div>
            </div>
            @php
                $queryParams = request()->only(['month', 'year']);
                $queryString = http_build_query($queryParams);
            @endphp
            <div class="mt-4">
                <a href="{{ route('admin.reports.complaints.pdf', $queryParams) }}" target="_blank"
                    class="block w-full bg-red-600 text-white text-center py-2 rounded text-sm hover:bg-red-700">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                </a>
            </div>
        </div>

        <div class="p-6 bg-white rounded-lg shadow-md border-l-4 border-yellow-500">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-yellow-100 rounded-full text-yellow-500 mr-4">
                    <i class="fas fa-chart-pie fa-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">2. Analisis Kategori</h4>
                    <p class="text-xs text-gray-500">Statistik jenis kerusakan</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.reports.category.pdf', $queryParams) }}" target="_blank"
                    class="block w-full bg-red-600 text-white text-center py-2 rounded text-sm hover:bg-red-700">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                </a>
            </div>
        </div>

        <div class="p-6 bg-white rounded-lg shadow-md border-l-4 border-indigo-500">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-indigo-100 rounded-full text-indigo-500 mr-4">
                    <i class="fas fa-stopwatch fa-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">3. Kecepatan Respon (SLA)</h4>
                    <p class="text-xs text-gray-500">Analisis SLA per prioritas</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.reports.sla.pdf', $queryParams) }}" target="_blank"
                    class="block w-full bg-red-600 text-white text-center py-2 rounded text-sm hover:bg-red-700">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                </a>
            </div>
        </div>

        <div class="p-6 bg-white rounded-lg shadow-md border-l-4 border-blue-500">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-blue-100 rounded-full text-blue-500 mr-4">
                    <i class="fas fa-users-cog fa-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">4. Kinerja Teknisi</h4>
                    <p class="text-xs text-gray-500">Data mitra & total pekerjaan</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.reports.technicians.pdf', $queryParams) }}" target="_blank"
                    class="block w-full bg-red-600 text-white text-center py-2 rounded text-sm hover:bg-red-700">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                </a>
            </div>
        </div>

        <div class="p-6 bg-white rounded-lg shadow-md border-l-4 border-orange-500">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-orange-100 rounded-full text-orange-500 mr-4">
                    <i class="fas fa-star fa-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">5. Indeks Kepuasan</h4>
                    <p class="text-xs text-gray-500">Rating & Testimoni Warga</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.reports.ratings.pdf', $queryParams) }}" target="_blank"
                    class="block w-full bg-red-600 text-white text-center py-2 rounded text-sm hover:bg-red-700">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                </a>
            </div>
        </div>

        <div class="p-6 bg-white rounded-lg shadow-md border-l-4 border-emerald-500">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-emerald-100 rounded-full text-emerald-500 mr-4">
                    <i class="fas fa-money-bill-wave fa-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">6. Pendapatan Perbaikan</h4>
                    <p class="text-xs text-gray-500">Laporan tagihan warga</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.reports.financial.pdf', $queryParams) }}" target="_blank"
                    class="block w-full bg-red-600 text-white text-center py-2 rounded text-sm hover:bg-red-700">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                </a>
            </div>
        </div>

        <div class="p-6 bg-white rounded-lg shadow-md border-l-4 border-cyan-500">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-cyan-100 rounded-full text-cyan-500 mr-4">
                    <i class="fas fa-shield-alt fa-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">7. Status Garansi Unit</h4>
                    <p class="text-xs text-gray-500">Aktif & Expired</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.reports.warranty.pdf', $queryParams) }}" target="_blank"
                    class="block w-full bg-red-600 text-white text-center py-2 rounded text-sm hover:bg-red-700">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                </a>
            </div>
        </div>

        <div class="p-6 bg-white rounded-lg shadow-md border-l-4 border-pink-500">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-pink-100 rounded-full text-pink-500 mr-4">
                    <i class="fas fa-city fa-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">8. Data & Status Unit</h4>
                    <p class="text-xs text-gray-500">Tersedia vs Terjual</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.reports.units.pdf', $queryParams) }}" target="_blank"
                    class="block w-full bg-red-600 text-white text-center py-2 rounded text-sm hover:bg-red-700">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak PDF
                </a>
            </div>
        </div>

        {{-- NEW: Laporan Analitik --}}
        <div class="p-6 bg-white rounded-lg shadow-md border-l-4 border-teal-500">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-teal-100 rounded-full text-teal-500 mr-4">
                    <i class="fas fa-chart-bar fa-lg"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">9. Dashboard Analitik</h4>
                    <p class="text-xs text-gray-500">Grafik & statistik lengkap</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('dashboard') }}" target="_blank"
                    class="block w-full bg-teal-600 text-white text-center py-2 rounded text-sm hover:bg-teal-700">
                    <i class="fas fa-chart-line mr-1"></i> Lihat Dashboard
                </a>
            </div>
        </div>

    </div>
</x-app-layout>
