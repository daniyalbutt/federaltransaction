<!-- https://dompet.dexignlab.com/xhtml/index.html -->
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Styles -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
    <link href="{{ asset('css/nice-select.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/nouislider.min.css') }}">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
    <div id="main-wrapper">
        <div class="nav-header">
            <a href="{{ route('home') }}" class="brand-logo">
                <img src="{{ asset('images/favicon.png') }}" alt="Logo" style="width: 50px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-text" style="width: 160px;margin-top: 0px;">
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="dashboard_bar">
                                @yield('title')
                            </div>
                        </div>
                        <ul class="navbar-nav header-right">
                            <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link bell dz-theme-mode p-0" href="javascript:void(0);">
                                <i id="icon-light" class="fas fa-sun"></i>
                                <i id="icon-dark" class="fas fa-moon"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                @can('create payment')
                                <a href="{{ route('payment.create') }}" class="btn btn-primary">Create Invoice</a>
                                @endcan
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <div class="dlabnav">
            <div class="dlabnav-scroll">
                <ul class="metismenu" id="menu">
                    <li class="dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            <img src="{{ asset('images/user.jpg') }}" width="20" alt="">
                            <div class="header-info ms-3">
                                <span class="font-w600 ">Hi,<b>{{ Auth::user()->name }}</b></span>
                                <small class="text-end font-w400">{{ Auth::user()->email }}</small>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('profile') }}" class="dropdown-item ai-icon">
                                <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span class="ms-2">Profile </span>
                            </a>
                            <a href="{{ route('logout') }}" class="dropdown-item ai-icon" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span class="ms-2">Logout </span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                    <li>
                        <a class="ai-icon" href="{{ route('home') }}" aria-expanded="false">
                            <i class="flaticon-025-dashboard"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    @can('payment')
                    <li class="{{ request()->routeIs('payment.index','payment.create','payment.edit', 'payment.show') ? 'mm-active' : '' }}">
                        <a class="ai-icon {{ request()->routeIs('payment.index','payment.create','payment.edit', 'payment.show') ? 'mm-active' : '' }}" href="{{ route('payment.index') }}" aria-expanded="false">
                            <i class="flaticon-072-printer"></i>
                            <span class="nav-text">Payments</span>
                        </a>
                    </li>
                    @endcan
                    @can('brand')
                    <li class="{{ request()->routeIs('brand.index','brand.create','brand.edit') ? 'mm-active' : '' }}">
                        <a class="ai-icon {{ request()->routeIs('brand.index','brand.create','brand.edit') ? 'mm-active' : '' }}" href="{{ route('brand.index') }}" aria-expanded="false">
                            <i class="flaticon-040-graph"></i>
                            <span class="nav-text">Brands</span>
                        </a>
                    </li>
                    @endcan
                    @can('merchant')
                    <li class="{{ request()->routeIs('merchant.index','merchant.create','merchant.edit') ? 'mm-active' : '' }}">
                        <a class="ai-icon {{ request()->routeIs('merchant.index','merchant.create','merchant.edit') ? 'mm-active' : '' }}"
                        href="{{ route('merchant.index') }}"
                        aria-expanded="false">
                            <i class="flaticon-052-inside"></i>
                            <span class="nav-text">Merchants</span>
                        </a>
                    </li>
                    @endcan

                    @can('role')
                    <li class="{{ request()->routeIs('roles.*') ? 'mm-active' : '' }}">
                        <a class="ai-icon" href="{{ route('roles.index') }}" aria-expanded="false">
                            <i class="fa-solid fa-gear fw-bold"></i>
                            <span class="nav-text">Roles</span>
                        </a>
                    </li>
                    @endcan
                    @can('user')
                    <li class="{{ request()->routeIs('users.*') ? 'mm-active' : '' }}">
                        <a class="ai-icon" href="{{ route('users.index') }}" aria-expanded="false">
                            <i class="fa-solid fa-user fw-bold"></i>
                            <span class="nav-text">Users</span>
                        </a>
                    </li>
                    @endcan
                </ul>
                <div class="copyright">
                    <p><strong>Admin Dashboard</strong> © {{ date('Y') }} All Rights Reserved</p>
                    <p class="fs-12">Made with <span class="flaticon-045-heart"></span> by Custom Developer</p>
                </div>
            </div>
        </div>
        <div class="content-body">
            @yield('content')
        </div>
        <div class="footer">
            <div class="copyright">
                <p>Copyright © Designed &amp; Developed by <a href="#" target="_blank">Custom Developer</a> {{ date('Y') }}</p>
            </div>
        </div>
    </div>
        <!-- ./wrapper -->
        <!-- Vendor JS -->
        <script src="{{ asset('js/global.min.js') }}"></script>
        <script src="{{ asset('js/chart.bundle.min.js') }}"></script>
        <script src="{{ asset('js/jquery.nice-select.min.js') }}"></script>
        <!-- Apex Chart -->
        <script src="{{ asset('js/apexchart.js') }}"></script>
        <script src="{{ asset('js/nouislider.min.js') }}"></script>
        <script src="{{ asset('js/wNumb.js') }}"></script>
        <!-- Dashboard 1 -->
        <script src="{{ asset('js/my-wallet.js') }}"></script>
        <script src="{{ asset('js/custom.min.js') }}"></script>
        <script src="{{ asset('js/dlabnav-init.js') }}"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
        @stack('scripts')
    </body>
</html>