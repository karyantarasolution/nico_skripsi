<x-app-layout>
    <x-slot name="header">
        {{ __('Detail Laporan') }}
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-4">{{ $order->complaint_title }}</h2>

            <div class="mb-4 flex flex-wrap items-center gap-2">
                <span class="text-gray-500 text-sm">Status:</span>
                @php
                    $statusBadge = [
                        'waiting_approval' => ['bg-yellow-100 text-yellow-800', 'Menunggu Persetujuan Admin'],
                        'pending' => ['bg-red-100 text-red-700', 'Pending'],
                        'scheduled' => ['bg-blue-100 text-blue-700', 'Terjadwal'],
                        'in_progress' => ['bg-orange-100 text-orange-700', 'Sedang Dikerjakan Teknisi'],
                        'on_hold' => ['bg-gray-100 text-gray-700', 'Ditunda'],
                        'rejected' => ['bg-red-200 text-red-800', 'Ditolak'],
                        'reopened' => ['bg-purple-100 text-purple-700', 'Dibuka Kembali'],
                        'done' => ['bg-green-100 text-green-700', 'Perbaikan Selesai'],
                        'cancelled' => ['bg-gray-200 text-gray-600', 'Dibatalkan'],
                    ];
                    [$badgeClass, $badgeText] = $statusBadge[$order->status] ?? ['bg-gray-100 text-gray-700', $order->status];
                @endphp
                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">{{ $badgeText }}</span>

                <span class="text-gray-500 text-sm ml-4">Prioritas:</span>
                @if ($order->priority == 'urgent')
                    <span class="px-2 py-1 text-xs font-semibold text-white bg-red-600 rounded-full">URGENT</span>
                @elseif($order->priority == 'medium')
                    <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">Medium</span>
                @else
                    <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Low</span>
                @endif
            </div>

            {{-- SLA Deadline --}}
            @if ($order->sla_deadline)
                <div class="mb-4 p-3 rounded {{ now() > $order->sla_deadline ? 'bg-red-50 border border-red-200' : 'bg-blue-50 border border-blue-200' }}">
                    <p class="text-sm {{ now() > $order->sla_deadline ? 'text-red-700' : 'text-blue-700' }}">
                        <i class="fas fa-clock mr-1"></i>
                        Target penyelesaian SLA: {{ $order->sla_deadline->format('d/m/Y H:i') }}
                        @if (now() > $order->sla_deadline)
                            <span class="font-bold">(TERLAMBAT)</span>
                        @endif
                    </p>
                </div>
            @endif

            {{-- TAGIHAN JIKA ADA --}}
            @if ($order->cost > 0)
                <div class="mt-4 p-4 bg-white border-l-4 border-orange-500 shadow-sm rounded">
                    <h4 class="font-bold text-orange-600 text-lg">Tagihan Perbaikan</h4>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-gray-600">Total Biaya:</span>
                        <span class="text-2xl font-bold text-gray-800">Rp {{ number_format($order->cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-2 text-sm">
                        Status:
                        @if ($order->payment_status == 'Paid')
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full font-bold">LUNAS</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full font-bold">BELUM DIBAYAR</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-2">*Silakan lakukan pembayaran ke Admin Developer.</p>
                </div>
            @endif

            {{-- ESTIMASI BIAYA DARI TEKNISI --}}
            @if ($order->cost_status === 'approved' && $order->estimated_cost > 0)
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="font-bold text-blue-700">Estimasi Biaya Teknisi</h4>
                    <p class="text-lg font-bold mt-1">Rp {{ number_format($order->estimated_cost, 0, ',', '.') }}</p>
                    @if ($order->estimated_description)
                        <p class="text-sm text-gray-600 mt-1">{{ $order->estimated_description }}</p>
                    @endif
                    <p class="text-xs text-gray-500 mt-1">Telah disetujui oleh Admin.</p>
                </div>
            @elseif($order->cost_status === 'pending')
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-yellow-700"><i class="fas fa-clock mr-1"></i> Estimasi biaya sedang menunggu persetujuan Admin.</p>
                </div>
            @endif

            <p class="text-gray-700 mb-6 mt-4">{{ $order->complaint_description }}</p>

            @if ($order->complaint_photo)
                <div class="mb-4">
                    <p class="text-sm font-semibold mb-2">Foto Bukti:</p>
                    <img src="{{ asset('storage/' . $order->complaint_photo) }}" class="w-full max-w-md rounded-lg border">
                </div>
            @endif

            @if ($order->rejection_reason)
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-700 font-bold"><i class="fas fa-times-circle mr-1"></i> Alasan Ditolak:</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $order->rejection_reason }}</p>
                </div>
            @endif

            @if ($order->admin_notes)
                <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="text-gray-700 font-bold text-sm"><i class="fas fa-sticky-note mr-1"></i> Catatan Admin:</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $order->admin_notes }}</p>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            @if ($order->technician)
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="font-bold text-lg mb-2">Teknisi Bertugas</h3>
                    <div class="flex items-center space-x-3">
                        <div class="bg-gray-200 p-3 rounded-full">
                            <i class="fa-solid fa-helmet-safety text-gray-600 fa-lg"></i>
                        </div>
                        <div>
                            <p class="font-semibold">{{ $order->technician->name }}</p>
                            <p class="text-sm text-gray-500">{{ $order->technician->specialty }}</p>
                            <p class="text-sm text-blue-600"><i class="fab fa-whatsapp"></i> {{ $order->technician->phone }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tombol Aksi --}}
            @if ($order->status === 'waiting_approval')
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <p class="text-yellow-700 text-sm"><i class="fas fa-info-circle mr-1"></i> Laporan Anda sedang menunggu persetujuan Admin.</p>
                </div>
            @endif

            @if ($order->cost_status === 'approved' && $order->payment_status === 'Free' && $order->estimated_cost > 0)
                <div class="bg-white p-4 rounded-lg shadow-md border-t-4 border-blue-400">
                    <h4 class="font-bold text-gray-700 mb-2">Konfirmasi Biaya</h4>
                    <p class="text-sm text-gray-600 mb-3">Admin telah menyetujui estimasi biaya. Konfirmasi untuk melanjutkan.</p>
                    <form action="{{ route('complaints.confirm-cost', $order->id) }}" method="POST">
                        @csrf @method('PUT')
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 font-semibold"
                            onclick="return confirm('Konfirmasi biaya Rp {{ number_format($order->estimated_cost, 0, ',', '.') }}?')">
                            <i class="fas fa-check mr-1"></i> Konfirmasi Biaya
                        </button>
                    </form>
                </div>
            @endif

            {{-- Rating --}}
            @if ($order->status == 'done' && $order->rating == null)
                <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-yellow-400">
                    <h3 class="font-bold text-lg mb-2">Beri Penilaian</h3>
                    <p class="text-sm text-gray-600 mb-4">Bagaimana hasil kerja teknisi kami?</p>

                    <form action="{{ route('complaints.rate', $order->id) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="flex space-x-2 mb-4">
                            <select name="rating" class="w-full border-gray-300 rounded-md">
                                <option value="5">⭐⭐⭐⭐⭐ (Sangat Puas)</option>
                                <option value="4">⭐⭐⭐⭐ (Puas)</option>
                                <option value="3">⭐⭐⭐ (Cukup)</option>
                                <option value="2">⭐⭐ (Kurang)</option>
                                <option value="1">⭐ (Kecewa)</option>
                            </select>
                        </div>

                        <textarea name="review" rows="2" class="w-full border-gray-300 rounded-md text-sm mb-2" placeholder="Tulis ulasan..."></textarea>

                        <button type="submit" class="w-full bg-yellow-500 text-white py-2 rounded-md hover:bg-yellow-600 font-semibold">
                            Kirim Ulasan
                        </button>
                    </form>
                </div>
            @elseif($order->rating)
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="font-bold text-lg mb-2">Ulasan Anda</h3>
                    <div class="text-yellow-500 mb-1">
                        @for ($i = 0; $i < $order->rating; $i++)
                            ⭐
                        @endfor
                    </div>
                    <p class="text-gray-600 italic">"{{ $order->review }}"</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
