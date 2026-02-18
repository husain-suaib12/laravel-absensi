@extends('layout.main')
@section('main')
    <x-running-text text="📌 Master Jabatan — Kelola data jabatan sebagai dasar sistem" color="rt-master" />

    <h3>Master Jabatan</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ route('jabatan.create') }}" class="btn btn-primary mb-2">Tambah Jabatan</a>

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Nama Jabatan</th>
            <th>Aksi</th>
        </tr>
        @foreach ($data as $i => $d)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $d->nama_jabatan }}</td>
                <td>
                    <a href="{{ route('jabatan.edit', $d->id_jabatan) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('jabatan.destroy', $d->id_jabatan) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus data?')">Hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
