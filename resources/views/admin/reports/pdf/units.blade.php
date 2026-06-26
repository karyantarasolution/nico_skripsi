<!DOCTYPE html>
<html>

<head>
    <title>Laporan Data & Status Unit</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    @include('admin.reports.pdf._header')

    <h3 class="text-center">LAPORAN DATA & STATUS UNIT RUMAH</h3>
    <p class="text-center">
        Periode:
        @if ($month && $year)
            {{ \Carbon\Carbon::create((int)$year, (int)$month)->locale('id')->translatedFormat('F Y') }}
        @elseif ($month)
            {{ \Carbon\Carbon::create(now()->year, (int)$month)->locale('id')->translatedFormat('F') }}
        @elseif ($year)
            Tahun {{ $year }}
        @else
            Semua Data
        @endif
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Blok - No</th>
                <th>Tipe</th>
                <th>LB/LT</th>
                <th>Harga (Rp)</th>
                <th>Status Unit</th>
                <th>Nama Pembeli</th>
                <th>Metode Bayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($units as $index => $unit)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-bold">{{ $unit->block }}-{{ $unit->number }}</td>
                    <td class="text-center">Tipe {{ $unit->type }}</td>
                    <td class="text-center">{{ $unit->building_size }} / {{ $unit->land_size }}</td>
                    <td>{{ number_format($unit->price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $unit->status }}</td>
                    <td>{{ $unit->customer_name ?? '-' }}</td>
                    <td class="text-center">{{ $unit->purchase_method ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
