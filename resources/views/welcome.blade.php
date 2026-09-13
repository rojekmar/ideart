<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Portfolio') }} — {{ __('site.meta.title_suffix') }}</title>
    <meta name="description" content="{{ __('site.meta.description') }}">
    <link rel="canonical" href="{{ localized_route('home') }}">
    <link rel="alternate" hreflang="pl" href="{{ route('home') }}">
    <link rel="alternate" hreflang="en" href="{{ route('en.home') }}">
    <link rel="alternate" hreflang="x-default" href="{{ route('home') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ localized_route('home') }}">
    <meta property="og:site_name" content="{{ config('app.name', 'Portfolio') }}">
    <meta property="og:title" content="{{ config('app.name', 'Portfolio') }} — {{ __('site.meta.title_suffix') }}">
    <meta property="og:description" content="{{ __('site.meta.description') }}">
    <meta property="og:image" content="{{ asset('assets/images/logo.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    @php
        // Dane strukturalne (Schema.org) — nie widać ich na stronie, ale
        // pomagają Google zrozumieć, kim jesteśmy (może to poskutkować np.
        // bogatszymi wynikami wyszukiwania z danymi kontaktowymi).
        $businessSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => config('app.name', 'IDEART'),
            'description' => __('site.meta.description'),
            'url' => localized_route('home'),
            'image' => asset('assets/images/logo.png'),
            'email' => 'rojekmar@gmail.com',
            'telephone' => '+48506992772',
            'areaServed' => 'PL',
            'founder' => [
                '@type' => 'Person',
                'name' => 'Marcin Rojek',
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($businessSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    @include('partials.cursor-glow')
    @include('partials.header')

    <main>

        {{-- ---------- HERO ---------- --}}
        <section class="hero">
            @php
                // Kolejność losowa (inna przy każdym wczytaniu strony) —
                // dotyczy wyłącznie tego suwaka, reszta galerii nadal sortuje
                // po dacie dodania (patrz get_images_from_dir()).
                $heroSlides = get_images_from_dir('assets/grafiki_animacje/slider');
                shuffle($heroSlides);
                $heroSlideSeconds = 4; // tempo zmiany obrazów w suwaku
                $heroSlideCount = count($heroSlides);
                $heroCycleSeconds = $heroSlideCount * $heroSlideSeconds;
            @endphp
            @if($heroSlideCount)
                {{--
                    Procenty klatek kluczowych zależą od liczby slajdów (każdy
                    dostaje równą działkę cyklu), więc liczymy je tutaj zamiast
                    trzymać na sztywno w app.css — suwak działa poprawnie
                    niezależnie od tego, ile plików jest w folderze slider.
                --}}
                <style>
                    @keyframes hero-slide-cycle {
                        0% { opacity: 0; }
                        {{ round(0.25 * $heroSlideSeconds / $heroCycleSeconds * 100, 4) }}% { opacity: 1; }
                        {{ round(0.95 * $heroSlideSeconds / $heroCycleSeconds * 100, 4) }}% { opacity: 1; }
                        {{ round(1.25 * $heroSlideSeconds / $heroCycleSeconds * 100, 4) }}% { opacity: 0; }
                        100% { opacity: 0; }
                    }
                </style>
                <div class="hero-slider" aria-hidden="true">
                    @foreach($heroSlides as $index => $slide)
                        <img
                            class="hero-slide"
                            src="{{ $slide }}"
                            alt=""
                            style="animation-duration: {{ $heroCycleSeconds }}s; animation-delay: {{ $index * $heroSlideSeconds }}s"
                            @if($index === 0) loading="eager" fetchpriority="high" @else loading="lazy" @endif
                        >
                    @endforeach
                </div>
                <div class="hero-overlay" aria-hidden="true"></div>
            @endif

            <div class="container">
                <p class="eyebrow">{!! __('site.hero.eyebrow') !!}</p>
                <h1 class="hero-title">{{ __('site.hero.title') }}</h1>
                <p class="hero-lead">
                    {{ __('site.hero.lead') }}
                </p>
                <div class="hero-actions">
                    <a href="#portfolio" class="btn">{{ __('site.hero.cta_primary') }}</a>
                    <a href="#oferta" class="btn btn--ghost">{{ __('site.hero.cta_secondary') }}</a>
                </div>
            </div>
        </section>

        {{-- ---------- OFERTA ---------- --}}
        <section id="oferta" class="section">
            <div class="container">
                <header class="section-head">
                    <p class="eyebrow">{{ __('site.oferta.eyebrow') }}</p>
                    <h2 class="section-title">{{ __('site.oferta.title') }}</h2>
                    <p class="section-text">
                        {{ __('site.oferta.lead') }}
                    </p>
                </header>

                <div class="cards">
                    <a class="card" href="#portfolio-grafika-3d">
                        <span class="card-num">01</span>
                        <h3>{{ __('site.oferta.card_grafika_3d_title') }}</h3>
                        <p>{{ __('site.oferta.card_grafika_3d_text') }}</p>
                    </a>
                    <a class="card" href="#portfolio-grafika-2d">
                        <span class="card-num">02</span>
                        <h3>{{ __('site.oferta.card_grafika_2d_title') }}</h3>
                        <p>{{ __('site.oferta.card_grafika_2d_text') }}</p>
                    </a>
                    <a class="card" href="#portfolio-animacja-i-film">
                        <span class="card-num">03</span>
                        <h3>{{ __('site.oferta.card_animacja_title') }}</h3>
                        <p>{{ __('site.oferta.card_animacja_text') }}</p>
                    </a>
                    <a class="card" href="#portfolio-360-interaktywnie">
                        <span class="card-num">04</span>
                        <h3>{{ __('site.oferta.card_360_title') }}</h3>
                        <p>{{ __('site.oferta.card_360_text') }}</p>
                    </a>
                    <a class="card" href="#portfolio-fotografia">
                        <span class="card-num">05</span>
                        <h3>{{ __('site.oferta.card_fotografia_title') }}</h3>
                        <p>{{ __('site.oferta.card_fotografia_text') }}</p>
                    </a>
                </div>
            </div>
        </section>

        {{-- ---------- O MNIE ---------- --}}
        <section id="o-mnie" class="section section--alt">
            <div class="container about">
                <div>
                    <p class="eyebrow">{{ __('site.about.eyebrow') }}</p>
                    <h2 class="section-title">{{ __('site.about.title') }}</h2>
                    <p class="section-text">
                        {{ __('site.about.text_1') }}
                    </p>
                    <p class="section-text">
                        {{ __('site.about.text_2') }}
                    </p>
                </div>
                <ul class="facts">
                    <li><strong>{{ __('site.about.fact_1_num') }}</strong><span>{{ __('site.about.fact_1_text') }}</span></li>
                    <li><strong>{{ __('site.about.fact_2_num') }}</strong><span>{{ __('site.about.fact_2_text') }}</span></li>
                    <li><strong>{{ __('site.about.fact_3_num') }}</strong><span>{{ __('site.about.fact_3_text') }}</span></li>
                    <li><strong>{{ __('site.about.fact_4_num') }}</strong><span>{{ __('site.about.fact_4_text') }}</span></li>
                </ul>
            </div>
        </section>

        {{-- ---------- DLACZEGO ---------- --}}
        <section class="section">
            <div class="container">
                <header class="section-head">
                    <p class="eyebrow">{{ __('site.why.eyebrow') }}</p>
                    <h2 class="section-title">{{ __('site.why.title') }}</h2>
                </header>
                <div class="reasons">
                    <div class="reason">
                        <h3>{{ __('site.why.reason_1_title') }}</h3>
                        <p>{{ __('site.why.reason_1_text') }}</p>
                    </div>
                    <div class="reason">
                        <h3>{{ __('site.why.reason_2_title') }}</h3>
                        <p>{{ __('site.why.reason_2_text') }}</p>
                    </div>
                    <div class="reason">
                        <h3>{{ __('site.why.reason_3_title') }}</h3>
                        <p>{{ __('site.why.reason_3_text') }}</p>
                    </div>
                    <div class="reason">
                        <h3>{{ __('site.why.reason_4_title') }}</h3>
                        <p>{{ __('site.why.reason_4_text') }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ---------- PORTFOLIO ---------- --}}
        <section id="portfolio" class="section section--alt">
            <div class="container">
                <header class="section-head">
                    <p class="eyebrow">{{ __('site.portfolio_section.eyebrow') }}</p>
                    <h2 class="section-title">{{ __('site.portfolio_section.title') }}</h2>
                    <p class="section-text">{{ __('site.portfolio_section.lead') }}</p>
                </header>

                <div class="portfolio-groups">
                    @foreach(portfolio_categories() as $index => $group)
                        @php($media = portfolio_preview_media($group, 3))
                        <div class="portfolio-group" id="portfolio-{{ $group['slug'] }}">
                            <div class="portfolio-group-head">
                                <span class="portfolio-group-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <h3 class="portfolio-group-title">{{ $group['title'] }}</h3>
                                <a href="{{ localized_route('portfolio.category', $group['slug']) }}" class="portfolio-group-link">{!! __('site.portfolio_section.see_gallery') !!}</a>
                            </div>

                            @if(count($media))
                                <div class="gallery">
                                    @foreach($media as $item)
                                        <a href="{{ localized_route('portfolio.category', $group['slug']) }}" class="gallery-item">
                                            @if($item['type'] === 'video')
                                                <video src="{{ $item['src'] }}" muted playsinline preload="metadata"></video>
                                                <span class="gallery-item-play" aria-hidden="true"></span>
                                            @elseif($item['type'] === '360')
                                                <img src="{{ $item['thumb'] }}" alt="{{ $group['title'] }}" loading="lazy">
                                                <span class="gallery-item-badge" aria-hidden="true">360&deg;</span>
                                            @else
                                                <img src="{{ $item['src'] }}" alt="{{ $group['title'] }}" loading="lazy">
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="portfolio-empty">{{ __('site.portfolio_section.coming_soon') }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ---------- STATYSTYKI ---------- --}}
        <section class="section section--alt stats-section">
            <div class="container">
                <header class="section-head">
                    <p class="eyebrow">{{ __('site.stats.eyebrow') }}</p>
                    <h2 class="section-title">{{ __('site.stats.title') }}</h2>
                </header>
                <div class="stats">
                    <div class="stat"><strong>{{ __('site.stats.stat_1_num') }}</strong><span>{{ __('site.stats.stat_1_text') }}</span></div>
                    <div class="stat"><strong>{{ __('site.stats.stat_2_num') }}</strong><span>{{ __('site.stats.stat_2_text') }}</span></div>
                    <div class="stat"><strong>{{ __('site.stats.stat_3_num') }}</strong><span>{{ __('site.stats.stat_3_text') }}</span></div>
                    <div class="stat"><strong>{!! __('site.stats.stat_4_num') !!}</strong><span>{{ __('site.stats.stat_4_text') }}</span></div>
                </div>
            </div>
        </section>

        {{-- ---------- FAQ ---------- --}}
        <section id="faq" class="section">
            <div class="container">
                <header class="section-head">
                    <p class="eyebrow">{{ __('site.faq.eyebrow') }}</p>
                    <h2 class="section-title">{{ __('site.faq.title') }}</h2>
                </header>
                <div class="faq">
                    @for($i = 1; $i <= 6; $i++)
                        <details>
                            <summary>{{ __('site.faq.q'.$i) }}</summary>
                            <p>{{ __('site.faq.a'.$i) }}</p>
                        </details>
                    @endfor
                </div>
            </div>
        </section>

        {{-- ---------- KONTAKT ---------- --}}
        <section id="kontakt" class="section section--alt">
            <div class="container contact">
                <div>
                    <p class="eyebrow">{{ __('site.contact.eyebrow') }}</p>
                    <h2 class="section-title">{{ __('site.contact.title') }}</h2>
                    <p class="section-text">
                        {{ __('site.contact.lead') }}
                    </p>
                    <ul class="contact-list">
                        <li><span>{{ __('site.contact.email_label') }}</span><a href="mailto:rojekmar@gmail.com">rojekmar@gmail.com</a></li>
                        <li><span>{{ __('site.contact.phone_label') }}</span><a href="tel:+48506992772">+48 506 992 772</a></li>
                    </ul>
                </div>

                <form class="contact-form" method="POST" action="{{ localized_route('contact.store') }}">
                    @csrf

                    @if(session('contact_status') === 'success')
                        <p class="form-alert form-alert--success">{{ __('site.contact.success') }}</p>
                    @endif

                    @if($errors->any())
                        <p class="form-alert form-alert--error">
                            {{ __('site.contact.error_intro') }}
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </p>
                    @endif

                    {{-- Pole-pułapka na boty — ukryte dla ludzi przez CSS --}}
                    <input type="text" name="website" tabindex="-1" autocomplete="off" class="form-honeypot">

                    <label>{{ __('site.contact.field_name') }}
                        <input type="text" name="name" value="{{ old('name') }}" autocomplete="name" required>
                    </label>
                    <label>{{ __('site.contact.field_email') }}
                        <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                    </label>
                    <label>{{ __('site.contact.field_phone') }}
                        <input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel">
                    </label>
                    <label>{{ __('site.contact.field_type') }}
                        <select name="type">
                            @foreach(__('site.contact.type_options') as $option)
                                <option @selected(old('type') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>{{ __('site.contact.field_message') }}
                        <textarea name="message" rows="4" required>{{ old('message') }}</textarea>
                    </label>
                    <button type="submit" class="btn">{{ __('site.contact.submit') }}</button>
                    <p class="form-note">{{ __('site.contact.note') }}</p>
                </form>
            </div>
        </section>

    </main>

    @include('partials.footer')
    @include('partials.cookie-banner')

</body>
</html>
