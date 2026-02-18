<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .title {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #e5e5e5;
        }
    </style>
</head>

<body>

    <h2 class="title">REKAP ABSENSI PEGAWAI</h2>
    <h4 class="title">
        Bulan: {{ DateTime::createFromFormat('!m', $bulan)->format('F') }} {{ $tahun }}
    </h4>
    <h5 class="title">Lokasi Kantor: {{ $lokasi->nama_lokasi }}</h5>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Pegawai</th>
                <th>Tanggal</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Status</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($absensi as $i => $a)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $a->nama_pegawai }}</td>
                    <td>{{ $a->tanggal }}</td>
                    <td>{{ $a->jam_masuk ?? '-' }}</td>
                    <td>{{ $a->jam_pulang ?? '-' }}</td>
                    <td>{{ $a->status }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
