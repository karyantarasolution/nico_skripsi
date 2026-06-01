<x-app-layout>
    <x-slot name="header">
        {{ __('Proses Laporan Keluhan') }}
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-bold mb-4 text-gray-700">Detail Masalah</h3>

            <div class="mb-4 border-b pb-4">
                <p class="text-sm text-gray-500">Unit Rumah:</p>
                <p class="font-semibold text-lg">Blok {{ $order->ownership->unit->block }} - No.
                    {{ $order->ownership->unit->number }}</p>
                <p class="text-sm text-gray-600">Pemilik: {{ $order->ownership->customer->name }}
                    ({{ $order->ownership->customer->phone }})</p>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-500">Prioritas:</p>
                @if ($order->priority == 'urgent')
                    <span class="px-2 py-1 text-xs font-semibold text-white bg-red-600 rounded-full">URGENT</span>
                @elseif($order->priority == 'medium')
                    <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">Medium</span>
                @else
                    <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Low</span>
                @endif
            </div>

            @if ($order->sla_deadline)
                <div class="mb-4 p-3 rounded {{ now() > $order->sla_deadline ? 'bg-red-50 border border-red-200' : 'bg-blue-50 border border-blue-200' }}">
                    <p class="text-sm font-semibold {{ now() > $order->sla_deadline ? 'text-red-700' : 'text-blue-700' }}">
                        <i class="fas {{ now() > $order->sla_deadline ? 'fa-exclamation-triangle' : 'fa-clock' }} mr-1"></i>
                        SLA Deadline: {{ $order->sla_deadline->format('d/m/Y H:i') }}
                        @if (now() > $order->sla_deadline)
                            <span class="font-bold">(TERLAMBAT!)</span>
                        @endif
                    </p>
                </div>
            @endif

            <div class="mb-4">
                <p class="text-sm text-gray-500">Keluhan:</p>
                <h4 class="font-bold">{{ $order->complaint_title }}</h4>
                <p class="text-gray-700 mt-2 bg-gray-50 p-3 rounded">{{ $order->complaint_description }}</p>
            </div>

            @if ($order->complaint_photo)
                <div>
                    <p class="text-sm text-gray-500 mb-2">Foto:</p>
                    <a href="{{ asset('storage/' . $order->complaint_photo) }}" target="_blank">
                        <img src="{{ asset('storage/' . $order->complaint_photo) }}" class="h-40 rounded border hover:opacity-75">
                    </a>
                </div>
            @endif

            {{-- Tracking Status Workflow --}}
            <div class="mt-6 border-t pt-4">
                <h4 class="font-bold text-gray-700 mb-3">Status Workflow</h4>
                <div class="flex flex-wrap gap-2">
                    @php
                        $statuses = [
                            'waiting_approval' => 'Menunggu',
                            'pending' => 'Pending',
                            'scheduled' => 'Terjadwal',
                            'in_progress' => 'Proses',
                            'on_hold' => 'Ditunda',
                            'rejected' => 'Ditolak',
                            'reopened' => 'Dibuka',
                            'done' => 'Selesai',
                            'cancelled' => 'Batal',
                        ];
                    @endphp
                    @foreach ($statuses as $key => $label)
                        <span class="px-2 py-1 text-xs rounded-full {{ $order->status === $key ? 'bg-purple-600 text-white font-bold' : 'bg-gray-100 text-gray-500' }}">
                            {{ $label }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-bold mb-4 text-gray-700">Tindak Lanjut Admin</h3>

            <form action="{{ route('admin.maintenance.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prioritas</label>
                    <select name="priority" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <option value="low" {{ $order->priority == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ $order->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="urgent" {{ $order->priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Update Status Pengerjaan</label>
                    <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <option value="waiting_approval" {{ $order->status == 'waiting_approval' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="scheduled" {{ $order->status == 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                        <option value="in_progress" {{ $order->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="on_hold" {{ $order->status == 'on_hold' ? 'selected' : '' }}>Ditunda (On Hold)</option>
                        <option value="rejected" {{ $order->status == 'rejected' ? 'selected' : '' }}>Tolak (Rejected)</option>
                        <option value="reopened" {{ $order->status == 'reopened' ? 'selected' : '' }}>Buka Kembali (Reopened)</option>
                        <option value="done" {{ $order->status == 'done' ? 'selected' : '' }}>Selesai (Done)</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Batalkan (Cancelled)</option>
                    </select>
                </div>

                {{-- Alasan penolakan --}}
                <div class="mb-6" id="rejection-reason-wrapper" style="display:none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                    <textarea name="rejection_reason" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="Alasan mengapa laporan ditolak...">{{ $order->rejection_reason }}</textarea>
                </div>

                {{-- Tanggal Terjadwal --}}
                <div class="mb-6" id="scheduled-date-wrapper" style="display:none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Terjadwal</label>
                    <input type="date" name="scheduled_date" value="{{ optional($order->scheduled_date)->format('Y-m-d') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                </div>

                <div class="mb-6" id="technician-wrapper">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tugaskan Teknisi</label>
                    <div class="flex gap-2 items-start">
                        <select name="technician_id" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <option value="">-- Pilih Teknisi Available --</option>
                            @foreach ($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ $order->technician_id == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->name }} - Spesialis {{ $tech->specialty }} ({{ $tech->status }})
                                </option>
                            @endforeach
                        </select>
                        <form action="{{ route('admin.maintenance.smart-assign', $order->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 whitespace-nowrap"
                                onclick="return confirm('Assign teknisi terbaik secara otomatis?')">
                                <i class="fas fa-magic mr-1"></i> Smart Assign
                            </button>
                        </form>
                    </div>
                    @if ($order->technician)
                        <p class="text-xs text-blue-600 mt-1">Saat ini: {{ $order->technician->name }}</p>
                    @endif
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin</label>
                    <textarea name="admin_notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="Catatan internal...">{{ $order->admin_notes }}</textarea>
                </div>

                @if ($isWarrantyExpired)
                    <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center mb-2 text-red-700 font-bold">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <span>Masa Garansi Unit Habis!</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Unit ini sudah melewati masa garansi.</p>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Referensi Daftar Harga:</label>
                            <select class="w-full text-sm border-gray-300 rounded-md bg-gray-100"
                                onchange="document.getElementById('costInput').value = this.value">
                                <option value="0">-- Pilih dari List Harga (Opsional) --</option>
                                @foreach ($repairPrices as $price)
                                    <option value="{{ (int) $price->price }}">{{ $price->service_name }} - Rp {{ number_format($price->price) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Tagihan (Rp)</label>
                            <input type="number" name="cost" id="costInput" value="{{ $order->cost }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 font-bold"
                                placeholder="0">
                            <p class="text-xs text-gray-500 mt-1">*Masukkan 0 jika digratiskan.</p>
                        </div>
                    </div>
                @else
                    <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center text-green-700 font-bold">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Garansi Aktif</span>
                        </div>
                        <p class="text-sm text-gray-600">Perbaikan ini ditanggung developer (Gratis).</p>
                        <input type="hidden" name="cost" value="0">
                    </div>
                @endif

                <div class="flex justify-end mt-4">
                    <button type="submit" class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 font-bold shadow">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

            {{-- ESTIMASI BIAYA DARI TEKNISI --}}
            @if ($order->cost_status === 'pending')
                <div class="mt-6 border-t pt-6">
                    <h4 class="font-bold text-gray-700 mb-2">Estimasi Biaya dari Teknisi</h4>
                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                        <p class="text-sm"><strong>Estimasi:</strong> Rp {{ number_format($order->estimated_cost, 0, ',', '.') }}</p>
                        @if ($order->estimated_description)
                            <p class="text-sm mt-2"><strong>Keterangan:</strong> {{ $order->estimated_description }}</p>
                        @endif
                        <div class="flex gap-2 mt-3">
                            <form action="{{ route('admin.maintenance.approve-estimate', $order->id) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700"
                                    onclick="return confirm('Setujui estimasi biaya ini? Pelanggan akan mendapat notifikasi.')">
                                    <i class="fas fa-check mr-1"></i> Setujui
                                </button>
                            </form>
                            <form action="{{ route('admin.maintenance.reject-estimate', $order->id) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="rejection_note" value="Estimasi ditolak oleh Admin">
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700"
                                    onclick="return confirm('Tolak estimasi biaya ini?')">
                                    <i class="fas fa-times mr-1"></i> Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @elseif($order->cost_status === 'approved')
                <div class="mt-6 border-t pt-6">
                    <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                        <p class="text-green-700 font-bold"><i class="fas fa-check-circle mr-1"></i> Estimasi Disetujui</p>
                        <p class="text-sm mt-1">Rp {{ number_format($order->estimated_cost, 0, ',', '.') }}</p>
                        @if ($order->cost_approved_at)
                            <p class="text-xs text-gray-500 mt-1">Disetujui: {{ $order->cost_approved_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            @elseif($order->cost_status === 'rejected')
                <div class="mt-6 border-t pt-6">
                    <div class="bg-red-50 border border-red-200 p-4 rounded-lg">
                        <p class="text-red-700 font-bold"><i class="fas fa-times-circle mr-1"></i> Estimasi Ditolak</p>
                    </div>
                </div>
            @endif

            {{-- STATUS PEMBAYARAN --}}
            @if ($order->cost > 0 && $order->payment_status == 'Unpaid' && $order->payment_proof)
                <div class="mt-6 border-t pt-6">
                    <h4 class="font-bold text-gray-700 mb-2">Status Pembayaran</h4>
                    <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                        <p class="text-blue-800 font-bold mb-2"><i class="fas fa-clock mr-1"></i> MENUNGGU VERIFIKASI</p>
                        <p class="text-sm text-gray-600">Total: Rp {{ number_format($order->cost, 0, ',', '.') }}</p>

                        {{-- Tampilkan Bukti Pembayaran --}}
                        <div class="mt-3">
                            <p class="text-sm font-semibold text-gray-700 mb-1">Bukti Pembayaran:</p>
                            <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank">
                                <img src="{{ asset('storage/' . $order->payment_proof) }}" class="h-40 rounded border hover:opacity-75">
                            </a>
                            @if ($order->payment_proof_uploaded_at)
                                <p class="text-xs text-gray-500 mt-1">Diupload: {{ $order->payment_proof_uploaded_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>

                        <div class="flex gap-2 mt-4">
                            <form action="{{ route('admin.maintenance.verify-payment', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 font-bold text-sm"
                                    onclick="return confirm('Verifikasi pembayaran ini? Status akan berubah menjadi LUNAS.')">
                                    <i class="fas fa-check mr-1"></i> Verifikasi & Lunas
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @elseif ($order->cost > 0 && $order->payment_status == 'Unpaid')
                <div class="mt-6 border-t pt-6">
                    <h4 class="font-bold text-gray-700 mb-2">Status Pembayaran</h4>
                    <div class="bg-orange-50 border border-orange-200 p-4 rounded-lg flex justify-between items-center">
                        <div>
                            <p class="text-orange-800 font-bold">BELUM DIBAYAR (Unpaid)</p>
                            <p class="text-sm text-gray-600">Total: Rp {{ number_format($order->cost, 0, ',', '.') }}</p>
                            @if ($order->payment_proof_uploaded_at)
                                <p class="text-xs text-gray-500 mt-1">Bukti diupload: {{ $order->payment_proof_uploaded_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                        <form id="form-mark-paid" action="{{ route('admin.maintenance.paid', $order->id) }}" method="POST">
                            @csrf @method('PUT')
                            <button type="button" onclick="confirmPayment()"
                                class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 font-bold text-sm">
                                <i class="fas fa-money-bill-wave mr-1"></i> Tandai Lunas
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($order->cost > 0 && $order->payment_status == 'Paid')
                <div class="mt-6 border-t pt-6">
                    <h4 class="font-bold text-gray-700 mb-2">Status Pembayaran</h4>
                    <div class="bg-green-50 border border-green-200 p-4 rounded-lg flex items-center">
                        <div class="p-2 bg-green-200 rounded-full text-green-700 mr-3">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <p class="text-green-800 font-bold">LUNAS (Paid)</p>
                            <p class="text-sm text-gray-600">Terima kasih, pembayaran telah diverifikasi.</p>
                            @if ($order->payment_proof)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" class="text-xs text-blue-600 hover:underline">
                                        <i class="fas fa-image mr-1"></i> Lihat Bukti Pembayaran
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function confirmPayment() {
            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                text: "Yakin ingin menandai tagihan ini sebagai LUNAS?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Tandai Lunas!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-mark-paid').submit();
                }
            })
        }

        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const rejectionWrapper = document.getElementById('rejection-reason-wrapper');
            const scheduledWrapper = document.getElementById('scheduled-date-wrapper');

            function toggleFields() {
                rejectionWrapper.style.display = statusSelect.value === 'rejected' ? 'block' : 'none';
                scheduledWrapper.style.display = statusSelect.value === 'scheduled' ? 'block' : 'none';
            }

            statusSelect.addEventListener('change', toggleFields);
            toggleFields();
        });
    </script>
</x-app-layout>
