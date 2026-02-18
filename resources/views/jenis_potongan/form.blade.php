@extends('layout.main')

@section('main')
    <h3>{{ isset($potongan) ? 'Edit' : 'Tambah' }} Jenis Potongan</h3>

    <form method="POST"
        action="{{ isset($potongan) ? route('jenis-potongan.update', $potongan->id_jenis) : route('jenis-potongan.store') }}">

        @csrf
        @isset($potongan)
            @method('PUT')
        @endisset

        <div class="mb-3">
            <label>Nama Potongan</label>
            <input type="text" name="nama_potongan" class="form-control" value="{{ $potongan->nama_potongan ?? '' }}"
                required>
        </div>

        <div class="mb-3">
            <label>Nilai</label>
            <input type="text" name="nilai" class="form-control" value="{{ $potongan->nilai ?? '' }}">
            <small class="text-muted">contoh: 50000 atau 5%</small>
        </div>

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('jenis-potongan.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
@endsection
