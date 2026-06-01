<!DOCTYPE html>
<html>

<head>
    <title>Laporan Rekapitulasi Keluhan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .status-done {
            color: green;
            font-weight: bold;
        }

        .status-pending {
            color: red;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: right;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>

<body>

    @include('admin.reports.pdf._header')

    <div style="text-align: center; margin-bottom: 20px;">
        <h3 style="margin: 0;">LAPORAN REKAPITULASI KELUHAN WARGA</h3>
        <p style="margin: 5px 0;">
            Periode: Semua Data s/d {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 20%">Lokasi Unit</th>
                <th style="width: 25%">Keluhan</th>
                <th style="width: 15%">Teknisi</th>
                <th style="width: 10%">Status</th>
                <th style="width: 10%">Rating</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $index => $order)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $order->complaint_date->format('d/m/Y') }}</td>
                    <td>
                        Blok {{ $order->ownership->unit->block }}-{{ $order->ownership->unit->number }}<br>
                        <small>({{ $order->ownership->customer->name }})</small>
                    </td>
                    <td>
                        <b>{{ $order->complaint_title }}</b><br>
                        <small>{{ Str::limit($order->complaint_description, 50) }}</small>
                    </td>
                    <td>{{ $order->technician->name ?? '-' }}</td>
                    <td>
                        @if ($order->status == 'done')
                            <span class="status-done">Selesai</span>
                        @elseif($order->status == 'pending')
                            <span class="status-pending">Baru</span>
                        @else
                            {{ $order->status }}
                        @endif
                    </td>
                    <td>{{ $order->rating ? $order->rating . ' / 5' : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh Admin pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y H:i') }}
    </div>

</body>

</html>
