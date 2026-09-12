<?php

if (! function_exists('files_from_dir')) {
    /**
     * Zwraca pliki o podanych rozszerzeniach z danego folderu jako adresy URL,
     * posortowane od najnowiej dodanego/zmodyfikowanego pliku.
     */
    function files_from_dir(string $dir, array $extensions): array
    {
        $full_path = public_path($dir);

        if (! is_dir($full_path)) {
            return [];
        }

        $files = [];
        foreach (scandir($full_path) as $file) {
            if ($file === '.' || $file === '..') continue;
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, $extensions)) {
                $files[$file] = filemtime($full_path . DIRECTORY_SEPARATOR . $file);
            }
        }

        // Najnowsze pierwsze.
        arsort($files);

        return array_map(
            fn (string $file) => asset(rtrim($dir, '/') . '/' . $file),
            array_keys($files)
        );
    }
}

if (! function_exists('get_images_from_dir')) {
    /**
     * Zwraca obrazki z podanego folderu (najnowsze pierwsze).
     */
    function get_images_from_dir(string $dir): array
    {
        return files_from_dir($dir, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'avif']);
    }
}

if (! function_exists('portfolio_categories')) {
    /**
     * Kategorie portfolio — wspólne dla strony głównej (sekcje Oferta/Portfolio)
     * i podstron poszczególnych kategorii.
     */
    function portfolio_categories(): array
    {
        // Foldery ('dir'/'video_dir'/'three_sixty_dir'/'preview_dir') wskazują
        // na prawdziwe pliki na dysku — te same niezależnie od języka. Tylko
        // 'title'/'description' (i tytuły grup) mają wersję angielską.
        $en = app()->getLocale() === 'en';

        return [
            [
                'slug' => 'grafika-3d',
                'title' => $en ? '3D Graphics & Visualizations' : 'Grafika 3D i wizualizacje',
                'description' => $en
                    ? '3D modeling, texturing, lighting and rendering. Architectural and product visualizations, packshots, scenes and assets.'
                    : 'Modelowanie, teksturowanie, oświetlenie i rendering. Wizualizacje architektoniczne, produktowe, packshoty, sceny i assety.',
                'groups' => [
                    ['title' => $en ? 'Architectural 3D' : '3D Architektonicznie', 'dir' => 'assets/grafiki_animacje/3d architektonicznie'],
                    ['title' => $en ? 'Product 3D' : '3D produktowe', 'dir' => 'assets/grafiki_animacje/3d produktowe'],
                ],
                // Podgląd na stronie głównej (portfolio_preview_media) ma
                // pokazywać zdjęcia tylko z tego folderu, nie z obu grup —
                // patrz portfolio_preview_media() w tym pliku.
                'preview_dir' => 'assets/grafiki_animacje/3d architektonicznie',
            ],
            [
                'slug' => 'grafika-2d',
                'title' => $en ? '2D Graphics' : 'Grafika 2D',
                'dir' => 'assets/grafiki_animacje/2d',
                'description' => $en
                    ? 'Illustrations, key visuals, posters, social media graphics, packaging and marketing materials.'
                    : 'Ilustracje, key visuale, plakaty, grafiki do social mediów, opakowania i materiały reklamowe.',
            ],
            [
                'slug' => 'animacja-i-film',
                'title' => $en ? 'Animation & Film' : 'Animacja i Film',
                'video_dir' => 'assets/grafiki_animacje/animacja',
                'description' => $en
                    ? '3D and 2D animation, motion design and film production — from script and shoot planning to spots, intros and loops.'
                    : 'Animacje 3D i 2D, motion design oraz realizacja filmowa — od scenariusza i planu zdjęciowego po spoty, intra i loopy.',
            ],
            [
                'slug' => '360-interaktywnie',
                'title' => $en ? '360° Interactive' : '360 Interaktywnie',
                'three_sixty_dir' => 'assets/grafiki_animacje/360',
                'description' => $en
                    ? 'Interactive 360° presentations — rotatable visualizations and panoramas visitors can explore themselves in the browser.'
                    : 'Interaktywne prezentacje 360° — obracane wizualizacje i panoramy, które można samodzielnie eksplorować w przeglądarce.',
            ],
            [
                'slug' => 'fotografia',
                'title' => $en ? 'Photography' : 'Fotografia',
                'description' => $en
                    ? 'Product, interior and portrait photo sessions, with high-quality retouching and post-processing.'
                    : 'Sesje produktowe, wnętrzarskie i wizerunkowe wraz z retuszem i obróbką w wysokiej jakości.',
                'groups' => [
                    ['title' => $en ? 'Product Photography' : 'Fotografia produktowa', 'dir' => 'assets/grafiki_animacje/fotografia produktowa'],
                    ['title' => $en ? 'Photo Sessions' : 'Fotografia - Sesje', 'dir' => 'assets/grafiki_animacje/fotografia sesje'],
                ],
            ],
        ];
    }
}

if (! function_exists('portfolio_group_media')) {
    /**
     * Zwraca zdjęcia, filmy i prezentacje 360° pojedynczej grupy/kategorii
     * portfolio. Klucz 'dir' dostarcza zdjęcia, 'video_dir' — filmy,
     * 'three_sixty_dir' — prezentacje 360°; mogą współistnieć. Każdy
     * element to ['type' => 'image'|'video'|'360', 'src' => url] (360 ma
     * dodatkowo 'thumb' — miniaturkę do wyświetlenia w siatce/podglądzie).
     */
    function portfolio_group_media(array $group): array
    {
        $items = [];

        if (! empty($group['dir'])) {
            foreach (get_images_from_dir($group['dir']) as $src) {
                $items[] = ['type' => 'image', 'src' => $src];
            }
        }

        if (! empty($group['video_dir'])) {
            foreach (get_videos_from_dir($group['video_dir']) as $src) {
                $items[] = ['type' => 'video', 'src' => $src];
            }
        }

        if (! empty($group['three_sixty_dir'])) {
            $items = array_merge($items, get_360_from_dir($group['three_sixty_dir']));
        }

        return $items;
    }
}

if (! function_exists('portfolio_category_media')) {
    /**
     * Zwraca wszystkie materiały (zdjęcia i filmy) danej kategorii portfolio —
     * z pojedynczej grupy albo połączone z kilku podkategorii ('groups').
     */
    function portfolio_category_media(array $category): array
    {
        if (! empty($category['groups'])) {
            $items = [];
            foreach ($category['groups'] as $group) {
                $items = array_merge($items, portfolio_group_media($group));
            }

            return $items;
        }

        return portfolio_group_media($category);
    }
}

if (! function_exists('evenly_spaced_sample')) {
    /**
     * Wybiera $count elementów równomiernie rozłożonych na liście (pierwszy,
     * ostatni i punkty pośrednie) — żeby pokazać próbkę z różnych miejsc
     * zbioru zamiast tylko elementów z początku.
     */
    function evenly_spaced_sample(array $items, int $count): array
    {
        $items = array_values($items);
        $total = count($items);

        if ($count <= 0 || $total === 0) {
            return [];
        }

        if ($total <= $count) {
            return $items;
        }

        $step = ($total - 1) / max($count - 1, 1);
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $result[] = $items[(int) round($i * $step)];
        }

        return $result;
    }
}

if (! function_exists('portfolio_preview_media')) {
    /**
     * Podgląd kategorii na stronie głównej — $count materiałów rozłożonych
     * równomiernie w czasie (od najnowszych po najstarsze), żeby pokazać
     * przekrój prac z różnych okresów zamiast tylko ostatnio dodanych.
     *
     * Klucz 'preview_dir' (opcjonalny) zawęża podgląd do jednego folderu —
     * przydatne dla kategorii złożonych z kilku grup (np. Grafika 3D:
     * "3D Architektonicznie" + "3D produktowe"), gdy podgląd na stronie
     * głównej ma pokazywać tylko jedną z nich, a nie wszystkie razem.
     */
    function portfolio_preview_media(array $category, int $count = 3): array
    {
        if (! empty($category['preview_dir'])) {
            $items = array_map(
                fn (string $src) => ['type' => 'image', 'src' => $src],
                get_images_from_dir($category['preview_dir'])
            );

            return evenly_spaced_sample($items, $count);
        }

        return evenly_spaced_sample(portfolio_category_media($category), $count);
    }
}

if (! function_exists('get_videos_from_dir')) {
    /**
     * Zwraca filmy z podanego folderu (najnowsze pierwsze).
     */
    function get_videos_from_dir(string $dir): array
    {
        return files_from_dir($dir, ['mp4', 'webm', 'ogg', 'mov', 'avi']);
    }
}

if (! function_exists('get_360_from_dir')) {
    /**
     * Zwraca prezentacje 360° z podanego folderu — każda prezentacja to
     * PODFOLDER z gotowym eksportem wirtualnego spaceru (np. z Pano2VR):
     * plik "index.html" (viewer) oraz miniatura ("preview"/"thumb"/
     * "miniatura" + .jpg/.jpeg/.png/.webp) w katalogu głównym tego
     * podfolderu, obok plików/folderów samego viewera (tiles/, media/,
     * pano.xml itd.). Podfolder bez index.html albo bez rozpoznanej
     * miniatury jest pomijany. Najnowsze (wg daty modyfikacji
     * index.html) pierwsze.
     */
    function get_360_from_dir(string $dir): array
    {
        $full_path = public_path($dir);

        if (! is_dir($full_path)) {
            return [];
        }

        $thumbNames = [];
        foreach (['preview', 'thumb', 'miniatura'] as $base) {
            foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                $thumbNames[] = "{$base}.{$ext}";
            }
        }

        $projects = [];
        foreach (scandir($full_path) as $entry) {
            if ($entry === '.' || $entry === '..') continue;

            $projectPath = $full_path . DIRECTORY_SEPARATOR . $entry;
            if (! is_dir($projectPath)) continue;

            $indexFile = $projectPath . DIRECTORY_SEPARATOR . 'index.html';
            if (! file_exists($indexFile)) continue;

            $thumbFile = null;
            foreach ($thumbNames as $name) {
                if (file_exists($projectPath . DIRECTORY_SEPARATOR . $name)) {
                    $thumbFile = $name;
                    break;
                }
            }

            if (! $thumbFile) continue;

            $projects[$entry] = [
                'mtime' => filemtime($indexFile),
                'thumb' => $thumbFile,
            ];
        }

        uasort($projects, fn (array $a, array $b) => $b['mtime'] <=> $a['mtime']);

        $items = [];
        foreach ($projects as $entry => $data) {
            $items[] = [
                'type' => '360',
                'src' => asset(rtrim($dir, '/') . '/' . $entry . '/index.html'),
                'thumb' => asset(rtrim($dir, '/') . '/' . $entry . '/' . $data['thumb']),
            ];
        }

        return $items;
    }
}

if (! function_exists('localized_route')) {
    /**
     * Jak route(), ale automatycznie dodaje prefiks "en." do nazwy trasy,
     * gdy aktualny język strony to angielski. Dzięki temu współdzielone
     * widoki (partials/header.blade.php, welcome.blade.php itd. — te same
     * pliki dla obu języków) generują poprawne linki niezależnie od tego,
     * czy renderowane są w wersji polskiej, czy angielskiej.
     *
     * Uwaga: celowo w media.php, a nie w osobnym pliku — ten plik jest już
     * zarejestrowany w composer.json (autoload.files) i wdrożony na
     * produkcji, więc dopisanie tu funkcji nie wymaga aktualizacji
     * vendor/composer/* (którego deploy nie wgrywa, gdy composer.lock się
     * nie zmienił — patrz DEPLOY.md).
     */
    function localized_route(string $name, $parameters = [], bool $absolute = true): string
    {
        if (app()->getLocale() === 'en' && ! str_starts_with($name, 'en.')) {
            $name = 'en.'.$name;
        }

        return route($name, $parameters, $absolute);
    }
}

if (! function_exists('alternate_locale_url')) {
    /**
     * Adres BIEŻĄCEJ strony w drugim języku — do przełącznika języka w
     * nagłówku i do tagów <link rel="alternate" hreflang> w <head>.
     * Działa tylko dla tras zarejestrowanych w obu wersjach (home,
     * portfolio.category) — dla pozostałych (np. /sitemap.xml) zwraca null.
     */
    function alternate_locale_url(): ?string
    {
        $routeName = request()->route()?->getName();

        if (! $routeName) {
            return null;
        }

        $isEnglish = str_starts_with($routeName, 'en.');
        $baseName = $isEnglish ? substr($routeName, 3) : $routeName;
        $targetName = $isEnglish ? $baseName : 'en.'.$baseName;

        if (! \Illuminate\Support\Facades\Route::has($targetName)) {
            return null;
        }

        return route($targetName, request()->route()->parameters());
    }
}
