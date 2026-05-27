<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    @if (Auth::user()->role === 'admin')
        {{-- DASHBOARD ADMIN --}}
        <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full">
                    <i class="fas fa-home fa-lg"></i>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Unit Terjual</p>
                    <p class="text-lg font-semibold text-gray-700">{{ $soldUnits }} / {{ $totalUnits }}</p>
                </div>
            </div>

            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-red-500 bg-red-100 rounded-full">
                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Menunggu Approve</p>
                    <p class="text-lg font-semibold text-gray-700">{{ $pendingComplaints }}</p>
                </div>
            </div>

            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full">
                    <i class="fas fa-tools fa-lg"></i>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Sedang Proses</p>
                    <p class="text-lg font-semibold text-gray-700">{{ $processComplaints }}</p>
                </div>
            </div>

            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-purple-500 bg-purple-100 rounded-full">
                    <i class="fas fa-users-cog fa-lg"></i>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Tukang Ready</p>
                    <p class="text-lg font-semibold text-gray-700">{{ $techAvailable }} / {{ $technicians }}</p>
                </div>
            </div>
        </div>

        {{-- Baris Kartu Tambahan --}}
        <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle fa-lg"></i>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Selesai</p>
                    <p class="text-lg font-semibold text-gray-700">{{ $doneComplaints }}</p>
                </div>
            </div>

            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-cyan-500 bg-cyan-100 rounded-full">
                    <i class="fas fa-chart-line fa-lg"></i>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Rata-rata Resolusi</p>
                    <p class="text-lg font-semibold text-gray-700">{{ $avgResolution ? number_format($avgResolution, 0) : 0 }} jam</p>
                </div>
            </div>

            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-red-500 bg-red-100 rounded-full">
                    <i class="fas fa-exclamation-circle fa-lg"></i>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">SLA Terlambat</p>
                    <p class="text-lg font-semibold text-gray-700">{{ $slaViolated }}</p>
                </div>
            </div>

            <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                <div class="p-3 mr-4 text-yellow-500 bg-yellow-100 rounded-full">
                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">SLA Warning</p>
                    <p class="text-lg font-semibold text-gray-700">{{ $slaWarning }}</p>
                </div>
            </div>
        </div>

        {{-- Grafik Baris 1 --}}
        <div class="grid gap-6 mb-8 md:grid-cols-2">
            <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs">
                <h4 class="mb-4 font-semibold text-gray-800">Status Penjualan Unit</h4>
                <canvas id="pieChart"></canvas>
            </div>

            <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs">
                <h4 class="mb-4 font-semibold text-gray-800">Tren Keluhan Warga (Bulan Ini)</h4>
                <canvas id="barChart"></canvas>
            </div>
        </div>

        {{-- Grafik Baris 2: ANALITIK --}}
        <div class="grid gap-6 mb-8 md:grid-cols-2">
            <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs">
                <h4 class="mb-4 font-semibold text-gray-800">Kerusakan Terbanyak</h4>
                <canvas id="topDamagesChart"></canvas>
            </div>

            <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs">
                <h4 class="mb-4 font-semibold text-gray-800">Performa Teknisi (Top 5)</h4>
                <canvas id="techPerformanceChart"></canvas>
            </div>
        </div>

        {{-- Grafik Baris 3 --}}
        <div class="grid gap-6 mb-8 md:grid-cols-2">
            <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs">
                <h4 class="mb-4 font-semibold text-gray-800">Distribusi Prioritas</h4>
                <canvas id="priorityChart"></canvas>
            </div>

            <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs">
                <h4 class="mb-4 font-semibold text-gray-800">SLA Status Overview</h4>
                <canvas id="slaChart"></canvas>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // 1. Pie Chart (Unit)
                const pieCtx = document.getElementById('pieChart').getContext('2d');
                new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Terjual', 'Tersedia'],
                        datasets: [{
                            data: [{{ $soldUnits }}, {{ $availableUnits }}],
                            backgroundColor: ['#10B981', '#E5E7EB'],
                            borderWidth: 0
                        }]
                    },
                    options: { responsive: true, cutout: '70%', plugins: { legend: { position: 'bottom' } } }
                });

                // 2. Bar Chart (Keluhan per Bulan)
                const barCtx = document.getElementById('barChart').getContext('2d');
                const complaintsData = @json($complaintsPerMonth);
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(complaintsData),
                        datasets: [{
                            label: 'Jumlah Laporan',
                            data: Object.values(complaintsData),
                            backgroundColor: '#7E3AF2',
                            borderRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                        plugins: { legend: { display: false } }
                    }
                });

                // 3. Top Damages Chart (Horizontal Bar)
                const topDamagesCtx = document.getElementById('topDamagesChart').getContext('2d');
                const topDamages = @json($topDamages);
                new Chart(topDamagesCtx, {
                    type: 'bar',
                    data: {
                        labels: topDamages.map(d => d.complaint_title.length > 20 ? d.complaint_title.substring(0, 20) + '...' : d.complaint_title),
                        datasets: [{
                            label: 'Jumlah',
                            data: topDamages.map(d => d.total),
                            backgroundColor: ['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6'],
                            borderRadius: 5
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } },
                        plugins: { legend: { display: false } }
                    }
                });

                // 4. Tech Performance Chart
                const techPerfCtx = document.getElementById('techPerformanceChart').getContext('2d');
                const techPerformance = @json($techPerformance);
                new Chart(techPerfCtx, {
                    type: 'bar',
                    data: {
                        labels: techPerformance.map(t => t.name),
                        datasets: [{
                            label: 'Pekerjaan Selesai',
                            data: techPerformance.map(t => t.total_done),
                            backgroundColor: '#3B82F6',
                            borderRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                        plugins: { legend: { display: false } }
                    }
                });

                // 5. Priority Distribution
                const priorityCtx = document.getElementById('priorityChart').getContext('2d');
                new Chart(priorityCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Urgent', 'Medium', 'Low'],
                        datasets: [{
                            data: [{{ $urgentCount }}, {{ $mediumCount }}, {{ $lowCount }}],
                            backgroundColor: ['#EF4444', '#F59E0B', '#10B981'],
                            borderWidth: 0
                        }]
                    },
                    options: { responsive: true, cutout: '60%', plugins: { legend: { position: 'bottom' } } }
                });

                // 6. SLA Status
                const slaCtx = document.getElementById('slaChart').getContext('2d');
                new Chart(slaCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['On Track', 'Warning', 'Violated'],
                        datasets: [{
                            data: [{{ $slaOnTrack }}, {{ $slaWarning }}, {{ $slaViolated }}],
                            backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                            borderWidth: 0
                        }]
                    },
                    options: { responsive: true, cutout: '60%', plugins: { legend: { position: 'bottom' } } }
                });
            });
        </script>

    @elseif (Auth::user()->role === 'teknisi')
        {{-- DASHBOARD TEKNISI --}}
        @php
            $isTechnicianLinked = \App\Models\Technician::where('user_id', Auth::id())->exists();
        @endphp

        @if (!$isTechnicianLinked)
            <div class="p-4 mb-6 text-sm text-yellow-800 rounded-lg bg-yellow-50" role="alert">
                <span class="font-medium">Perhatian!</span> Akun Anda belum dihubungkan dengan Master Data Tukang. Silakan hubungi Administrator.
            </div>
        @else
            <div class="grid gap-6 mb-8 md:grid-cols-3">
                <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                    <div class="p-3 mr-4 text-red-500 bg-red-100 rounded-full">
                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-600">Keluhan Baru</p>
                        <p class="text-lg font-semibold text-gray-700">{{ $pendingComplaints }}</p>
                    </div>
                </div>

                <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                    <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full">
                        <i class="fas fa-tools fa-lg"></i>
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-600">Sedang Diproses</p>
                        <p class="text-lg font-semibold text-gray-700">{{ $processComplaints }}</p>
                    </div>
                </div>

                <div class="flex items-center p-4 bg-white rounded-lg shadow-xs">
                    <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-600">Selesai</p>
                        <p class="text-lg font-semibold text-gray-700">{{ $doneComplaints }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 border-t pt-4">
                <a href="{{ route('technician.maintenance.index') }}"
                    class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 shadow transition">
                    <i class="fas fa-clipboard-list mr-2"></i> Lihat Daftar Tugas
                </a>
            </div>
        @endif

    @else
        {{-- DASHBOARD WARGA / NASABAH --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Selamat Datang, {{ Auth::user()->name }}!</h2>
            <p class="text-gray-600 mb-6">Berikut adalah ringkasan aktivitas akun Anda di Resident Help.</p>

            <div class="grid gap-6 mb-8 md:grid-cols-4">
                <div class="p-4 bg-purple-100 rounded-lg text-purple-700">
                    <div class="text-3xl font-bold">{{ $myComplaintsTotal }}</div>
                    <div class="text-sm">Total Laporan</div>
                </div>
                <div class="p-4 bg-yellow-100 rounded-lg text-yellow-700">
                    <div class="text-3xl font-bold">{{ $myComplaintsPending }}</div>
                    <div class="text-sm">Menunggu</div>
                </div>
                <div class="p-4 bg-blue-100 rounded-lg text-blue-700">
                    <div class="text-3xl font-bold">{{ $myComplaintsProcess ?? 0 }}</div>
                    <div class="text-sm">Diproses</div>
                </div>
                <div class="p-4 bg-green-100 rounded-lg text-green-700">
                    <div class="text-3xl font-bold">{{ $myComplaintsDone }}</div>
                    <div class="text-sm">Selesai</div>
                </div>
            </div>

            <div class="border-t pt-4">
                <h3 class="font-semibold mb-3">Aksi Cepat:</h3>
                <a href="{{ route('complaints.create') }}"
                    class="inline-block bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 shadow transition">
                    <i class="fas fa-plus-circle mr-2"></i> Buat Laporan Baru
                </a>
            </div>
        </div>
    @endif

</x-app-layout>
