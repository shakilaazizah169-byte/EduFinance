@php
    $sidebarSetting = \App\Models\SchoolSetting::forUser(auth()->id());
    $user = auth()->user();
@endphp

<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand">
                @if($sidebarSetting && $sidebarSetting->logo_sekolah)
                    <img src="{{ $sidebarSetting->logoSekolahUrl() }}"
                         class="logo logo-lg img-fluid"
                         style="object-fit:contain; max-height:50px;"
                         alt="{{ $sidebarSetting->nama_sekolah ?? 'Logo Instansi' }}">
                    <img src="{{ $sidebarSetting->logoSekolahUrl() }}"
                         class="logo logo-sm"
                         style="object-fit:contain; max-height:36px;"
                         alt="Logo">
                @else
                    <img src="{{ asset('assets/images/1.jpeg') }}"
                         class="logo logo-lg img-fluid"
                         style="object-fit:contain;"
                         alt="EduFinance">
                    <img src="{{ asset('assets/images/EDUFINANCE.jpg') }}"
                         class="logo logo-sm"
                         alt="EduFinance">
                @endif
            </a>
        </div>

        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item nxl-caption">
                    <label>Navigation</label>
                </li>

                {{-- Dashboard --}}
                <li class="nxl-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-home"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>

                @if($user)

                    {{-- ════════════════════════════════════
                         SUPER ADMIN MENU
                         ════════════════════════════════════ --}}
                    @if($user->role === 'super_admin')

                        <li class="nxl-item nxl-caption">
                            <label>Super Admin</label>
                        </li>

                        {{-- Manajemen User --}}
                        <li class="nxl-item nxl-hasmenu
                            {{ request()->routeIs('admin.users.*') ? 'active open' : '' }}">
                            <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-users"></i></span>
                                <span class="nxl-mtext">Manajemen User</span>
                                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                            </a>
                            <ul class="nxl-submenu">
                                <li class="nxl-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('admin.users.index') }}">
                                        <i class="feather-list me-2"></i>Daftar User Instansi
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Mutasi Pemasukan --}}
                        <li class="nxl-item nxl-hasmenu
                            {{ request()->routeIs('admin.mutasi.*') ? 'active open' : '' }}">
                            <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-trending-up"></i></span>
                                <span class="nxl-mtext">Mutasi Pemasukan</span>
                                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                            </a>
                            <ul class="nxl-submenu">
                                <li class="nxl-item {{ request()->routeIs('admin.mutasi.index') ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('admin.mutasi.index') }}">
                                        <i class="feather-list me-2"></i>Riwayat Pemasukan
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nxl-item nxl-caption">
                            <label>Pengguna</label>
                        </li>

                    @endif
                    {{-- ════ END SUPER ADMIN ════ --}}

                    {{-- ════════════════════════════════════
                         NON SUPER ADMIN MENU
                         ════════════════════════════════════ --}}
                    @if($user->role !== 'super_admin')

                        {{-- Data Master --}}
                        <li class="nxl-item nxl-hasmenu
                            {{ request()->routeIs('kategori.*') || request()->routeIs('kode-transaksi.*') ? 'active open' : '' }}">
                            <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-database"></i></span>
                                <span class="nxl-mtext">Data Master</span>
                                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                            </a>
                            <ul class="nxl-submenu">
                                <li class="nxl-item {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('kategori.index') }}">
                                        <i class="feather-tag me-2"></i>Kategori
                                    </a>
                                </li>
                                <li class="nxl-item {{ request()->routeIs('kode-transaksi.*') ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('kode-transaksi.index') }}">
                                        <i class="feather-hash me-2"></i>Kode Mutasi
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Perencanaan --}}
                        <li class="nxl-item nxl-hasmenu
                            {{ request()->routeIs('perencanaan.*') ? 'active open' : '' }}">
                            <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-target"></i></span>
                                <span class="nxl-mtext">Perencanaan</span>
                                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                            </a>
                            <ul class="nxl-submenu">
                                <li class="nxl-item {{ request()->routeIs('perencanaan.index') ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('perencanaan.index') }}">
                                        <i class="feather-list me-2"></i>Daftar Perencanaan
                                    </a>
                                </li>
                                <li class="nxl-item {{ request()->routeIs('perencanaan.create') ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('perencanaan.create') }}">
                                        <i class="feather-plus-circle me-2"></i>Tambah Perencanaan
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Realisasi --}}
                        <li class="nxl-item nxl-hasmenu
                            {{ request()->routeIs('realisasi.*') ? 'active open' : '' }}">
                            <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-check-circle"></i></span>
                                <span class="nxl-mtext">Realisasi</span>
                                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                            </a>
                            <ul class="nxl-submenu">
                                <li class="nxl-item {{ request()->routeIs('realisasi.index') ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('realisasi.index') }}">
                                        <i class="feather-list me-2"></i>Daftar Realisasi
                                    </a>
                                </li>
                                <li class="nxl-item {{ request()->routeIs('realisasi.create') ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('realisasi.create') }}">
                                        <i class="feather-plus-circle me-2"></i>Tambah Realisasi
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Mutasi Kas --}}
                        <li class="nxl-item nxl-hasmenu
                            {{ request()->routeIs('mutasi-kas.*') ? 'active open' : '' }}">
                            <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                                <span class="nxl-mtext">Mutasi Kas</span>
                                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                            </a>
                            <ul class="nxl-submenu">
                                <li class="nxl-item {{ request()->routeIs('mutasi-kas.index') ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('mutasi-kas.index') }}">
                                        <i class="feather-list me-2"></i>Daftar Mutasi
                                    </a>
                                </li>
                                <li class="nxl-item {{ request()->routeIs('mutasi-kas.create') ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('mutasi-kas.create') }}">
                                        <i class="feather-plus-circle me-2"></i>Tambah Mutasi
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Invoice --}}
                        <li class="nxl-item nxl-hasmenu
                            {{ request()->routeIs('invoice.*') ? 'active open' : '' }}">
                            <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-file-text"></i></span>
                                <span class="nxl-mtext">Invoice</span>
                                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                            </a>
                            <ul class="nxl-submenu">
                                <li class="nxl-item {{ request()->routeIs('invoice.index') ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('invoice.index') }}">
                                        <i class="feather-list me-2"></i>Daftar Invoice
                                    </a>
                                </li>
                                <li class="nxl-item {{ request()->routeIs('invoice.create') ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('invoice.create') }}">
                                        <i class="feather-plus-circle me-2"></i>Buat Invoice
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Laporan --}}
                        <li class="nxl-item nxl-hasmenu
                            {{ request()->routeIs('laporan.*') ? 'active open' : '' }}">
                            <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-bar-chart-2"></i></span>
                                <span class="nxl-mtext">Laporan</span>
                                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                            </a>
                            <ul class="nxl-submenu">
                                <li class="nxl-item {{ request()->routeIs('laporan.mutasi') ? 'active' : '' }}">
                                    <a class="nxl-link" href="{{ route('laporan.mutasi') }}">
                                        <i class="feather-bar-chart-2 me-2"></i>Laporan Mutasi Kas
                                    </a>
                                </li>
                            </ul>
                        </li>

                    @endif
                    {{-- ════ END NON SUPER ADMIN ════ --}}

                    {{-- Settings — semua role --}}
                    <li class="nxl-item nxl-hasmenu
                        {{ request()->routeIs('school.settings') || request()->routeIs('profile.*') ? 'active open' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-settings"></i></span>
                            <span class="nxl-mtext">Settings</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('profile.details') }}">
                                    <i class="feather-user me-2"></i>Profile
                                </a>
                            </li>
                            <li class="nxl-item {{ request()->routeIs('school.settings') ? 'active' : '' }}">
                                <a class="nxl-link" href="{{ route('school.settings') }}">
                                    <i class="feather-home me-2"></i>
                                    {{ $user->role === 'super_admin' ? 'Setting Sistem' : 'Setting Instansi' }}
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Logout --}}
                    <li class="nxl-item">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                            @csrf
                        </form>
                        <a href="#" class="nxl-link"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="nxl-micon"><i class="feather-log-out"></i></span>
                            <span class="nxl-mtext">Logout</span>
                        </a>
                    </li>

                @endif
            </ul>

            {{-- Bottom card --}}
            <div class="card text-center mt-4">
                <div class="card-body">
                    @if($user)
                        @if($user->role === 'super_admin')
                            <i class="feather-shield fs-4 text-dark"></i>
                            <h6 class="mt-3 text-dark fw-bolder">Super Admin</h6>
                            <p class="fs-11 my-2 text-dark">Panel administrasi sistem.</p>
                            <a href="{{ route('school.settings') }}" class="btn btn-primary text-white w-100">
                                <i class="bi bi-gear"></i>Setting Sistem
                            </a>
                        @else
                            <i class="feather-sunrise fs-4 text-dark"></i>
                            <h6 class="mt-3 text-dark fw-bolder">
                                {{ $sidebarSetting->nama_sekolah ?? 'EduFinance' }}
                            </h6>
                            <p class="fs-11 my-2 text-dark">Manajemen keuangan sekolah yang mudah.</p>
                            <a href="{{ route('mutasi-kas.create') }}" class="btn btn-primary text-white w-100">
                                <i class="feather-plus me-1"></i>Tambah Mutasi
                            </a>
                        @endif
                    @else
                        <i class="feather-log-in fs-4 text-dark"></i>
                        <h6 class="mt-3 text-dark fw-bolder">Selamat Datang</h6>
                        <p class="fs-11 my-2 text-dark">Silakan login untuk mengakses fitur.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary text-white w-100">
                            <i class="feather-log-in me-1"></i>Login
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</nav>