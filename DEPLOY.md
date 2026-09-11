# Automatyczne wdrażanie (CI/CD)

Po każdym `git push` na branch `main` GitHub Actions samodzielnie:
zbuduje aplikację (composer + npm), wyśle ją na VPS przez SSH i przełączy
serwer na nową wersję bez przestoju.

Treść portfolio (`public/assets/grafiki_animacje/`) **nie jest częścią
repozytorium** — jest zbyt duża (500+ MB, w tym pliki wideo grubo ponad
limit 100 MB/plik na GitHub) i zarządzana bezpośrednio na serwerze przez
SFTP/rsync, tak jak dotychczas.

## Jednorazowa konfiguracja serwera (VPS)

Wykonaj raz, zalogowany przez SSH na serwer:

```bash
# 1. Struktura katalogów (deploy.sh oczekuje dokładnie takiego układu)
sudo mkdir -p /var/www/marcin-projekt/{releases,shared/storage,shared/public/assets/grafiki_animacje}
sudo mkdir -p /var/www/marcin-projekt/shared/storage/{app/public,framework/cache/data,framework/sessions,framework/testing,framework/views,logs}
sudo chown -R $USER:www-data /var/www/marcin-projekt
sudo chmod -R 775 /var/www/marcin-projekt/shared/storage

# 2. Plik .env (współdzielony między wydaniami — NIE jest w git)
nano /var/www/marcin-projekt/shared/.env
# wklej zawartość podobną do .env.example z repo, uzupełnij dane produkcyjne
# (APP_ENV=production, APP_DEBUG=false, dane bazy danych, APP_URL=https://twojadomena.pl)

# 3. Klucz aplikacji (jednorazowo, potem zostaje w .env)
cd /var/www/marcin-projekt/shared
php artisan key:generate --path=.env  # albo wygeneruj lokalnie i wklej ręcznie do .env

# 4. Prawa do uruchamiania deploy.sh (skrypt trafi na serwer przy pierwszym wdrożeniu,
#    ale możesz go też wgrać ręcznie z repo już teraz)
```

Serwer WWW (Nginx/Apache) musi wskazywać na `/var/www/marcin-projekt/current/public`
— to jest katalog, na który zawsze wskazuje symlink `current`, podmieniany
przy każdym wdrożeniu.

### Przykład vhosta — Nginx

```nginx
server {
    listen 80;
    server_name twojadomena.pl;
    root /var/www/marcin-projekt/current/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Przykład vhosta — Apache

```apache
<VirtualHost *:80>
    ServerName twojadomena.pl
    DocumentRoot /var/www/marcin-projekt/current/public

    <Directory /var/www/marcin-projekt/current/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

(Upewnij się, że `mod_rewrite` jest włączone — `sudo a2enmod rewrite`.)

### Klucz SSH dla GitHub Actions

Na serwerze wygeneruj osobną parę kluczy tylko do wdrożeń (bez hasła):

```bash
ssh-keygen -t ed25519 -f ~/.ssh/github_deploy -N ""
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys
cat ~/.ssh/github_deploy   # to wklejasz jako sekret DEPLOY_SSH_KEY w GitHub
```

## Sekrety w repozytorium GitHub

Ustawienia repo → **Settings → Secrets and variables → Actions → New repository secret**:

| Nazwa sekretu | Wartość |
|---|---|
| `DEPLOY_HOST` | adres IP lub domena serwera |
| `DEPLOY_USER` | użytkownik SSH (np. `deploy` albo `ubuntu`) |
| `DEPLOY_SSH_KEY` | prywatny klucz SSH (zawartość `~/.ssh/github_deploy` z serwera) |
| `DEPLOY_PORT` | port SSH (domyślnie 22 — sekret opcjonalny) |
| `DEPLOY_PATH` | `/var/www/marcin-projekt` (ścieżka z kroku 1 wyżej) |

## Pierwsze wdrożenie

```bash
git remote add origin <URL-twojego-repo>
git push -u origin main
```

Od tego momentu każdy `git push` na `main` uruchamia wdrożenie automatycznie
(podgląd postępu: zakładka **Actions** w repozytorium na GitHub).

## Aktualizacja treści portfolio (zdjęcia/filmy)

To osobny proces od wdrożenia kodu — wgrywasz pliki bezpośrednio przez
SFTP/rsync do:

```
/var/www/marcin-projekt/shared/public/assets/grafiki_animacje/
```

(ten katalog jest symlinkowany do każdego nowego wydania, więc nie znika
przy kolejnych wdrożeniach kodu).
