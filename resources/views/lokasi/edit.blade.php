@extends('layout.main')

@section('main')
    <div class="container">
        <h4 class="mb-3">Edit Lokasi Kantor</h4>

        <form action="{{ route('lokasi.update', $lokasi->id_lokasi) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nama Lokasi</label>
                <input type="text" name="nama_lokasi" class="form-control" value="{{ $lokasi->nama_lokasi }}" required>
            </div>

            <div class="form-group">
                <label>Latitude</label>
                <input type="text" name="latitude" class="form-control" value="{{ $lokasi->latitude }}" required>
            </div>

            <div class="form-group">
                <label>Longitude</label>
                <input type="text" name="longitude" class="form-control" value="{{ $lokasi->longitude }}" required>
            </div>

            <div class="form-group">
                <label>Radius (meter)</label>
                <input type="number" name="radius" class="form-control" value="{{ $lokasi->radius_master }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>

    </div>
@endsection
