@extends('layout.main')

@section('main')
    <div class="container">

        <h4 class="mb-3">Detail Gaji Pegawai</h4>

        {{-- DATA PEGAWAI --}}
        <div class="card mb-3">
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th width="200">Nama</th>
                        <td>{{ $pegawai->nama }}</td>
                    </tr>
                    <tr>
                        <th>NIK</th>
                        <td>{{ $pegawai->nik }}</td>
                    </tr>
                    <tr>
                        <th>Gaji Pokok</th>
                        <td>Rp {{ number_format($pegawai->gaji_pokok, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Bulan</th>
                        <td>{{ $bulan }} / {{ $tahun }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- REKAP GAJI --}}
        <div class="card mb-3">
            <div class="card-header bg-light">
                <strong>Rekap Potongan</strong>
            </div>
            <div class="card-body">
                @if ($rekap)
                    <table class="table table-bordered">
                        <tr>
                            <th>Jumlah Hari Potong</th>
                            <td>{{ $rekap->jumlah_tanpa_keterangan }} hari</td>
                        </tr>
                        <tr>
                            <th>Total Potongan</th>
                            <td class="text-danger fw-semibold">
                                Rp {{ number_format($rekap->total_potongan, 0, ',', '.') }}
                                ({{ round(($rekap->total_potongan / $pegawai->gaji_pokok) * 100, 2) }}%)
                            </td>
                        </tr>
                        <tr>
                            <th>Gaji Bersih</th>
                            <td class="text-success">
                                Rp {{ number_format($rekap->gaji_bersih, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                @else
                    <div class="alert alert-warning">
                        Gaji bulan ini belum digenerate
                    </div>
                @endif
            </div>
        </div>

        {{-- RIWAYAT ABSENSI & IZIN --}}
        <div class="card">
            <div class="card-header bg-light">
                <strong>Riwayat Kehadiran & Izin</strong>
            </div>
            <div class="card-body">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Potong Gaji</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($absensi as $a)
                            <tr class="{{ $a->id_jenis == 1 ? 'table-danger' : '' }}">
                                <td>{{ $a->tanggal }}</td>
                                <td>
                                    {{-- Warna badge tetap bisa dibedakan berdasarkan id_jenis --}}
                                    <span
                                        class="badge {{ $a->id_jenis == 1 ? 'bg-danger' : ($a->id_jenis == 4 ? 'bg-success' : 'bg-warning text-dark') }}">
                                        {{ $a->nama_potongan }} {{-- Membaca langsung dari tabel jenis_potongan --}}
                                    </span>
                                </td>
                                <td>{{ $a->keterangan ?? '-' }}</td>
                                <td class="{{ $a->id_jenis == 1 ? 'text-danger fw-bold' : 'text-success' }}">
                                    {{ $a->id_jenis == 1 ? 'Ya' : 'Tidak' }}
                                </td>
                            </tr>
                        @endforeach

                        {{-- IZIN DITOLAK --}}
                        @php
                            $izinDitolak = DB::table('izin_pegawai')
                                ->where('id_pegawai', $pegawai->id_pegawai)
                                ->where('status_izin', 'ditolak')
                                ->whereMonth('tanggal', $bulan)
                                ->whereYear('tanggal', $tahun)
                                ->get();
                        @endphp

                        @foreach ($izinDitolak as $i)
                            <tr class="table-danger">
                                <td>{{ $i->tanggal }}</td>
                                <td>Izin Ditolak</td>
                                <td>{{ $i->jenis }}</td>
                                <td class="text-danger">Ya</td>
                            </tr>
                        @endforeach

                        {{-- IZIN DISETUJUI --}}
                        @php
                            $izinDisetujui = DB::table('izin_pegawai')
                                ->where('id_pegawai', $pegawai->id_pegawai)
                                ->where('status_izin', 'disetujui')
                                ->whereMonth('tanggal', $bulan)
                                ->whereYear('tanggal', $tahun)
                                ->get();
                        @endphp

                        @foreach ($izinDisetujui as $i)
                            <tr class="table-success">
                                <td>{{ $i->tanggal }}</td>
                                <td>Izin Disetujui</td>
                                <td>{{ $i->jenis }}</td>
                                <td class="text-success">Tidak</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>

    </div>
@endsection
