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
        return [
            [
                'slug' => 'grafika-3d',
                'title' => 'Grafika 3D i wizualizacje',
                'description' => 'Modelowanie, teksturowanie, oświetlenie i rendering. Wizualizacje architektoniczne, produktowe, packshoty, sceny i assety.',
                'groups' => [
                    ['title' => '3D Architektonicznie', 'dir' => 'assets/grafiki_animacje/3d architektonicznie'],
                    ['title' => '3D produktowe', 'dir' => 'assets/grafiki_animacje/3d produktowe'],
                ],
            ],
            [
                'slug' => 'grafika-2d',
                'title' => 'Grafika 2D',
                'dir' => 'assets/grafiki_animacje/2d',
                'description' => 'Ilustracje, key visuale, plakaty, grafiki do social mediów, opakowania i materiały reklamowe.',
            ],
            [
                'slug' => 'animacja-i-film',
                'title' => 'Animacja i Film',
                'video_dir' => 'assets/grafiki_animacje/animacja',
                'description' => 'Animacje 3D i 2D, motion design oraz realizacja filmowa — od scenariusza i planu zdjęciowego po spoty, intra i loopy.',
            ],
            [
                'slug' => 'fotografia',
                'title' => 'Fotografia',
                'description' => 'Sesje produktowe, wnętrzarskie i wizerunkowe wraz z retuszem i obróbką w wysokiej jakości.',
                'groups' => [
                    ['title' => 'Fotografia produktowa', 'dir' => 'assets/grafiki_animacje/fotografia produktowa'],
                    ['title' => 'Fotografia - Sesje', 'dir' => 'assets/grafiki_animacje/fotografia sesje'],
                ],
            ],
        ];
    }
}

if (! function_exists('portfolio_group_media')) {
    /**
     * Zwraca zdjęcia i filmy pojedynczej grupy/kategorii portfolio.
     * Klucz 'dir' dostarcza zdjęcia, 'video_dir' — filmy; oba mogą
     * współistnieć. Każdy element to ['type' => 'image'|'video', 'src' => url].
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
     */
    function portfolio_preview_media(array $category, int $count = 3): array
    {
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
