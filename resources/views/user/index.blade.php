@extends('layout.main')

@section('main')
    <x-running-text text="🧑‍💻 Manajemen User — Atur akun dan hak akses pengguna sistem" color="rt-manajemen" />


    <div class="page-heading">
        <h3>Data User</h3>
    </div>

    <div class="page-content">
        <a href="/user/create" class="btn btn-primary mb-3">
            <i class="bi bi-plus-circle"></i> Tambah User
        </a>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pegawai</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $u->pegawai->nama ?? '-' }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    <a href="/user/{{ $u->id }}/edit" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="/user/{{ $u->id }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Hapus user?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
