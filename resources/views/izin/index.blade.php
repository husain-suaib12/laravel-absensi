@extends('layout.main')

@section('title', 'Data Izin Pegawai')

@section('main')
    <x-running-text text="📝 Validasi Izin Pegawai — Setujui atau tolak pengajuan izin" color="rt-warning" />

    <div class="container-fluid px-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Data Pengajuan Izin Pegawai</h4>
                <small class="text-muted">
                    Validasi dan pengelolaan izin pegawai
                </small>
            </div>
        </div>

        {{-- FLASH MESSAGE --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-x-circle-fill me-1"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- TABLE --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold">
                Daftar Pengajuan Izin
            </div>

            <div class="card-body p-0">

                @if ($izin->count() == 0)
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-envelope-x fs-1 d-block mb-2"></i>
                        Belum ada data pengajuan izin
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Nama Pegawai</th>
                                    <th>Tanggal</th>
                                    <th>Jenis Izin</th>
                                    <th>Keterangan</th>
                                    <th>Foto</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($izin as $i)
                                    <tr>
                                        <td class="ps-4">{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $i->nama_pegawai }}</td>
                                        <td>{{ \Carbon\Carbon::parse($i->tanggal)->format('d M Y') }}</td>
                                        <td>{{ $i->jenis }}</td>
                                        <td>
                                            {{ $i->keterangan ?? '-' }}
                                            @if ($i->status_izin == 'ditolak' && $i->alasan_tolak)
                                                <br><small class="text-danger fw-bold">Alasan Tolak:
                                                    {{ $i->alasan_tolak }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($i->foto)
                                                <a href="{{ asset('storage/izins/' . $i->foto) }}" target="_blank">
                                                    <img src="{{ asset('storage/izins/' . $i->foto) }}" alt="Foto Izin"
                                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($i->status_izin == 'disetujui')
                                                <span class="badge rounded-pill bg-success px-3">Disetujui</span>
                                            @elseif ($i->status_izin == 'ditolak')
                                                <span class="badge rounded-pill bg-danger px-3">Ditolak</span>
                                            @else
                                                <span class="badge rounded-pill bg-warning text-dark px-3">Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($i->status_izin == 'pending')
                                                {{-- Tombol Setujui --}}
                                                <form action="{{ route('izin.validasi', $i->id_izin) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status_izin" value="disetujui">
                                                    <button type="submit" class="btn btn-success btn-sm" title="Setujui">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>

                                                {{-- Tombol Tolak (Pemicu Modal) --}}
                                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#modalTolak{{ $i->id_izin }}" title="Tolak">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>

                                                {{-- Modal Alasan Penolakan --}}
                                                <div class="modal fade" id="modalTolak{{ $i->id_izin }}" tabindex="-1"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form action="{{ route('izin.validasi', $i->id_izin) }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status_izin" value="ditolak">
                                                            <div class="modal-content text-start">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Alasan Penolakan</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Berikan alasan penolakan untuk
                                                                        <strong>{{ $i->nama_pegawai }}</strong>:
                                                                    </p>
                                                                    <textarea name="alasan_tolak" class="form-control" rows="3" required placeholder="Tulis alasan di sini..."></textarea>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-danger">Tolak
                                                                        Pengajuan</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted small">Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
        }
    </style>
@endsection
