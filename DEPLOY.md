# Automatyczne wdrażanie na cba.pl (hosting współdzielony, bez SSH)

Po każdym `git push` na branch `main` GitHub Actions samodzielnie zbuduje
aplikację (composer + npm) i wgra ją na hosting **przez FTP** — bez
potrzeby logowania się na serwer.

## Dlaczego taka dziwna struktura?

Na zwykłym hostingu nie da się odpalić `php artisan` ani `composer` —
nie ma terminala. Więc:

- **cały kod aplikacji budujemy w GitHub Actions** (tam JEST PHP,
  composer i node) i wgrywamy gotowe pliki (razem z folderem `vendor/`)
- **kod Laravela (poza `public/`) NIE może leżeć w katalogu publicznym
  domeny** — inaczej każdy mógłby wejść np. na `ideart.com.pl/.env` i
  zobaczyć Twoje hasła. Dlatego trafia do osobnego folderu **obok**
  katalogu publicznego, a `index.php` w katalogu publicznym tylko się
  do niego odwołuje.

Docelowo na koncie FTP powinno wyglądać to tak:

```
/ (konto FTP)
├── ideart-app/          <- kod Laravela (NIE jest dostępny z przeglądarki)
│   ├── app/
│   ├── vendor/
│   ├── .env
│   └── ...
└── public_html/         <- katalog publiczny domeny ideart.com.pl
    ├── index.php        <- wskazuje na ../ideart-app/
    ├── .htaccess
    ├── build/            (CSS/JS z Vite)
    └── assets/
        ├── images/logo.png
        └── grafiki_animacje/   <- TU wgrywasz zdjęcia/filmy portfolio
                                   (osobno, nie przez git/CI — patrz niżej)
```

**Zanim uruchomisz pierwsze wdrożenie, sprawdź w kliencie FTP
(np. FileZilla, host `www.mkwk086.cba.pl`) rzeczywistą strukturę
folderów na koncie** — nazwa `ideart-app` i ścieżka `/public_html/` w
workflow to założenie, które możesz swobodnie zmienić (patrz sekcja
"Zmienne repozytorium" niżej), jeśli Twoje konto ma inny układ.

## Sekrety w repozytorium GitHub

Ustawienia repo → **Settings → Secrets and variables → Actions → Secrets → New repository secret**:

| Nazwa sekretu | Wartość |
|---|---|
| `FTP_SERVER` | `www.mkwk086.cba.pl` |
| `FTP_USERNAME` | Twoja nazwa użytkownika FTP (z panelu cba.pl) |
| `FTP_PASSWORD` | Twoje hasło FTP |
| `APP_KEY` | `base64:75EaBiUFxqMqxvD1Jg4Y8nK9Ye8wFrNwVtM9TAhiRN0=` |

(`APP_KEY` wygenerowany raz, lokalnie — nie zmieniaj go później, inaczej
stracisz dostęp do ewentualnych zaszyfrowanych danych/sesji.)

## Zmienne repozytorium (jawne, nie sekrety)

Tam samo, ale zakładka **Variables → New repository variable**:

| Nazwa zmiennej | Wartość |
|---|---|
| `APP_URL` | `https://ideart.com.pl` |
| `APP_NAME` | `IDEART` |
| `FTP_APP_DIR` | `/ideart-app/` (zmień, jeśli Twoja struktura FTP jest inna) |
| `FTP_PUBLIC_DIR` | `/public_html/` (zmień, jeśli katalog publiczny domeny nazywa się inaczej) |

## Pierwsze wdrożenie

Nic więcej nie musisz robić ręcznie na serwerze — pierwszy `git push`
utworzy oba foldery i wgra wszystko automatycznie. Podgląd postępu:
zakładka **Actions** w repozytorium na GitHub.

## Aktualizacja treści portfolio (zdjęcia/filmy)

To celowo **osobny proces** od wdrażania kodu — folder
`public/assets/grafiki_animacje/` jest wykluczony z automatycznego
wdrożenia (500+ MB, dużo ponad to, co sensownie przechodzi przez CI).
Wgrywaj/aktualizuj go bezpośrednio przez FTP do:

```
/public_html/assets/grafiki_animacje/
```

dokładnie tak jak dotychczas — reszta strony (galerie, slider) czyta
ten folder dynamicznie, więc nowe pliki pojawią się automatycznie.

## O co jeszcze warto zapytać / sprawdzić na cba.pl

- Czy `mod_rewrite` (potrzebny do ładnych adresów URL Laravela) jest
  włączony domyślnie — zwykle tak na hostingu Apache, ale jeśli po
  wdrożeniu strona pokazuje błędy 404 na wszystkim poza stroną główną,
  to jest pierwszy podejrzany.
- Czy domena `ideart.com.pl` jest już wpięta w konto cba.pl (DNS/serwery
  nazw) — to osobna sprawa od samego wdrożenia kodu.
