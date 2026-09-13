@php
    // Pierwsza "grafika" z galerii tej kategorii — używana jako tło hero
    // (patrz sekcja INTRO niżej) i jako obraz w tagach Open Graph. Dla '360'
    // bierzemy miniaturkę. Kategorie złożone wyłącznie z wideo (np. Animacja
    // i Film) dostają zamiast zdjęcia pierwszą klatkę pierwszego filmu (dla
    // hero — do Open Graph film się nie nadaje, tam zostaje logo jako fallback).
    $heroBgItem = collect($media)->first(fn ($item) => in_array($item['type'], ['image', '360']));
    $heroBgSrc = $heroBgItem ? ($heroBgItem['type'] === '360' ? $heroBgItem['thumb'] : $heroBgItem['src']) : null;
    $heroBgVideo = $heroBgSrc ? null : collect($media)->first(fn ($item) => $item['type'] === 'video');

    // Dane strukturalne (Schema.org) — "okruszkowa" ścieżka, żeby Google mógł
    // pokazać w wynikach czytelną hierarchię (Strona główna > Portfolio >
    // {kategoria}) zamiast gołego adresu URL.
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => __('site.category_page.breadcrumb_home'), 'item' => localized_route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => __('site.category_page.breadcrumb_portfolio'), 'item' => localized_route('home').'#portfolio'],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $category['title'], 'item' => localized_route('portfolio.category', $category['slug'])],
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $category['title'] }} — {{ config('app.name', 'Portfolio') }}</title>
    <meta name="description" content="{{ $category['description'] }}">
    <link rel="canonical" href="{{ localized_route('portfolio.category', $category['slug']) }}">
    @if($altUrl = alternate_locale_url())
        <link rel="alternate" hreflang="{{ app()->getLocale() === 'en' ? 'pl' : 'en' }}" href="{{ $altUrl }}">
        <link rel="alternate" hreflang="{{ app()->getLocale() }}" href="{{ localized_route('portfolio.category', $category['slug']) }}">
    @endif
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ localized_route('portfolio.category', $category['slug']) }}">
    <meta property="og:site_name" content="{{ config('app.name', 'Portfolio') }}">
    <meta property="og:title" content="{{ $category['title'] }} — {{ config('app.name', 'Portfolio') }}">
    <meta property="og:description" content="{{ $category['description'] }}">
    <meta property="og:image" content="{{ $heroBgSrc ?? asset('assets/images/logo.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @include('partials.fonts')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    @include('partials.cursor-glow')
    @include('partials.header')

    <main>

        {{-- ---------- INTRO ---------- --}}
        <section class="hero hero--category">
            {{-- $heroBgSrc / $heroBgVideo policzone na górze pliku (patrz przed <head> — potrzebne też do Open Graph) --}}
            @if($heroBgSrc)
                <div class="hero-slider" aria-hidden="true">
                    <img class="hero-slide hero-slide--static" src="{{ $heroBgSrc }}" alt="" loading="eager" fetchpriority="high">
                </div>
                <div class="hero-overlay" aria-hidden="true"></div>
            @elseif($heroBgVideo)
                <div class="hero-slider" aria-hidden="true">
                    <video class="hero-slide hero-slide--static" src="{{ $heroBgVideo['src'] }}" muted playsinline preload="metadata"></video>
                </div>
                <div class="hero-overlay" aria-hidden="true"></div>
            @endif
            <div class="container">
                <a href="{{ localized_route('home') }}#portfolio" class="breadcrumb">{!! __('site.category_page.breadcrumb') !!}</a>
                <p class="eyebrow">{{ __('site.category_page.eyebrow') }}</p>
                <h1 class="hero-title">{{ $category['title'] }}</h1>
                <p class="hero-lead">{{ $category['description'] }}</p>
            </div>
        </section>

        {{-- ---------- PRZEŁĄCZNIK KATEGORII ---------- --}}
        <nav class="category-switcher">
            <div class="container category-switcher-inner">
                @foreach($categories as $item)
                    <a
                        href="{{ localized_route('portfolio.category', $item['slug']) }}"
                        class="category-pill {{ $item['slug'] === $category['slug'] ? 'is-active' : '' }}"
                    >{{ $item['title'] }}</a>
                @endforeach
            </div>
        </nav>

        {{-- ---------- GALERIA ---------- --}}
        <section class="section">
            <div class="container">
                @if(count($media))
                    @if(!empty($category['groups']))
                        <div class="portfolio-groups">
                            @foreach($category['groups'] as $group)
                                <div class="portfolio-group">
                                    <div class="portfolio-group-head">
                                        <h2 class="portfolio-group-title">{{ $group['title'] }}</h2>
                                    </div>

                                    @include('partials.media-gallery', ['items' => portfolio_group_media($group), 'label' => $group['title']])
                                </div>
                            @endforeach
                        </div>
                    @else
                        @include('partials.media-gallery', ['items' => $media, 'label' => $category['title']])
                    @endif
                @else
                    <p class="portfolio-empty">{{ __('site.category_page.coming_soon') }}</p>
                @endif
            </div>
        </section>

        @if(count($media))
            {{-- ---------- LIGHTBOX ---------- --}}
            <div class="lightbox" data-lightbox-overlay hidden>
                <button type="button" class="lightbox-close" data-lightbox-close aria-label="{{ __('site.category_page.lightbox_close') }}">&times;</button>
                <button type="button" class="lightbox-arrow lightbox-arrow--prev" data-lightbox-prev aria-label="{{ __('site.category_page.lightbox_prev') }}">&larr;</button>
                <div class="lightbox-stage" data-lightbox-stage></div>
                <button type="button" class="lightbox-arrow lightbox-arrow--next" data-lightbox-next aria-label="{{ __('site.category_page.lightbox_next') }}">&rarr;</button>
            </div>
        @endif

        {{-- ---------- CTA ---------- --}}
        <section class="section section--alt section--center">
            <div class="container">
                <h2 class="section-title">{{ __('site.category_page.cta_title') }}</h2>
                <p class="section-text">{{ __('site.category_page.cta_text') }}</p>
                <a href="{{ localized_route('home') }}#kontakt" class="btn">{{ __('site.category_page.cta_button') }}</a>
            </div>
        </section>

    </main>

    @include('partials.footer')
    @include('partials.cookie-banner')

</body>
</html>
