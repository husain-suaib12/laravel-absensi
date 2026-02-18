<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ABSENSI DESA')</title>

    <link rel="shortcut icon" href="{{ asset('template/assets/compiled/svg/favicon.svg') }}" type="image/x-icon">

    <link rel="stylesheet" href="{{ asset('template/assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/compiled/css/iconly.css') }}">

    {{-- STYLE TAMBAHAN --}}
    <style>
        body {
            background-color: #f5f6fa;
        }

        #main {
            padding: 20px;
        }

        .page-content {
            padding: 0;
        }

        .content-wrapper {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
        }

        .navbar {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
        }

        .page-title h3 {
            font-weight: 700;
        }

        .page-title p {
            color: #6c757d;
            margin-bottom: 0;
        }

        body.sidebar-hidden #sidebar {
            transform: translateX(-100%);
        }

        body.sidebar-hidden #main {
            margin-left: 0 !important;
            width: 100%;
        }

        #sidebar {
            transition: all .3s ease;
        }

        #main {
            transition: all .3s ease;
        }


        /* BASE */
        .running-wrapper {
            color: #ffffff;
            padding: 12px 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
        }

        .running-text {
            white-space: nowrap;
            display: inline-block;
            padding-left: 100%;
            animation: runningText 18s linear infinite;
            font-size: 15px;
            font-weight: 500;
        }

        @keyframes runningText {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-100%);
            }
        }

        .running-wrapper:hover .running-text {
            animation-play-state: paused;
        }

        /* WARNA PER MENU */
        .rt-primary {
            background: linear-gradient(135deg, #13abec, #224abe);
            /* Dashboard */
        }

        .rt-success {
            background: linear-gradient(135deg, #86cead, #157347);
            /* Absensi */
        }

        .rt-warning {
            background: linear-gradient(135deg, #d6c9a3, #e0a800);
            /* Izin */
            color: #212529;
        }

        .rt-info {
            background: linear-gradient(135deg, #aecacf, #0aa2c0);
            /* Info */
        }

        .rt-purple {
            background: linear-gradient(135deg, #c7b0f3, #59339d);
            /* Gaji */
        }

        /* MANAJEMEN */
        .rt-manajemen {
            background: linear-gradient(135deg, #cf0505, #1b0ce8);
            /* ORANGE */
            color: #ffffff;
        }

        /* MASTER */
        .rt-master {
            background: linear-gradient(135deg, #0dbbde, #09e61f);
            /* CYAN / BIRU MUDA */
            color: #ffffff;
        }
    </style>

    @stack('style')
</head>

<body>
    <script src="{{ asset('template/assets/static/js/initTheme.js') }}"></script>

    <div id="app">

        {{-- SIDEBAR --}}
        @include('layout.sidebar')

        <div id="main" class="layout-navbar">

            {{-- HEADER --}}
            <header class="mb-4">
                <nav class="navbar navbar-expand navbar-light px-3">
                    <div class="container-fluid p-0">
                        <a href="#" class="burger-btn d-block" id="toggleSidebar">
                            <i class="bi bi-justify fs-3"></i>
                        </a>


                        <div class="ms-auto d-flex align-items-center">
                            <span class="me-3 text-muted small">
                                {{ now()->format('l, d M Y') }}
                            </span>
                            <i class="bi bi-person-circle fs-4"></i>
                        </div>
                    </div>
                </nav>
            </header>

            {{-- CONTENT --}}
            <div class="page-content">
                <div class="content-wrapper">
                    @yield('main')
                </div>
            </div>

        </div>
    </div>

    <script src="{{ asset('template/assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('template/assets/compiled/js/app.js') }}"></script>

    @stack('scripts')
    <script>
        document.getElementById('toggleSidebar').addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('sidebar-hidden');
        });
    </script>

</body>

</html>
