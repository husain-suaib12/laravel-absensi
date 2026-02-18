@extends('layout.main')

@section('title', 'Edit Pegawai')

@section('main')
    <div class="page-heading">
        <h3>Edit Data Pegawai</h3>
    </div>

    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Form Edit Pegawai</h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('pegawai.update', $pegawai->id_pegawai) }}" method="POST"
                            enctype="multipart/form-data">

                            @csrf
                            @method('PUT')



                            <div class="col-md-6 mb-3">
                                <label>NIK</label>
                                <input type="text" name="nik" class="form-control" value="{{ $pegawai->nik }}"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Nama</label>
                                <input type="text" name="nama" class="form-control" value="{{ $pegawai->nama }}"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Jabatan</label>
                                <select name="id_jabatan" class="form-control">
                                    @foreach ($jabatan as $j)
                                        <option value="{{ $j->id_jabatan }}"
                                            {{ $j->id_jabatan == $pegawai->id_jabatan ? 'selected' : '' }}>
                                            {{ $j->nama_jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Pendidikan</label>
                                <select name="id_pendidikan" class="form-control">
                                    @foreach ($pendidikan as $p)
                                        <option value="{{ $p->id_pendidikan }}"
                                            {{ $p->id_pendidikan == $pegawai->id_pendidikan ? 'selected' : '' }}>
                                            {{ $p->tingkat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Status</label>
                                <select name="status_aktif" class="form-control">
                                    <option value="1" {{ $pegawai->status_aktif == 1 ? 'selected' : '' }}>Pegawai
                                    </option>
                                    <option value="0" {{ $pegawai->status_aktif == 0 ? 'selected' : '' }}>Honorer
                                    </option>
                                </select>
                            </div>



                            <div class="col-md-6 mb-3">
                                <label>No HP</label>
                                <input type="text" name="no_hp" class="form-control" value="{{ $pegawai->no_hp }}"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Alamat</label>
                                <textarea name="alamat" class="form-control" required>{{ $pegawai->alamat }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Gaji Pokok</label>
                                <input type="number" name="gaji_pokok" class="form-control"
                                    value="{{ $pegawai->gaji_pokok }}" required>
                            </div>


                            <div class="col-md-6 mb-3">
                                <label>Foto</label><br>
                                @if ($pegawai->foto)
                                    <img src="{{ asset('foto/' . $pegawai->foto) }}" width="80" class="mb-2">
                                @endif
                                <input type="file" name="foto" class="form-control">
                            </div>

                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">Kembali</a>

                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
