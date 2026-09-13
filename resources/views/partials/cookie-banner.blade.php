{{--
    Baner zgody na cookies — pokazuje się tylko wtedy, gdy w ogóle jest
    coś ponad niezbędne ciasteczka do zaakceptowania (czyli gdy skonfigurowany
    jest Google Analytics — patrz config('services.google_analytics.id')).
    Bez tego strona używa wyłącznie ciasteczek sesyjnych/CSRF, które nie
    wymagają zgody, więc baner byłby mylący.
--}}
@if(config('services.google_analytics.id'))
    <div class="cookie-banner" data-cookie-banner hidden>
        <div class="container cookie-banner-inner">
            <p>{{ __('site.cookies.text') }} <a href="{{ localized_route('privacy') }}">{{ __('site.cookies.link') }}</a></p>
            <div class="cookie-banner-actions">
                <button type="button" data-cookie-reject class="btn btn--ghost btn--small">{{ __('site.cookies.reject') }}</button>
                <button type="button" data-cookie-accept class="btn btn--small">{{ __('site.cookies.accept') }}</button>
            </div>
        </div>
    </div>
    <script>window.__gaId = @json(config('services.google_analytics.id'));</script>
@endif
