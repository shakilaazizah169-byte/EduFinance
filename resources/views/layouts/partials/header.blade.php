<header class="nxl-header">
    <div class="header-wrapper">
        <div class="header-left d-flex align-items-center gap-4">
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-align-left"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
            <div class="nxl-drp-link nxl-lavel-mega-menu">
                <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                    <a href="javascript:void(0)" id="nxl-lavel-mega-menu-hide">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="header-right ms-auto">
                <div class="d-flex align-items-center">

                    {{-- Fullscreen --}}
                    <div class="nxl-h-item d-none d-sm-flex">
                        <div class="full-screen-switcher">
                            <a href="javascript:void(0);" class="nxl-head-link me-0"
                               onclick="$('body').fullScreenHelper('toggle');">
                                <i class="feather-maximize maximize"></i>
                                <i class="feather-minimize minimize"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Dark/Light mode --}}
                    <div class="nxl-h-item dark-light-theme">
                        <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                            <i class="feather-moon"></i>
                        </a>
                        <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display:none">
                            <i class="feather-sun"></i>
                        </a>
                    </div>

                    {{-- User dropdown / Login button untuk guest --}}
                    @auth
                        {{-- Tampilkan dropdown user jika sudah login --}}
                        <div class="dropdown nxl-h-item">
                            <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                                <img src="{{ auth()->user()->getAvatarUrl() }}"
                                     alt="User"
                                     class="img-fluid user-avtar me-0 rounded-circle"
                                     onerror="this.src='{{ asset('assets/images/avatar/1.jpg') }}'">
                            </a>

                            <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                                <div class="dropdown-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ auth()->user()->getAvatarUrl() }}"
                                             alt="User"
                                             class="img-fluid user-avtar rounded-circle"
                                             onerror="this.src='{{ asset('assets/images/avatar/1.jpg') }}'">
                                        <div>
                                            <h6 class="text-dark mb-0">
                                                {{ auth()->user()->name }}
                                                <span class="badge bg-soft-success text-success ms-1">
                                                    {{ strtoupper(auth()->user()->role) }}
                                                </span>
                                            </h6>
                                            <span class="fs-12 fw-medium text-muted">
                                                {{ auth()->user()->email }}
                                            </span>
                                            @php
                                                $headerSetting = \App\Models\SchoolSetting::forUser(auth()->id());
                                            @endphp
                                            @if($headerSetting && $headerSetting->nama_sekolah)
                                                <br><span class="fs-11 text-muted">
                                                    <i class="feather-home me-1"></i>{{ $headerSetting->nama_sekolah }}
                                                </span>
                                            @elseif(auth()->user()->nama_sekolah)
                                                <br><span class="fs-11 text-muted">
                                                    <i class="feather-home me-1"></i>{{ auth()->user()->nama_sekolah }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('profile.details') }}" class="dropdown-item">
                                    <i class="feather-user"></i>
                                    <span>Profile Details</span>
                                </a>

                                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                    <i class="feather-camera"></i>
                                    <span>Edit Foto Profile</span>
                                </a>

                                <a href="{{ route('school.settings') }}" class="dropdown-item">
                                    <i class="feather-settings"></i>
                                    <span>
                                        @if(auth()->user()->role == 'super_admin')
                                            Setting
                                        @else
                                            Setting Instansi
                                        @endif
                                    </span>
                                </a>

                                <div class="dropdown-divider"></div>

                                <form method="POST" action="{{ route('logout') }}" class="dropdown-item-form">
                                    @csrf
                                    <button type="submit" class="dropdown-item w-100 text-start border-0 bg-transparent">
                                        <i class="feather-log-out"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        {{-- Tampilkan tombol login untuk guest --}}
                        <div class="nxl-h-item">
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="feather-log-in me-2"></i>Login
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary ms-2">
                                <i class="feather-user-plus me-2"></i>Register
                            </a>
                        </div>
                    @endauth

                </div>
            </div>
        </div>
    </header>

<style>
.dropdown-item-form { margin: 0; padding: 0; }
.dropdown-item-form .dropdown-item { padding: .5rem 1rem; color: #6c757d; }
.dropdown-item-form .dropdown-item:hover { color: #1e2125; background: #f8f9fa; }
.user-avtar { border-radius: 50%; object-fit: cover; width: 40px; height: 40px; }
</style>