<x-app-layout>
    <x-slot name="header">
        {{ __('Edit Jadwal Pemeliharaan') }}
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <form action="{{ route('admin.scheduled-maintenance.update', $scheduledMaintenance->id) }}" method="POST">
            @csrf @method('PUT')

            @if ($errors->any())
                <div class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Judul Kegiatan</label>
                <input type="text" name="title" value="{{ old('title', $scheduledMaintenance->title) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="description" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('description', $scheduledMaintenance->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Tipe Fasilitas</label>
                <select name="facility_type" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    <option value="Fasilitas Umum" {{ $scheduledMaintenance->facility_type == 'Fasilitas Umum' ? 'selected' : '' }}>Fasilitas Umum</option>
                    <option value="Fasilitas Sosial" {{ $scheduledMaintenance->facility_type == 'Fasilitas Sosial' ? 'selected' : '' }}>Fasilitas Sosial</option>
                    <option value="Infrastruktur" {{ $scheduledMaintenance->facility_type == 'Infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                    <option value="Lainnya" {{ $scheduledMaintenance->facility_type == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Lokasi</label>
                <input type="text" name="location" value="{{ old('location', $scheduledMaintenance->location) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Teknisi</label>
                <select name="technician_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    <option value="">-- Pilih Teknisi --</option>
                    @foreach ($technicians as $tech)
                        <option value="{{ $tech->id }}" {{ $scheduledMaintenance->technician_id == $tech->id ? 'selected' : '' }}>
                            {{ $tech->name }} ({{ $tech->specialty }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Tanggal Pelaksanaan</label>
                <input type="date" name="scheduled_date" value="{{ old('scheduled_date', $scheduledMaintenance->scheduled_date->format('Y-m-d')) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    <option value="scheduled" {{ $scheduledMaintenance->status == 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="in_progress" {{ $scheduledMaintenance->status == 'in_progress' ? 'selected' : '' }}>Diproses</option>
                    <option value="done" {{ $scheduledMaintenance->status == 'done' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ $scheduledMaintenance->status == 'cancelled' ? 'selected' : '' }}>Batal</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Catatan</label>
                <textarea name="notes" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('notes', $scheduledMaintenance->notes) }}</textarea>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.scheduled-maintenance.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</a>
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                    <i class="fas fa-save mr-1"></i> Update
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
