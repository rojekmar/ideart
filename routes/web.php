<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

// Wspólna logika stron głównej i kategorii — rejestrowana dwa razy niżej
// (raz dla polskiej wersji bez prefiksu, raz dla /en), żeby nie powielać
// kodu. Locale ustawiane jest PRZED wywołaniem akcji, więc wszystko, co ona
// renderuje (w tym portfolio_categories(), które samo sprawdza aktualny
// język), dostaje poprawnie przetłumaczoną treść.
$homeAction = function () {
    return view('welcome');
};

$categoryAction = function (string $slug) {
    $categories = portfolio_categories();
    $category = collect($categories)->firstWhere('slug', $slug);

    abort_unless($category, 404);

    return view('portfolio-category', [
        'category' => $category,
        'categories' => $categories,
        'media' => portfolio_category_media($category),
    ]);
};

// ---------- Wersja polska (domyślna, bez prefiksu) ----------
// Locale ustawiane jawnie (nie polegamy na APP_LOCALE z .env) — lokalny
// XAMPP i produkcja mają tam różne wartości, więc to jedyny pewny sposób,
// żeby te trasy zawsze renderowały się po polsku niezależnie od środowiska.
Route::get('/', function () use ($homeAction) {
    app()->setLocale('pl');

    return $homeAction();
})->name('home');

Route::post('/kontakt', [ContactController::class, 'store'])
    ->name('contact.store')
    ->middleware('throttle:5,1'); // maks. 5 prób na minutę — ochrona przed spamem

Route::get('/portfolio/{slug}', function (string $slug) use ($categoryAction) {
    app()->setLocale('pl');

    return $categoryAction($slug);
})->name('portfolio.category');

// ---------- Wersja angielska (prefiks /en) ----------
Route::prefix('en')->name('en.')->group(function () use ($homeAction, $categoryAction) {
    Route::get('/', function () use ($homeAction) {
        app()->setLocale('en');

        return $homeAction();
    })->name('home');

    Route::post('/contact', [ContactController::class, 'store'])
        ->name('contact.store')
        ->middleware('throttle:5,1');

    Route::get('/portfolio/{slug}', function (string $slug) use ($categoryAction) {
        app()->setLocale('en');

        return $categoryAction($slug);
    })->name('portfolio.category');
});

// Przekierowania 301 ze starych adresów poprzedniej wersji strony —
// Google ma je zaindeksowane (site:ideart.com.pl), a dziś zwracały 404.
// Wersja z ukośnikiem na końcu (np. "/grafika-3d/", tak jak jest
// zaindeksowana) trafia tu automatycznie — Symfony samo przekierowuje
// ją najpierw do wersji bez ukośnika.
foreach ([
    '360-foto' => '/portfolio/360-interaktywnie',
    'prezentacja-360' => '/portfolio/360-interaktywnie',
    'grafika-3d' => '/portfolio/grafika-3d',
    'grafika-2d' => '/portfolio/grafika-2d',
    'fotografia' => '/portfolio/fotografia',
    'film-animacja' => '/portfolio/animacja-i-film',
] as $legacyPath => $target) {
    Route::redirect("/{$legacyPath}", $target, 301);
}

Route::get('/sitemap.xml', function () {
    $slugs = collect(portfolio_categories())->pluck('slug');

    $urls = collect([
        ['loc' => url('/'), 'priority' => '1.0'],
        ['loc' => route('en.home'), 'priority' => '0.9'],
    ])
        ->concat($slugs->map(fn (string $slug) => ['loc' => route('portfolio.category', $slug), 'priority' => '0.8']))
        ->concat($slugs->map(fn (string $slug) => ['loc' => route('en.portfolio.category', $slug), 'priority' => '0.7']));

    // Budowane jako czysty string PHP (bez widoku Blade) celowo — nagłówek
    // XML w pliku .blade.php myli kompilator Blade'a na serwerach z
    // włączonym short_open_tag (dokładnie tak było na cba.pl: kompilator
    // zostawiał dyrektywę {!! !!} nierozwiniętą, co dawało syntax error przy
    // renderowaniu). Uwaga na przyszłość: literalna sekwencja zamykająca
    // znacznik PHP nie może się pojawić nawet w komentarzu // w zwykłym
    // pliku .php — kończy blok PHP w tym miejscu (dokladnie to samo zdarzylo
    // sie przy pierwszej probie napisania tego komentarza).
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($urls as $url) {
        $xml .= '<url><loc>' . e($url['loc']) . '</loc><priority>' . e($url['priority']) . '</priority></url>' . "\n";
    }
    $xml .= '</urlset>';

    return response($xml, 200)->header('Content-Type', 'text/xml; charset=UTF-8');
})->name('sitemap');
