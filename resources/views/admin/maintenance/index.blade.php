<x-app-layout>
    <x-slot name="header">
        {{ __('Laporan Keluhan Masuk') }}
    </x-slot>

    {{-- Filter Prioritas --}}
    <div class="flex flex-wrap gap-2 mb-4">
        <a href="{{ route('admin.maintenance.index') }}"
            class="px-3 py-1 rounded-full text-sm {{ !request('priority') ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700' }}">
            Semua
        </a>
        <a href="{{ route('admin.maintenance.index', ['priority' => 'urgent']) }}"
            class="px-3 py-1 rounded-full text-sm {{ request('priority') === 'urgent' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700' }}">
            Urgent
        </a>
        <a href="{{ route('admin.maintenance.index', ['priority' => 'medium']) }}"
            class="px-3 py-1 rounded-full text-sm {{ request('priority') === 'medium' ? 'bg-yellow-600 text-white' : 'bg-gray-200 text-gray-700' }}">
            Medium
        </a>
        <a href="{{ route('admin.maintenance.index', ['priority' => 'low']) }}"
            class="px-3 py-1 rounded-full text-sm {{ request('priority') === 'low' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }}">
            Low
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-xs p-4">
        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">Tanggal & Unit</th>
                            <th class="px-4 py-3">Keluhan</th>
                            <th class="px-4 py-3">Prioritas / SLA</th>
                            <th class="px-4 py-3">Teknisi</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tagihan</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @foreach ($orders as $order)
                            <tr class="text-gray-700">
                                <td class="px-4 py-3">
                                    <div class="flex items-center text-sm">
                                        <div>
                                            <p class="font-semibold">
                                                Blok {{ $order->ownership->unit->block }}-{{ $order->ownership->unit->number }}
                                            </p>
                                            <p class="text-xs text-gray-600">
                                                {{ $order->complaint_date->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="font-bold block">{{ Str::limit($order->complaint_title, 20) }}</span>
                                    <span class="text-xs text-gray-500">{{ $order->ownership->customer->name }}</span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if ($order->priority == 'urgent')
                                        <span class="px-2 py-1 font-semibold text-white bg-red-600 rounded-full">URGENT</span>
                                    @elseif($order->priority == 'medium')
                                        <span class="px-2 py-1 font-semibold text-yellow-800 bg-yellow-200 rounded-full">Medium</span>
                                    @else
                                        <span class="px-2 py-1 font-semibold text-green-800 bg-green-200 rounded-full">Low</span>
                                    @endif
                                    @if ($order->sla_deadline)
                                        <br><span class="text-[10px] {{ now() > $order->sla_deadline ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                            @if (now() > $order->sla_deadline)
                                                <i class="fas fa-exclamation-circle"></i> SLA Terlambat!
                                            @else
                                                Deadline: {{ $order->sla_deadline->format('d/m/Y H:i') }}
                                            @endif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $order->technician->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @php
                                        $statusBadge = [
                                            'waiting_approval' => ['bg-yellow-100 text-yellow-800', 'Menunggu'],
                                            'pending' => ['bg-red-100 text-red-700', 'Pending'],
                                            'scheduled' => ['bg-blue-100 text-blue-700', 'Terjadwal'],
                                            'in_progress' => ['bg-orange-100 text-orange-700', 'Proses'],
                                            'on_hold' => ['bg-gray-100 text-gray-700', 'Ditunda'],
                                            'rejected' => ['bg-red-200 text-red-800', 'Ditolak'],
                                            'reopened' => ['bg-purple-100 text-purple-700', 'Dibuka'],
                                            'done' => ['bg-green-100 text-green-700', 'Selesai'],
                                            'cancelled' => ['bg-gray-200 text-gray-600', 'Batal'],
                                        ];
                                        [$badgeClass, $badgeText] = $statusBadge[$order->status] ?? ['bg-gray-100 text-gray-700', $order->status];
                                    @endphp
                                    <span class="px-2 py-1 font-semibold leading-tight rounded-full {{ $badgeClass }}">
                                        {{ $badgeText }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if ($order->payment_status == 'Free')
                                        <span class="px-2 py-1 font-semibold text-blue-700 bg-blue-100 rounded-full">Gratis (Garansi)</span>
                                    @elseif($order->payment_status == 'Paid')
                                        <div class="font-bold text-green-600">Rp {{ number_format($order->cost, 0, ',', '.') }}</div>
                                        <span class="px-2 py-0.5 text-[10px] font-semibold text-green-700 bg-green-100 rounded-full">LUNAS</span>
                                    @else
                                        <div class="font-bold text-red-600">Rp {{ number_format($order->cost, 0, ',', '.') }}</div>
                                        <span class="px-2 py-0.5 text-[10px] font-semibold text-red-700 bg-red-100 rounded-full">BELUM BAYAR</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-4 text-sm">
                                        <a href="{{ route('admin.maintenance.show', $order->id) }}"
                                            class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg focus:outline-none focus:shadow-outline-gray"
                                            aria-label="Edit">
                                            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t">
                {{ $orders->links() }}
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if (session('success'))
                    Swal.fire('Berhasil', '{{ session('success') }}', 'success');
                @endif
            });
        </script>
</x-app-layout>
