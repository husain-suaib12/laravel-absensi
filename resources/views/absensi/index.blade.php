@extends('layout.main')

@section('title', 'Data Absensi')

@section('main')
    <div class="container-fluid px-4">
        <x-running-text text="📊 Data Absensi Pegawai — Pantau kehadiran pegawai secara akurat" color="rt-success" />

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Data Absensi Pegawai</h4>
                <small class="text-muted">
                    Rekap absensi pegawai berdasarkan periode
                </small>
            </div>

            <a href="{{ route('absensi.cetak', ['bulan' => $bulan, 'tahun' => $tahun, 'q' => $q]) }}" target="_blank"
                class="btn btn-danger shadow-sm">
                <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
            </a>
        </div>

        {{-- FILTER --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form class="row g-3" method="GET">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Bulan</label>
                        <select name="bulan" class="form-select">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ sprintf('%02d', $i) }}"
                                    {{ $bulan == sprintf('%02d', $i) ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tahun</label>
                        <select name="tahun" class="form-select">
                            @for ($y = 2022; $y <= date('Y'); $y++)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Cari Pegawai</label>
                        <input type="text" name="q" value="{{ $q }}" class="form-control"
                            placeholder="Masukkan nama pegawai">
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- DATA --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <div class="fw-bold">
                    Rekap Absensi
                    {{ DateTime::createFromFormat('!m', $bulan)->format('F') }}
                    {{ $tahun }}
                </div>
                @if ($lokasi)
                    <small class="text-muted">
                        Lokasi Kantor: <strong>{{ $lokasi->nama_lokasi }}</strong>
                    </small>
                @endif
            </div>

            <div class="card-body p-0">

                @if ($absensi->count() == 0)
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-calendar-x fs-1 mb-3 d-block"></i>
                        Belum ada data absensi untuk periode ini
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Pegawai</th>
                                    <th>Tanggal</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($absensi as $i => $a)
                                    <tr>
                                        <td class="ps-4">
                                            {{ $absensi->firstItem() + $i }}
                                        </td>
                                        <td class="fw-semibold">
                                            {{ $a->nama_pegawai }}
                                        </td>
                                        <td>{{ $a->tanggal }}</td>
                                        <td>{{ $a->jam_masuk ?? '-' }}</td>
                                        <td>{{ $a->jam_pulang ?? '-' }}</td>
                                        <td>
                                            @if ($a->status === 'hadir')
                                                <span class="badge rounded-pill bg-success px-3">Hadir</span>
                                            @elseif ($a->status === 'izin')
                                                <span class="badge rounded-pill bg-warning text-dark px-3">Izin</span>
                                            @elseif ($a->status === 'sakit')
                                                <span class="badge rounded-pill bg-info px-3">Sakit</span>
                                            @elseif ($a->status === 'alpa')
                                                <span class="badge rounded-pill bg-danger px-3">Alpa</span>
                                            @elseif ($a->status === 'dinas_luar')
                                                <span class="badge rounded-pill bg-primary px-3">
                                                    <i class="bi bi-briefcase-fill me-1"></i> Dinas Luar
                                                </span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary px-3">-</span>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3">
                        {{ $absensi->links() }}
                    </div>
                @endif

            </div>
        </div>

    </div>

    {{-- STYLE HALUS --}}
    <style>
        .table-hover tbody tr:hover {
            background-color: #838f9b;
        }
    </style>
@endsection
