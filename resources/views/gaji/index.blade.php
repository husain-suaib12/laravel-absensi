@extends('layout.main')

@section('main')
    <x-running-text text="💰 Rekap Gaji Bulanan — Perhitungan gaji otomatis & transparan" color="rt-purple" />

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Rekap Gaji Bulanan</h4>
            <small class="text-muted">
                Perhitungan gaji pegawai berdasarkan absensi
            </small>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ sprintf('%02d', $i) }}" {{ $bulan == sprintf('%02d', $i) ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-5">
                    <button class="btn btn-primary me-1">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>

                    <button type="button" class="btn btn-success" id="btnGenerate">
                        <i class="bi bi-calculator"></i> Generate Gaji
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ALERT --}}
    <div id="alertContainer"></div>

    {{-- TABEL REKAP --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold">
            Daftar Rekap Gaji
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Nama Pegawai</th>
                            <th>NIK</th>
                            <th>Gaji Pokok</th>
                            <th>Hari Dipotong</th>
                            <th>Total Potongan</th>
                            <th>Gaji Bersih</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekap as $r)
                            <tr class="{{ $r->jumlah_tanpa_keterangan > 3 ? 'table-danger' : '' }}">
                                <td class="ps-4">{{ $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $r->nama }}</td>
                                <td>{{ $r->nik }}</td>
                                <td>Rp {{ number_format($r->gaji_pokok, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $r->jumlah_tanpa_keterangan }}</td>
                                <td class="text-danger fw-semibold">
                                    Rp {{ number_format($r->total_potongan, 0, ',', '.') }}
                                </td>
                                <td class="text-success fw-bold">
                                    Rp {{ number_format($r->gaji_bersih, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if ($r->is_locked)
                                        <span class="badge rounded-pill bg-success px-3">Locked</span>
                                    @else
                                        <span class="badge rounded-pill bg-warning text-dark px-3">Draft</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('gaji.detail', $r->id_pegawai) }}?bulan={{ $bulan }}&tahun={{ $tahun }}"
                                        class="btn btn-info btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="bi bi-folder-x fs-3 d-block mb-2"></i>
                                    Data gaji belum digenerate
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SCRIPT AJAX GENERATE --}}
    <script>
        document.getElementById('btnGenerate').addEventListener('click', function() {
            if (!confirm('Generate gaji bulan ini?')) return;

            const bulan = document.querySelector('select[name="bulan"]').value;
            const tahun = document.querySelector('select[name="tahun"]').value;

            fetch(`/gaji/generate?bulan=${bulan}&tahun=${tahun}`)
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Server Error');
                    return data;
                })
                .then(data => {
                    alert(data.message);
                    location.reload();
                })
                .catch(err => {
                    // INI PENTING: Menampilkan detail error yang sebenarnya dari Laravel
                    alert("DETAIL ERROR: " + err.message);
                    console.error(err);
                });
        });
    </script>

    <style>
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
@endsection
