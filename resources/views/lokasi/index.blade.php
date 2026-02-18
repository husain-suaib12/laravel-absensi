@extends('layout.main')

@section('title', 'Master Lokasi Absen')

@section('main')

    <x-running-text text="📌 Master Lokasi Absen — Kelola lokasi absensi kantor" color="rt-master" />

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Master Lokasi Absen</h1>
            </div>

            <a href="{{ route('lokasi.create') }}" class="btn btn-primary mb-3">+ Tambah Lokasi</a>

            <div class="card">
                <div class="card-header">
                    <h4>Daftar Lokasi</h4>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Lokasi</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Radius</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lokasi as $i => $x)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $x->nama_lokasi }}</td>
                                    <td>{{ $x->latitude }}</td>
                                    <td>{{ $x->longitude }}</td>
                                    <td>{{ $x->radius_master }} m</td>
                                    <td>
                                        <a href="{{ route('lokasi.edit', $x->id_lokasi) }}"
                                            class="btn btn-warning btn-sm">Edit</a>

                                        <form action="{{ route('lokasi.destroy', $x->id_lokasi) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf @method('DELETE')
                                            <button onclick="return confirm('Yakin hapus?')"
                                                class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </section>
    </div>
@endsection
