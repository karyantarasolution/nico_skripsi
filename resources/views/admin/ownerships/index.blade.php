<x-app-layout>
    <x-slot name="header">
        {{ __('Data Kepemilikan & Garansi') }}
    </x-slot>

    <div class="p-4 bg-white rounded-lg shadow-xs">
        <div class="flex justify-between mb-4">
            <h4 class="mb-4 text-lg font-semibold text-gray-600">
                Daftar Pemilik Rumah & Status Garansi
            </h4>
            <a href="{{ route('admin.ownerships.create') }}"
                class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                <i class="fas fa-plus mr-2"></i> Input Kepemilikan
            </a>
        </div>

        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">Unit Rumah</th>
                            <th class="px-4 py-3">Pemilik (Nasabah)</th>
                            <th class="px-4 py-3">Info Pembelian</th>
                            <th class="px-4 py-3">Masa Garansi</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @foreach ($ownerships as $data)
                            <tr class="text-gray-700">
                                <td class="px-4 py-3">
                                    <div class="font-semibold">Blok {{ $data->unit->block }} No.
                                        {{ $data->unit->number }}</div>
                                    <div class="text-xs text-gray-500">Tipe {{ $data->unit->type }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="font-semibold">{{ $data->customer->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $data->customer->phone }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="block font-bold">{{ $data->purchase_method }}</span>
                                    <span class="text-xs text-gray-500">{{ $data->bank_name ?? '-' }}</span>
                                    <div class="text-xs mt-1">Akad: {{ $data->handover_date->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @php
                                        $isExpired = \Carbon\Carbon::now()->greaterThan($data->warranty_end_date);
                                    @endphp

                                    <div class="mb-1">
                                        @if (!$isExpired)
                                            <span
                                                class="px-2 py-1 text-xs font-semibold leading-tight text-green-700 bg-green-100 rounded-full">
                                                Aktif
                                            </span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-xs font-semibold leading-tight text-red-700 bg-red-100 rounded-full">
                                                Expired
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs">s/d {{ $data->warranty_end_date->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <div class="flex justify-center space-x-4">
                                        <a href="{{ route('admin.ownerships.show', $data->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900" title="Lihat Data">
                                            <i class="fas fa-eye fa-lg"></i>
                                        </a>
                                        <a href="{{ route('admin.ownerships.edit', $data->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                            <i class="fas fa-edit fa-lg"></i>
                                        </a>

                                        <form action="{{ route('admin.ownerships.destroy', $data->id) }}"
                                            method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-600 hover:text-red-900"
                                                onclick="confirmDelete(this)" title="Hapus">
                                                <i class="fas fa-trash fa-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t bg-gray-50">
                {{ $ownerships->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}',
                    timer: 1500,
                    showConfirmButton: false
                });
            @endif

            window.confirmDelete = function(button) {
                Swal.fire({
                    title: 'Hapus data kepemilikan?',
                    text: "Status rumah akan kembali menjadi 'Tersedia'!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest('form').submit();
                    }
                })
            }
        });
    </script>
</x-app-layout>
