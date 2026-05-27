<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kinerja Respon (SLA)</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .fast { color: green; font-weight: bold; }
        .slow { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; color: #777; }
        .urgent { background-color: #fecaca; }
        .medium { background-color: #fef08a; }
        .low { background-color: #bbf7d0; }
    </style>
</head>
<body>
    @include('admin.reports.pdf._header')

    <center>
        <h3 style="margin-top: 0;">LAPORAN KINERJA RESPON (SLA)</h3>
        <p style="margin-top: -10px; margin-bottom: 20px;">
            Periode: Semua Data s/d {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
        </p>
    </center>

    @php
        $slaStandards = ['urgent' => '1x24 Jam', 'medium' => '3 Hari', 'low' => '7 Hari'];
    @endphp

    <table style="width: auto; margin-bottom: 15px;">
        <tr>
            <th>Prioritas</th>
            <th>Standar SLA</th>
        </tr>
        @foreach ($slaStandards as $prio => $std)
        <tr>
            <td>{{ ucfirst($prio) }}</td>
            <td>{{ $std }}</td>
        </tr>
        @endforeach
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 10%">Tgl Lapor</th>
                <th style="width: 10%">Tgl Selesai</th>
                <th style="width: 25%">Keluhan</th>
                <th style="width: 8%">Prioritas</th>
                <th style="width: 12%">Teknisi</th>
                <th style="width: 10%">Durasi</th>
                <th style="width: 10%">Status SLA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $index => $order)
                @php
                    $days = $order->complaint_date->diffInDays($order->completion_date);
                    $hours = $order->complaint_date->diffInHours($order->completion_date);
                    $duration = $hours < 24 ? $hours . ' Jam' : $days . ' Hari';

                    $slaHours = $order->priority === 'urgent' ? 24 : ($order->priority === 'medium' ? 72 : 168);
                    $slaStatus = $hours <= $slaHours ? 'TEPAT' : 'TERLAMBAT';
                    $slaClass = $hours <= $slaHours ? 'fast' : 'slow';

                    $priorityClass = $order->priority === 'urgent' ? 'urgent' : ($order->priority === 'medium' ? 'medium' : 'low');
                @endphp
                <tr class="{{ $priorityClass }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $order->complaint_date->format('d/m/Y') }}</td>
                    <td>{{ $order->completion_date->format('d/m/Y') }}</td>
                    <td>{{ $order->complaint_title }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ ucfirst($order->priority ?? 'medium') }}</td>
                    <td>{{ $order->technician->name ?? '-' }}</td>
                    <td style="text-align: center;">{{ $duration }}</td>
                    <td style="text-align: center;">
                        <span class="{{ $slaClass }}">{{ $slaStatus }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size: 10px; margin-top: 10px;">
        *Standar SLA: Urgent = 1x24 Jam, Medium = 3 Hari, Low = 7 Hari
    </p>
    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>
