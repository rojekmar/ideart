@php
    $isHome = request()->routeIs('home');
    $navHref = fn (string $anchor) => $isHome ? "#{$anchor}" : route('home') . "#{$anchor}";
@endphp
<header class="site-header" id="top">
    <div class="container header-inner">
        <a href="{{ route('home') }}" class="logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
        </a>

        <input type="checkbox" id="nav-toggle" class="nav-toggle">
        <label for="nav-toggle" class="nav-burger" aria-label="Menu">
            <span></span><span></span><span></span>
        </label>

        <nav class="site-nav">
            <a href="{{ $navHref('oferta') }}">Oferta</a>
            <a href="{{ $navHref('o-mnie') }}">O mnie</a>
            <a href="{{ $navHref('portfolio') }}">Portfolio</a>
            <a href="{{ $navHref('faq') }}">FAQ</a>
            <a href="{{ $navHref('kontakt') }}">Kontakt</a>
            <a href="{{ $navHref('kontakt') }}" class="btn btn--small">Wyceń projekt</a>
        </nav>
    </div>
</header>
