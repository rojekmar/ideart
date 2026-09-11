<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

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
