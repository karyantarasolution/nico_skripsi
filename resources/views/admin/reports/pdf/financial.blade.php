<!DOCTYPE html>
<html>

<head>
    <title>Laporan Pendapatan Perbaikan</title>
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

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary {
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    @include('admin.reports.pdf._header')

    <h3 class="text-center">LAPORAN PENDAPATAN PERBAIKAN LUAR GARANSI</h3>
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
                <th>Tgl Selesai</th>
                <th>Unit</th>
                <th>Nama Pelanggan</th>
                <th>Keluhan / Pekerjaan</th>
                <th>Status Bayar</th>
                <th class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $index => $order)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ optional($order->completion_date)->format('d/m/Y') ?? '-' }}</td>
                    <td>Blok {{ $order->ownership->unit->block }}-{{ $order->ownership->unit->number }}</td>
                    <td>{{ $order->ownership->customer->name }}</td>
                    <td>{{ $order->complaint_title }}</td>
                    <td class="text-center">{{ $order->payment_status == 'Paid' ? 'LUNAS' : 'BELUM BAYAR' }}</td>
                    <td class="text-right">{{ number_format($order->cost, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary" style="width: 50%; float: right; margin-top: 20px;">
        <tr>
            <th>Total Tagihan Lunas (Pendapatan)</th>
            <td class="text-right font-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Piutang (Belum Dibayar)</th>
            <td class="text-right text-red-600">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</td>
        </tr>
    </table>
</body>

</html>
