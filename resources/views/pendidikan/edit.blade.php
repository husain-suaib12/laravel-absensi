@extends('layout.main')
@section('main')
    <h3>{{ isset($pendidikan) ? 'Edit' : 'Tambah' }} Pendidikan</h3>

    <form method="POST"
        action="{{ isset($pendidikan) ? route('pendidikan.update', $pendidikan->id_pendidikan) : route('pendidikan.store') }}">
        @csrf
        @isset($pendidikan)
            @method('PUT')
        @endisset

        <div class="mb-3">
            <label>Tingkat Pendidikan</label>
            <input type="text" name="tingkat" class="form-control" value="{{ $pendidikan->tingkat ?? '' }}" required>
        </div>

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('pendidikan.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
@endsection
