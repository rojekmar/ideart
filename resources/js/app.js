import './bootstrap';

// Zgoda na cookies + Google Analytics — skrypt Analytics ładowany dopiero
// po kliknięciu "Akceptuję" (albo od razu, jeśli zgoda była już zapisana
// wcześniej), nigdy automatycznie. window.__gaId ustawiane inline w
// partials/cookie-banner.blade.php — jeśli go nie ma (Analytics jeszcze
// nieskonfigurowany), cały ten blok jest nieaktywny (brak banera w DOM).
(function () {
    var CONSENT_KEY = 'ideart-cookie-consent';
    var banner = document.querySelector('[data-cookie-banner]');

    function loadAnalytics() {
        if (!window.__gaId || window.__gaLoaded) return;
        window.__gaLoaded = true;

        var script = document.createElement('script');
        script.async = true;
        script.src = 'https://www.googletagmanager.com/gtag/js?id=' + window.__gaId;
        document.head.appendChild(script);

        window.dataLayer = window.dataLayer || [];
        window.gtag = function () { window.dataLayer.push(arguments); };
        window.gtag('js', new Date());
        window.gtag('config', window.__gaId, { anonymize_ip: true });
    }

    function getConsent() {
        try { return localStorage.getItem(CONSENT_KEY); } catch (e) { return null; }
    }
    function setConsent(value) {
        try { localStorage.setItem(CONSENT_KEY, value); } catch (e) {}
    }

    var consent = getConsent();
    if (consent === 'accepted') {
        loadAnalytics();
    } else if (!consent && banner) {
        banner.hidden = false;
    }

    if (banner) {
        var acceptBtn = banner.querySelector('[data-cookie-accept]');
        var rejectBtn = banner.querySelector('[data-cookie-reject]');

        if (acceptBtn) {
            acceptBtn.addEventListener('click', function () {
                setConsent('accepted');
                banner.hidden = true;
                loadAnalytics();
            });
        }
        if (rejectBtn) {
            rejectBtn.addEventListener('click', function () {
                setConsent('rejected');
                banner.hidden = true;
            });
        }
    }

    // "Ustawienia cookies" w stopce — pozwala ponownie otworzyć baner, żeby
    // zmienić wcześniejszy wybór.
    document.querySelectorAll('[data-cookie-settings]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            if (banner) banner.hidden = false;
        });
    });
})();

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
// bez pobierania całego pliku (tylko metadane + drobny "seek"). Dotyczy
// też tła w hero na podstronach kategorii złożonych wyłącznie z wideo
// (video.hero-slide — patrz portfolio-category.blade.php).
document.querySelectorAll('.gallery-item video, video.hero-slide').forEach(function (video) {
    video.addEventListener('loadedmetadata', function () {
        try { video.currentTime = 0.1; } catch (e) {}
    });
});

// Lightbox galerii (podstrony kategorii portfolio) — zdjęcia, filmy i
// prezentacje 360°. Przejście między materiałami to "przeciągnięcie"
// (poprzedni/następny wyjeżdża w bok, kolejny wjeżdża z przeciwnej
// strony) — ten sam kierunek co gest swipe na dotyku.
(function () {
    var overlay = document.querySelector('[data-lightbox-overlay]');
    var galleries = document.querySelectorAll('[data-lightbox]');
    if (!overlay || !galleries.length) return;

    var stage = overlay.querySelector('[data-lightbox-stage]');
    var closeBtn = overlay.querySelector('[data-lightbox-close]');
    var prevBtn = overlay.querySelector('[data-lightbox-prev]');
    var nextBtn = overlay.querySelector('[data-lightbox-next]');

    var SLIDE_MS = 420;

    var items = [];
    var currentIndex = 0;
    var currentSlide = null;
    var isAnimating = false;
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

    // Buduje jeden "slajd" (kontener + odpowiedni element: img/video/iframe)
    // dla danego materiału. autoplay dotyczy wyłącznie wideo otwieranego
    // bezpośrednim kliknięciem w miniaturkę (nie przy nawigacji).
    function buildSlide(data, autoplay) {
        var slide = document.createElement('div');
        slide.className = 'lightbox-slide';

        var media;
        if (data.type === 'video') {
            media = document.createElement('video');
            media.className = 'lightbox-media';
            media.src = data.src;
            media.controls = true;
            media.playsInline = true;
            media.setAttribute('aria-label', data.label);
            if (autoplay) {
                var playPromise = media.play();
                if (playPromise && playPromise.catch) playPromise.catch(function () {});
            }
        } else if (data.type === '360') {
            media = document.createElement('iframe');
            media.className = 'lightbox-media lightbox-frame';
            media.src = data.src;
            media.title = data.label;
            media.setAttribute('allowfullscreen', '');
        } else {
            media = document.createElement('img');
            media.className = 'lightbox-media';
            media.src = data.src;
            media.alt = data.label;
        }

        slide.appendChild(media);
        return slide;
    }

    // Zatrzymuje wideo/iframe zanim slajd zniknie z DOM — inaczej dźwięk/
    // odtwarzanie leciałoby dalej w tle.
    function destroySlide(slide) {
        if (!slide) return;
        var media = slide.firstElementChild;
        if (media) {
            if (media.tagName === 'VIDEO') {
                media.pause();
                media.removeAttribute('src');
                media.load();
            } else if (media.tagName === 'IFRAME') {
                media.src = 'about:blank';
            }
        }
        slide.remove();
    }

    function openLightbox(index) {
        currentIndex = index;
        lastFocused = document.activeElement;

        stage.innerHTML = '';
        currentSlide = buildSlide(items[currentIndex], true);
        stage.appendChild(currentSlide);

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
        if (lastFocused) lastFocused.focus();

        window.setTimeout(function () {
            overlay.hidden = true;
            destroySlide(currentSlide);
            currentSlide = null;
            stage.innerHTML = '';
        }, 320);
    }

    // delta > 0: następny (wjeżdża z prawej, obecny wyjeżdża w lewo).
    // delta < 0: poprzedni (wjeżdża z lewej, obecny wyjeżdża w prawo).
    // Ten sam kierunek co przeciąganie palcem po ekranie.
    function navigate(delta) {
        if (isAnimating || items.length < 2) return;
        isAnimating = true;

        var nextIndex = (currentIndex + delta + items.length) % items.length;
        var outgoing = currentSlide;
        var incoming = buildSlide(items[nextIndex], false);

        incoming.classList.add(delta > 0 ? 'lightbox-slide--enter-from-right' : 'lightbox-slide--enter-from-left');
        stage.appendChild(incoming);

        // Wymuś przeliczenie stylów, zanim usuniemy klasę startową —
        // inaczej przeglądarka scali oba stany w jeden i nic się nie przesunie.
        void incoming.offsetWidth;
        requestAnimationFrame(function () {
            incoming.classList.remove('lightbox-slide--enter-from-right', 'lightbox-slide--enter-from-left');
            outgoing.classList.add(delta > 0 ? 'lightbox-slide--exit-to-left' : 'lightbox-slide--exit-to-right');
        });

        window.setTimeout(function () {
            destroySlide(outgoing);
            currentSlide = incoming;
            currentIndex = nextIndex;
            isAnimating = false;
        }, SLIDE_MS);
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

    // Przesunięcie palcem (mobile) — działa obok strzałek, nie zamiast nich.
    // Liczony jest tylko wyraźnie poziomy gest (dłuższy niż pionowy i
    // przekraczający próg), żeby nie kolidować ze scrubowaniem wideo/
    // przeciąganiem panoramy 360 ani z przypadkowym drgnięciem palca.
    var touchStartX = null;
    var touchStartY = null;

    overlay.addEventListener('touchstart', function (e) {
        if (e.touches.length !== 1) return;
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    }, { passive: true });

    overlay.addEventListener('touchend', function (e) {
        if (touchStartX === null) return;

        var touch = e.changedTouches[0];
        var deltaX = touch.clientX - touchStartX;
        var deltaY = touch.clientY - touchStartY;
        touchStartX = null;
        touchStartY = null;

        var SWIPE_THRESHOLD = 50;
        if (Math.abs(deltaX) < SWIPE_THRESHOLD) return;
        if (Math.abs(deltaX) < Math.abs(deltaY) * 1.5) return;

        if (deltaX > 0) {
            showPrev();
        } else {
            showNext();
        }
    }, { passive: true });

    document.addEventListener('keydown', function (e) {
        if (overlay.hidden) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') showPrev();
        if (e.key === 'ArrowRight') showNext();
    });
})();

// Sieć "neuronów" w tle — particle/constellation effect na canvasie
// (.particle-network, patrz partials/particle-network.blade.php). Punkty
// dryfują powoli i łączą się liniami, gdy są blisko siebie; te w pobliżu
// kursora łączą się dodatkowo z nim. Wyłączone przy prefers-reduced-motion.
(function () {
    var canvas = document.querySelector('.particle-network');
    if (!canvas || !canvas.getContext) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    var ctx = canvas.getContext('2d');
    var GOLD = '212, 175, 55';
    var LINK_DIST = 130;
    var MOUSE_DIST = 170;

    var width = 0;
    var height = 0;
    var dpr = Math.min(window.devicePixelRatio || 1, 2);
    var particles = [];
    var mouse = { x: null, y: null };

    function resize() {
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width * dpr;
        canvas.height = height * dpr;
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        var count = Math.min(80, Math.round((width * height) / 16000));
        particles = [];
        for (var i = 0; i < count; i++) {
            particles.push({
                x: Math.random() * width,
                y: Math.random() * height,
                vx: (Math.random() - 0.5) * 0.3,
                vy: (Math.random() - 0.5) * 0.3,
            });
        }
    }

    function step() {
        ctx.clearRect(0, 0, width, height);

        // Ruch i punkty
        for (var i = 0; i < particles.length; i++) {
            var p = particles[i];
            p.x += p.vx;
            p.y += p.vy;
            if (p.x < 0 || p.x > width) p.vx *= -1;
            if (p.y < 0 || p.y > height) p.vy *= -1;

            ctx.beginPath();
            ctx.arc(p.x, p.y, 1.6, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(' + GOLD + ', 0.5)';
            ctx.fill();
        }

        // Linie między bliskimi punktami
        for (var i = 0; i < particles.length; i++) {
            for (var j = i + 1; j < particles.length; j++) {
                var a = particles[i];
                var b = particles[j];
                var dx = a.x - b.x;
                var dy = a.y - b.y;
                var dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < LINK_DIST) {
                    ctx.beginPath();
                    ctx.moveTo(a.x, a.y);
                    ctx.lineTo(b.x, b.y);
                    ctx.strokeStyle = 'rgba(' + GOLD + ', ' + (0.16 * (1 - dist / LINK_DIST)) + ')';
                    ctx.lineWidth = 1;
                    ctx.stroke();
                }
            }
        }

        // Linie od kursora do pobliskich punktów
        if (mouse.x !== null) {
            for (var i = 0; i < particles.length; i++) {
                var p = particles[i];
                var dx = p.x - mouse.x;
                var dy = p.y - mouse.y;
                var dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < MOUSE_DIST) {
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(mouse.x, mouse.y);
                    ctx.strokeStyle = 'rgba(' + GOLD + ', ' + (0.35 * (1 - dist / MOUSE_DIST)) + ')';
                    ctx.lineWidth = 1;
                    ctx.stroke();
                }
            }
        }

        requestAnimationFrame(step);
    }

    window.addEventListener('resize', resize);
    window.addEventListener('mousemove', function (e) {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
    });
    window.addEventListener('mouseleave', function () {
        mouse.x = null;
        mouse.y = null;
    });

    resize();
    requestAnimationFrame(step);
})();
