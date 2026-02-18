<!DOCTYPE html>
<html>

<head>
    <title>Slip Gaji {{ $pegawai->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        h3,
        h4 {
            margin: 0;
            padding: 0;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>

    <h3>Slip Gaji Pegawai</h3>
    <h4>{{ $pegawai->nama }} ({{ $pegawai->nik ?? '-' }})</h4>
    <p>Periode: {{ $bulan }}/{{ $tahun }}</p>

    <table>
        <tr>
            <th>Gaji Pokok</th>
            <td class="text-right">Rp {{ number_format($rekap->gaji_pokok ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Hadir</th>
            <td class="text-right">{{ $rekap->jumlah_hadir ?? 0 }} hari</td>
        </tr>
        <tr>
            <th>Terlambat</th>
            <td class="text-right">{{ $rekap->jumlah_terlambat ?? 0 }} menit</td>
        </tr>
        <tr>
            <th>Tanpa Keterangan</th>
            <td class="text-right">{{ $rekap->jumlah_tanpa_keterangan ?? 0 }} hari</td>
        </tr>
        <tr>
            <th>Total Potongan</th>
            <td class="text-right text-danger">Rp {{ number_format($rekap->total_potongan ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Gaji Bersih</th>
            <td class="text-right text-success">Rp {{ number_format($rekap->gaji_bersih ?? 0, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if ($izin->count())
        <h4>Data Izin</h4>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($izin as $i)
                    <tr>
                        <td>{{ $i->tanggal }}</td>
                        <td>{{ $i->jenis }}</td>
                        <td>{{ ucfirst($i->status_izin) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>

</html>
