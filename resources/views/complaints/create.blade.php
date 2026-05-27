<x-app-layout>
    <x-slot name="header">
        {{ __('Buat Laporan Baru') }}
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($errors->any())
                <div class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Lokasi Kerusakan (Unit Rumah)</label>
                <select name="ownership_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required>
                    <option value="">-- Pilih Rumah --</option>
                    @foreach ($myHomes as $home)
                        <option value="{{ $home->id }}">
                            Blok {{ $home->unit->block }} No. {{ $home->unit->number }} ({{ $home->customer->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Tingkat Prioritas</label>
                <select name="priority"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required>
                    <option value="low">Low - Ringan (target 7 hari)</option>
                    <option value="medium" selected>Medium - Sedang (target 3 hari)</option>
                    <option value="urgent">Urgent - Kritis (target 1x24 jam)</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">*Pilih tingkat prioritas sesuai tingkat kerusakan.</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Judul Keluhan</label>
                <input type="text" name="complaint_title"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Contoh: Atap Bocor di Dapur" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Detail Kerusakan</label>
                <textarea name="complaint_description" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Jelaskan secara rinci kerusakan yang terjadi..." required></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Foto Bukti (Opsional)</label>
                <input type="file" name="complaint_photo"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
