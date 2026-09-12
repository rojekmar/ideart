@php
    // Pierwsza "grafika" z galerii tej kategorii — używana jako tło hero
    // (patrz sekcja INTRO niżej) i jako obraz w tagach Open Graph. Dla '360'
    // bierzemy miniaturkę. Kategorie złożone wyłącznie z wideo (np. Animacja
    // i Film) dostają zamiast zdjęcia pierwszą klatkę pierwszego filmu (dla
    // hero — do Open Graph film się nie nadaje, tam zostaje logo jako fallback).
    $heroBgItem = collect($media)->first(fn ($item) => in_array($item['type'], ['image', '360']));
    $heroBgSrc = $heroBgItem ? ($heroBgItem['type'] === '360' ? $heroBgItem['thumb'] : $heroBgItem['src']) : null;
    $heroBgVideo = $heroBgSrc ? null : collect($media)->first(fn ($item) => $item['type'] === 'video');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $category['title'] }} — {{ config('app.name', 'Portfolio') }}</title>
    <meta name="description" content="{{ $category['description'] }}">
    <link rel="canonical" href="{{ route('portfolio.category', $category['slug']) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('portfolio.category', $category['slug']) }}">
    <meta property="og:site_name" content="{{ config('app.name', 'Portfolio') }}">
    <meta property="og:title" content="{{ $category['title'] }} — {{ config('app.name', 'Portfolio') }}">
    <meta property="og:description" content="{{ $category['description'] }}">
    <meta property="og:image" content="{{ $heroBgSrc ?? asset('assets/images/logo.png') }}">
    <meta name="twitter:card" content="summary_large_image">
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
                <a href="{{ route('home') }}#portfolio" class="breadcrumb">&larr; Wróć do Portfolio</a>
                <p class="eyebrow">Portfolio</p>
                <h1 class="hero-title">{{ $category['title'] }}</h1>
                <p class="hero-lead">{{ $category['description'] }}</p>
            </div>
        </section>

        {{-- ---------- PRZEŁĄCZNIK KATEGORII ---------- --}}
        <nav class="category-switcher">
            <div class="container category-switcher-inner">
                @foreach($categories as $item)
                    <a
                        href="{{ route('portfolio.category', $item['slug']) }}"
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
                    <p class="portfolio-empty">Wkrótce nowe realizacje w tej kategorii.</p>
                @endif
            </div>
        </section>

        @if(count($media))
            {{-- ---------- LIGHTBOX ---------- --}}
            <div class="lightbox" data-lightbox-overlay hidden>
                <button type="button" class="lightbox-close" data-lightbox-close aria-label="Zamknij podgląd">&times;</button>
                <button type="button" class="lightbox-arrow lightbox-arrow--prev" data-lightbox-prev aria-label="Poprzednie zdjęcie">&larr;</button>
                <div class="lightbox-stage" data-lightbox-stage></div>
                <button type="button" class="lightbox-arrow lightbox-arrow--next" data-lightbox-next aria-label="Następne zdjęcie">&rarr;</button>
            </div>
        @endif

        {{-- ---------- CTA ---------- --}}
        <section class="section section--alt section--center">
            <div class="container">
                <h2 class="section-title">Podoba Ci się ten styl?</h2>
                <p class="section-text">Napisz, jaki projekt masz na myśli — odezwę się z wyceną.</p>
                <a href="{{ route('home') }}#kontakt" class="btn">Napisz wiadomość</a>
            </div>
        </section>

    </main>

    @include('partials.footer')

</body>
</html>
