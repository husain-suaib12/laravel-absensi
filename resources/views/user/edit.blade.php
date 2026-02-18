@extends('layout.main')

@section('main')
    <div class="container mt-5">

        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-primary text-white rounded-top-4">
                <h4 class="mb-0">✏️ Edit User</h4>
            </div>

            <div class="card-body p-4">

                <form action="{{ route('user.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">

                        {{-- Email --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control rounded-3"
                                value="{{ old('email', $user->email) }}" required>
                        </div>

                        {{-- Role --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Role</label>
                            <select name="role" class="form-control rounded-3">
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>
                                <option value="pegawai" {{ $user->role == 'pegawai' ? 'selected' : '' }}>
                                    Pegawai
                                </option>
                            </select>
                        </div>

                        {{-- Password Baru --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                Username
                            </label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                class="form-control rounded-3">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                Password Baru
                                <small class="text-muted">(kosongkan jika tidak diganti)</small>
                            </label>
                            <input type="password" name="password" class="form-control rounded-3">
                        </div>

                    </div>

                    {{-- Tombol --}}
                    <div class="d-flex justify-content-between mt-4">

                        <a href="{{ route('user.index') }}" class="btn btn-secondary px-4 rounded-3">
                            ⬅ Kembali
                        </a>

                        <button type="submit" class="btn btn-success px-4 rounded-3 shadow-sm">
                            💾 Update User
                        </button>

                    </div>

                </form>

            </div>
        </div>

    </div>
@endsection
