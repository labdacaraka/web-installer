<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - {{ trans('Installation Setup') }}</title>
    <link href="{{ asset('vendor/web-installer/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
</head>
<body>
@php
    $installationTabs = [
        [
            'title' => 'Welcome!',
            'icon' => 'fa-solid fa-door-open',
            'route' => 'web-installer.welcome',
            'active' => request()->routeIs('web-installer.welcome'),
            'disabled' => false
        ],
        [
            'title' => 'Check Requirements',
            'icon' => 'fa-solid fa-list-check',
            'route' => 'web-installer.check-requirements',
            'active' => request()->routeIs('web-installer.check-requirements'),
            'disabled' => !session()->has('installation.purchases.verified')
        ],
        [
            'title' => 'Check Permissions',
            'icon' => 'fa-solid fa-folder',
            'route' => 'web-installer.check-permissions',
            'active' => request()->routeIs('web-installer.check-permissions'),
            'disabled' => !session()->has('installation.check_requirements')
        ],
        [
            'title' => 'App Settings',
            'icon' => 'fa-solid fa-gear',
            'route' => 'web-installer.app-settings',
            'active' => request()->routeIs('web-installer.app-settings'),
            'disabled' => !session()->has('installation.check_permissions')
        ],
        [
            'title' => 'Database',
            'icon' => 'fa-solid fa-database',
            'route' => 'web-installer.database-settings',
            'active' => request()->routeIs('web-installer.database-settings'),
            'disabled' => !session()->has('installation.app_settings') || !session()->has('installation.check_permissions'),
        ],
        [
            'title' => 'Final',
            'icon' => 'fa-solid fa-clipboard-list',
            'route' => 'web-installer.final',
            'active' => request()->routeIs('web-installer.final'),
            'disabled' => !session()->has('installation.database_settings') || !session()->has('installation.check_permissions') || !session()->has('installation.app_settings'),
        ],
    ];
@endphp
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="container mt-5">
                <div class="row d-flex justify-content-center align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <img src="{{ config('web-installer.installation_logo', asset('vendor/web-installer/images/logo-labdacaraka-colorful.png')) }}" alt="Logo" class="img-fluid text-center">
                        </div>
                        <div class="d-flex justify-content-center align-items-center mb-4">
                            @if(Session::has('success'))
                                <div class="alert alert-success w-100">{{ Session::get('success') }}</div>
                            @endif
                            @if(Session::has('error'))
                                <div class="alert alert-danger w-100">{{ Session::get('error') }}</div>
                            @endif
                        </div>
                        <ul class="nav nav-pills nav-fill justify-content-center mb-2">
                            @foreach($installationTabs as $tab)
                                <li class="nav-item">
                                    <a class="nav-link {{ $tab['active'] ? 'active' : '' }} {{ $tab['disabled'] ? 'disabled' : '' }}" aria-current="page" href="{{ route($tab['route']) }}" title="{{ $tab['title'] }}"><i class="{{ $tab['icon'] }}"></i></a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="card">
                            <div class="card-header text-bg-info py-2">
                                <h5 class="card-title m-0"> @yield('title') - {{ trans('Installation Setup') }}</h5>
                            </div>
                            <div class="card-body">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('vendor/web-installer/js/jquery.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
<script src="https://kit.fontawesome.com/79ec00d8d4.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: "bootstrap-5"
        });
    });
</script>

@stack('scripts')
</body>
</html>
