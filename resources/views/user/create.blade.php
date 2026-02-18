@extends('layout.main')

@section('main')
    <div class="page-heading">
        <h3>Tambah User</h3>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <form action="/user" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label>Pegawai</label>
                        <select name="id_pegawai" class="form-control" required>
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach ($pegawai as $p)
                                <option value="{{ $p->id_pegawai }}">{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button class="btn btn-success">Simpan</button>
                    <a href="/user" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
@endsection
