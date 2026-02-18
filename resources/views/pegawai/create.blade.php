@extends('layout.main')

@section('title', 'Tambah Pegawai')

@section('main')
    <div class="page-heading">
        <h3>Tambah Pegawai</h3>
    </div>

    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">

                        <form action="{{ route('pegawai.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label>NIK</label>
                                    <input type="number" name="nik" class="form-control"required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Nama Pegawai</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>No Hp</label>
                                    <input type="number" name="no_hp" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Alamat</label>
                                    <input type="text" name="alamat" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Jabatan</label>
                                    <select name="id_jabatan" class="form-control" required>
                                        <option value="">-- Pilih Jabatan --</option>
                                        @foreach ($jabatan as $j)
                                            <option value="{{ $j->id_jabatan }}">{{ $j->nama_jabatan }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Pendidikan</label>
                                    <select name="id_pendidikan" class="form-control" required>
                                        <option value="">-- Pilih Pendidikan --</option>
                                        @foreach ($pendidikan as $p)
                                            <option value="{{ $p->id_pendidikan }}">{{ $p->tingkat }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Status Pegawai</label>
                                    <select name="status_aktif" class="form-control" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="1">Pegawai</option>
                                        <option value="0">Honorer</option>
                                    </select>
                                </div>


                                <div class="col-md-6 mb-3">
                                    <label>Gaji Pokok</label>
                                    <input type="number" name="gaji_pokok" class="form-control" required
                                        placeholder="Masukkan gaji pokok">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Foto</label>
                                    <input type="file" name="foto" class="form-control" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan</button>

                        </form>

                    </div>
                </div>
        </section>


    @endsection
