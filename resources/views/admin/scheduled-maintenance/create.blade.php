<x-app-layout>
    <x-slot name="header">
        {{ __('Buat Jadwal Pemeliharaan Baru') }}
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <form action="{{ route('admin.scheduled-maintenance.store') }}" method="POST">
            @csrf

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
                <input type="text" name="title" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Contoh: Perawatan Taman Blok A">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="description" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Detail kegiatan pemeliharaan..."></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Tipe Fasilitas</label>
                <select name="facility_type" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    <option value="Fasilitas Umum">Fasilitas Umum</option>
                    <option value="Fasilitas Sosial">Fasilitas Sosial</option>
                    <option value="Infrastruktur">Infrastruktur</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Lokasi</label>
                <input type="text" name="location"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Contoh: Taman Tengah, Jalan Utama, dll">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Teknisi (Opsional)</label>
                <select name="technician_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    <option value="">-- Pilih Teknisi --</option>
                    @foreach ($technicians as $tech)
                        <option value="{{ $tech->id }}">{{ $tech->name }} ({{ $tech->specialty }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Tanggal Pelaksanaan</label>
                <input type="date" name="scheduled_date" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                    <i class="fas fa-save mr-1"></i> Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
