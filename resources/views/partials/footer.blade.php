@php
    $isHome = request()->routeIs('home') || request()->routeIs('en.home');
    $navHref = fn (string $anchor) => $isHome ? "#{$anchor}" : localized_route('home')."#{$anchor}";
@endphp
<footer class="site-footer">
    <div class="container footer-inner">
        <a href="{{ localized_route('home') }}" class="logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
        </a>
        <p class="footer-text">
            {{ __('site.footer.tagline') }}
        </p>
        <nav class="footer-nav">
            <a href="{{ $navHref('oferta') }}">{{ __('site.nav.oferta') }}</a>
            <a href="{{ $navHref('portfolio') }}">{{ __('site.nav.portfolio') }}</a>
            <a href="{{ $navHref('faq') }}">{{ __('site.nav.faq') }}</a>
            <a href="{{ $navHref('kontakt') }}">{{ __('site.nav.kontakt') }}</a>
            <a href="{{ localized_route('privacy') }}">{{ __('site.footer.privacy_link') }}</a>
            @if(config('services.google_analytics.id'))
                <a href="#" data-cookie-settings>{{ __('site.footer.cookie_settings') }}</a>
            @endif
        </nav>
    </div>
    <p class="footer-copy">&copy; {{ date('Y') }} — {{ __('site.footer.copy') }}</p>
</footer>
