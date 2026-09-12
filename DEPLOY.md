# Automatyczne wdrażanie na cba.pl (hosting współdzielony, bez SSH)

Po każdym `git push` na branch `main` GitHub Actions samodzielnie zbuduje
aplikację (composer + npm) i wgra ją na hosting **przez FTP** — bez
potrzeby logowania się na serwer.

Strona działa pod: **https://ideart.com.pl/**

## Struktura na serwerze

cba.pl nazywa katalog domeny jej nazwą — **`/ideart.com.pl/`**, nie
`public_html`. Konto FTP nie pozwala tworzyć nowych folderów NA NAJWYŻSZYM
POZIOMIE (poza tym jednym, istniejącym), więc kod aplikacji trafia do
podfolderu wewnątrz niego:

```
/ideart.com.pl/                  <- katalog publiczny domeny (webroot)
├── index.php                    <- wskazuje na ./ideart-app/
├── .htaccess
├── build/                        (CSS/JS z Vite)
├── assets/
│   ├── images/logo.png
│   └── grafiki_animacje/         <- treść portfolio, wgrywana OSOBNO (patrz niżej)
└── ideart-app/                  <- kod Laravela, zablokowany .htaccess (Deny all)
    ├── app/, config/, routes/, resources/, database/, storage/
    ├── vendor/
    ├── .env                      (generowany automatycznie przy wdrożeniu)
    └── bootstrap/app.php
```

`bootstrap/app.php` wykrywa ten układ automatycznie (po strukturze
plików, nie przez zmienną środowiskową — `.env` ładuje się PO tym pliku)
i przestawia `public_path()` na katalog nadrzędny. Lokalny XAMPP działa
bez zmian, bo ma normalny `public/` obok `bootstrap/`.

## Szybkość wdrożenia

`vendor/` (tysiące plików) jest wgrywane **tylko wtedy, gdy zmienia się
`composer.lock`** — zwykłe zmiany w kodzie wdrażają się w ~1–2 minuty.
Wdrożenie z nową/zmienioną zależnością (zmiana `composer.lock`) zajmuje
20–35 minut, bo cba.pl mocno ogranicza przepustowość FTP przy dużej
liczbie plików — to nieuniknione przy tym hostingu, ale zdarza się rzadko.

## Sekrety w repozytorium GitHub

Ustawienia repo → **Settings → Secrets and variables → Actions → Secrets**:

| Nazwa sekretu | Wartość |
|---|---|
| `FTP_SERVER` | `www.mkwk086.cba.pl` |
| `FTP_USERNAME` | login FTP z panelu cba.pl |
| `FTP_PASSWORD` | hasło do tego konta FTP |
| `APP_KEY` | `base64:75EaBiUFxqMqxvD1Jg4Y8nK9Ye8wFrNwVtM9TAhiRN0=` |
| `MAIL_PASSWORD` | hasło aplikacji Gmail dla `rojekmar@gmail.com` (używane przez formularz kontaktowy do wysyłki e-mail przez SMTP) |

**Uwaga przy dodawaniu sekretów:** jeśli po zapisaniu sekret pokazuje się
na liście, ale w logu GitHub Actions wychodzi jako pusty — sprawdź, czy
w przeglądarce nie jest aktywny AdBlock/podobne rozszerzenie na
github.com. U nas to właśnie blokowało zapis wartości (nazwa się
zapisywała, wartość nie), mimo że formularz nie zgłaszał żadnego błędu.

`APP_URL` i `APP_NAME` są na stałe wpisane w workflow (nie jako
zmienne repo) — prościej, bo token użyty do konfiguracji nie miał
uprawnień do zakładki "Variables". Można to zmienić w
`.github/workflows/deploy.yml`, jeśli zajdzie taka potrzeba.

## Aktualizacja treści portfolio (zdjęcia/filmy)

Osobny proces od wdrażania kodu — folder `grafiki_animacje/` jest
wykluczony z automatycznego wdrożenia (500+ MB). Wgrywaj bezpośrednio
przez FTP do:

```
/ideart.com.pl/assets/grafiki_animacje/
```

## Historia i rzeczy warte zapamiętania

- **Konto FTP nie pozwala tworzyć folderów poza istniejącym katalogiem
  domeny** — stąd struktura z `ideart-app/` w środku, nie obok.
- Przy pierwszym wdrożeniu na serwerze pojawiła się (z niewyjaśnionej
  przyczyny — nie z tego workflow) osierocona, w pełni odsłonięta kopia
  repozytorium bezpośrednio w `/ideart.com.pl/` — **w tym publicznie
  dostępny `.git`**. Zostało to wykryte i usunięte. Jeśli kiedyś
  zobaczysz w katalogu domeny pliki inne niż wymienione w strukturze
  wyżej, zbadaj to i usuń.
- PHP na cba.pl trzeba było ręcznie przestawić w panelu z domyślnego
  5.6 na 8.2+ (projekt wymaga PHP ^8.2) — obecnie ustawione na 8.5.
- **`MAIL_PASSWORD` też padło ofiarą tego samego problemu z AdBlockiem**
  (sekret zapisał się pusty, formularz kontaktowy wywalał się na
  produkcji błędem `535 Username and Password not accepted` mimo że
  lokalnie działał). Jeśli formularz kontaktowy przestanie wysyłać
  maile po zmianie tego sekretu, sprawdź go tą samą metodą.
- Formularz kontaktowy (`/kontakt`) wysyła e-mail przez Gmail SMTP na
  adres z `MAIL_CONTACT_TO` (domyślnie `rojekmar@gmail.com`). Loginem
  jest `rojekmar@gmail.com`, a hasłem — **hasło aplikacji Gmail**
  (App Password), nie zwykłe hasło do konta.
