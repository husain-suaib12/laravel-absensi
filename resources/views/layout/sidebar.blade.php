<div id="sidebar" class="active">
    <div class="sidebar-wrapper active shadow-sm">

        {{-- HEADER --}}
        <div class="sidebar-header d-flex align-items-center px-3 py-3 border-bottom">
            <img src="{{ asset('template/assets/compiled/svg/favicon.svg') }}" alt="" style="width:32px;">
            <div class="ms-2">
                <h5 class="mb-0 fw-bold">Absensi Desa</h5>

            </div>
        </div>

        {{-- MENU --}}
        <div class="sidebar-menu mt-2">
            <ul class="menu">

                <li class="sidebar-title text-uppercase">Menu Utama</li>

                {{-- DASHBOARD --}}
                <li class="sidebar-item {{ request()->routeIs('welcome') ? 'active' : '' }}">
                    <a href="{{ route('welcome') }}" class="sidebar-link">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                {{-- ABSENSI --}}
                <li class="sidebar-item {{ request()->is('absensi*') ? 'active' : '' }}">
                    <a href="{{ route('absensi.index') }}" class="sidebar-link">
                        <i class="bi bi-journal-check"></i>
                        <span>Data Absensi</span>
                    </a>
                </li>

                {{-- VALIDASI IZIN --}}
                <li class="sidebar-item {{ request()->is('izin*') ? 'active' : '' }}">
                    <a href="{{ route('izin.index') }}" class="sidebar-link">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Validasi Izin</span>
                    </a>
                </li>

                {{-- GAJI --}}
                <li class="sidebar-item {{ request()->is('gaji*') ? 'active' : '' }}">
                    <a href="{{ route('gaji.index') }}" class="sidebar-link">
                        <i class="bi bi-cash-stack"></i>
                        <span>Rekap Gaji</span>
                    </a>
                </li>

                <li class="sidebar-title text-uppercase mt-3">Manajemen</li>

                {{-- MANAJEMEN --}}
                <li
                    class="sidebar-item has-sub {{ request()->is('pegawai*') || request()->is('user*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-collection-fill"></i>
                        <span>Manajemen</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item {{ request()->is('pegawai*') ? 'active' : '' }}">
                            <a href="/pegawai">
                                <i class="bi bi-people-fill"></i>
                                <span>Data Pegawai</span>
                            </a>
                        </li>
                        <li class="submenu-item {{ request()->is('user*') ? 'active' : '' }}">
                            <a href="/user">
                                <i class="bi bi-person-badge-fill"></i>
                                <span>Data User</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-title text-uppercase mt-3">Master Data</li>

                {{-- MASTER --}}
                <li class="sidebar-item has-sub {{ request()->is('lokasi*') ? 'active' : '' }}">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Master</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item {{ request()->is('lokasi*') ? 'active' : '' }}">
                            <a href="{{ route('lokasi.index') }}">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span>Lokasi Kantor</span>
                            </a>
                        </li>
                        <li class="submenu-item {{ request()->is('jam-kerja*') ? 'active' : '' }}">
                            <a href="{{ route('jam-kerja.index') }}">
                                <i class="bi bi-clock-fill"></i>
                                <span>Jam Kerja</span>
                            </a>
                        </li>
                        <li class="submenu-item {{ request()->is('jabatan*') ? 'active' : '' }}">
                            <a href="{{ route('jabatan.index') }}">
                                <i class="bi bi-person-badge-fill"></i>
                                <span>Jabatan</span>
                            </a>
                        </li>
                        <li class="submenu-item {{ request()->is('pendidikan*') ? 'active' : '' }}">
                            <a href="{{ route('pendidikan.index') }}">
                                <i class="bi bi-mortarboard-fill"></i>
                                <span>Pendidikan</span>
                            </a>
                        </li>
                        <li class="submenu-item {{ request()->is('jenis-potongan*') ? 'active' : '' }}">
                            <a href="{{ route('jenis-potongan.index') }}">
                                <i class="bi bi-scissors"></i>
                                <span>Jenis Potongan</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- LOGOUT --}}
                <li class="sidebar-item mt-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="sidebar-link text-danger"
                            style="border:none;background:none;width:100%;text-align:left;">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>

            </ul>
        </div>
    </div>
</div>
