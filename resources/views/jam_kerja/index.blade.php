@extends('layout.main')
@section('title', 'Detail Jam Kerja')
@section('main')
    <x-running-text text="⏰ Master Jam Kerja — Atur jam kerja pegawai" color="rt-master" />

    <div class="page-heading">
        <h3>Jam Kerja</h3>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($jamKerja)
                    <table class="table table-bordered">
                        <tr>
                            <th>Jam Masuk</th>
                            <td>
                                {{ substr($jamKerja->jam_masuk_mulai, 0, 5) }}
                                -
                                {{ substr($jamKerja->jam_masuk_selesai, 0, 5) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Jam Pulang</th>
                            <td>
                                {{ substr($jamKerja->jam_pulang_mulai, 0, 5) }}
                                -
                                {{ substr($jamKerja->jam_pulang_selesai, 0, 5) }}
                            </td>
                        </tr>
                    </table>

                    <a href="{{ route('jam-kerja.edit', $jamKerja->id_jam) }}" class="btn btn-primary">
                        Edit Jam Kerja
                    </a>
                @else
                    <div class="alert alert-warning">
                        Data jam kerja belum tersedia
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
