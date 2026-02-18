@extends('layout.main')

@section('main')
    <x-running-text text="📝 Master Jenis Potongan — Atur jenis Potongan pegawai yang tersedia" color="rt-master" />

    <h3>Jenis Potongan</h3>

    <a href="{{ route('jenis-potongan.create') }}" class="btn btn-primary mb-3">
        + Tambah Jenis Potongan
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Potongan</th>
                <th>Nilai</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row->nama_potongan }}</td>
                    <td>{{ $row->nilai }}</td>
                    <td>
                        <a href="{{ route('jenis-potongan.edit', $row->id_jenis) }}" class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('jenis-potongan.destroy', $row->id_jenis) }}" method="POST"
                            style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus data?')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
