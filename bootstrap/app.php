<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// Na hostingu bez SSH (np. cba.pl, patrz DEPLOY.md) kod aplikacji leży w
// podfolderze WEWNĄTRZ katalogu publicznego domeny (bo konto FTP nie może
// tworzyć folderów poza nim) — czyli prawdziwy public/ to katalog nadrzędny
// względem aplikacji, nie <projekt>/public jak domyślnie zakłada Laravel.
// Włączane tylko flagą w .env — lokalny XAMPP działa bez zmian.
if (env('DEPLOY_SPLIT_PUBLIC', false)) {
    $app->usePublicPath(dirname($app->basePath()));
}

return $app;
