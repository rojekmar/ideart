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
