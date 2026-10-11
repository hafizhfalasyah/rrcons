# Redeploy RRCONS ke Shared Hosting (cPanel)

Panduan untuk memindahkan versi terbaru (Laravel 13, PHP 8.5) ke shared hosting **tanpa** Composer, Node, atau VPS.
Paket rilis dibangun di komputer lokal (sudah berisi `vendor/` produksi), lalu diunggah.

> Gladi resik sudah dilakukan pada salinan database produksi di Docker: 9 migration baru berjalan
> sukses (± 40 detik), semua halaman publik dan admin 200, upload dan sitemap/feed normal.

---

## 0. Syarat hosting (cek dulu, jangan lanjut bila belum terpenuhi)

| Syarat | Cara cek di cPanel |
|---|---|
| **PHP 8.5** untuk domain ini | *Select PHP Version* / *MultiPHP Manager* |
| Ekstensi: `bcmath`, `ctype`, `curl`, `dom`, `exif`, `fileinfo`, `gd` (JPEG + WebP), `intl`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip` | *Select PHP Version → Extensions* |
| MySQL/MariaDB (database lama dipakai lagi) | *MySQL Databases* |
| Cara menjalankan perintah `php artisan`: **Terminal** cPanel / SSH, atau **Cron Jobs** sebagai alternatif | *Terminal* / *Cron Jobs* |

Bila PHP 8.5 belum tersedia: **hentikan**, karena aplikasi akan langsung berhenti (Composer platform check).
Minta hosting mengaktifkannya atau minta bantuan untuk menurunkan syarat ke PHP 8.3 (minimum Laravel 13).

---

## A. Di komputer lokal

1. **Ambil kode terbaru `main`**
   ```powershell
   git checkout main
   git pull
   ```
2. **Pastikan semua test lulus**
   ```powershell
   php artisan test
   # atau di Docker: docker compose --env-file .env.docker exec app php artisan test
   ```
3. **(Disarankan) Gladi resik dengan data produksi terbaru di Docker**
   1. Export database produksi dari phpMyAdmin (format SQL).
   2. Ganti isi database Docker dengan dump itu lalu jalankan migration seperti saat deploy:
      ```powershell
      powershell -ExecutionPolicy Bypass -File scripts\import-db.ps1 -File D:\backup\produksi.sql -Migrate
      ```
   3. Buka http://localhost:8082, cek halaman publik dan login admin. Perhatikan durasi dan pesan migration;
      hal yang sama akan terjadi di hosting.
4. **Bangun paket rilis**
   ```powershell
   powershell -ExecutionPolicy Bypass -File scripts\build-release.ps1
   ```
   Hasil: `release\rrcons-<commit>-<tanggal>.zip`, berisi kode + `vendor/` produksi.
   Paket **tidak** berisi `.env`, file upload (`storage/app/public`), test, Docker, maupun file agent.
5. **Siapkan nilai `.env` produksi** (lihat bagian C). Pakai **APP_KEY yang sama** dengan produksi.
   Jangan membuat key baru, karena sesi login dan token analytics bergantung pada key itu.

---

## B. Di hosting (cPanel)

> Contoh path: aplikasi di `/home/USER/rrcons`, domain menunjuk ke `/home/USER/rrcons/public`.
> Sesuaikan dengan struktur hosting Anda saat ini.

1. **Backup dulu**
   - Database: phpMyAdmin → *Export* (SQL), simpan di komputer.
   - Folder aplikasi lama, **terutama `storage/app/public`** (semua gambar/video yang diunggah admin) dan file `.env`.
2. **Set PHP 8.5** untuk domain (bagian 0) dan aktifkan ekstensinya.
3. **Unggah dan ekstrak paket ke folder baru**, misalnya `/home/USER/rrcons_new`
   (*File Manager → Upload → Extract*).
4. **Pindahkan data yang tidak ada di paket** dari folder lama ke folder baru:
   - `.env` → `rrcons_new/.env`, lalu cocokkan isinya dengan bagian C. Wajib ada `APP_TIMEZONE=Asia/Jakarta`.
   - `storage/app/public/` → `rrcons_new/storage/app/public/` (seluruh isinya).
5. **Tukar folder** (memudahkan rollback):
   `rrcons` → `rrcons_old`, lalu `rrcons_new` → `rrcons`.
6. **Document root**
   - Bila domain sudah menunjuk ke `rrcons/public`: tidak perlu apa-apa.
   - Bila web root adalah `public_html` dan isinya salinan folder `public/`: salin ulang isi
     `rrcons/public/` ke `public_html/`, lalu di `public_html/index.php` sesuaikan dua path
     `__DIR__.'/../vendor/autoload.php'` dan `__DIR__.'/../bootstrap/app.php'` ke folder `rrcons`
     (sama seperti `index.php` lama di hosting). Ikut sertakan `public_html/.htaccess` yang baru,
     karena batas upload sudah dinaikkan ke 64M.
7. **Jalankan perintah** lewat *Terminal* (`cd ~/rrcons`):
   ```bash
   php -v                                  # harus 8.5.x
   php artisan optimize:clear              # WAJIB: buang cache lama Laravel 10
   php artisan storage:link                # bila public/storage belum ada
   php artisan migrate --force             # jalankan SEKALIGUS, jangan --step
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
   Tanpa Terminal: buat **Cron Job sekali jalan** (set beberapa menit ke depan, lalu hapus setelah selesai), misalnya
   `cd /home/USER/rrcons && /usr/local/bin/php artisan optimize:clear && /usr/local/bin/php artisan migrate --force >> storage/logs/deploy.log 2>&1`
   (path PHP 8.5 bisa berbeda, lihat petunjuk di halaman Cron Jobs).
   Bila `storage:link` tidak bisa dijalankan, buat link lewat cron: `ln -s ../storage/app/public /home/USER/rrcons/public/storage`.
8. **Izin folder**: `storage/` dan `bootstrap/cache/` harus bisa ditulis (755 atau 775).
9. **Periksa** (lihat checklist D). Bila semua normal, hapus `rrcons_old` beberapa hari kemudian.

### Rollback
1. Tukar kembali folder: `rrcons` → `rrcons_broken`, `rrcons_old` → `rrcons`.
2. Pulihkan database dari backup langkah B.1 (phpMyAdmin → *Import*), karena migration baru mengubah struktur dan data.
3. Kembalikan versi PHP ke versi sebelumnya bila diubah.

---

## C. `.env` produksi (contoh, tanpa nilai rahasia)

```dotenv
APP_NAME=RRCONS
APP_ENV=production
APP_KEY=base64:...            # SAMA dengan .env produksi lama
APP_DEBUG=false
APP_URL=https://rrconsindonesia.com
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id                  # bahasa default pengunjung: id atau en

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=...                # dari cPanel
DB_USERNAME=...
DB_PASSWORD=...

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public

MAIL_MAILER=smtp               # isi sesuai email hosting
MAIL_HOST=...
MAIL_PORT=465
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=...
MAIL_FROM_NAME="${APP_NAME}"

# SEO & analytics (lihat .env.example untuk penjelasan)
SEO_GOOGLE_VERIFICATION=
ANALYTICS_ENABLED=true
ANALYTICS_EXCLUDE_AUTHENTICATED=true
TRUSTED_PROXIES=               # isi * bila memakai Cloudflare
ANALYTICS_COUNTRY_HEADER=      # CF-IPCountry bila memakai Cloudflare
OPTIMIZE_UPLOADED_IMAGES=true
```

---

## D. Checklist setelah deploy

- [ ] `/`, `/?lang=en`, `/rrfuture`, `/service`, `/blog`, satu artikel, `/contact` tampil normal (cek juga di HP).
- [ ] `/sitemap.xml`, `/feed`, `/robots.txt`, `/llms.txt` terbuka (200).
- [ ] Gambar lama tampil (artinya `storage/app/public` dan `public/storage` benar).
- [ ] Login admin → Dashboard (grafik analytics), Blog → daftar artikel, edit artikel, unggah gambar di editor (File Manager).
- [ ] Unggah satu gambar poster atau galeri lalu hapus lagi.
- [ ] Tidak ada error baru di `storage/logs/laravel.log`.
- [ ] Tidak perlu cron/queue worker. Artikel terjadwal tayang otomatis.
