{{--
    Siatka miniaturek (zdjęcia + filmy + prezentacje 360°) połączona ze
    wspólnym lightboxem. Oczekiwane zmienne: $items (lista
    ['type' => 'image'|'video'|'360', 'src' => url, 'thumb'? => url]),
    $label (etykieta grupy). Dla typu '360' miniaturką jest 'thumb',
    a 'src' (plik .html z własnym viewerem) otwiera się w lightboxie jako iframe.
--}}
@if(count($items))
    <div class="gallery gallery--thumbs" data-lightbox>
        @foreach($items as $item)
            @php($kind = $item['type'] === 'video' ? __('site.gallery.kind_video') : ($item['type'] === '360' ? __('site.gallery.kind_360') : __('site.gallery.kind_image')))
            <button
                type="button"
                class="gallery-item{{ $item['type'] === 'video' ? ' gallery-item--video' : '' }}{{ $item['type'] === '360' ? ' gallery-item--360' : '' }}"
                data-lightbox-item
                data-type="{{ $item['type'] }}"
                data-src="{{ $item['src'] }}"
                data-title="{{ $label }} — {{ $kind }} {{ $loop->iteration }}"
            >
                @if($item['type'] === 'video')
                    <video src="{{ $item['src'] }}" muted playsinline preload="metadata"></video>
                    <span class="gallery-item-play" aria-hidden="true"></span>
                @elseif($item['type'] === '360')
                    <img src="{{ $item['thumb'] }}" alt="{{ $label }} — {{ __('site.gallery.kind_360') }} {{ $loop->iteration }}" loading="lazy">
                    <span class="gallery-item-badge" aria-hidden="true">360&deg;</span>
                @else
                    <img src="{{ $item['src'] }}" alt="{{ $label }} — {{ __('site.gallery.kind_image') }} {{ $loop->iteration }}" loading="lazy">
                @endif
            </button>
        @endforeach
    </div>
@else
    <p class="portfolio-empty">{{ __('site.portfolio_section.coming_soon') }}</p>
@endif
