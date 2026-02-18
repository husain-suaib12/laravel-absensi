@extends('layout.main')

@section('title', 'Rekap Gaji Bulanan')

@section('main')
    <div class="page-content">

        <h3>Rekap Gaji Bulanan</h3>

        {{-- FILTER BULAN --}}
        <form method="GET" class="row mb-3">
            <div class="col-md-3">
                <input type="month" name="bulan" class="form-control" value="{{ $tahun }}-{{ $bulan }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </form>

        {{-- GENERATE GAJI --}}
        <form action="{{ route('gaji.generate') }}" method="POST" class="mb-3">
            @csrf
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <button type="submit" class="btn btn-success" onclick="return confirm('Hitung ulang gaji bulan ini?')">
                Generate Gaji Bulanan
            </button>
        </form>

        {{-- REKAP TABEL --}}
        <div class="card">
            <div class="card-body table-responsive">

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pegawai</th>
                            <th>Hadir</th>
                            <th>Terlambat</th>
                            <th>Tanpa Keterangan</th>
                            <th>Gaji Pokok</th>
                            <th>Total Potongan</th>
                            <th>Gaji Bersih</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rekap as $r)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $r->nama }} <br>
                                    <small class="text-muted">{{ $r->nik }}</small>
                                </td>
                                <td>{{ $r->jumlah_hadir }}</td>
                                <td>{{ $r->jumlah_terlambat }}</td>
                                <td>{{ $r->jumlah_tanpa_keterangan }}</td>
                                <td>Rp {{ number_format($r->gaji_pokok, 0, ',', '.') }}</td>
                                <td class="text-danger">
                                    Rp {{ number_format($r->total_potongan, 0, ',', '.') }}
                                </td>
                                <td class="fw-bold text-success">
                                    Rp {{ number_format($r->gaji_bersih, 0, ',', '.') }}
                                </td>
                                <td>
                                    <a href="{{ route('gaji.detail', $r->id_pegawai) }}?bulan={{ $bulan }}&tahun={{ $tahun }}"
                                        class="btn btn-sm btn-primary">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Belum ada data rekap
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>

    </div>
@endsection

