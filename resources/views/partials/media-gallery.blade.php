{{--
    Siatka miniaturek (zdjęcia + filmy) połączona ze wspólnym lightboxem.
    Oczekiwane zmienne: $items (lista ['type' => 'image'|'video', 'src' => url]), $label (etykieta grupy).
--}}
@if(count($items))
    <div class="gallery gallery--thumbs" data-lightbox>
        @foreach($items as $item)
            <button
                type="button"
                class="gallery-item{{ $item['type'] === 'video' ? ' gallery-item--video' : '' }}"
                data-lightbox-item
                data-type="{{ $item['type'] }}"
                data-src="{{ $item['src'] }}"
                data-title="{{ $label }} — {{ $item['type'] === 'video' ? 'film' : 'zdjęcie' }} {{ $loop->iteration }}"
            >
                @if($item['type'] === 'video')
                    <video src="{{ $item['src'] }}" muted playsinline preload="metadata"></video>
                    <span class="gallery-item-play" aria-hidden="true"></span>
                @else
                    <img src="{{ $item['src'] }}" alt="{{ $label }} — zdjęcie {{ $loop->iteration }}" loading="lazy">
                @endif
            </button>
        @endforeach
    </div>
@else
    <p class="portfolio-empty">Wkrótce nowe realizacje.</p>
@endif
