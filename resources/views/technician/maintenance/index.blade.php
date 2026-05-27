<x-app-layout>
    <x-slot name="header">
        {{ __('Keluhan Masuk & Pekerjaan Saya') }}
    </x-slot>

    <div class="space-y-8">
        {{-- KELUHAN BARU (PENDING) --}}
        <div class="bg-white rounded-lg shadow-xs p-4">
            <h2 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
                <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>
                Keluhan Baru (Pending)
            </h2>

            <div class="w-full overflow-hidden rounded-lg shadow-xs">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                                <th class="px-4 py-3">Tanggal & Unit</th>
                                <th class="px-4 py-3">Keluhan</th>
                                <th class="px-4 py-3">Prioritas</th>
                                <th class="px-4 py-3">Pelapor</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            @forelse ($pendingOrders as $order)
                                <tr class="text-gray-700">
                                    <td class="px-4 py-3">
                                        <div class="text-sm">
                                            <p class="font-semibold">Blok {{ $order->ownership->unit->block }}-{{ $order->ownership->unit->number }}</p>
                                            <p class="text-xs text-gray-600">{{ $order->complaint_date->format('d/m/Y') }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="font-bold block">{{ Str::limit($order->complaint_title, 30) }}</span>
                                        <span class="text-xs text-gray-500">{{ Str::limit($order->complaint_description, 60) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-xs">
                                        @if ($order->priority == 'urgent')
                                            <span class="px-2 py-1 font-semibold text-white bg-red-600 rounded-full">URGENT</span>
                                        @elseif($order->priority == 'medium')
                                            <span class="px-2 py-1 font-semibold text-yellow-800 bg-yellow-200 rounded-full">Medium</span>
                                        @else
                                            <span class="px-2 py-1 font-semibold text-green-800 bg-green-200 rounded-full">Low</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $order->ownership->customer->name }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <form action="{{ route('technician.maintenance.update', $order->id) }}" method="POST" class="inline">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="in_progress">
                                            <button type="submit" class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                                Terima & Proses
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-center text-sm text-gray-500">Tidak ada keluhan pending.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TERJADWAL --}}
        <div class="bg-white rounded-lg shadow-xs p-4">
            <h2 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
                <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                Terjadwal
            </h2>

            <div class="w-full overflow-hidden rounded-lg shadow-xs">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                                <th class="px-4 py-3">Tanggal & Unit</th>
                                <th class="px-4 py-3">Keluhan</th>
                                <th class="px-4 py-3">Jadwal</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            @forelse ($scheduledOrders ?? collect([]) as $order)
                                <tr class="text-gray-700">
                                    <td class="px-4 py-3">
                                        <div class="text-sm">
                                            <p class="font-semibold">Blok {{ $order->ownership->unit->block }}-{{ $order->ownership->unit->number }}</p>
                                            <p class="text-xs text-gray-600">{{ $order->complaint_date->format('d/m/Y') }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="font-bold block">{{ Str::limit($order->complaint_title, 30) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ $order->scheduled_date ? $order->scheduled_date->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <form action="{{ route('technician.maintenance.update', $order->id) }}" method="POST" class="inline">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="in_progress">
                                            <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                Mulai Kerja
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-center text-sm text-gray-500">Tidak ada jadwal.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- SEDANG DIPROSES --}}
        <div class="bg-white rounded-lg shadow-xs p-4">
            <h2 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
                <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span>
                Sedang Diproses
            </h2>

            <div class="w-full overflow-hidden rounded-lg shadow-xs">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                                <th class="px-4 py-3">Tanggal & Unit</th>
                                <th class="px-4 py-3">Keluhan</th>
                                <th class="px-4 py-3">Estimasi Biaya</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            @forelse ($inProgressOrders as $order)
                                <tr class="text-gray-700">
                                    <td class="px-4 py-3">
                                        <div class="text-sm">
                                            <p class="font-semibold">Blok {{ $order->ownership->unit->block }}-{{ $order->ownership->unit->number }}</p>
                                            <p class="text-xs text-gray-600">{{ $order->complaint_date->format('d/m/Y') }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="font-bold block">{{ Str::limit($order->complaint_title, 30) }}</span>
                                        <span class="text-xs text-gray-500">{{ Str::limit($order->complaint_description, 60) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if ($order->cost_status === 'none')
                                            <span class="text-gray-400">Belum input</span>
                                        @elseif($order->cost_status === 'pending')
                                            <span class="text-yellow-600">Menunggu approve</span>
                                        @elseif($order->cost_status === 'approved')
                                            <span class="text-green-600">Disetujui</span>
                                        @elseif($order->cost_status === 'rejected')
                                            <span class="text-red-600">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm space-x-2">
                                        <a href="{{ route('technician.maintenance.show', $order->id) }}"
                                            class="px-3 py-1 border border-gray-300 rounded text-xs text-gray-700 hover:bg-gray-50">
                                            Detail
                                        </a>
                                        <form action="{{ route('technician.maintenance.update', $order->id) }}" method="POST" class="inline">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="done">
                                            <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700"
                                                onclick="return confirm('Selesaikan tugas ini?')">
                                                Tandai Selesai
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-center text-sm text-gray-500">Tidak ada pekerjaan yang sedang diproses.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIWAYAT SELESAI --}}
        <div class="bg-white rounded-lg shadow-xs p-4">
            <h2 class="text-lg font-bold mb-4 text-gray-800 flex items-center">
                <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                Riwayat Selesai (10 Terakhir)
            </h2>

            <div class="w-full overflow-hidden rounded-lg shadow-xs">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                                <th class="px-4 py-3">Tanggal & Unit</th>
                                <th class="px-4 py-3">Keluhan</th>
                                <th class="px-4 py-3">Selesai</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y">
                            @forelse ($doneOrders as $order)
                                <tr class="text-gray-700">
                                    <td class="px-4 py-3">
                                        <div class="text-sm">
                                            <p class="font-semibold">Blok {{ $order->ownership->unit->block }}-{{ $order->ownership->unit->number }}</p>
                                            <p class="text-xs text-gray-600">{{ $order->complaint_date->format('d/m/Y') }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="font-bold block">{{ Str::limit($order->complaint_title, 30) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ optional($order->completion_date)->format('d/m/Y') ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-center text-sm text-gray-500">Belum ada pekerjaan yang selesai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire('Berhasil', '{{ session('success') }}', 'success');
            @endif
            @if (session('error'))
                Swal.fire('Gagal', '{{ session('error') }}', 'error');
            @endif
        });
    </script>
</x-app-layout>
