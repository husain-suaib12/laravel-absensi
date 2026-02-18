<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — ABSENSI DESA</title>

    <link rel="stylesheet" href="{{ asset('template/assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/compiled/css/auth.css') }}">

    <style>
        /* BAGIAN KANAN (FOTO DESA) */
        #auth-right {
            min-height: 100vh;
            background: url('/foto/kegiatan.jpg') no-repeat center center;
            background-size: cover;
        }

        /* BAGIAN KIRI (LOGIN) */
        #auth-left {
            padding: 3rem 2.5rem;
        }

        .auth-title {
            font-weight: 700;
            color: #198754;
            /* hijau senada dashboard */
        }

        .auth-subtitle {
            color: #6c757d;
        }

        .login-box {
            background: #fff;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        }

        .btn-login {
            background-color: #198754;
            border: none;
        }

        .btn-login:hover {
            background-color: #157347;
        }
    </style>
</head>

<body>
    <div id="auth">
        <div class="row h-100">

            <!-- ===== KIRI (FORM LOGIN) ===== -->
            <div class="col-lg-5 col-12">
                <div id="auth-left" class="p-4">

                    <!-- Identitas Sistem -->
                    <center>
                        <div class="mb-3">
                            <h4 class="fw-bold text-primary mb-1">
                                SISTEM ABSENSI DESA
                            </h4>
                            <small class="text-muted">
                                Pemerintah Desa • Disiplin • Transparan
                            </small>
                        </div>
                    </center>
                    <!-- Teks Berjalan -->
                    <div class="alert alert-primary py-2 mb-4">
                        <marquee behavior="scroll" direction="left" scrollamount="4">
                            👋 Selamat datang di Sistem Informasi Absensi Pegawai Desa —
                            Silakan login untuk melanjutkan
                        </marquee>
                    </div>

                    <!-- Judul Login -->

                    <h1 class="mb-2">Login</h1>

                    <p class="auth-subtitle mb-4">
                        Masukkan email dan password Anda.
                    </p>

                    <!-- Error -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <!-- Form Login -->
                    <form action="/login" method="POST">
                        @csrf

                        <div class="form-group position-relative mb-3">
                            <input type="text" class="form-control form-control-xl" name="username"
                                placeholder="Username" required>
                        </div>

                        <div class="form-group position-relative mb-4">
                            <input type="password" class="form-control form-control-xl" name="password"
                                placeholder="Password" required>
                        </div>

                        <button class="btn btn-primary btn-block btn-lg shadow-lg mt-3">
                            Login
                        </button>
                    </form>

                </div>
            </div>

            <!-- ===== KANAN (FOTO KEGIATAN DESA) ===== -->
            <div class="col-lg-7 d-none d-lg-block"
                style="
        background-image: url('{{ asset('foto/kegiatan.jpeg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 100vh;
    ">
            </div>


        </div>
    </div>
</body>

</html>
