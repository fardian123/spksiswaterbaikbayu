<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="bg-light">
    <div class="d-md-flex">
        @include('layouts.partials.sidebar')

        <div class="flex-grow-1" style="min-width:0">
            <nav class="navbar bg-white border-bottom px-3 sticky-top">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary btn-sm d-md-none" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <span class="fw-semibold">@yield('title')</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted small d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger">Keluar</button>
                    </form>
                </div>
            </nav>

            <main class="p-3 p-md-4">
                @include('layouts.partials.alert')
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>

</html>