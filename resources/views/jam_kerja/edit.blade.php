@extends('layout.main')
@section('title', 'Edit Jam Kerja')
@section('main')
    <div class="page-heading">
        <h3>Edit Jam Kerja</h3>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="card-body">

                <form action="{{ route('jam-kerja.update', $jamKerja->id_jam) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label>Jam Masuk Mulai</label>
                        <input type="time" name="jam_masuk_mulai" class="form-control"
                            value="{{ \Carbon\Carbon::createFromFormat('H:i:s', $jamKerja->jam_masuk_mulai)->format('H:i') }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Jam Masuk Selesai</label>
                        <input type="time" name="jam_masuk_selesai" class="form-control"
                            value="{{ \Carbon\Carbon::createFromFormat('H:i:s', $jamKerja->jam_masuk_selesai)->format('H:i') }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Jam Pulang Mulai</label>
                        <input type="time" name="jam_pulang_mulai" class="form-control"
                            value="{{ \Carbon\Carbon::createFromFormat('H:i:s', $jamKerja->jam_pulang_mulai)->format('H:i') }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Jam Pulang Selesai</label>
                        <input type="time" name="jam_pulang_selesai" class="form-control"
                            value="{{ \Carbon\Carbon::createFromFormat('H:i:s', $jamKerja->jam_pulang_selesai)->format('H:i') }}"
                            required>
                    </div>

                    <button class="btn btn-success">Simpan</button>
                    <a href="{{ route('jam-kerja.index') }}" class="btn btn-secondary">Kembali</a>
                </form>


            </div>
        </div>
    </div>
@endsection
