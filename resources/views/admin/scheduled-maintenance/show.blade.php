<x-app-layout>
    <x-slot name="header">
        {{ __('Detail Jadwal Pemeliharaan') }}
    </x-slot>

    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-500">Judul:</p>
                <p class="font-bold text-lg">{{ $maintenance->title }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status:</p>
                @if ($maintenance->status == 'scheduled')
                    <span class="px-2 py-1 font-semibold text-blue-700 bg-blue-100 rounded-full">Terjadwal</span>
                @elseif($maintenance->status == 'in_progress')
                    <span class="px-2 py-1 font-semibold text-orange-700 bg-orange-100 rounded-full">Diproses</span>
                @elseif($maintenance->status == 'done')
                    <span class="px-2 py-1 font-semibold text-green-700 bg-green-100 rounded-full">Selesai</span>
                @else
                    <span class="px-2 py-1 font-semibold text-gray-700 bg-gray-100 rounded-full">Batal</span>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-500">Tipe Fasilitas:</p>
                <p class="font-semibold">{{ $maintenance->facility_type }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Lokasi:</p>
                <p class="font-semibold">{{ $maintenance->location ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Jadwal:</p>
                <p class="font-semibold">{{ $maintenance->scheduled_date->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Selesai:</p>
                <p class="font-semibold">{{ $maintenance->completion_date ? $maintenance->completion_date->format('d/m/Y') : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Teknisi:</p>
                <p class="font-semibold">{{ $maintenance->technician->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Dibuat Oleh:</p>
                <p class="font-semibold">{{ $maintenance->creator->name ?? '-' }}</p>
            </div>
        </div>

        @if ($maintenance->description)
            <div class="mb-6">
                <p class="text-sm text-gray-500">Deskripsi:</p>
                <p class="text-gray-700 bg-gray-50 p-3 rounded mt-1">{{ $maintenance->description }}</p>
            </div>
        @endif

        @if ($maintenance->notes)
            <div class="mb-6">
                <p class="text-sm text-gray-500">Catatan:</p>
                <p class="text-gray-700 bg-gray-50 p-3 rounded mt-1">{{ $maintenance->notes }}</p>
            </div>
        @endif

        <div class="flex justify-between border-t pt-4">
            <a href="{{ route('admin.scheduled-maintenance.index') }}"
                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
            <div class="flex gap-2">
                <a href="{{ route('admin.scheduled-maintenance.edit', $maintenance->id) }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <form action="{{ route('admin.scheduled-maintenance.destroy', $maintenance->id) }}" method="POST"
                    onsubmit="return confirm('Hapus jadwal ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
