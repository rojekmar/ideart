<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('site.privacy.title') }} — {{ config('app.name', 'Portfolio') }}</title>
    <meta name="robots" content="noindex, follow">
    <link rel="canonical" href="{{ localized_route('privacy') }}">
    @if($altUrl = alternate_locale_url())
        <link rel="alternate" hreflang="{{ app()->getLocale() === 'en' ? 'pl' : 'en' }}" href="{{ $altUrl }}">
    @endif
    @include('partials.fonts')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    @include('partials.cursor-glow')
    @include('partials.header')

    <main>
        <section class="hero hero--category">
            <div class="container">
                <a href="{{ localized_route('home') }}" class="breadcrumb">{!! __('site.category_page.breadcrumb') !!}</a>
                <h1 class="hero-title">{{ __('site.privacy.title') }}</h1>
                <p class="hero-lead">{{ __('site.privacy.updated') }}</p>
            </div>
        </section>

        <section class="section legal">
            <div class="container legal-content">
                <p>{{ __('site.privacy.intro') }}</p>

                <h2>{{ __('site.privacy.controller_title') }}</h2>
                <p>{{ __('site.privacy.controller_text') }}</p>

                <h2>{{ __('site.privacy.data_title') }}</h2>
                <p>{{ __('site.privacy.data_text') }}</p>

                <h2>{{ __('site.privacy.basis_title') }}</h2>
                <p>{{ __('site.privacy.basis_text') }}</p>

                <h2>{{ __('site.privacy.recipients_title') }}</h2>
                <p>{{ __('site.privacy.recipients_text') }}</p>

                <h2>{{ __('site.privacy.retention_title') }}</h2>
                <p>{{ __('site.privacy.retention_text') }}</p>

                <h2>{{ __('site.privacy.rights_title') }}</h2>
                <p>{{ __('site.privacy.rights_text') }}</p>

                <h2>{{ __('site.privacy.cookies_title') }}</h2>
                <h3>{{ __('site.privacy.cookies_necessary_title') }}</h3>
                <p>{{ __('site.privacy.cookies_necessary_text') }}</p>
                <h3>{{ __('site.privacy.cookies_analytics_title') }}</h3>
                <p>{{ __('site.privacy.cookies_analytics_text') }}</p>

                <h2>{{ __('site.privacy.changes_title') }}</h2>
                <p>{{ __('site.privacy.changes_text') }}</p>
            </div>
        </section>
    </main>

    @include('partials.footer')
    @include('partials.cookie-banner')

</body>
</html>
