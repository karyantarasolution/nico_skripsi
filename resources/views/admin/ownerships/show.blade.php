<x-app-layout>
    <x-slot name="header">
        {{ __('Detail Kepemilikan & Garansi') }}
    </x-slot>

    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">

            <div class="p-6 bg-purple-50">
                <h3 class="text-lg font-bold text-purple-800 mb-4">Informasi Aset</h3>

                <div class="mb-4">
                    <label class="text-xs text-gray-500 uppercase">Unit Rumah</label>
                    <p class="text-xl font-bold">Blok {{ $ownership->unit->block }} - {{ $ownership->unit->number }}</p>
                    <span class="text-sm text-gray-600">Tipe {{ $ownership->unit->type }}</span>
                </div>

                <div class="mb-4">
                    <label class="text-xs text-gray-500 uppercase">Pemilik Sah</label>
                    <p class="text-lg font-bold">{{ $ownership->customer->name }}</p>
                    <p class="text-sm text-gray-600">{{ $ownership->customer->phone }}</p>
                </div>

                <div class="mt-6 pt-4 border-t border-purple-200">
                    <label class="text-xs text-gray-500 uppercase">Status Penghuni</label>
                    <br>
                    <span class="px-3 py-1 bg-white text-purple-700 rounded-full font-bold text-sm shadow-sm">
                        {{ $ownership->status }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Detail Transaksi</h3>

                <ul class="space-y-4">
                    <li class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Metode Bayar</span>
                        <span class="font-bold">{{ $ownership->purchase_method }}</span>
                    </li>
                    <li class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Bank (KPR)</span>
                        <span class="font-bold">{{ $ownership->bank_name ?? '-' }}</span>
                    </li>
                    <li class="flex justify-between border-b pb-2">
                        <span class="text-gray-600">Tgl Serah Terima</span>
                        <span class="font-bold">{{ $ownership->handover_date->format('d/m/Y') }}</span>
                    </li>
                </ul>

                @php
                    $isExpired = \Carbon\Carbon::now()->greaterThan($ownership->warranty_end_date);
                @endphp
                <div
                    class="mt-6 p-4 rounded-lg border-2 {{ $isExpired ? 'border-red-200 bg-red-50' : 'border-green-200 bg-green-50' }}">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-bold text-gray-600">Status Garansi</span>
                        @if (!$isExpired)
                            <span class="bg-green-600 text-white text-xs px-2 py-1 rounded font-bold">AKTIF</span>
                        @else
                            <span class="bg-red-600 text-white text-xs px-2 py-1 rounded font-bold">EXPIRED</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500">Berakhir pada:</p>
                    <p class="text-lg font-bold {{ $isExpired ? 'text-red-700' : 'text-green-700' }}">
                        {{ $ownership->warranty_end_date->locale('id')->translatedFormat('d F Y') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-4 text-right">
            <a href="{{ route('admin.ownerships.index') }}"
                class="text-gray-600 hover:text-gray-900 font-semibold mr-4">Kembali</a>
            <a href="{{ route('admin.ownerships.edit', $ownership->id) }}"
                class="bg-indigo-600 text-white px-6 py-2 rounded shadow hover:bg-indigo-700">Edit Data</a>
        </div>
    </div>
</x-app-layout>
