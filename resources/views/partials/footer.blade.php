@php
    $isHome = request()->routeIs('home');
    $navHref = fn (string $anchor) => $isHome ? "#{$anchor}" : route('home') . "#{$anchor}";
@endphp
<footer class="site-footer">
    <div class="container footer-inner">
        <a href="{{ route('home') }}" class="logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
        </a>
        <p class="footer-text">
            Grafika 3D i 2D, animacja, film i fotografia.
        </p>
        <nav class="footer-nav">
            <a href="{{ $navHref('oferta') }}">Oferta</a>
            <a href="{{ $navHref('portfolio') }}">Portfolio</a>
            <a href="{{ $navHref('faq') }}">FAQ</a>
            <a href="{{ $navHref('kontakt') }}">Kontakt</a>
        </nav>
    </div>
    <p class="footer-copy">&copy; {{ date('Y') }} — wszystkie prawa zastrzeżone.</p>
</footer>
