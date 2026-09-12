@php
    $isHome = request()->routeIs('home') || request()->routeIs('en.home');
    $navHref = fn (string $anchor) => $isHome ? "#{$anchor}" : localized_route('home')."#{$anchor}";
    $altUrl = alternate_locale_url();
@endphp
<header class="site-header" id="top">
    <div class="container header-inner">
        <a href="{{ localized_route('home') }}" class="logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
        </a>

        <input type="checkbox" id="nav-toggle" class="nav-toggle">
        <label for="nav-toggle" class="nav-burger" aria-label="Menu">
            <span></span><span></span><span></span>
        </label>

        <nav class="site-nav">
            <a href="{{ $navHref('oferta') }}">{{ __('site.nav.oferta') }}</a>
            <a href="{{ $navHref('o-mnie') }}">{{ __('site.nav.o_mnie') }}</a>
            <a href="{{ $navHref('portfolio') }}">{{ __('site.nav.portfolio') }}</a>
            <a href="{{ $navHref('faq') }}">{{ __('site.nav.faq') }}</a>
            <a href="{{ $navHref('kontakt') }}">{{ __('site.nav.kontakt') }}</a>
            <a href="{{ $navHref('kontakt') }}" class="btn btn--small">{{ __('site.nav.cta') }}</a>
            @if($altUrl)
                <a href="{{ $altUrl }}" class="lang-switch" aria-label="{{ __('site.lang_switch.aria') }}">{{ __('site.lang_switch.label') }}</a>
            @endif
        </nav>
    </div>
</header>
