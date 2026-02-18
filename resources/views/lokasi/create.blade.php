@extends('layout.main')

@section('main')
    <div class="card">
        <div class="card-body">

            <h3 class="mb-3">Pilih Titik Lokasi di Peta</h3>

            <div id="map" style="height: 400px; width: 100%; border-radius:10px;"></div>

            <form action="{{ route('lokasi.store') }}" method="POST" class="mt-4">
                @csrf

                <div class="form-group">
                    <label>Nama Lokasi</label>
                    <input type="text" name="nama_lokasi" class="form-control" required>
                </div>

                <div class="form-group mt-2">
                    <label>Latitude</label>
                    <input type="text" id="latitude" name="latitude" class="form-control" readonly required>
                </div>

                <div class="form-group mt-2">
                    <label>Longitude</label>
                    <input type="text" id="longitude" name="longitude" class="form-control" readonly required>
                </div>

                <div class="form-group mt-2">
                    <label>Radius (meter)</label>
                    <input type="number" name="radius" value="50" class="form-control" required>
                </div>

                <button class="btn btn-primary mt-3">Simpan</button>
            </form>

        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        var map = L.map('map').setView([-0.6565, 122.9628], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        var marker;

        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            document.getElementById("latitude").value = lat;
            document.getElementById("longitude").value = lng;

            if (!marker) {
                marker = L.marker([lat, lng]).addTo(map);
            } else {
                marker.setLatLng([lat, lng]);
            }
        });
    </script>
@endpush
