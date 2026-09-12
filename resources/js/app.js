import './bootstrap';

// Zamknij menu mobilne po kliknięciu w link
document.querySelectorAll('.site-nav a').forEach(function (link) {
    link.addEventListener('click', function () {
        var toggle = document.getElementById('nav-toggle');
        if (toggle) toggle.checked = false;
    });
});

// Cień nagłówka po przewinięciu
var header = document.querySelector('.site-header');
if (header) {
    window.addEventListener('scroll', function () {
        header.classList.toggle('is-scrolled', window.scrollY > 10);
    });
}

// Złota poświata na kafelkach Oferty — podąża za kursorem myszy.
document.querySelectorAll('.card').forEach(function (card) {
    card.addEventListener('mousemove', function (e) {
        var rect = card.getBoundingClientRect();
        card.style.setProperty('--glow-x', (e.clientX - rect.left) + 'px');
        card.style.setProperty('--glow-y', (e.clientY - rect.top) + 'px');
    });
});

// Złota poświata kursora na całej stronie.
var cursorGlow = document.querySelector('.cursor-glow');
if (cursorGlow && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    document.addEventListener('mousemove', function (e) {
        cursorGlow.style.setProperty('--x', e.clientX + 'px');
        cursorGlow.style.setProperty('--y', e.clientY + 'px');
        cursorGlow.classList.add('is-visible');
    });
    document.addEventListener('mouseleave', function () {
        cursorGlow.classList.remove('is-visible');
    });
}

// Miniaturki wideo: pokaż realną pierwszą klatkę zamiast czarnego kadru,
// bez pobierania całego pliku (tylko metadane + drobny "seek").
document.querySelectorAll('.gallery-item video').forEach(function (video) {
    video.addEventListener('loadedmetadata', function () {
        try { video.currentTime = 0.1; } catch (e) {}
    });
});

// Lightbox galerii (podstrony kategorii portfolio) — zdjęcia i filmy
(function () {
    var overlay = document.querySelector('[data-lightbox-overlay]');
    var galleries = document.querySelectorAll('[data-lightbox]');
    if (!overlay || !galleries.length) return;

    var imageEl = overlay.querySelector('[data-lightbox-image]');
    var videoEl = overlay.querySelector('[data-lightbox-video]');
    var iframeEl = overlay.querySelector('[data-lightbox-iframe]');
    var closeBtn = overlay.querySelector('[data-lightbox-close]');
    var prevBtn = overlay.querySelector('[data-lightbox-prev]');
    var nextBtn = overlay.querySelector('[data-lightbox-next]');

    var items = [];
    var currentIndex = 0;
    var lastFocused = null;

    galleries.forEach(function (gallery) {
        var galleryItems = gallery.querySelectorAll('[data-lightbox-item]');
        galleryItems.forEach(function (item) {
            var index = items.length;
            items.push({
                type: item.getAttribute('data-type') || 'image',
                src: item.getAttribute('data-src'),
                label: item.getAttribute('data-title') || '',
            });
            item.addEventListener('click', function () {
                openLightbox(index);
            });
        });
    });

    function stopVideo() {
        if (!videoEl.paused) videoEl.pause();
        videoEl.removeAttribute('src');
        videoEl.load();
    }

    function stopIframe() {
        iframeEl.removeAttribute('src');
    }

    // autoplay: odtwórz automatycznie tylko przy bezpośrednim kliknięciu
    // w miniaturkę — nie przy przełączaniu strzałkami (część przeglądarek
    // blokuje wtedy dźwięk, bo to już nie jest bezpośrednia akcja użytkownika).
    function render(autoplay) {
        var current = items[currentIndex];

        imageEl.hidden = true;
        videoEl.hidden = true;
        iframeEl.hidden = true;

        if (current.type === 'video') {
            imageEl.removeAttribute('src');
            stopIframe();

            videoEl.hidden = false;
            videoEl.src = current.src;
            videoEl.setAttribute('aria-label', current.label);
            if (autoplay) {
                var playPromise = videoEl.play();
                if (playPromise && playPromise.catch) playPromise.catch(function () {});
            }
        } else if (current.type === '360') {
            stopVideo();
            imageEl.removeAttribute('src');

            iframeEl.hidden = false;
            iframeEl.src = current.src;
            iframeEl.setAttribute('title', current.label);
        } else {
            stopVideo();
            stopIframe();

            imageEl.hidden = false;
            imageEl.src = current.src;
            imageEl.alt = current.label;
        }
    }

    function openLightbox(index) {
        currentIndex = index;
        lastFocused = document.activeElement;
        render(true);
        overlay.hidden = false;
        document.body.style.overflow = 'hidden';

        // Wymuś przeliczenie stylów, żeby przejście z "is-open" faktycznie się odtworzyło
        // (zamiast od razu wystartować z docelowego stanu).
        void overlay.offsetWidth;
        requestAnimationFrame(function () {
            overlay.classList.add('is-open');
        });

        closeBtn.focus();
    }

    function closeLightbox() {
        overlay.classList.remove('is-open');
        document.body.style.overflow = '';
        stopVideo();
        stopIframe();
        if (lastFocused) lastFocused.focus();

        window.setTimeout(function () {
            overlay.hidden = true;
        }, 320);
    }

    // Zanikanie przy zmianie materiału (poprzedni/następny) — 280ms, tyle
    // samo co przejście opacity zdefiniowane na .lightbox-media w CSS.
    // Uwaga: element do wygaszenia trzeba ustalić PRZED zmianą currentIndex —
    // inaczej (np. przy przejściu ze zdjęcia na film) wygaszony zostałby
    // element, który wcale nie jest jeszcze widoczny.
    function navigate(delta) {
        var currentType = items[currentIndex].type;
        var activeEl = currentType === 'video' ? videoEl : (currentType === '360' ? iframeEl : imageEl);
        stopVideo();
        activeEl.classList.add('is-fading');

        currentIndex = (currentIndex + delta + items.length) % items.length;

        window.setTimeout(function () {
            render(false);
            imageEl.classList.remove('is-fading');
            videoEl.classList.remove('is-fading');
            iframeEl.classList.remove('is-fading');
        }, 280);
    }

    function showPrev() {
        navigate(-1);
    }

    function showNext() {
        navigate(1);
    }

    closeBtn.addEventListener('click', closeLightbox);
    prevBtn.addEventListener('click', showPrev);
    nextBtn.addEventListener('click', showNext);

    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeLightbox();
    });

    document.addEventListener('keydown', function (e) {
        if (overlay.hidden) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') showPrev();
        if (e.key === 'ArrowRight') showNext();
    });
})();
