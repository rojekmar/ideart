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
//
// Wykrywane po samej strukturze plików (NIE przez .env) — w tym miejscu
// .env jeszcze nie jest wczytany (Dotenv startuje dopiero w bootstrapperach
// kernela, który odpala się później niż ten plik), więc env() zwróciłoby
// tu zawsze wartość domyślną.
$hasOwnPublicDir = is_dir($app->basePath('public'));
$parentHasIndexPhp = file_exists(dirname($app->basePath()).'/index.php');

if (! $hasOwnPublicDir && $parentHasIndexPhp) {
    $app->usePublicPath(dirname($app->basePath()));
}

return $app;
