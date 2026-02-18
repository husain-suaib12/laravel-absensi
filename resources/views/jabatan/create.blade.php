@extends('layout.main')
@section('main')
    <h3>{{ isset($jabatan) ? 'Edit' : 'Tambah' }} Jabatan</h3>

    <form method="POST"
        action="{{ isset($jabatan) ? route('jabatan.update', $jabatan->id_jabatan) : route('jabatan.store') }}">
        @csrf
        @isset($jabatan)
            @method('PUT')
        @endisset

        <div class="mb-3">
            <label>Nama Jabatan</label>
            <input type="text" name="nama_jabatan" class="form-control" value="{{ $jabatan->nama_jabatan ?? '' }}" required>
        </div>

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('jabatan.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
@endsection
