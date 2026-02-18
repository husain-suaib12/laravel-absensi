@extends('layout.main')
@section('main')
    <x-running-text text="🎓 Master Pendidikan — Kelola data tingkat pendidikan pegawai" color="rt-master" />

    <h3>Master Pendidikan</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ route('pendidikan.create') }}" class="btn btn-primary mb-2">Tambah Pendidikan</a>

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Tingkat Pendidikan</th>
            <th>Aksi</th>
        </tr>
        @foreach ($data as $i => $d)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $d->tingkat }}</td>
                <td>
                    <a href="{{ route('pendidikan.edit', $d->id_pendidikan) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('pendidikan.destroy', $d->id_pendidikan) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus data?')">Hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
