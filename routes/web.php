<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('/kontakt', [ContactController::class, 'store'])
    ->name('contact.store')
    ->middleware('throttle:5,1'); // maks. 5 prób na minutę — ochrona przed spamem

Route::get('/portfolio/{slug}', function (string $slug) {
    $categories = portfolio_categories();
    $category = collect($categories)->firstWhere('slug', $slug);

    abort_unless($category, 404);

    return view('portfolio-category', [
        'category' => $category,
        'categories' => $categories,
        'media' => portfolio_category_media($category),
    ]);
})->name('portfolio.category');

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
    $urls = collect([
        ['loc' => url('/'), 'priority' => '1.0'],
    ])->concat(
        collect(portfolio_categories())->map(fn (array $category) => [
            'loc' => route('portfolio.category', $category['slug']),
            'priority' => '0.8',
        ])
    );

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
