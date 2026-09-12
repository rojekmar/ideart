<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Portfolio') }} — Grafika 3D, 2D, animacja, film</title>
    <meta name="description" content="Grafika 3D i 2D, animacja, film, montaż, postprodukcja i fotografia. Tworzę obrazy, które przyciągają uwagę.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    @include('partials.cursor-glow')
    @include('partials.header')

    <main>

        {{-- ---------- HERO ---------- --}}
        <section class="hero">
            @php
                $heroSlides = get_images_from_dir('assets/grafiki_animacje/slider');
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
                <p class="eyebrow">Grafika 3D &middot; 2D &middot; animacja &middot; film</p>
                <h1 class="hero-title">Tworzę obrazy, które przyciągają wzrok i zostają w pamięci</h1>
                <p class="hero-lead">
                    Od modelu 3D i klatki animacji po zmontowany film i wyretuszowaną fotografię —
                    kompleksowo prowadzę projekt od pomysłu do gotowego materiału.
                </p>
                <div class="hero-actions">
                    <a href="#portfolio" class="btn">Zobacz moje realizacje</a>
                    <a href="#oferta" class="btn btn--ghost">Poznaj zakres usług</a>
                </div>
            </div>
        </section>

        {{-- ---------- OFERTA ---------- --}}
        <section id="oferta" class="section">
            <div class="container">
                <header class="section-head">
                    <p class="eyebrow">Oferta</p>
                    <h2 class="section-title">Co mogę dla Ciebie stworzyć?</h2>
                    <p class="section-text">
                        Cztery obszary, które łączę w spójną całość — zależnie od tego,
                        czego potrzebuje Twój projekt.
                    </p>
                </header>

                <div class="cards">
                    <a class="card" href="#portfolio-grafika-3d">
                        <span class="card-num">01</span>
                        <h3>Grafika 3D</h3>
                        <p>Modelowanie, teksturowanie, oświetlenie i rendering. Wizualizacje produktowe, packshoty, sceny i assety.</p>
                    </a>
                    <a class="card" href="#portfolio-grafika-2d">
                        <span class="card-num">02</span>
                        <h3>Grafika 2D</h3>
                        <p>Ilustracje, key visuale, plakaty, grafiki do social mediów, opakowania i materiały reklamowe.</p>
                    </a>
                    <a class="card" href="#portfolio-animacja-i-film">
                        <span class="card-num">03</span>
                        <h3>Animacja i Film</h3>
                        <p>Animacje 3D i 2D, motion design oraz realizacja filmowa — od scenariusza i planu zdjęciowego po spoty, intra i loopy.</p>
                    </a>
                    <a class="card" href="#portfolio-fotografia">
                        <span class="card-num">04</span>
                        <h3>Fotografia</h3>
                        <p>Sesje produktowe, wnętrzarskie i wizerunkowe wraz z retuszem i obróbką w wysokiej jakości.</p>
                    </a>
                </div>
            </div>
        </section>

        {{-- ---------- O MNIE ---------- --}}
        <section id="o-mnie" class="section section--alt">
            <div class="container about">
                <div>
                    <p class="eyebrow">O mnie</p>
                    <h2 class="section-title">Zadbam o to, żeby Twój projekt wyglądał profesjonalnie</h2>
                    <p class="section-text">
                        Od lat zajmuję się grafiką 3D i 2D, animacją oraz produkcją filmową.
                        Prowadzę projekt kompleksowo — od koncepcji, przez produkcję,
                        aż po montaż i postprodukcję — dbając o spójny styl i dopięty każdy detal.
                    </p>
                    <p class="section-text">
                        Pracuję bezpośrednio z klientem, bez pośredników. Dzięki temu ustalenia
                        są szybkie, a efekt zgodny z tym, co ustaliliśmy na starcie.
                    </p>
                </div>
                <ul class="facts">
                    <li><strong>20+ lat</strong><span>doświadczeń w zakresie szeroko pojętej grafiki i multimediów</span></li>
                    <li><strong>500+</strong><span>zrealizowanych projektów</span></li>
                    <li><strong>Bezpośredni</strong><span>kontakt na każdym etapie</span></li>
                    <li><strong>Kreatywność</strong><span>poparta warsztatem technicznym</span></li>
                </ul>
            </div>
        </section>

        {{-- ---------- DLACZEGO ---------- --}}
        <section class="section">
            <div class="container">
                <header class="section-head">
                    <p class="eyebrow">Dlaczego warto</p>
                    <h2 class="section-title">Współpraca, która się broni</h2>
                </header>
                <div class="reasons">
                    <div class="reason">
                        <h3>Estetyka, która pracuje na efekt</h3>
                        <p>Obraz ma nie tylko wyglądać dobrze — ma sprzedawać, budować markę i przyciągać widza.</p>
                    </div>
                    <div class="reason">
                        <h3>Jeden wykonawca, pełen zakres</h3>
                        <p>Grafika, animacja, zdjęcia, montaż i postprodukcja w jednym miejscu — bez rozproszonej odpowiedzialności.</p>
                    </div>
                    <div class="reason">
                        <h3>Projekty szyte na miarę</h3>
                        <p>Żadnych gotowców. Każda realizacja powstaje od zera pod konkretny cel i odbiorcę.</p>
                    </div>
                    <div class="reason">
                        <h3>Wsparcie po oddaniu materiału</h3>
                        <p>Zostaję do dyspozycji przy poprawkach, kolejnych formatach i rozwijaniu projektu.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ---------- PORTFOLIO ---------- --}}
        <section id="portfolio" class="section section--alt">
            <div class="container">
                <header class="section-head">
                    <p class="eyebrow">Portfolio</p>
                    <h2 class="section-title">Zobacz, co możemy razem stworzyć</h2>
                    <p class="section-text">Wybrane kadry z realizacji, podzielone tak samo jak oferta.</p>
                </header>

                <div class="portfolio-groups">
                    @foreach(portfolio_categories() as $index => $group)
                        @php($media = portfolio_preview_media($group, 3))
                        <div class="portfolio-group" id="portfolio-{{ $group['slug'] }}">
                            <div class="portfolio-group-head">
                                <span class="portfolio-group-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <h3 class="portfolio-group-title">{{ $group['title'] }}</h3>
                                <a href="{{ route('portfolio.category', $group['slug']) }}" class="portfolio-group-link">Zobacz całą galerię &rarr;</a>
                            </div>

                            @if(count($media))
                                <div class="gallery">
                                    @foreach($media as $item)
                                        <a href="{{ route('portfolio.category', $group['slug']) }}" class="gallery-item">
                                            @if($item['type'] === 'video')
                                                <video src="{{ $item['src'] }}" muted playsinline preload="metadata"></video>
                                                <span class="gallery-item-play" aria-hidden="true"></span>
                                            @else
                                                <img src="{{ $item['src'] }}" alt="{{ $group['title'] }}" loading="lazy">
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="portfolio-empty">Wkrótce nowe realizacje.</p>
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
                    <p class="eyebrow">Zaufali mi</p>
                    <h2 class="section-title">Liczby, które mówią same za siebie</h2>
                </header>
                <div class="stats">
                    <div class="stat"><strong>20+</strong><span>lat doświadczenia</span></div>
                    <div class="stat"><strong>100+</strong><span>zadowolonych klientów</span></div>
                    <div class="stat"><strong>500+</strong><span>ukończonych projektów</span></div>
                    <div class="stat"><strong>&#8734;</strong><span>wypitych kaw</span></div>
                </div>
            </div>
        </section>

        {{-- ---------- FAQ ---------- --}}
        <section id="faq" class="section">
            <div class="container">
                <header class="section-head">
                    <p class="eyebrow">FAQ</p>
                    <h2 class="section-title">Często zadawane pytania</h2>
                </header>
                <div class="faq">
                    <details>
                        <summary>Ile trwa realizacja projektu?</summary>
                        <p>Zależnie od zakresu: pojedyncza grafika to kilka dni, rozbudowana animacja lub film — od dwóch do kilku tygodni. Termin ustalamy na starcie.</p>
                    </details>
                    <details>
                        <summary>Jak wygląda współpraca krok po kroku?</summary>
                        <p>Brief i wycena, akceptacja koncepcji, produkcja, prezentacja wersji roboczej, runda poprawek i przekazanie plików w docelowych formatach.</p>
                    </details>
                    <details>
                        <summary>Czy przygotowujesz materiał 3D i 2D w jednym projekcie?</summary>
                        <p>Tak. Często łączę render 3D z grafiką 2D i motion designem, żeby całość tworzyła spójny materiał.</p>
                    </details>
                    <details>
                        <summary>Ile poprawek jest w cenie?</summary>
                        <p>Standardowo dwie rundy poprawek na etapie akceptacji. Większe zmiany kierunku wyceniam osobno.</p>
                    </details>
                    <details>
                        <summary>W jakich formatach dostanę gotowy materiał?</summary>
                        <p>Grafika: JPG, PNG, PDF, pliki źródłowe. Wideo: MP4 w wybranej rozdzielczości oraz warianty pod social media (pion, kwadrat, poziom).</p>
                    </details>
                    <details>
                        <summary>Czy robisz zdjęcia produktowe do renderów?</summary>
                        <p>Tak — sesja fotograficzna może być podstawą do dalszej obróbki, kompozycji lub materiału 3D.</p>
                    </details>
                </div>
            </div>
        </section>

        {{-- ---------- KONTAKT ---------- --}}
        <section id="kontakt" class="section section--alt">
            <div class="container contact">
                <div>
                    <p class="eyebrow">Kontakt</p>
                    <h2 class="section-title">Twój nowy projekt zaczyna się tutaj</h2>
                    <p class="section-text">
                        Napisz, co chcesz zrealizować — grafikę, animację, film czy sesję.
                        Odezwę się z pytaniami i wstępną wyceną.
                    </p>
                    <ul class="contact-list">
                        {{-- Podmień na swoje dane --}}
                        <li><span>E-mail</span><a href="mailto:rojekmar@gmail.com">rojekmar@gmail.com</a></li>
                        <li><span>Telefon</span><a href="tel:+48506992772">+48 506 992 772</a></li>
                    </ul>
                </div>

                <form class="contact-form" method="POST" action="{{ route('contact.store') }}">
                    @csrf

                    @if(session('contact_status') === 'success')
                        <p class="form-alert form-alert--success">Dziękuję! Wiadomość została wysłana — odezwę się wkrótce.</p>
                    @endif

                    @if($errors->any())
                        <p class="form-alert form-alert--error">
                            Popraw poniższe pola:
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </p>
                    @endif

                    {{-- Pole-pułapka na boty — ukryte dla ludzi przez CSS --}}
                    <input type="text" name="website" tabindex="-1" autocomplete="off" class="form-honeypot">

                    <label>Imię
                        <input type="text" name="name" value="{{ old('name') }}" autocomplete="name" required>
                    </label>
                    <label>E-mail
                        <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                    </label>
                    <label>Telefon
                        <input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel">
                    </label>
                    <label>Rodzaj projektu
                        <select name="type">
                            @foreach(['Grafika 3D', 'Grafika 2D', 'Animacja i Film', 'Fotografia'] as $option)
                                <option @selected(old('type') === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Wiadomość
                        <textarea name="message" rows="4" required>{{ old('message') }}</textarea>
                    </label>
                    <button type="submit" class="btn">Wyślij zapytanie</button>
                    <p class="form-note">Wysyłając formularz, zgadzasz się na kontakt w sprawie zapytania.</p>
                </form>
            </div>
        </section>

    </main>

    @include('partials.footer')

</body>
</html>
