<x-app-layout>
    <x-slot name="header">
        {{ __('Detail Pekerjaan Perbaikan') }}
    </x-slot>

    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2">Informasi Tugas</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-500">Unit Rumah:</p>
                <p class="font-semibold text-lg text-blue-700">Blok {{ $order->ownership->unit->block }} - No. {{ $order->ownership->unit->number }}</p>
                <p class="text-sm text-gray-600">Pemilik: {{ $order->ownership->customer->name }} ({{ $order->ownership->customer->phone }})</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Lapor:</p>
                <p class="font-semibold">{{ $order->complaint_date->format('d F Y') }}</p>
                <p class="text-sm text-gray-500 mt-2">Prioritas:</p>
                @if ($order->priority == 'urgent')
                    <span class="px-2 py-1 text-xs font-semibold text-white bg-red-600 rounded-full">URGENT</span>
                @elseif($order->priority == 'medium')
                    <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">Medium</span>
                @else
                    <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Low</span>
                @endif
                <p class="text-sm text-gray-500 mt-2">Status:</p>
                @php
                    $statusLabels = [
                        'waiting_approval' => ['label' => 'Menunggu', 'class' => 'text-gray-800 bg-gray-200'],
                        'pending' => ['label' => 'Pending', 'class' => 'text-red-800 bg-red-100'],
                        'scheduled' => ['label' => 'Terjadwal', 'class' => 'text-blue-800 bg-blue-100'],
                        'in_progress' => ['label' => 'Sedang Diproses', 'class' => 'text-yellow-800 bg-yellow-100'],
                        'on_hold' => ['label' => 'Ditunda', 'class' => 'text-orange-800 bg-orange-100'],
                        'rejected' => ['label' => 'Ditolak', 'class' => 'text-red-800 bg-red-200'],
                        'reopened' => ['label' => 'Dibuka Kembali', 'class' => 'text-purple-800 bg-purple-100'],
                        'done' => ['label' => 'Selesai', 'class' => 'text-green-800 bg-green-100'],
                        'cancelled' => ['label' => 'Dibatalkan', 'class' => 'text-gray-600 bg-gray-200'],
                    ];
                    $s = $statusLabels[$order->status] ?? ['label' => $order->status, 'class' => 'text-gray-800 bg-gray-200'];
                @endphp
                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $s['class'] }}">{{ $s['label'] }}</span>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded border mb-6">
            <p class="text-sm text-gray-500">Judul Keluhan:</p>
            <h4 class="font-bold text-gray-800 text-lg">{{ $order->complaint_title }}</h4>
            <p class="text-sm text-gray-500 mt-3">Detail Kerusakan:</p>
            <p class="text-gray-700 mt-1">{{ $order->complaint_description }}</p>
        </div>

        @if ($order->complaint_photo)
            <div class="mb-6">
                <p class="text-sm text-gray-500 mb-2">Foto Kerusakan:</p>
                <a href="{{ asset('storage/' . $order->complaint_photo) }}" target="_blank">
                    <img src="{{ asset('storage/' . $order->complaint_photo) }}" class="max-w-full h-auto max-h-64 rounded border hover:opacity-75 transition">
                </a>
            </div>
        @endif

        {{-- FORM UPDATE STATUS TEKNISI --}}
        <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
            <h4 class="font-bold text-gray-700 mb-3"><i class="fas fa-edit mr-1"></i> Update Status Pekerjaan</h4>
            <form action="{{ route('technician.maintenance.update', $order->id) }}" method="POST">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="scheduled" {{ $order->status == 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                        <option value="in_progress" {{ $order->status == 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                        <option value="on_hold" {{ $order->status == 'on_hold' ? 'selected' : '' }}>Ditunda (On Hold)</option>
                        <option value="reopened" {{ $order->status == 'reopened' ? 'selected' : '' }}>Dibuka Kembali</option>
                        <option value="done" {{ $order->status == 'done' ? 'selected' : '' }}>Selesai (Done)</option>
                        <option value="rejected" {{ $order->status == 'rejected' ? 'selected' : '' }}>Tolak (Rejected)</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Batalkan (Cancelled)</option>
                    </select>
                </div>

                <div class="mb-3" id="scheduled-date-wrapper" style="display:none;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Terjadwal</label>
                    <input type="date" name="scheduled_date" value="{{ optional($order->scheduled_date)->format('Y-m-d') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="mb-3" id="rejection-reason-wrapper" style="display:none;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan (jika ditolak/dibatalkan)</label>
                    <textarea name="rejection_reason" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Alasan penolakan/pembatalan...">{{ $order->rejection_reason }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="admin_notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Catatan pekerjaan...">{{ $order->admin_notes }}</textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 font-bold shadow"
                    onclick="return confirm('Update status pekerjaan ini?')">
                    <i class="fas fa-save mr-1"></i> Update Status
                </button>
            </form>
        </div>

        {{-- FORM INPUT ESTIMASI BIAYA (hanya saat in_progress) --}}
        @if ($order->status === 'in_progress' && in_array($order->cost_status, ['none', 'rejected']))
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="font-bold text-blue-700 mb-3"><i class="fas fa-calculator mr-1"></i> Input Estimasi Biaya</h4>
                <form action="{{ route('technician.maintenance.estimate', $order->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Perkiraan Biaya (Rp)</label>
                        <input type="number" name="estimated_cost" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Masukkan perkiraan biaya..." required>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rincian Pekerjaan</label>
                        <textarea name="estimated_description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Jelaskan rincian perbaikan dan biaya..."></textarea>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-semibold">
                        <i class="fas fa-paper-plane mr-1"></i> Kirim Estimasi ke Admin
                    </button>
                </form>
            </div>
        @endif

        {{-- Status Estimasi --}}
        @if ($order->cost_status === 'pending')
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-yellow-700 font-bold"><i class="fas fa-clock mr-1"></i> Estimasi Menunggu Persetujuan Admin</p>
                <p class="text-sm mt-1">Rp {{ number_format($order->estimated_cost, 0, ',', '.') }}</p>
                @if ($order->estimated_description)
                    <p class="text-sm text-gray-600 mt-1">{{ $order->estimated_description }}</p>
                @endif
            </div>
        @elseif($order->cost_status === 'approved')
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-700 font-bold"><i class="fas fa-check-circle mr-1"></i> Estimasi Disetujui Admin</p>
                <p class="text-sm mt-1">Rp {{ number_format($order->estimated_cost, 0, ',', '.') }}</p>
            </div>
        @elseif($order->cost_status === 'rejected')
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-700 font-bold"><i class="fas fa-times-circle mr-1"></i> Estimasi Ditolak</p>
                <p class="text-sm text-gray-600 mt-1">Silakan input ulang estimasi biaya.</p>
            </div>
        @endif

        <div class="flex justify-between border-t pt-4">
            <a href="{{ route('technician.maintenance.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const statusSelect = document.getElementById('status');
                const scheduledWrapper = document.getElementById('scheduled-date-wrapper');
                const rejectionWrapper = document.getElementById('rejection-reason-wrapper');

                function toggleFields() {
                    scheduledWrapper.style.display = statusSelect.value === 'scheduled' ? 'block' : 'none';
                    rejectionWrapper.style.display = (statusSelect.value === 'rejected' || statusSelect.value === 'cancelled') ? 'block' : 'none';
                }

                statusSelect.addEventListener('change', toggleFields);
                toggleFields();
            });
        </script>
    </div>
</x-app-layout>
