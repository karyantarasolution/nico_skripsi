<x-app-layout>
    <x-slot name="header">
        {{ __('Jadwal Pemeliharaan Rutin') }}
    </x-slot>

    <div class="mb-4 text-right">
        <a href="{{ route('admin.scheduled-maintenance.create') }}"
            class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 text-sm">
            + Buat Jadwal Baru
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-xs p-4">
        <div class="w-full overflow-hidden rounded-lg border">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead class="bg-gray-50 border-b">
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Kegiatan</th>
                            <th class="px-4 py-3">Tipe Fasilitas</th>
                            <th class="px-4 py-3">Lokasi</th>
                            <th class="px-4 py-3">Teknisi</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @forelse ($maintenances as $m)
                            <tr class="text-gray-700">
                                <td class="px-4 py-3 text-sm">{{ $m->scheduled_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-sm font-semibold">{{ $m->title }}</td>
                                <td class="px-4 py-3 text-xs">{{ $m->facility_type }}</td>
                                <td class="px-4 py-3 text-sm">{{ $m->location ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $m->technician->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs">
                                    @if ($m->status == 'scheduled')
                                        <span class="px-2 py-1 font-semibold text-blue-700 bg-blue-100 rounded-full">Terjadwal</span>
                                    @elseif($m->status == 'in_progress')
                                        <span class="px-2 py-1 font-semibold text-orange-700 bg-orange-100 rounded-full">Diproses</span>
                                    @elseif($m->status == 'done')
                                        <span class="px-2 py-1 font-semibold text-green-700 bg-green-100 rounded-full">Selesai</span>
                                    @else
                                        <span class="px-2 py-1 font-semibold text-gray-700 bg-gray-100 rounded-full">Batal</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('admin.scheduled-maintenance.show', $m->id) }}"
                                        class="text-purple-600 hover:underline mr-2">Detail</a>
                                    <a href="{{ route('admin.scheduled-maintenance.edit', $m->id) }}"
                                        class="text-blue-600 hover:underline">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-3 text-center text-sm text-gray-500">
                                    Belum ada jadwal pemeliharaan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $maintenances->links() }}</div>
    </div>
</x-app-layout>
