<!DOCTYPE html>
<html>

<head>
    <title>Laporan Status Garansi</title>
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
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }

        .expired {
            color: red;
            font-weight: bold;
        }

        .active {
            color: green;
            font-weight: bold;
        }
    </style>
</head>

<body>
    @include('admin.reports.pdf._header')

    <h3 class="text-center">LAPORAN STATUS GARANSI UNIT RUMAH</h3>
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
                <th>Unit Blok & No</th>
                <th>Nama Nasabah</th>
                <th>Tgl Serah Terima</th>
                <th>Batas Akhir Garansi</th>
                <th>Status Saat Ini</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ownerships as $index => $own)
                @php
                    $isExpired = \Carbon\Carbon::now()->greaterThan($own->warranty_end_date);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>Blok {{ $own->unit->block }} - No {{ $own->unit->number }}</td>
                    <td>{{ $own->customer->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($own->handover_date)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($own->warranty_end_date)->format('d M Y') }}</td>
                    <td class="text-center">
                        @if ($isExpired)
                            <span class="expired">HABIS</span>
                        @else
                            <span class="active">AKTIF</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
