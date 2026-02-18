@extends('layout.main')

@section('title', 'Izin Pegawai')

@section('main')
    <div class="container">

        <h4 class="mb-3">Input Izin / Sakit</h4>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('izin.store') }}" class="row mb-4">
            @csrf

            <div class="col-md-3 mb-2">
                <select name="id_pegawai" class="form-control" required>
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach ($pegawai as $p)
                        <option value="{{ $p->id_pegawai }}">{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 mb-2">
                <input type="date" name="tanggal" class="form-control" required>
            </div>

            <div class="col-md-2 mb-2">
                <select name="jenis" class="form-control" required>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                </select>
            </div>

            <div class="col-md-3 mb-2">
                <input type="text" name="keterangan" class="form-control" placeholder="Keterangan">
            </div>

            <div class="col-md-2 mb-2">
                <button class="btn btn-primary w-100">Simpan</button>
            </div>
        </form>

        <hr>

        <h5>Data Pengajuan Izin Pegawai</h5>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Tanggal</th>
                        <th>Jenis Izin</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($izin as $i)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $i->nama }}</td>
                            <td>{{ $i->tanggal }}</td>
                            <td>{{ $i->jenis }}</td>
                            <td>{{ $i->keterangan }}</td>
                            <td>
                                <span
                                    class="badge
                                {{ $i->status_izin == 'disetujui' ? 'bg-success' : ($i->status_izin == 'ditolak' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ ucfirst($i->status_izin) }}
                                </span>
                            </td>
                            <td>
                                @if ($i->status_izin == 'pending')
                                    <form action="{{ route('izin.validasi', $i->id_izin) }}" method="POST"
                                        class="d-flex gap-1">
                                        @csrf
                                        <button name="status_izin" value="disetujui"
                                            class="btn btn-success btn-sm">Setujui</button>
                                        <button name="status_izin" value="ditolak"
                                            class="btn btn-danger btn-sm">Tolak</button>
                                    </form>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada data pengajuan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection
