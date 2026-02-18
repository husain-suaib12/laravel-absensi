@extends('layout.main')

@section('title', 'Data Pegawai')

@section('main')
    <x-running-text text="👥 Manajemen Data Pegawai — Kelola data pegawai secara terstruktur dan aman" color="rt-manajemen" />

    <div class="page-heading">
        <h3>Data Pegawai</h3>
    </div>

    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-12">
                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Daftar Pegawai</h4>
                        <a href="/pegawai/create" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Tambah Pegawai
                        </a>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Foto</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Pendidikan</th>
                                        <th>Status</th>
                                        <th>No HP</th>
                                        <th>Gaji Pokok</th> {{-- Tambahan --}}
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($pegawai as $p)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>

                                            <td>
                                                @if ($p->foto)
                                                    <img src="{{ asset('foto/' . $p->foto) }}"
                                                        alt="Foto {{ $p->nama }}" width="50" height="50"
                                                        class="rounded">
                                                @else
                                                    <span>Tidak ada foto</span>
                                                @endif
                                            </td>


                                            <td>{{ $p->nik }}</td>
                                            <td>{{ $p->nama }}</td>
                                            <td>{{ $p->nama_jabatan }}</td>
                                            <td>{{ $p->tingkat }}</td>

                                            <td>
                                                @if ($p->status_aktif == '1')
                                                    <span class="badge bg-success">Pegawai</span>
                                                @else
                                                    <span class="badge bg-info">Honorer</span>
                                                @endif
                                            </td>


                                            <td>{{ $p->no_hp }}</td>

                                            {{-- Gaji Pokok --}}
                                            <td>
                                                Rp {{ number_format($p->gaji_pokok, 0, ',', '.') }}
                                            </td>

                                            <td>
                                                <a href="{{ route('pegawai.edit', $p->id_pegawai) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>

                                                <form action="{{ route('pegawai.destroy', $p->id_pegawai) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Hapus data ini?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection
