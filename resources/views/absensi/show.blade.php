@extends('layout.main')

@section('title', 'Detail Lokasi Absensi')

@section('main')

    <div class="card">
        <div class="card-header">
            <h4>Detail Lokasi Absensi</h4>
            <small>Pegawai: <b>{{ $data->nama }}</b></small><br>
            <small>Tanggal: <b>{{ $data->tanggal }}</b></small>
        </div>

        <div class="card-body">

            <div id="map" style="height: 450px; width: 100%; border-radius:10px;"></div>

            <a href="{{ route('absensi.index') }}" class="btn btn-secondary mt-3">Kembali</a>

        </div>
    </div>

@endsection

@push('scripts')
    {{-- Leaflet CSS & JS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        // Inisialisasi Peta
        var map = L.map('map').setView(
            [{{ $kantor->latitude }}, {{ $kantor->longitude }}],
            17
        );

        // Layer Peta (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // Lingkaran Radius Kantor
        L.circle(
            [{{ $kantor->latitude }}, {{ $kantor->longitude }}], {
                color: 'blue',
                fillColor: 'rgba(0,0,255,0.3)',
                fillOpacity: 0.3,
                radius: {{ $kantor->radius_master }}
            }
        ).addTo(map);

        // Marker Kantor
        L.marker([{{ $kantor->latitude }}, {{ $kantor->longitude }}])
            .addTo(map)
            .bindPopup("Lokasi Kantor");

        // Marker Absen Masuk
        @if ($data->lat_masuk)
            L.marker(
                    [{{ $data->lat_masuk }}, {{ $data->long_masuk }}], {
                        icon: L.icon({
                            iconUrl: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png'
                        })
                    }
                ).addTo(map)
                .bindPopup(`
        <b>Absen Masuk</b><br>
        Pegawai: {{ $data->nama }}<br>
        Jam: {{ $data->jam_masuk }}<br>
        Lokasi dicatat dari GPS perangkat
    `);
        @endif


        // Marker Absen Pulang
        @if ($data->lat_pulang)
            L.marker(
                    [{{ $data->lat_pulang }}, {{ $data->long_pulang }}], {
                        icon: L.icon({
                            iconUrl: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
                        })
                    }
                ).addTo(map)
                .bindPopup("Absen Pulang: {{ $data->jam_pulang }}");
        @endif
    </script>
@endpush
