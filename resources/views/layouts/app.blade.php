<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SK ProgramHub') · SK ProgramHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="site-header">
        <div class="container nav-wrap">
            <a class="brand" href="{{ route('home') }}" aria-label="SK ProgramHub home">
                <span class="brand-mark">SK</span>
                <span class="brand-copy"><strong>ProgramHub</strong><small>YOUTH. COMMUNITY. ACTION.</small></span>
            </a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-navigation" data-menu-toggle>Menu</button>
            <nav class="main-nav" id="main-navigation" data-menu>
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('public.programs') }}">Programs</a>
                <a href="{{ route('public.events') }}">Events</a>
                <a href="{{ route('public.announcements') }}">Announcements</a>
                <a href="{{ route('home') }}#about">About</a>
                @auth
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="button button-small" href="{{ route('profile.edit') }}">{{ auth()->user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-link" type="submit">Log out</button></form>
                @else
                    <a href="{{ route('login') }}">Log in</a>
                    <a class="button button-small" href="{{ route('register') }}">Create account</a>
                @endauth
            </nav>
        </div>
    </header>

    @if (session('status'))
        <div class="container flash flash-success" role="status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="container flash flash-error" role="alert">
            <strong>Please check the highlighted fields.</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <main>@yield('content')</main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <a class="brand brand-light" href="{{ route('home') }}"><span class="brand-mark">SK</span><span class="brand-copy"><strong>ProgramHub</strong><small>YOUTH. COMMUNITY. ACTION.</small></span></a>
            <p>Helping local youth councils turn good ideas into community action. Contact the council listed on each program page for local details.</p>
            <div class="footer-links"><a href="{{ route('home') }}#contact">Contact</a><a href="{{ route('home') }}#privacy">Privacy</a><a href="{{ route('home') }}#terms">Terms</a></div>
            <small>© {{ now()->year }} SK ProgramHub. Built for stronger communities.</small>
        </div>
    </footer>
</body>
</html>
