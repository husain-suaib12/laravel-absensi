@extends('layout.main')

@section('main')
    <x-running-text text="👋 Selamat Datang di Sistem Absensi Pegawai Desa — Kelola data secara terintegrasi"
        color="rt-primary" />

    <div class="container-fluid px-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Dashboard</h4>
                <small class="text-muted">
                    Sistem Informasi Absensi Pegawai Desa
                </small>
            </div>
            <span class="badge bg-light text-dark px-3 py-2">
                {{ now()->format('d M Y') }}
            </span>
        </div>

        {{-- CARD STATISTIK --}}
        <div class="row g-4 mb-4">

            {{-- HADIR HARI INI (Sekarang di Posisi Pertama) --}}
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100 card-hover">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Hadir Hari Ini</small>
                            <h3 class="fw-bold mb-0">{{ $hadir }}</h3>
                        </div>
                        <div class="icon bg-success text-white rounded-circle p-3">
                            <i class="bi bi-check-circle-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DINAS LUAR (Sekarang di Posisi Kedua) --}}
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100 card-hover">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Dinas Luar</small>
                            <h3 class="fw-bold mb-0">{{ $dinas }}</h3>
                        </div>
                        <div class="icon bg-primary text-white rounded-circle p-3">
                            <i class="bi bi-briefcase-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ALPA --}}
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100 card-hover">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Alpa</small>
                            <h3 class="fw-bold mb-0">{{ $alpa }}</h3>
                        </div>
                        <div class="icon bg-danger text-white rounded-circle p-3">
                            <i class="bi bi-x-circle-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- IZIN / SAKIT --}}
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100 card-hover">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Izin / Sakit</small>
                            <h3 class="fw-bold mb-0">{{ $izin }}</h3>
                        </div>
                        <div class="icon bg-warning text-white rounded-circle p-3">
                            <i class="bi bi-exclamation-circle-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOTAL PEGAWAI (Full Width di bawah atau tetap col-md-3 sesuai desain Anda) --}}
            <div class="col-md-12">
                <div class="card shadow-sm border-0 card-hover">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total Pegawai Terdaftar</small>
                            <h3 class="fw-bold mb-0">{{ $totalPegawai }}</h3>
                        </div>
                        <div class="icon bg-secondary text-white rounded-circle p-3">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL ABSENSI TERBARU --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold">
                Absensi Terbaru
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nama Pegawai</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($absensiTerbaru as $a)
                            <tr>
                                <td class="ps-4">{{ $a->nama }}</td>
                                <td>{{ \Carbon\Carbon::parse($a->tanggal)->format('d M Y') }}</td>
                                <td>
                                    @if ($a->id_jenis == 4)
                                        <span class="badge rounded-pill bg-success px-3">Hadir</span>
                                    @elseif ($a->id_jenis == 1)
                                        <span class="badge rounded-pill bg-danger px-3">Alpa</span>
                                    @elseif ($a->id_jenis == 2)
                                        <span class="badge rounded-pill bg-warning text-dark px-3">Sakit</span>
                                    @elseif ($a->id_jenis == 3)
                                        <span class="badge rounded-pill bg-info px-3">Izin</span>
                                    @elseif ($a->id_jenis == 5)
                                        <span class="badge rounded-pill bg-primary px-3">Dinas Luar</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    Belum ada data absensi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- STYLE KHUSUS DASHBOARD --}}
    <style>
        .card-hover {
            transition: all .2s ease-in-out;
        }

        .card-hover:hover {
            transform: translateY(-4px);
        }
    </style>
@endsection
