# RRCONS — Docker untuk development lokal

Docker hanya untuk komputer lokal. Produksi tetap di shared hosting (lihat `docs/DEPLOY-SHARED-HOSTING.md`).

| Service | Image | Akses dari Windows | Fungsi |
|---|---|---|---|
| `app` | `rrcons-app` (dibangun dari `Dockerfile`: PHP **8.5.7** + Apache 2.4, ekstensi bcmath, exif, gd (JPEG/WebP/FreeType), intl, mysqli, pdo_mysql, zip, OPcache bawaan) | http://localhost:8082 | Aplikasi Laravel 13 |
| `db` | `mariadb:10.6.27` | `127.0.0.1:3307` | Database (data di volume `rrcons-db-data`) |
| `node` | `node:22-bookworm-slim` | — | `npm ci` bila perlu, lalu `build:front-css --watch` (Tailwind → `public/front/css/app.css`) |
| `mailpit` | `axllent/mailpit` | UI http://localhost:8025, SMTP 1025 | Menangkap semua email lokal |

Folder proyek di-mount ke container, jadi perubahan kode langsung terlihat tanpa build ulang.

> Watcher Tailwind (`node`) hanya **menambah** class selama berjalan; class yang dihapus dari view baru hilang setelah build ulang.
> Sebelum commit `public/front/css/app.css`, jalankan build bersih: `npm run build:front-css`
> (atau `docker compose --env-file .env.docker restart node`).

## Pertama kali

```powershell
copy .env.docker.example .env.docker      # isi password DB & RRCONS_APP_KEY
copy .env.example .env                    # bila belum ada; isi APP_KEY yang sama
docker compose --env-file .env.docker up -d --build
docker compose --env-file .env.docker exec app php artisan migrate
```

Data produksi (dump phpMyAdmin) bisa dimuat dengan:

```powershell
powershell -ExecutionPolicy Bypass -File scripts\import-db.ps1 -File D:\backup\produksi.sql -Migrate
```

## Perintah harian

```powershell
docker compose --env-file .env.docker up -d                    # start
docker compose --env-file .env.docker down                     # stop (data DB tetap)
docker compose --env-file .env.docker exec app php artisan test
docker compose --env-file .env.docker exec app php artisan optimize:clear
docker compose --env-file .env.docker logs -f app
```

Test **selalu** memakai SQLite in-memory (`phpunit.xml` + pengaman di `tests/CreatesApplication.php`),
jadi aman dijalankan di container walaupun container terhubung ke MariaDB.

## Tanpa Docker untuk PHP (native PHP 8.5)

PHP 8.5 di Windows bisa menjalankan aplikasi langsung, cukup `db` dan `mailpit` dari Docker:

```powershell
docker compose --env-file .env.docker up -d db mailpit
php artisan serve                         # http://localhost:8000
```

Nilai `.env` untuk mode ini (lihat `.env.example`): `DB_HOST=127.0.0.1`, `DB_PORT=3307`,
`DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD` sama dengan `RRCONS_DB_*` di `.env.docker`, `MAIL_HOST=127.0.0.1`,
`APP_URL=http://localhost:8000`. Nilai `DB_*` di `.env` tidak mengganggu Docker karena `compose.yaml` menimpanya.

## Gambar upload (`public/storage`)

`public/storage` adalah symlink **relatif** ke `../storage/app/public` sehingga bekerja di container dan di Windows.
Bila hilang, buat ulang dari Windows (butuh Developer Mode atau terminal Administrator):

```powershell
cmd /c mklink /D public\storage ..\storage\app\public
```

Container juga membuat link relatif yang sama saat start bila link belum ada.

## Batas upload

`docker/php/custom.ini` dan `public/.htaccess`: `upload_max_filesize=64M`, `post_max_size=72M`
(video Social Media boleh 50 MB).
