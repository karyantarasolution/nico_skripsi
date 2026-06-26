<!DOCTYPE html>
<html>

<head>
    <title>Laporan Indeks Kepuasan Pelanggan</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        /* PENTING: Gunakan DejaVu Sans agar simbol terbaca */
        body {
            font-family: 'DejaVu Sans', sans-serif;
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

        /* Styling Bintang */
        .star {
            color: #ffc107;
            /* Warna Kuning Emas */
            font-size: 18px;
            line-height: 1;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: right;
            font-size: 10px;
            color: #777;
        }

        .summary-box {
            border: 1px solid #333;
            padding: 10px;
            width: 200px;
            margin-bottom: 15px;
            background: #ffffcc;
        }
    </style>
</head>

<body>

    @include('admin.reports.pdf._header')

    <div class="summary-box">
        <b>Rata-rata Rating:</b><br>
        <span style="font-size: 20px; font-weight: bold;">{{ number_format($averageRating, 1) }} / 5.0</span>
    </div>

    <center>
        <h3 style="margin-top: 0;">LAPORAN INDEKS KEPUASAN PELANGGAN</h3>
        <p style="margin-top: -10px; margin-bottom: 20px;">
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
    </center>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 20%">Nama Warga</th>
                <th style="width: 10%">Unit</th>
                <th style="width: 20%">Masalah</th>
                <th style="width: 20%">Rating</th>
                <th style="width: 30%">Testimoni</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reviews as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->ownership->customer->name }}</td>
                    <td>{{ $row->ownership->unit->block }}-{{ $row->ownership->unit->number }}</td>
                    <td>{{ $row->complaint_title }}</td>
                    <td>
                        {{-- PENTING: Gunakan kode HTML Entity &#9733; untuk bintang --}}
                        @for ($i = 0; $i < $row->rating; $i++)
                            <span class="star">&#9733;</span>
                        @endfor
                        <span style="font-size: 12px; color: #555;">({{ $row->rating }})</span>
                    </td>
                    <td><i>"{{ $row->review ?? '-' }}"</i></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh Admin pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y H:i') }}
    </div>

</body>

</html>
