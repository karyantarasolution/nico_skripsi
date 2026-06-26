<!DOCTYPE html>
<html>

<head>
    <title>Analisis Kategori Kerusakan</title>
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

        .bar-container {
            width: 100%;
            background-color: #e0e0e0;
            height: 10px;
            border-radius: 5px;
        }

        .bar {
            height: 10px;
            background-color: #4CAF50;
            border-radius: 5px;
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

    <center>
        <h3 style="margin-top: 0;">LAPORAN ANALISIS KATEGORI KERUSAKAN</h3>
        <p style="margin-top: -10px; margin-bottom: 20px;">
            Periode:
            @if ($month && $year)
                {{ \Carbon\Carbon::create((int)$year, (int)$month)->locale('id')->translatedFormat('F Y') }}
            @elseif ($month)
                {{ \Carbon\Carbon::create(now()->year, (int)$month)->locale('id')->translatedFormat('F') }}
            @elseif ($year)
                Tahun {{ $year }}
            @else
                Semua Data s/d {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}
            @endif
        </p>
    </center>

    <table>
        <thead>
            <tr>
                <th style="width: 30%">Jenis Kerusakan (Kategori)</th>
                <th style="width: 15%; text-align: center;">Jumlah Kasus</th>
                <th style="width: 15%; text-align: center;">Persentase</th>
                <th style="width: 40%">Grafik</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stats as $stat)
                @php
                    $percentage = $totalCases > 0 ? round(($stat->total / $totalCases) * 100, 1) : 0;
                @endphp
                <tr>
                    <td><b>{{ $stat->specialty }}</b></td>
                    <td style="text-align: center;">{{ $stat->total }}</td>
                    <td style="text-align: center;">{{ $percentage }}%</td>
                    <td>
                        <div class="bar-container">
                            <div class="bar" style="width: {{ $percentage }}%;"></div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <p><strong>Kesimpulan:</strong></p>
        <p>Data di atas menunjukkan distribusi kerusakan berdasarkan spesialisasi teknisi yang ditugaskan. Kategori
            dengan persentase tertinggi memerlukan evaluasi kualitas material atau pengerjaan awal.</p>
    </div>

    <div class="footer">
        Dicetak oleh Admin pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y H:i') }}
    </div>
</body>

</html>
